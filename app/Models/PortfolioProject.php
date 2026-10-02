<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortfolioProject extends Model
{
    protected $fillable = ['title', 'category_id', 'cover_media_asset_id', 'image_url', 'alt_text', 'caption', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PortfolioCategory::class, 'category_id');
    }

    public function coverMediaAsset(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'cover_media_asset_id');
    }
}
