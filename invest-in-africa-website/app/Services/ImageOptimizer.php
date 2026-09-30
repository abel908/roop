<?php

namespace App\Services;

use GdImage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Automatic conversion of uploaded images to AVIF and WebP at several widths
 * (§10.4 "WebP et AVIF", "Images responsives"). The original file is kept as
 * JPEG / PNG fallback. Variants are named deterministically so the site can
 * build srcset attributes without a database lookup.
 */
class ImageOptimizer
{
    public const WIDTHS = [480, 960, 1600];

    public const FORMATS = ['avif' => 52, 'webp' => 80];

    private const SOURCE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    public static function variantPath(string $path, int $width, string $format): string
    {
        $info = pathinfo($path);
        $dir = ($info['dirname'] ?? '.') === '.' ? '' : $info['dirname'].'/';

        return $dir.'optimized/'.$info['filename'].'-'.$width.'.'.$format;
    }

    public static function supports(string $path): bool
    {
        return in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), self::SOURCE_EXTENSIONS, true);
    }

    /**
     * @return array{width: int, height: int, variants: array<string, array<int, string>>}|null
     */
    public function optimize(string $path, string $disk = 'public'): ?array
    {
        $storage = Storage::disk($disk);

        if (! self::supports($path) || ! $storage->exists($path)) {
            return null;
        }

        $image = @imagecreatefromstring($storage->get($path));

        if (! $image instanceof GdImage) {
            Log::warning('Image could not be decoded for optimisation', ['path' => $path]);

            return null;
        }

        $width = imagesx($image);
        $height = imagesy($image);
        $variants = [];

        // Widths below the original, plus the original width capped at the largest target (no upscaling).
        $targets = array_values(array_filter(self::WIDTHS, fn (int $t) => $t < $width));
        $targets[] = min($width, max(self::WIDTHS));
        $targets = array_values(array_unique($targets));

        foreach ($targets as $w) {
            $h = (int) round($height * $w / $width);
            $resized = imagecreatetruecolor($w, $h);
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            imagecopyresampled($resized, $image, 0, 0, 0, 0, $w, $h, $width, $height);

            foreach (self::FORMATS as $format => $quality) {
                if (! function_exists('image'.$format)) {
                    continue;
                }

                ob_start();
                $format === 'avif' ? imageavif($resized, null, $quality, 6) : imagewebp($resized, null, $quality);
                $binary = ob_get_clean();

                if ($binary) {
                    $variantPath = self::variantPath($path, $w, $format);
                    $storage->put($variantPath, $binary, 'public');
                    $variants[$format][$w] = $variantPath;
                }
            }

            imagedestroy($resized);
        }

        imagedestroy($image);

        $manifest = ['width' => $width, 'height' => $height, 'variants' => $variants];
        $storage->put(self::manifestPath($path), json_encode($manifest), 'public');

        return $manifest;
    }

    public static function manifestPath(string $path): string
    {
        $info = pathinfo($path);
        $dir = ($info['dirname'] ?? '.') === '.' ? '' : $info['dirname'].'/';

        return $dir.'optimized/'.$info['filename'].'.json';
    }

    /**
     * Variants previously generated for an image (read from its manifest).
     *
     * @return array{width: int, height: int, variants: array<string, array<int, string>>}|null
     */
    public static function manifest(string $path, string $disk = 'public'): ?array
    {
        static $cache = [];

        return $cache[$disk.$path] ??= (function () use ($path, $disk) {
            $file = self::manifestPath($path);

            return Storage::disk($disk)->exists($file) ? json_decode(Storage::disk($disk)->get($file), true) : null;
        })();
    }

    /** Deletes the variants of an image that has been replaced or removed. */
    public function forget(string $path, string $disk = 'public'): void
    {
        $manifest = self::manifest($path, $disk);

        foreach ($manifest['variants'] ?? [] as $files) {
            Storage::disk($disk)->delete(array_values($files));
        }

        Storage::disk($disk)->delete(self::manifestPath($path));
    }
}
