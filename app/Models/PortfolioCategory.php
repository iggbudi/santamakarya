<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class PortfolioCategory extends Model
{
    protected static function booted(): void
    {
        static::deleting(function (self $category) {
            if ($category->projects()->exists()) {
                throw ValidationException::withMessages(['category' => 'Pindahkan atau hapus proyek dalam kategori ini terlebih dahulu (termasuk proyek arsip).']);
            }
        });
    }

    protected $fillable = ['name', 'slug', 'sort_order'];

    public function projects(): HasMany
    {
        return $this->hasMany(PortfolioProject::class, 'category_id');
    }
}
