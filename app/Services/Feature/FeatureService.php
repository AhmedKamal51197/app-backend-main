<?php

namespace App\Services\Feature;

use App\Models\Category;
use App\Models\Feature;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * A class defines the feature service
 */
class FeatureService
{
    /**
     * Get all features for admin
     *
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function index(int $perPage): LengthAwarePaginator
    {
        return Feature::query()
            ->with(['category'])
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Store new feature
     *
     * @param array $data
     *
     * @return Feature
     */
    public function store(array $data): Feature
    {
        $feature =  Feature::create([
            'title_ar' => $data['title_ar'],
            'title_en' => $data['title_en'],
            'category_id' => Category::where('uuid',$data['category_id'])->value('id'),
        ]);

        return $feature->load(['category']);
    }

    /**
     * Update feature
     *
     * @param Feature $feature
     * @param array $data
     *
     * @return Feature
     */
    public function update(Feature $feature, array $data): Feature
    {
        $feature->update([
            'title_ar' => $data['title_ar'] ?? $feature->title_ar,
            'title_en' => $data['title_en'] ?? $feature->title_en,
            'category_id' => isset($data['category_id']) ? Category::where('uuid',$data['category_id'])->value('id') : $feature->category_id,
        ]);

        return $feature->load(['category']);
    }

    /**
     * Delete feature
     *
     * @param Feature $feature
     *
     * @return bool
     */
    public function delete(Feature $feature): bool
    {
        return $feature->delete();
    }
}
