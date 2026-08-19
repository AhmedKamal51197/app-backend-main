<?php

namespace App\Services\Service;

use App\Actions\Attachments\StoreAttachmentAction;
use App\Enums\ServiceTypeEnum;
use App\Events\LogExceptionEvent;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\Feature;
use App\Models\PackageFeature;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Models\ServiceTag;
use App\Models\Skill;
use App\Models\SubCategory;
use App\Models\User;
use App\Notifications\Services\OneTimeServiceAddedNotification;
use App\Notifications\Services\PartTimeServiceAddedNotification;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * A class defines the service
 */
class ServiceService
{
    /**
     * Get services with unified logic
     *
     * @param string $search
     * @param int $limit
     * @param int $page
     * @param string $categoryId
     * @param string $userId
     * @param bool $isUserServices
     *
     * @return LengthAwarePaginator
     */
    public function index(string $search, int $limit, int $page, string $categoryId, string $userId, bool $isUserServices = false): LengthAwarePaginator
    {
        $query = Service::query()->with(['user', 'category', 'subCategory', 'attachments', 'packages', 'skills', 'skills.skill']);

        if ($isUserServices) {
            $query->where('user_id', $userId)
                  ->where('custom_offer', false);
        } else {
            $query->where('is_enabled', true)
                  ->where('hidden', false)
                  ->where('custom_offer', false)
                  ->where('is_approved', true);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        if (!empty($userId) && !$isUserServices) {
            $query->where('user_id', $userId);
        }

        return $query->orderByDesc('created_at')
            ->paginate($limit, ['*'], 'page', $page);
    }

    /**
     * Get services matching the user's interests (categories, sub-categories and skills)
     *
     * @param User $user
     * @param int $limit
     * @param int $page
     *
     * @return LengthAwarePaginator
     */
    public function indexByUserInterests(User $user, int $limit, int $page): LengthAwarePaginator
    {
        $query = Service::query()->with(['user', 'category', 'subCategory', 'attachments', 'packages', 'skills', 'skills.skill']);

        $userCategoryIds = collect([$user->category_id])->filter();
        $userSubCategoryIds = $user->userSubCategories()->pluck('sub_category_id');
        $userSkillIds = $user->userSkills()->pluck('skill_id');

        $query->where(function ($q) use ($userCategoryIds, $userSubCategoryIds, $userSkillIds) {
            $hasInterests = $userCategoryIds->isNotEmpty()
                || $userSubCategoryIds->isNotEmpty()
                || $userSkillIds->isNotEmpty();

            if (!$hasInterests) {
                $q->whereRaw('1 = 0');

                return;
            }

            if ($userCategoryIds->isNotEmpty()) {
                $q->orWhereIn('category_id', $userCategoryIds);
            }

            if ($userSubCategoryIds->isNotEmpty()) {
                $q->orWhereIn('sub_category_id', $userSubCategoryIds);
            }

            if ($userSkillIds->isNotEmpty()) {
                $q->orWhereHas('skills', function ($skills) use ($userSkillIds) {
                    $skills->whereIn('skill_id', $userSkillIds);
                });
            }
        })
            ->where('is_enabled', true)
            ->where('hidden', false)
            ->where('custom_offer', false)
            ->where('is_approved', true);

        return $query->orderByDesc('created_at')
            ->paginate($limit, ['*'], 'page', $page);
    }

    /**
     * Store new service
     *
     * @param User $user
     * @param array $data
     *
     * @return Service
     *
     * @throws Exception
     */
    public function store(User $user, array $data): Service
    {
        $category = Category::where('uuid', '=', $data['category_id'])->firstOrFail();
        $subCategory = SubCategory::where('uuid', '=', $data['sub_category_id'])->firstOrFail();

        DB::beginTransaction();
        try {
            $service = Service::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'client_guidelines' => $data['client_guidelines'],
                'category_id' => $category->getAttribute('id'),
                'sub_category_id' => $subCategory->getAttribute('id'),
                'type' => $data['type'],
                'hidden' => false,
                'custom_offer' => false,
                'is_approved' => false,
                'user_id' => $user->getAttribute('id'),
            ]);

            StoreAttachmentAction::store($service, $data['attachments'], 'attachments');

            self::storeServiceSkills($service, $data['skill_ids'] ?? []);

            self::storeServicePackages($service, $data['packages'] ?? []);

            DB::commit();

            if ($service->type === ServiceTypeEnum::ONE_TIME->value) {
                $user->notify(new OneTimeServiceAddedNotification());
            } else {
                $user->notify(new PartTimeServiceAddedNotification());
            }

            return $service->load(['attachments', 'category', 'skills', 'skills.skill', 'packages', 'subCategory']);

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Update existing service
     *
     * @param Service $service
     * @param array $data
     *
     * @return Service
     *
     * @throws Exception
     */
    public function edit(Service $service, array $data): Service
    {
        try {
            $updateData = [
                'title' => $data['title'] ?? $service->title,
                'description' => $data['description'] ?? $service->description,
                'client_guidelines' => $data['client_guidelines'] ?? $service->client_guidelines,
                'is_approved' => false,
            ];

            if (!empty($data['category_id'])) {
                $category = Category::where('uuid', $data['category_id'])->firstOrFail();
                $updateData['category_id'] = $category->getAttribute('id');
            }

            if (!empty($data['sub_category_id'])) {
                $subCategory = SubCategory::where('uuid', $data['sub_category_id'])->firstOrFail();
                $updateData['sub_category_id'] = $subCategory->getAttribute('id');
            }

            $service->update($updateData);

            return $service->load(['attachments', 'category', 'skills', 'skills.skill', 'packages', 'subCategory']);

        } catch (Exception $exception) {
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Edit service packages
     *
     * @param Service $service
     * @param ServicePackage $package
     * @param array $data
     *
     * @return Service
     *
     * @throws Exception
     */
    public function editServicePackage(Service $service, ServicePackage $package, array $data): Service
    {
        DB::beginTransaction();
        try {
            // Update the package
            $package->update([
                'title' => $data['title'] ?? $package->title,
                'price' => $data['price'] ?? $package->price,
                'days' => $data['days'] ?? $package->days,
                'revisions' => $data['revisions'] ?? $package->revisions,
            ]);

            // Update package features
            $this->updatePackageFeatures($package, $data['feature_ids'] ?? []);

            DB::commit();

            return $service->load(['attachments', 'category', 'skills', 'skills.skill', 'packages', 'subCategory']);

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Update package features
     *
     * @param ServicePackage $servicePackage
     * @param array $featureIds
     *
     * @return void
     *
     * @throws Exception
     */
    private function updatePackageFeatures(ServicePackage $servicePackage, array $featureIds): void
    {
        DB::beginTransaction();
        try {

            // Delete existing features
            $servicePackage->features()->delete();

            // Add new features
            $this->storePackageFeatures($servicePackage, $featureIds);

            DB::commit();

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Delete service
     *
     * @param Service $service
     *
     * @return bool
     *
     * @throws Exception
     */
    public function delete(Service $service): bool
    {
        DB::beginTransaction();
        try {
            // Delete associated attachments from storage
            if ($service->attachments->count() > 0) {
                foreach ($service->attachments as $attachment) {
                    Storage::disk($attachment->disk)->delete($attachment->path);
                }
            }

            $result = $service->delete();

            DB::commit();

            return $result;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Delete service attachment
     *
     * @param Service $service
     * @param Attachment $attachment
     *
     * @return Service
     *
     * @throws Exception
     */
    public function deleteAttachment(Service $service, Attachment $attachment): Service
    {
        DB::beginTransaction();
        try {
            // Verify the attachment belongs to this service
            $serviceAttachment = $service->attachments()->where('id', $attachment->id)->first();

            if (!$serviceAttachment) {
                throw new Exception(__('Attachment not found for this service'));
            }

            if (Storage::disk($serviceAttachment->disk)->exists($serviceAttachment->path)) {
                Storage::disk($serviceAttachment->disk)->delete($serviceAttachment->path);
            }

            $serviceAttachment->delete();

            DB::commit();

            return $service->load(['attachments', 'category', 'skills', 'skills.skill', 'subCategory']);

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Add an attachment to the service
     *
     * @param User $user
     * @param Service $service
     * @param array $data
     *
     * @return Service
     *
     * @throws Exception
     */
    public function addAttachment(User $user, Service $service, array $data): Service
    {
        DB::beginTransaction();
        try {
            StoreAttachmentAction::store($service, $data['attachment'], 'attachments', false);

            DB::commit();

            return $service->load(['attachments', 'category', 'skills', 'skills.skill', 'subCategory']);

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Store service skills without detaching the old ones.
     *
     * @param Service $service
     * @param array $skills
     *
     * @return Service
     *
     * @throws Exception
     */
    public function storeServiceSkills(Service $service, array $skills): Service
    {
        DB::beginTransaction();
        try {
            $skills = Skill::whereIn('uuid', $skills)->get();

            foreach ($skills as $skill) {
                $alreadyAttached = $service->skills()
                    ->where('skill_id', $skill->id)
                    ->exists();


                if (!$alreadyAttached) {
                    ServiceTag::create([
                        'skill_id' => $skill->id,
                        'service_id' => $service->getAttribute('id')
                    ]);
                }
            }

            DB::commit();

            return $service->load(['attachments', 'category', 'skills', 'skills.skill', 'subCategory']);

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * A function to store the packages for service
     *
     * @param Service $service
     * @param array $packages
     *
     * @return Service
     *
     * @throws Exception
     */
    public function storeServicePackages(Service $service, array $packages): Service
    {
        DB::beginTransaction();
        try {
            if ($service->type === ServiceTypeEnum::PART_TIME->value && count($packages) > 0) {
                $packages = [$packages[0]];
            }

            foreach ($packages as $package) {
                $days = $service->type === ServiceTypeEnum::PART_TIME->value ? 1 : $package['days'];

                $servicePackage = ServicePackage::create([
                    'service_id' => $service->getAttribute('id'),
                    'title' => $package['title'] ?? null,
                    'price' => $package['price'],
                    'days' => $days,
                    'revisions' => $package['revisions'] ?? 0,
                    'unlimited_revisions' => $package['unlimited_revisions'],
                ]);

                self::storePackageFeatures($servicePackage, $package['feature_ids']);
            }

            DB::commit();

            return $service;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Store the package features
     *
     * @param ServicePackage $servicePackage
     * @param array $features
     *
     * @return void
     *
     * @throws Exception
     */
    public function storePackageFeatures(ServicePackage $servicePackage, array $features): void
    {
        $features = Feature::whereIn('uuid', $features)->get();

        DB::beginTransaction();
        try {
            foreach ($features as $feature) {
                PackageFeature::create([
                    'service_package_id' => $servicePackage->getAttribute('id'),
                    'feature_id' => $feature->getAttribute('id'),
                ]);
            }

            DB::commit();

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Remove service skills.
     *
     * @param Service $service
     * @param array $skills
     *
     * @return Service
     *
     * @throws Exception
     */
    public function removeServiceSkills(Service $service, array $skills): Service
    {
        DB::beginTransaction();
        try {
            $skills = Skill::whereIn('uuid', $skills)->get();
            $skillIds = $skills->pluck('id')->toArray();

            $service->skills()
                ->whereIn('skill_id', $skillIds)
                ->delete();

            DB::commit();

            return $service->load(['attachments', 'category', 'skills', 'skills.skill', 'subCategory']);

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }
}
