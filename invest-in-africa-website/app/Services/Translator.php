<?php

namespace App\Services;

use Anthropic\Client;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

/**
 * Automatic translation of missing languages with Claude (EN / FR / 中文).
 *
 * Finds, anywhere in a nested structure, the maps {"en": …, "fr": …, "zh": …}
 * that are filled in at least one language and empty in others, translates
 * them in a single request with a structured JSON answer, and writes the
 * translations back. Existing texts are never overwritten.
 */
class Translator
{
    public const LOCALES = ['en' => 'English', 'fr' => 'French', 'zh' => 'Simplified Chinese (zh-Hans)'];

    /** Texts per request, to keep answers well within the output limit. */
    private const BATCH = 40;

    public function __construct(private ?Client $client = null) {}

    public static function enabled(): bool
    {
        return filled(config('services.anthropic.key')) && Setting::get('translation.auto', true) !== false;
    }

    /**
     * @param  array<mixed>  $data  any array containing locale maps
     * @return array{0: array<mixed>, 1: int} completed data and number of texts translated
     */
    public function fill(array $data, array $skipKeys = []): array
    {
        $items = [];
        $this->collect($data, [], $items, $skipKeys);

        if (! $items || ! self::enabled()) {
            return [$data, 0];
        }

        $count = 0;

        foreach (array_chunk($items, self::BATCH) as $batch) {
            $translations = $this->translate($batch);

            foreach ($batch as $i => $item) {
                foreach ($item['targets'] as $locale => $targetPath) {
                    $text = $translations["t$i"][$locale] ?? null;

                    if (is_string($text) && $text !== '') {
                        data_set($data, $targetPath, $text);
                        $count++;
                    }
                }
            }
        }

        return [$data, $count];
    }

    public static function isLocaleMap(mixed $value): bool
    {
        return is_array($value) && $value !== [] && ! array_diff(array_keys($value), array_keys(self::LOCALES))
            && collect($value)->every(fn ($v) => $v === null || is_string($v) || (is_array($v) && array_is_list($v)));
    }

    /** @param array<int, array<string, mixed>> $items */
    private function collect(mixed $value, array $path, array &$items, array $skipKeys): void
    {
        if (! is_array($value)) {
            return;
        }

        if (self::isLocaleMap($value)) {
            $filled = array_filter($value, fn ($v) => filled($v));
            $missing = array_values(array_diff(array_keys(self::LOCALES), array_keys($filled)));

            if (! $filled || ! $missing) {
                return;
            }

            $source = array_key_exists('en', $filled) ? 'en' : array_key_first($filled);

            if (is_array($filled[$source])) {
                // Lists (services, benefits…): one item per entry, written at the same index.
                foreach (array_values($filled[$source]) as $index => $entry) {
                    if (is_string($entry) && $entry !== '') {
                        $items[] = ['source' => $source, 'text' => $entry, 'targets' => collect($missing)
                            ->mapWithKeys(fn ($l) => [$l => array_merge($path, [$l, $index])])->all()];
                    }
                }
            } else {
                $items[] = ['source' => $source, 'text' => $filled[$source], 'targets' => collect($missing)
                    ->mapWithKeys(fn ($l) => [$l => array_merge($path, [$l])])->all()];
            }

            return;
        }

        foreach ($value as $key => $child) {
            if (! in_array($key, $skipKeys, true)) {
                $this->collect($child, array_merge($path, [$key]), $items, $skipKeys);
            }
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $batch
     * @return array<string, array<string, string>>
     */
    private function translate(array $batch): array
    {
        $input = [];
        $properties = [];

        foreach ($batch as $i => $item) {
            $targets = array_keys($item['targets']);
            $input["t$i"] = ['from' => $item['source'], 'to' => $targets, 'text' => $item['text']];
            $properties["t$i"] = [
                'type' => 'object',
                'properties' => collect($targets)->mapWithKeys(fn ($l) => [$l => ['type' => 'string']])->all(),
                'required' => $targets,
                'additionalProperties' => false,
            ];
        }

        $schema = ['type' => 'object', 'properties' => $properties, 'required' => array_keys($properties), 'additionalProperties' => false];
        $answer = $this->complete(json_encode($input, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $schema);

        return $answer ? (json_decode($answer, true) ?: []) : [];
    }

    /** Sends the request to Claude and returns the JSON text of the answer. */
    protected function complete(string $payload, array $schema): ?string
    {
        try {
            $message = $this->client()->beta->messages->create(
                model: config('services.anthropic.model'),
                maxTokens: 16000,
                system: $this->instructions(),
                messages: [['role' => 'user', 'content' => $payload]],
                outputConfig: [
                    'effort' => 'medium',
                    'format' => ['type' => 'json_schema', 'schema' => $schema],
                ],
                // Server-side fallback model if the request is declined.
                fallbacks: 'default',
                betas: ['server-side-fallback-2026-07-01'],
            );
        } catch (\Throwable $e) {
            Log::warning('Automatic translation failed', ['error' => $e->getMessage()]);

            return null;
        }

        if ($message->stopReason === 'refusal') {
            Log::warning('Automatic translation declined');

            return null;
        }

        foreach ($message->content as $block) {
            if ($block->type === 'text') {
                return $block->text;
            }
        }

        return null;
    }

    private function instructions(): string
    {
        return <<<'TXT'
        You translate the website of "The Invest In Africa Initiative", an institution promoting investment and economic development in Africa, for investors, project holders, governments and international partners.

        The user message is a JSON object. For each entry, translate "text" from the "from" language into every language listed in "to" (en = English, fr = French, zh = Simplified Chinese for mainland China). Answer with the same entry keys and one translation per target language.

        Rules:
        - Institutional, precise and sober tone; natural wording for a native reader, never word-for-word.
        - Keep the meaning exactly: add nothing, remove nothing, invent no facts, figures or names.
        - Keep unchanged: "The Invest In Africa Initiative", other proper names, project references such as P-2026-001, URLs, e-mail addresses, numbers and currency codes.
        - Keep HTML tags and attributes exactly as they are and translate only the text between them. Keep placeholders such as :name or :reference untouched.
        - French: French typography (non-breaking space before : ; ? !, « » quotation marks, ’ apostrophe).
        - Chinese: Simplified Chinese with full-width punctuation (，。：；？！“”), no italics, no forced capitals; keep Latin-script names as they are.
        - Keep the length and register close to the source (titles stay short titles).
        TXT;
    }

    private function client(): Client
    {
        return $this->client ??= new Client(apiKey: (string) config('services.anthropic.key'));
    }
}
