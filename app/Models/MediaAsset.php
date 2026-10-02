<?php

namespace App\Models;

use App\Services\MediaService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class MediaAsset extends Model
{
    protected $fillable = ['path', 'thumbnail_path', 'mime_type', 'size_bytes', 'width', 'height', 'alt_text', 'original_name'];

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->path);
    }

    protected static function booted(): void
    {
        static::deleting(function (MediaAsset $asset) {
            if (app(MediaService::class)->isReferenced($asset)) {
                throw ValidationException::withMessages(['media' => 'Gambar masih digunakan dan tidak dapat dihapus.']);
            }
        });
    }
}
