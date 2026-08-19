<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A class defines the portfolio skill model with relations
 */
class PortfolioSkill extends Model
{
    use HasUuid;
    use SoftDeletes;
    use HasFactory;

    protected $table = 'portfolio_skills';

    protected $guarded = ['id'];

    /**
     * Define the skill relation
     *
     * @return BelongsTo
     */
    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

    /**
     * Define the relation with the portfolio
     *
     * @return BelongsTo
     */
    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }
}
