<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A class defines the commission setting model with relations
 */
class CommissionSetting extends Model
{
    use HasUuid;
    use SoftDeletes;
    use HasFactory;

    protected $guarded = ['id'];
}
