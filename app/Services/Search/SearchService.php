<?php

namespace App\Services\Search;

use App\Enums\KycStatusEnum;
use App\Enums\ServiceTypeEnum;
use App\Enums\UserStatusEnum;
use App\Models\Category;
use App\Models\Role;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * A class defines the service for search
 */
class SearchService
{
    /**
     * Search on services
     *
     * @param array $data
     * @param int $perPage
     *
     * @return array
     */
    public function searchServices(array $data, int $perPage = Setting::PAGE_RESULT_LIMIT): array
    {
        $oneTimeQuery = Service::query()
            ->with(['attachments', 'category', 'skills', 'skills.skill', 'user', 'subCategory', 'packages'])
            ->where('type', '=', ServiceTypeEnum::ONE_TIME->value)
            ->where('hidden', '=', false)
            ->orderByDesc('created_at');

        if (!empty($data['sub_category_id'])) {
            $oneTimeQuery->whereHas('subCategory', function ($q) use ($data) {
                $q->where('uuid', $data['sub_category_id']);
            });
        }

        $partTimeQuery = Service::query()
            ->with(['attachments', 'category', 'skills', 'skills.skill', 'user', 'subCategory', 'packages'])
            ->where('type', '=', ServiceTypeEnum::PART_TIME->value)
            ->where('hidden', '=', false)
            ->orderByDesc('created_at');

        // Filter by sub_category_id if provided
        if (!empty($data['sub_category_id'])) {
            $partTimeQuery->whereHas('subCategory', function ($q) use ($data) {
                $q->where('uuid', $data['sub_category_id']);
            });
        }

        return [
            'part_time' => $partTimeQuery->paginate($perPage),
            'one_time' => $oneTimeQuery->paginate($perPage),
        ];
    }

    /**
     * Search
     *
     * @param array $data
     * @param int $perPage
     *
     * @return array
     */
    public function search(array $data, int $perPage = Setting::PAGE_RESULT_LIMIT): array
    {
        $categoryId = $this->getCategoryIdFromUuid($data['category_id'] ?? null);

        $type = $data['type'] ?? null;
        $text = $data['text'] ?? null;

        $oneTimeServices = collect();
        $partTimeServices = collect();

        if ($type === ServiceTypeEnum::ONE_TIME->value || is_null($type)) {
            $oneTimeServices = $this->buildServiceQuery(ServiceTypeEnum::ONE_TIME->value, $data, $categoryId, $text)
                ->paginate($perPage);
        }

        if ($type === ServiceTypeEnum::PART_TIME->value || is_null($type)) {
            $partTimeServices = $this->buildServiceQuery(ServiceTypeEnum::PART_TIME->value, $data, $categoryId, $text)
                ->paginate($perPage);
        }

        $freelancers = $this->getFreelancers($categoryId, $perPage, $text);

        return [
            'services' => $oneTimeServices,
            'part_time_services' => $partTimeServices,
            'freelancers' => $freelancers,
        ];
    }


    /**
     * Get category id from uuid
     *
     * @param string|null $uuid
     *
     * @return int|null
     */
    private function getCategoryIdFromUuid(?string $uuid): ?int
    {
        if (!$uuid) {
            return null;
        }

        return Category::where('uuid', $uuid)->value('id');
    }

    /**
     * Build the service query
     *
     * @param string $type
     * @param array $data
     * @param int|null $categoryId
     * @param string|null $text
     *
     * @return Builder
     */
    protected function buildServiceQuery(string $type, array $data, ?int $categoryId, ?string $text = null)
    {
        $query = Service::query()
            ->with(['user', 'packages', 'attachments'])
            ->where('type', $type)
        ->where('hidden', '=', false);

        if (!is_null($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        if (!empty($data['min'])) {
            $query->where('price', '>=', $data['min']);
        }

        if (!empty($data['max'])) {
            $query->where('price', '<=', $data['max']);
        }

        if (!empty($text)) {
            $query->where(function ($q) use ($text) {
                $q->where('title', 'like', "%{$text}%") // PostgreSQL (case-insensitive)
                ->orWhere('description', 'like', "%{$text}%");
            });
        }

        return $query;
    }



    /**
     * Get the freelancers
     *
     * @param int|null $categoryId
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    private function getFreelancers(?int $categoryId, int $perPage): LengthAwarePaginator
    {
        $providerRoleId = Role::where('name', 'provider')->value('id');

        return User::with('category')
            ->whereHas('services')
            ->whereHas('roles', fn($q) => $q->where('id', $providerRoleId))
            ->where('status', UserStatusEnum::ACTIVE->value)
            ->whereHas('kycs', fn($q) => $q->where('status', KycStatusEnum::APPROVED->value))
            ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
            ->paginate($perPage, ['*'], 'freelancers_page');
    }
}
