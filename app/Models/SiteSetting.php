<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteSetting extends Model
{
    protected $fillable = ['draft_payload', 'published_version_id'];

    protected function casts(): array
    {
        return ['draft_payload' => 'array'];
    }

    public function publishedVersion(): BelongsTo
    {
        return $this->belongsTo(PageVersion::class, 'published_version_id');
    }
}
