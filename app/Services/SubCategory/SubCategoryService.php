<?php

namespace App\Services\SubCategory;

use App\Events\LogExceptionEvent;
use App\Models\Category;
use App\Models\Setting;
use App\Models\SubCategory;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * A class defines the sub category service
 */
class SubCategoryService
{
    /**
     * Get all sub categories
     *
     * @param Category $category
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function index(Category $category, int $perPage = Setting::PAGE_RESULT_LIMIT): LengthAwarePaginator
    {
        return SubCategory::query()
            ->with('image')
            ->where('category_id', '=', $category->getAttribute('id'))
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Store new sub category
     *
     * @param array $data
     *
     * @return SubCategory
     *
     * @throws Exception
     */
    public function store(Category $category, array $data): SubCategory
    {
        DB::beginTransaction();
        try {
            $subCategory = SubCategory::create([
                'title_ar' => $data['title_ar'],
                'title_en' => $data['title_en'],
                'category_id' => $category->getAttribute('id'),
            ]);

            DB::commit();

            return $subCategory;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Update Sub Category
     *
     * @param SubCategory $subCategory
     * @param array $data
     *
     * @return SubCategory
     *
     * @throws Exception
     */
    public function update(SubCategory $subCategory, array $data): SubCategory
    {
        DB::beginTransaction();
        try {
            $subCategory->update([
                'title_ar' => $data['title_ar'],
                'title_en' => $data['title_en'],
            ]);

            DB::commit();

            return $subCategory;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Delete sub category
     *
     * @param SubCategory $subCategory
     *
     * @return bool
     *
     * @throws Exception
     */
    public function delete(SubCategory $subCategory): bool
    {
        DB::beginTransaction();
        try {
            $result = $subCategory->delete();

            DB::commit();

            return $result;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }
}
