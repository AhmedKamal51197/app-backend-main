<?php

namespace App\Services\Portfolio;

use App\Events\LogExceptionEvent;
use App\Models\Portfolio;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * A class defines the admin portfolio service
 */
class AdminPortfolioService
{
    /**
     * List portfolios with analytics
     *
     * @param string $search
     * @param int $limit
     * @param int $page
     * @param string $hidden
     * @param string $orderBy
     *
     * @return LengthAwarePaginator
     */
    public function index(string $search, int $limit, int $page, string $hidden, string $orderBy): LengthAwarePaginator
    {
        $query = Portfolio::withCount('favorites')
            ->with(['user', 'category', 'subCategory', 'attachments', 'skills', 'skills.skill']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($hidden !== '') {
            $query->where('hidden', (bool) $hidden);
        }

        $query->orderBy('created_at', $orderBy === 'oldest' ? 'asc' : 'desc');

        return $query->paginate($limit, ['*'], 'page', $page);
    }


    /**
     * Toggle portfolio hidden status
     *
     * @param Portfolio $portfolio
     *
     * @return Portfolio
     */
    public function toggleHidden(Portfolio $portfolio): Portfolio
    {
        $portfolio->update([
            'hidden' => !$portfolio->hidden
        ]);

        return $portfolio->loadCount('favorites')
            ->load(['user', 'category', 'subCategory', 'attachments', 'skills', 'skills.skill']);
    }

    /**
     * Delete portfolio and all its attachments
     *
     * @param Portfolio $portfolio
     *
     * @return void
     *
     * @throws Exception
     */
    public function delete(Portfolio $portfolio): void
    {
        DB::beginTransaction();
        try {
            foreach ($portfolio->attachments as $attachment) {
                if (Storage::disk($attachment->disk)->exists($attachment->path)) {
                    Storage::disk($attachment->disk)->delete($attachment->path);
                }

                $attachment->delete();
            }

            $portfolio->delete();

            DB::commit();

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }
}
