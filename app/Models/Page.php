<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    use HasUuid;
    use HasFactory;

    protected $fillable = [
        'key',
        'title_ar',
        'title_en',
        'summary_ar',
        'summary_en',
        'version',
        'published_at',
        'en_enabled',
        'content',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'en_enabled' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Sections in display order (including hidden ones).
     */
    public function sections(): HasMany
    {
        return $this->hasMany(PageSection::class)->orderBy('sort_order');
    }
}
