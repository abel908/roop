<?php

namespace App\Filament\Support;

use App\Models\MediaDocument;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Support\Icons\Heroicon;

/**
 * Block editor (§8.1): sections are composed only from validated components
 * of the design system, which guarantees the integrity of the design.
 * Every text is entered in English, French and Chinese side by side.
 */
class PageBlocks
{
    public const ICONS = [
        'target' => 'Target', 'briefcase' => 'Briefcase', 'users' => 'Users', 'globe' => 'Globe',
        'handshake' => 'Handshake', 'leaf' => 'Leaf', 'shield' => 'Shield', 'spark' => 'Spark',
        'calendar' => 'Calendar', 'file' => 'Document', 'map-pin' => 'Location', 'check-circle' => 'Check',
    ];

    public static function make(): Builder
    {
        return Builder::make('blocks')
            ->label(__('admin.fields.blocks'))
            ->collapsible()
            ->cloneable()
            ->reorderableWithButtons()
            ->blockNumbers(false)
            ->addActionLabel(__('admin.blocks.add'))
            ->blocks([
                Block::make('hero')->label(__('admin.blocks.hero'))->icon(Heroicon::OutlinedSparkles)->schema([
                    Translatable::text('eyebrow', __('admin.blocks.eyebrow')),
                    Translatable::text('title', __('admin.fields.title'), required: true),
                    Translatable::textarea('lead', __('admin.blocks.lead'), rows: 2),
                    self::image(),
                    Toggle::make('dark')->label(__('admin.blocks.dark'))->default(true),
                    Toggle::make('show_ctas')->label(__('admin.blocks.show_ctas')),
                ]),
                Block::make('text')->label(__('admin.blocks.text'))->icon(Heroicon::OutlinedBars3BottomLeft)->schema([
                    Translatable::text('heading', __('admin.blocks.heading')),
                    Translatable::rich('body', __('admin.fields.description')),
                ]),
                Block::make('image_text')->label(__('admin.blocks.image_text'))->icon(Heroicon::OutlinedPhoto)->schema([
                    Translatable::text('heading', __('admin.blocks.heading')),
                    Translatable::rich('body', __('admin.fields.description')),
                    self::image(required: true),
                    Translatable::text('alt', __('admin.blocks.alt')),
                    Select::make('position')->label(__('admin.blocks.image_position'))
                        ->options(['left' => __('admin.blocks.left'), 'right' => __('admin.blocks.right')])->default('right'),
                ]),
                Block::make('stats')->label(__('admin.blocks.stats'))->icon(Heroicon::OutlinedChartBar)->schema([
                    Translatable::text('heading', __('admin.blocks.heading')),
                    Toggle::make('dark')->label(__('admin.blocks.dark')),
                    Repeater::make('items')->label(__('admin.blocks.items'))->maxItems(4)->collapsible()->schema([
                        TextInput::make('value')->label(__('admin.fields.value'))->numeric()->required(),
                        TextInput::make('suffix')->label(__('admin.fields.suffix'))->maxLength(6),
                        Translatable::text('label', __('admin.fields.label'), required: true)->columnSpanFull(),
                    ])->columns(2),
                ]),
                Block::make('cards')->label(__('admin.blocks.cards'))->icon(Heroicon::OutlinedSquares2x2)->schema([
                    Translatable::text('heading', __('admin.blocks.heading')),
                    Translatable::textarea('lead', __('admin.blocks.lead'), rows: 2),
                    Repeater::make('items')->label(__('admin.blocks.items'))->collapsible()->schema([
                        Select::make('icon')->label(__('admin.fields.icon'))->options(self::ICONS),
                        TextInput::make('url')->label(__('admin.blocks.link'))->maxLength(255),
                        Translatable::text('title', __('admin.fields.title'), required: true)->columnSpanFull(),
                        Translatable::textarea('text', __('admin.fields.description'), rows: 2)->columnSpanFull(),
                    ])->columns(2),
                ]),
                Block::make('quote')->label(__('admin.blocks.quote'))->icon(Heroicon::OutlinedChatBubbleBottomCenterText)->schema([
                    Translatable::textarea('text', __('admin.blocks.quote'), required: true, rows: 3),
                    Translatable::text('author', __('admin.blocks.author')),
                ]),
                Block::make('video')->label(__('admin.blocks.video'))->icon(Heroicon::OutlinedFilm)->schema([
                    FileUpload::make('video')->label(__('admin.blocks.video_file'))->acceptedFileTypes(['video/mp4'])
                        ->disk('public')->directory('pages/videos')->maxSize(51200)->required(),
                    self::image(label: __('admin.fields.hero_poster')),
                    Translatable::text('caption', __('admin.blocks.caption')),
                ]),
                Block::make('faq')->label(__('admin.blocks.faq'))->icon(Heroicon::OutlinedQuestionMarkCircle)->schema([
                    Translatable::text('heading', __('admin.blocks.heading')),
                    Repeater::make('items')->label(__('admin.blocks.items'))->collapsible()->schema([
                        Translatable::text('q', __('admin.blocks.question'), required: true),
                        Translatable::textarea('a', __('admin.blocks.answer'), required: true, rows: 3),
                    ]),
                ]),
                Block::make('documents')->label(__('admin.documents'))->icon(Heroicon::OutlinedDocumentArrowDown)->schema([
                    Translatable::text('heading', __('admin.blocks.heading')),
                    Select::make('documents')->label(__('admin.documents'))->multiple()
                        ->options(fn () => MediaDocument::published()->get()->mapWithKeys(fn ($d) => [$d->id => $d->tr('title')])),
                ]),
                Block::make('domains')->label(__('admin.blocks.domains'))->icon(Heroicon::OutlinedSquares2x2)->schema([
                    Translatable::text('heading', __('admin.blocks.heading')),
                ]),
                Block::make('projects')->label(__('admin.blocks.projects'))->icon(Heroicon::OutlinedBriefcase)->schema([
                    Translatable::text('heading', __('admin.blocks.heading')),
                    Select::make('limit')->label(__('admin.blocks.limit'))->options([3 => 3, 6 => 6])->default(3),
                ]),
                Block::make('cta')->label(__('admin.blocks.cta'))->icon(Heroicon::OutlinedMegaphone)->schema([
                    Translatable::text('title', __('admin.fields.title'), required: true),
                    Translatable::textarea('text', __('admin.fields.description'), rows: 2),
                    Toggle::make('show_ctas')->label(__('admin.blocks.show_ctas'))->default(true),
                    Translatable::text('button_label', __('admin.blocks.button_label')),
                    TextInput::make('button_url')->label(__('admin.blocks.link'))->maxLength(255),
                ]),
            ]);
    }

    private static function image(bool $required = false, ?string $label = null): FileUpload
    {
        return FileUpload::make('image')->label($label ?? __('admin.blocks.image'))
            ->image()->imageEditor()->disk('public')->directory('pages')->maxSize(4096)
            ->automaticallyResizeImagesToWidth('2400')
            ->required($required);
    }
}
