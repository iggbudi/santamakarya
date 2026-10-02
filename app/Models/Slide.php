<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Slide extends Model
{
    protected $fillable = ['location', 'media_asset_id', 'image_url', 'alt_text', 'position_x', 'position_y', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'position_x' => 'integer', 'position_y' => 'integer', 'sort_order' => 'integer'];
    }

    public function mediaAsset(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class);
    }
}
