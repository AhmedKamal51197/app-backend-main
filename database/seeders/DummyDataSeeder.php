<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\Order;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Wallet;
use App\Models\Rate;
use App\Models\Commission;
use App\Models\CommissionSetting;
use App\Enums\UserStatusEnum;
use App\Enums\ProjectStatusEnum;
use App\Enums\ProposalStatusEnum;
use App\Enums\OrderStatusEnum;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Create dummy users
        $providers = collect();
        $seekers = collect();

        for ($i = 0; $i < 10; $i++) {
            $provider = User::updateOrCreate(
                ['email' => $faker->unique()->safeEmail],
                [
                    'name' => $faker->name,
                    'mobile' => $faker->phoneNumber,
                    'password' => bcrypt('password@123'),
                    'email_verified_at' => now(),
                    'mobile_verified_at' => now(),
                    'status' => UserStatusEnum::ACTIVE->value,
                ]
            );
            if (!$provider->hasRole('provider')) {
                $provider->assignRole('provider');
            }
            $providers->push($provider);
        }

        for ($i = 0; $i < 10; $i++) {
            $seeker = User::updateOrCreate(
                ['email' => $faker->unique()->safeEmail],
                [
                    'name' => $faker->name,
                    'mobile' => $faker->phoneNumber,
                    'password' => bcrypt('password@123'),
                    'email_verified_at' => now(),
                    'mobile_verified_at' => now(),
                    'status' => UserStatusEnum::ACTIVE->value,
                ]
            );
            if (!$seeker->hasRole('seeker')) {
                $seeker->assignRole('seeker');
            }
            $seekers->push($seeker);
        }

        // Create wallets for all users
        $allUsers = $providers->merge($seekers);
        foreach ($allUsers as $user) {
            for ($i = 0; $i < rand(3, 8); $i++) {
                Wallet::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'title_en' => $faker->sentence(2)
                    ],
                    [
                        'credit' => $faker->randomFloat(2, 10, 500),
                        'debit' => $faker->randomFloat(2, 0, 100),
                        'title_ar' => $faker->sentence(2),
                        'referencable_type' => 'App\\Models\\Order',
                        'referencable_id' => 1,
                    ]
                );
            }
        }

        // Get existing categories and subcategories
        $categories = Category::all();
        $subCategories = SubCategory::all();

        if ($categories->isEmpty() || $subCategories->isEmpty()) {
            $this->command->error('No categories or subcategories found. Please run CategorySeeder first.');
            return;
        }
        $services = collect();

        for ($i = 0; $i < 15; $i++) {
            $service = Service::updateOrCreate(
                ['title' => $faker->sentence(3)],
                [
                    'description' => $faker->paragraph,
                    'user_id' => $providers->random()->id,
                    'category_id' => $categories->random()->id,
                    'sub_category_id' => $subCategories->random()->id,
                ]
            );

            // Create service packages
            ServicePackage::updateOrCreate(
                ['service_id' => $service->id],
                [
                    'price' => $faker->numberBetween(50, 200),
                    'days' => $faker->numberBetween(1, 5),
                    'revisions' => $faker->numberBetween(1, 3),
                ]
            );

            $services->push($service);
        }

        // Create projects with different statuses
        $projects = collect();
        for ($i = 0; $i < 10; $i++) {
            $status = $i < 3 ? ProjectStatusEnum::DRAFT->value : ProjectStatusEnum::PENDING->value;
            $project = Project::updateOrCreate(
                ['title' => $faker->sentence(4)],
                [
                    'description' => $faker->paragraph,
                    'user_id' => $seekers->random()->id,
                    'category_id' => $categories->random()->id,
                    'sub_category_id' => $subCategories->random()->id,
                    'min_price' => $faker->numberBetween(100, 500),
                    'max_price' => $faker->numberBetween(500, 1000),
                    'time' => $faker->numberBetween(5, 30),
                    'status' => $status,
                    'start_date' => $faker->dateTimeBetween('now', '+1 month'),
                    'end_date' => $faker->dateTimeBetween('+1 month', '+3 months'),
                ]
            );
            $projects->push($project);
        }

        // Create proposals only for pending projects
        $pendingProjects = $projects->where('status', ProjectStatusEnum::PENDING->value);
        foreach ($pendingProjects as $project) {
            $proposals = collect();
            for ($i = 0; $i < rand(2, 4); $i++) {
                $proposal = Proposal::updateOrCreate(
                    [
                        'project_id' => $project->id,
                        'user_id' => $providers->random()->id
                    ],
                    [
                        'price' => $faker->numberBetween(100, 800),
                        'description' => $faker->paragraph,
                        'time' => $faker->numberBetween(1, 30),
                        'status' => ProposalStatusEnum::PENDING->value,
                    ]
                );
                $proposals->push($proposal);
            }

            // Accept one proposal randomly (50% chance)
            if ($proposals->count() > 0 && $faker->boolean(50)) {
                $acceptedProposal = $proposals->random();
                $project->update(['selected_proposal_id' => $acceptedProposal->id]);

                $acceptedProposal->update(['is_selected' => true]);
            }
        }

        // Create orders from services
        foreach ($services->take(8) as $service) {
            $package = $service->packages->first();

            // Get commission setting
            $commissionSetting = CommissionSetting::where('key', 'seeker')->first();
            $commissionAmount = $commissionSetting ? ($commissionSetting->value * $package->price) : 0;

            $order = Order::updateOrCreate(
                [
                    'orderable_type' => ServicePackage::class,
                    'orderable_id' => $package->id,
                    'provider_id' => $service->user_id
                ],
                [
                    'seeker_id' => $seekers->random()->id,
                    'price' => $package->price,
                    'seeker_commissions' => $commissionAmount,
                    'time' => $package->days,
                    'category_id' => $service->category_id,
                    'sub_category_id' => $service->sub_category_id,
                    'status' => $faker->randomElement([
                        OrderStatusEnum::COMPLETED->value,
                        OrderStatusEnum::RELEASED->value,
                        OrderStatusEnum::IN_PROGRESS->value,
                    ]),
                ]
            );

            // Create commission record
            if ($commissionSetting) {
                Commission::updateOrCreate(
                    [
                        'payable_id' => $order->id,
                        'payable_type' => Order::class,
                    ],
                    [
                        'title' => 'Order seeker commissions',
                        'amount' => $commissionAmount,
                    ]
                );
            }

            // Create rates for completed orders
            if (in_array($order->status, [OrderStatusEnum::COMPLETED->value, OrderStatusEnum::RELEASED->value])) {
                Rate::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'user_id' => $order->seeker_id,
                        'rate' => $faker->numberBetween(3, 5),
                        'comment' => $faker->sentence(),
                    ]
                );
            }
        }

        // Create orders from accepted proposals
        $acceptedProjects = $pendingProjects->whereNotNull('selected_proposal_id');
        foreach ($acceptedProjects->take(3) as $project) {
            $proposal = $project->selectedProposal;

            // Get commission setting
            $commissionSetting = CommissionSetting::where('key', 'seeker')->first();
            $commissionAmount = $commissionSetting ? ($commissionSetting->value * $proposal->price) : 0;

            $order = Order::updateOrCreate(
                [
                    'orderable_type' => $project::class,
                    'orderable_id' => $project->id,
                    'provider_id' => $proposal->user_id
                ],
                [
                    'seeker_id' => $project->user_id,
                    'price' => $proposal->price,
                    'seeker_commissions' => $commissionAmount,
                    'time' => $proposal->time,
                    'category_id' => $project->category_id,
                    'sub_category_id' => $project->sub_category_id,
                    'status' => $faker->randomElement([
                        OrderStatusEnum::COMPLETED->value,
                        OrderStatusEnum::RELEASED->value,
                        OrderStatusEnum::IN_PROGRESS->value,
                    ]),
                ]
            );

            // Create commission record
            if ($commissionSetting) {
                Commission::updateOrCreate(
                    [
                        'payable_id' => $order->id,
                        'payable_type' => Order::class,
                    ],
                    [
                        'title' => 'Order seeker commissions',
                        'amount' => $commissionAmount,
                    ]
                );
            }

            // Create rates for completed orders
            if (in_array($order->status, [OrderStatusEnum::COMPLETED->value, OrderStatusEnum::RELEASED->value])) {
                Rate::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'user_id' => $order->seeker_id,
                        'rate' => $faker->numberBetween(3, 5),
                        'comment' => $faker->sentence(),
                    ]
                );
            }
        }

        $this->command->info('Created/Updated users, wallets, services, projects, orders, and reviews');
    }
}
