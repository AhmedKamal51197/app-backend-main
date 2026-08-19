<?php

namespace App\Models\PaymentGateway;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A class defines the payment webhook logs
 */
class PaymentGatewayWebhookLog extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasUuid;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'request_body' => AsCollection::class,
        'request_header' => AsCollection::class,
    ];
}
