<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\IsEnable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A class defines the budget range model with relations
 */
class BudgetRange extends Model
{
    use IsEnable;
    use HasUuid;
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = ['is_enabled' => 'boolean'];
}
