<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A class defined for the education
 */
class Education extends Model
{
    use HasFactory;
    use HasUuid;
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $table = 'educations';

    protected $casts = [
        'start_year' => 'year',
        'end_year' => 'year',
    ];

    /**
     * Get the user associated with the education.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user associated with the education degree.
     */
    public function educationDegree(): BelongsTo
    {
        return $this->belongsTo(EducationDegree::class);
    }

    /**
     * Adding image for education, and its nullable
     *
     * @return MorphOne
     */
    public function image(): MorphOne
    {
        return $this->morphOne(Attachment::class, 'attachable');
    }
}

