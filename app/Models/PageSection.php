<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One section of a legal page (privacy policy, terms of use), in both languages.
 */
class PageSection extends Model
{
    use HasUuid;

    protected $guarded = ['id', 'uuid'];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}
