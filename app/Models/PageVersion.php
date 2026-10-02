<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

class PageVersion extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['payload', 'created_by'];

    protected function casts(): array
    {
        return ['payload' => 'array'];
    }

    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('Snapshot publikasi tidak dapat diubah.'));
        static::deleting(fn () => throw new LogicException('Snapshot publikasi tidak dapat dihapus.'));
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
