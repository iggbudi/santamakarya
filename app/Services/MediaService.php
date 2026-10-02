<?php

namespace App\Services;

use App\Models\MediaAsset;
use App\Models\PageVersion;
use App\Models\PortfolioProject;
use App\Models\SiteSetting;
use App\Models\Slide;
use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class MediaService
{
    public function upload(UploadedFile $file): MediaAsset
    {
        if (! $file->isValid() || $file->getSize() > 5 * 1024 * 1024) {
            throw ValidationException::withMessages(['file' => 'Ukuran gambar maksimal 5 MB.']);
        }
        $bytes = file_get_contents($file->getRealPath());
        $info = @getimagesizefromstring($bytes);
        $types = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (! $info || ! isset($types[$info['mime']]) || $info[0] * $info[1] > 25000000) {
            throw ValidationException::withMessages(['file' => 'Gunakan gambar JPEG, PNG, atau WebP yang valid (maksimal 25 megapixel).']);
        }
        $image = @imagecreatefromstring($bytes);
        if (! $image) {
            throw ValidationException::withMessages(['file' => 'Gambar tidak dapat dibaca.']);
        }
        if ($info['mime'] === 'image/jpeg') {
            $exif = @exif_read_data($file->getRealPath());
            $orientation = $exif['Orientation'] ?? 1;
            if (in_array($orientation, [2, 4, 5, 7], true)) {
                imageflip($image, IMG_FLIP_HORIZONTAL);
            }
            $angle = match ($orientation) {
                3, 4 => 180, 5, 6 => -90, 7, 8 => 90, default => 0
            };
            if ($angle) {
                $rotated = imagerotate($image, $angle, 0);
                imagedestroy($image);
                $image = $rotated;
            }
        }
        $main = $this->resize($image, 2400);
        $thumbnail = $this->resize($image, 480);
        $extension = $types[$info['mime']];
        $name = (string) Str::uuid();
        $path = "media/{$name}.{$extension}";
        $thumbnailPath = "media/thumbnails/{$name}.{$extension}";
        $disk = Storage::disk('public');
        try {
            $encoded = $this->encode($main, $info['mime']);
            if (! $disk->put($path, $encoded) || ! $disk->put($thumbnailPath, $this->encode($thumbnail, $info['mime']))) {
                throw new \RuntimeException('Penyimpanan gambar gagal.');
            }

            return MediaAsset::create([
                'path' => $path, 'thumbnail_path' => $thumbnailPath,
                'mime_type' => $info['mime'], 'size_bytes' => strlen($encoded),
                'width' => imagesx($main), 'height' => imagesy($main),
                'original_name' => mb_substr(basename($file->getClientOriginalName()), 0, 255),
                'alt_text' => '',
            ]);
        } catch (Throwable $error) {
            $disk->delete([$path, $thumbnailPath]);
            throw $error;
        } finally {
            imagedestroy($image);
            imagedestroy($main);
            imagedestroy($thumbnail);
        }
    }

    private function resize(GdImage $source, int $maximum): GdImage
    {
        $ratio = min(1, $maximum / max(imagesx($source), imagesy($source)));
        $width = max(1, (int) round(imagesx($source) * $ratio));
        $height = max(1, (int) round(imagesy($source) * $ratio));
        $result = imagecreatetruecolor($width, $height);
        imagealphablending($result, false);
        imagesavealpha($result, true);
        imagefill($result, 0, 0, imagecolorallocatealpha($result, 0, 0, 0, 127));
        imagecopyresampled($result, $source, 0, 0, 0, 0, $width, $height, imagesx($source), imagesy($source));

        return $result;
    }

    private function encode(GdImage $image, string $mime): string
    {
        ob_start();
        match ($mime) {
            'image/jpeg' => imagejpeg($image, null, 88),
            'image/png' => imagepng($image, null, 6),
            'image/webp' => imagewebp($image, null, 88),
        };

        return ob_get_clean();
    }

    /** @return list<string> */
    public function usage(MediaAsset $asset): array
    {
        $usage = [];
        foreach (SiteSetting::all() as $settings) {
            if (in_array($asset->id, $this->referencedIds($settings->draft_payload), true)) {
                $usage[] = 'Pengaturan draft';
            }
        }
        foreach (Slide::where('media_asset_id', $asset->id)->get() as $slide) {
            $usage[] = 'Slideshow '.($slide->location === 'hero' ? 'Hero' : 'Tentang').' (draft)';
        }
        foreach (PortfolioProject::where('cover_media_asset_id', $asset->id)->get() as $project) {
            $usage[] = 'Portofolio: '.$project->title.' (draft)';
        }
        foreach (PageVersion::all(['id', 'payload']) as $version) {
            if (in_array($asset->id, $this->referencedIds($version->payload), true)) {
                $usage[] = 'Snapshot #'.$version->id;
            }
        }

        return array_values(array_unique($usage));
    }

    /** @return list<int> */
    public function referencedIds(array $payload): array
    {
        $ids = [];
        foreach ($payload as $key => $value) {
            if (is_array($value)) {
                $ids = [...$ids, ...$this->referencedIds($value)];
            } elseif ($value && is_string($key) && (str_ends_with($key, '_media_id') || str_ends_with($key, 'media_asset_id'))) {
                $ids[] = (int) $value;
            }
        }

        return array_values(array_unique($ids));
    }

    public function isReferenced(MediaAsset $asset): bool
    {
        return $this->usage($asset) !== [];
    }

    public function delete(MediaAsset $asset): void
    {
        $paths = DB::transaction(function () use ($asset) {
            // Serialize with publish/restore so a snapshot cannot acquire an asset being deleted.
            SiteSetting::lockForUpdate()->findOrFail(1);
            if ($this->isReferenced($asset)) {
                throw ValidationException::withMessages(['media' => 'Gambar masih digunakan: '.implode(', ', $this->usage($asset))]);
            }
            $paths = array_filter([$asset->path, $asset->thumbnail_path]);
            $asset->delete();

            return $paths;
        });
        Storage::disk('public')->delete($paths);
    }
}
