<?php

namespace App\Models;

use App\Enums\AttachmentDocumentTypeEnum;
use App\Enums\KycStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Enums\ProjectStatusEnum;
use App\Models\PaymentGateway\PaymentGatewayCheckout;
use App\Notifications\VerifyEmail\VerifyEmailOtpNotification;
use App\Notifications\Welcome\AccountActivationNotification;
use App\Notifications\Welcome\CompleteProfileNotification;
use App\Notifications\Welcome\WelcomeNotification;
use App\Traits\HasUuid;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * A class defines the User model with relations
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use HasRoles;
    use HasUuid;
    use SoftDeletes;
    use TwoFactorAuthenticatable;

    /**
     * The Guard for user is api
     *
     * @var string
     */
    protected string $guard_name = 'api';

    protected $with = ['avatar'];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'mobile_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_team_member' => 'boolean',
        'email_verification_otp_expires_at' => 'datetime',
        'fcm_token' => 'string',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['profile_photo_url',];

    /**
     * Define the relation with Country model
     *
     * @return BelongsTo
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    /**
     * Define the relation with Role
     *
     * @return BelongsTo
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Generate and send OTP for email verification
     *
     * @return void
     */
    public function sendEmailVerificationNotification(): void
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->update([
            'email_verification_otp' => $otp,
            'email_verification_otp_expires_at' => Carbon::now()->addMinutes(5),
        ]);

        $this->notify(new VerifyEmailOtpNotification($otp));
    }

    /**
     * Verify email using OTP
     *
     * @param string $otp
     * @param bool $reset
     *
     * @return bool
     */
    public function verifyEmailWithOtp(string $otp, bool $reset): bool
    {
        // Check if OTP matches and hasn't expired
        if ($this->email_verification_otp === $otp &&
            $this->email_verification_otp_expires_at &&
            Carbon::now()->isBefore($this->email_verification_otp_expires_at)) {

            if ($reset) {
                $this->update([
                    'email_verified_at' => Carbon::now(),
                ]);
            } else {
                $this->update([
                    'email_verified_at' => Carbon::now(),
                    'email_verification_otp' => null,
                    'email_verification_otp_expires_at' => null,
                ]);
            }
            return true;
        }
        return false;
    }

    /**
     * Check if OTP has expired
     *
     * @return bool
     */
    public function isOtpExpired(): bool
    {
        return !$this->email_verification_otp_expires_at ||
            Carbon::now()->isAfter($this->email_verification_otp_expires_at);
    }

    /**
     * Define the user category
     *
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Define the user sub category
     *
     * @return BelongsTo
     */
    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class);
    }

    /**
     * Define the relation with sub categories table
     *
     * @return HasMany
     */
    public function userSubCategories(): HasMany
    {
        return $this->hasMany(SubCategoryUser::class);
    }

    /**
     * Define the relation with skills table
     *
     * @return HasMany
     */
    public function userSkills(): HasMany
    {
        return $this->hasMany(SkillUser::class);
    }

    /**
     * Get the wallet
     */
    public function wallets(): HasMany
    {
        return $this->hasMany(Wallet::class);
    }

    /**
     * Define the wallet balance
     */
    public function walletBalance(): float
    {
        return $this->wallets()->sum('balance');
    }

    /**
     * Get the Gateway Checkouts for the user
     */
    public function paymentGatewayCheckouts(): MorphMany
    {
        return $this->morphMany(PaymentGatewayCheckout::class, 'payable');
    }

    /**
     * Get the Gateway Checkouts for the user
     */
    public function pendingBalance(): float
    {
        return $this->paymentGatewayCheckouts()->where('is_processed', false)->sum('amount');
    }

    /**
     * Define the relation with certificates table
     *
     * @return HasMany
     */
    public function userCertificates(): HasMany
    {
        return $this->hasMany(CertificateUser::class);
    }

    /**
     * Adding profile photo
     *
     * @return MorphOne
     */
    public function avatar(): MorphOne
    {
        return $this->morphOne(Attachment::class, 'attachable');
    }

    /**
     * Get the bank account associated with the user.
     */
    public function bankAccount(): HasOne
    {
        return $this->hasOne(BankAccount::class);
    }

    /**
     * Check if user has bank account
     *
     * @return bool
     */
    public function hasBankAccount(): bool
    {
        return $this->bankAccount()->exists();
    }

    /**
     * Get the PayPal associated with the user.
     */
    public function paypal(): HasOne
    {
        return $this->hasOne(Paypal::class);
    }

    /**
     * Check if user has PayPal
     *
     * @return bool
     */
    public function hasPaypal(): bool
    {
        return $this->paypal()->exists();
    }

    /**
     * Adding educations relation
     *
     * @return HasMany
     */
    public function educations(): HasMany
    {
        return $this->hasMany(Education::class);
    }

    /**
     * Adding portfolios relation
     *
     * @return HasMany
     */
    public function portfolios(): HasMany
    {
        return $this->hasMany(Portfolio::class);
    }

    /**
     * Adding services relation
     *
     * @return HasMany
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class)->where('hidden', '=', false);
    }

    /**
     * Get services user ordered as seeker
     */
    public function orderedServices()
    {
        return Service::query()
            ->with('user', 'category', 'subCategory') // Load the service provider (user)
            ->join('service_packages', 'service_packages.service_id', '=', 'services.id')
            ->join('orders', 'orders.orderable_id', '=', 'service_packages.id')
            ->where('orders.orderable_type', ServicePackage::class)
            ->where('orders.seeker_id', $this->id)
            ->select('services.*', 'orders.created_at as order_date', 'orders.completed_at', 'orders.status', 'orders.price')
            ->orderBy('orders.created_at', 'desc'); // Most recent orders first
    }
    /**
     * Get projects user worked on as provider
     */
    public function workedProjects(): HasManyThrough
    {
        return $this->hasManyThrough(
            Project::class,
            Order::class,
            'provider_id',
            'id',
            'id',
            'orderable_id'
        )->where('orders.orderable_type', Project::class);
    }

    /**
     * Adding kycs relation
     *
     * @return HasMany
     */
    public function kycs(): HasMany
    {
        return $this->hasMany(Kyc::class);
    }

    /**
     * Get the latest kyc
     *
     * @return HasOne
     */
    public function latestKyc(): HasOne
    {
        return $this->hasOne(Kyc::class)->latestOfMany();
    }

    /**
     * Check if user verified
     *
     * @return bool
     */
    public function isKycVerified(): bool
    {
        return Kyc::where('user_id', $this->id)
            ->where('status', KycStatusEnum::APPROVED->value)
            ->exists();
    }

    /**
     * Get the user contract
     *
     * @return Attachment|null
     */
    public function contract(): ?Attachment
    {
        $approvedKyc = $this->kycs()
            ->where('status', KycStatusEnum::APPROVED->value)
            ->latest()
            ->first();

        if (!$approvedKyc) {
            return null;
        }

        return $approvedKyc->attachments()
            ->where('document_type', AttachmentDocumentTypeEnum::CONTRACT->value)
            ->first();
    }

    /**
     * Check if user has category
     *
     * @return bool
     */
    public function hasCategory(): bool
    {
        return $this->category()->exists();
    }

    /**
     * Welcome notifications
     *
     * @return void
     */
    public function welcomeMails(): void
    {
        $this->notify(new AccountActivationNotification());
        $this->notify(new CompleteProfileNotification());
        $this->notify(new WelcomeNotification());
    }

    /**
     * Define the average rate
     *
     * @return float
     */
    public function averageRate(): float
    {
        return round($this->rates()->avg('rate') ?? 0, 2);
    }

    /**
     * Define the ratings count
     *
     * @return int
     */
    public function ratingsCount(): int
    {
        return $this->rates()->count();
    }

    /**
     * Get the rates received by this user
     *
     * @return HasMany
     */
    public function rates(): HasMany
    {
        return $this->hasMany(Rate::class, 'rated_user_id');
    }

    /**
     * Define the experiences relation
     *
     * @return HasMany
     */
    public function experiences(): HasMany
    {
        return $this->hasMany(Experience::class);
    }

    /**
     * Adding completed projects
     *
     * @return int
     */
    public function completedProjects(): int
    {
        return Order::where(function ($query) {
            $query->where('seeker_id', $this->id)
                ->orWhere('provider_id', $this->id);
        })
            ->whereIn('status', [OrderStatusEnum::COMPLETED->value, OrderStatusEnum::RELEASED->value])
            ->count();
    }

    /**
     * Adding provider orders relation
     */
    public function providerOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'provider_id');
    }

    /**
     * Adding seeker orders relation
     */
    public function seekerOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'seeker_id');
    }

    /**
     * Adding payment requests relation
     */
    public function paymentRequests(): HasMany
    {
        return $this->hasMany(PaymentRequest::class);
    }

    /**
     * Adding reports relation
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    /**
     * Adding projects relation
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Get completed projects count (from Project orders)
     */
    public function completedProjectsCount(): int
    {
        return Order::where('provider_id', $this->id)
            ->whereIn('status', [OrderStatusEnum::COMPLETED->value, OrderStatusEnum::RELEASED->value])
            ->where('orderable_type', Project::class)
            ->count();
    }

    /**
     * Get active projects count (from Project entities)
     */
    public function activeProjectsCount(): int
    {
        return $this->projects()->where('status', ProjectStatusEnum::IN_PROGRESS->value)->count();
    }

    /**
     * Get pending projects count (from Project entities)
     */
    public function pendingProjectsCount(): int
    {
        return $this->projects()->where('status', ProjectStatusEnum::PENDING->value)->count();
    }

    /**
     * Get canceled projects count (from Project entities)
     */
    public function cancelledProjectsCount(): int
    {
        return $this->projects()->where('status', ProjectStatusEnum::CANCELLED->value)->count();
    }

    /**
     * Get total spending (as seeker)
     */
    public function totalSpending(): float
    {
        return $this->seekerOrders()
            ->whereIn('status', [OrderStatusEnum::COMPLETED->value, OrderStatusEnum::RELEASED->value])
            ->selectRaw('SUM(price + seeker_commissions) as total')
            ->value('total') ?? 0;
    }

    /**
     * Get platform net profit from user
     */
    public function platformNetProfit(): float
    {
        return Commission::whereHasMorph('payable', [Order::class], function ($query) {
            $query->where('seeker_id', $this->id)
                  ->whereIn('status', [OrderStatusEnum::COMPLETED->value, OrderStatusEnum::RELEASED->value]);
        })->sum('amount') ?? 0;
    }

    /**
     * Get active services count
     */
    public function activeServicesCount(): int
    {
        return $this->services()->count();
    }

    /**
     * Get total budget from active projects
     */
    public function totalBudget(): float
    {
        return $this->projects()
            ->whereIn('status', [ProjectStatusEnum::PENDING->value, ProjectStatusEnum::IN_PROGRESS->value])
            ->sum('max_price');
    }

    /**
     * Get completed services count
     */
    public function completedServicesCount(): int
    {
        return Order::where('provider_id', $this->id)
            ->whereIn('status', [OrderStatusEnum::COMPLETED->value, OrderStatusEnum::RELEASED->value])
            ->where('orderable_type', ServicePackage::class)
            ->count();
    }

    /**
     * Get completed projects budget
     */
    public function completedProjectsBudget(): float
    {
        return Order::where('provider_id', $this->id)
            ->whereIn('status', [OrderStatusEnum::COMPLETED->value, OrderStatusEnum::RELEASED->value])
            ->where('orderable_type', Project::class)
            ->sum('price');
    }

    /**
     * Get active services budget
     */
    public function activeServicesBudget(): float
    {
        return Order::where('provider_id', $this->id)
            ->whereIn('status', [OrderStatusEnum::IN_PROGRESS->value, OrderStatusEnum::APPROVAL_PENDING->value])
            ->where('orderable_type', ServicePackage::class)
            ->sum('price');
    }

    /**
     * Get canceled services count
     */
    public function cancelledServicesCount(): int
    {
        return Order::where('provider_id', $this->id)
            ->where('status', OrderStatusEnum::CANCELLED->value)
            ->where('orderable_type', ServicePackage::class)
            ->count();
    }

    /**
     * Get total profit (earnings as provider)
     */
    public function totalProfit(): float
    {
        return $this->providerOrders()
            ->whereIn('status', [OrderStatusEnum::COMPLETED->value, OrderStatusEnum::RELEASED->value])
            ->sum('price');
    }

    /**
     * Get total withdraw amount
     */
    public function totalWithdrawn(): float
    {
        return $this->paymentRequests()
            ->where('status', 'approved')
            ->sum('amount');
    }

    /**
     * Define the chats relation
     *
     * @return BelongsToMany
     */
    public function chats(): BelongsToMany
    {
        return $this->belongsToMany(Chat::class, 'chat_participants')
            ->withPivot(['joined_at', 'last_read_at', 'is_admin'])
            ->withTimestamps();
    }

    /**
     * Route notifications for the FCM channel.
     *
     * @return string|null
     */
    public function routeNotificationForFcm(): ?string
    {
        return $this->fcm_token;
    }
}
