<?php

namespace Database\Seeders;

use App\Enums\OrderStatusEnum;
use App\Enums\ServiceTypeEnum;
use App\Models\Category;
use App\Models\Country;
use App\Models\Order;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Sample orders waiting for an admin decision, so the dashboard can test
 * approve-cancellation / reject-cancellation and the disputes list.
 *
 * Everything belongs to two dedicated sample accounts and an unapproved sample
 * service, so nothing shows in the app or notifies real users. Running it
 * again only tops the list back up after rows were approved or rejected.
 *
 *   php artisan db:seed --class="Database\Seeders\DashboardDisputesSeeder"
 */
class DashboardDisputesSeeder extends Seeder
{
    /**
     * Marker that identifies the orders created by this seeder.
     */
    private const SENTINEL = 'sample-seed';

    /**
     * How many sample orders to keep in each status.
     */
    private const TARGET = 3;

    public function run(): void
    {
        $seeker = $this->sampleUser('seed.seeker@moawen.test', 'عميل تجريبي', 'seeker');
        $provider = $this->sampleUser('seed.provider@moawen.test', 'مستقل تجريبي', 'provider');
        $package = $this->samplePackage($provider);

        $this->topUp($package, $seeker, $provider, OrderStatusEnum::CANCEL_PENDING, [
            'cancellation_reason' => 'تأخر المستقل في التسليم (بيانات تجريبية)',
            'cancelled_by' => 'seeker',
            'disputed_by' => 'seeker',
        ]);

        $this->topUp($package, $seeker, $provider, OrderStatusEnum::DISPUTED, [
            'disputed_reason' => 'العمل المسلَّم لا يطابق المتفق عليه (بيانات تجريبية)',
            'disputed_by' => 'seeker',
            'disputed_at' => now(),
        ]);
    }

    /**
     * Keep TARGET sample orders in the given status
     */
    private function topUp(ServicePackage $package, User $seeker, User $provider, OrderStatusEnum $status, array $extra): void
    {
        $need = self::TARGET - Order::where('code', self::SENTINEL)->where('status', $status->value)->count();

        for ($i = 0; $i < $need; $i++) {
            // No model events: the sample rows must not raise dashboard notifications
            Order::withoutEvents(fn () => Order::create(array_merge([
                'uuid' => (string) Str::uuid(),
                'price' => $package->price,
                'seeker_commissions' => round($package->price * 0.065, 2),
                'code' => self::SENTINEL,
                'orderable_id' => $package->id,
                'orderable_type' => ServicePackage::class,
                'status' => $status->value,
                'seeker_id' => $seeker->id,
                'provider_id' => $provider->id,
                'category_id' => $package->service->category_id,
                'sub_category_id' => $package->service->sub_category_id,
                'time' => 5,
                'approved_at' => now()->subDays(3),
            ], $extra)));
        }
    }

    /**
     * A dedicated sample account
     */
    private function sampleUser(string $email, string $name, string $role): User
    {
        $user = User::firstOrCreate(['email' => $email], [
            'name' => $name,
            'password' => bcrypt(Str::random(40)),
            'status' => 'active',
            'active' => true,
            'country_id' => Country::query()->value('id'),
        ]);

        if (!$user->hasRole($role)) {
            $user->assignRole($role);
        }

        return $user;
    }

    /**
     * An unapproved sample service (never listed in the app) with one package
     */
    private function samplePackage(User $provider): ServicePackage
    {
        $service = Service::where('user_id', $provider->id)->where('title', 'خدمة تجريبية للنزاعات')->first();

        if (!$service) {
            $subCategory = SubCategory::query()->first();

            $service = Service::withoutEvents(fn () => Service::create([
                'uuid' => (string) Str::uuid(),
                'title' => 'خدمة تجريبية للنزاعات',
                'description' => 'خدمة تجريبية لاختبار الإلغاء والنزاعات في لوحة التحكم.',
                'category_id' => $subCategory?->category_id ?? Category::query()->value('id'),
                'sub_category_id' => $subCategory?->id,
                'user_id' => $provider->id,
                'type' => ServiceTypeEnum::ONE_TIME->value,
                'is_approved' => false,
            ]));
        }

        return ServicePackage::firstOrCreate(['service_id' => $service->id], [
            'title' => 'الباقة الأساسية',
            'price' => 250,
            'days' => 5,
            'revisions' => 1,
            'unlimited_revisions' => false,
        ]);
    }
}
