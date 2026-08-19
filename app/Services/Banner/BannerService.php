<?php

namespace App\Services\Banner;

use App\Actions\Attachments\StoreAttachmentAction;
use App\Enums\BannerTypeEnum;
use App\Events\LogExceptionEvent;
use App\Models\Banner;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class BannerService
{
    /**
     * Get all banners with filters
     *
     * @param int $perPage
     * @param string $type
     *
     * @return LengthAwarePaginator
     */
    public function index(int $perPage, string $type): LengthAwarePaginator
    {
        $query = Banner::with('attachment');

        if ($type) {
            $query->where('type', $type);
        }

        return $query->paginate($perPage);
    }

    /**
     * Create a new banner
     *
     * @param array $data
     *
     * @return Banner
     *
     * @throws Exception
     */
    public function create(array $data): Banner
    {
        DB::beginTransaction();

        try{
            $banner = Banner::create([
                'type' => $data['type'] ?? BannerTypeEnum::MAIN,
                'key' => $data['key'],
                'title_ar' => $data['title_ar'] ?? null,
                'title_en' => $data['title_en'] ?? null,
                'description_ar' => $data['description_ar'] ?? null,
                'description_en' => $data['description_en'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            if (isset($data['attachment'])) {
                StoreAttachmentAction::store($banner,$data['attachment'], 'attachment', true);
            }

            DB::commit();

            return $banner->fresh('attachment');

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Update banner
     *
     * @param Banner $banner
     * @param array $data
     *
     * @return Banner
     *
     * @throws Exception
     */
    public function update(Banner $banner, array $data): Banner
    {
        DB::beginTransaction();

        try {
            $banner->update([
                'type' => $data['type'] ?? $banner->type,
                'key' => $data['key'] ?? $banner->key,
                'title_ar' => $data['title_ar'] ?? $banner->title_ar,
                'title_en' => $data['title_en'] ?? $banner->title_en,
                'description_ar' => $data['description_ar'] ?? $banner->description_ar,
                'description_en' => $data['description_en'] ?? $banner->description_en,
                'is_active' => $data['is_active'] ?? $banner->is_active,
            ]);

            if (isset($data['attachment'])) {
                StoreAttachmentAction::store($banner, $data['attachment'], 'attachment', true);
            }

            DB::commit();

            return $banner->fresh('attachment');

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Delete banner
     *
     * @param Banner $banner
     *
     * @return bool
     */
    public function delete(Banner $banner): bool
    {
        // Delete attachment if exists
        if ($banner->attachment) {
            $banner->attachment->delete();
        }

        return $banner->delete();
    }

    /**
     * Toggle banner active status
     *
     * @param Banner $banner
     *
     * @return Banner
     */
    public function toggleActive(Banner $banner): Banner
    {
        $banner->update(['is_active' => !$banner->is_active]);
        return $banner->fresh('attachment');
    }
}
