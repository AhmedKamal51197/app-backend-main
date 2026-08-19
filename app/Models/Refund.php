<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A class defines the refund model with relations
 */
class Refund extends Model
{
    use HasUuid;
    use SoftDeletes;
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Define commission morph relation
     *
     * @return MorphTo
     */
    public function refundable(): MorphTo
    {
        return $this->morphTo();
    }
}
