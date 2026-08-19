<?php

namespace App\Services\Category;

use App\Actions\Attachments\StoreAttachmentAction;
use App\Events\LogExceptionEvent;
use App\Models\Category;
use App\Models\Setting;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * A class defines the category service
 */
class CategoryService
{
    /**
     * Get all categories
     *
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function index(int $perPage = Setting::PAGE_RESULT_LIMIT): LengthAwarePaginator
    {
        return Category::query()
            ->with('image')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Store new category
     *
     * @param array $data
     *
     * @return Category
     *
     * @throws Exception
     */
    public function store(array $data): Category
    {
        DB::beginTransaction();
        try {
            $category = Category::create([
                'title_ar' => $data['title_ar'],
                'title_en' => $data['title_en'],
                'is_enabled' => true
            ]);

            if (isset($data['image'])) {
                StoreAttachmentAction::store($category, $data['image'], 'image');
            }

            DB::commit();

            return $category->load('image');

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Update Category
     *
     * @param Category $category
     * @param array $data
     *
     * @return Category
     *
     * @throws Exception
     */
    public function update(Category $category, array $data): Category
    {
        DB::beginTransaction();
        try {
            $category->update([
                'title_ar' => $data['title_ar'],
                'title_en' => $data['title_en'],
            ]);

             if (isset($data['image'])) {
                StoreAttachmentAction::store($category, $data['image'], 'image');
            }
            DB::commit();

            return $category->load('image');

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Delete category
     *
     * @param Category $category
     *
     * @return bool
     *
     * @throws Exception
     */
    public function delete(Category $category): bool
    {
        DB::beginTransaction();
        try {
            $result = $category->delete();

            DB::commit();

            return $result;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }
}
