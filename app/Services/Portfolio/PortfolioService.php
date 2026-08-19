<?php

namespace App\Services\Portfolio;

use App\Actions\Attachments\StoreAttachmentAction;
use App\Events\LogExceptionEvent;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\FavoritePortfolio;
use App\Models\Portfolio;
use App\Models\Skill;
use App\Models\SubCategory;
use App\Models\User;
use App\Notifications\Portfolio\NewPortfolioAddedNotification;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * A class defines the user product service
 */
class PortfolioService
{
    /**
     * Get all portfolios
     *
     * @param int $perPage
     * @param int $page
     * @param int $userId
     * @param bool $includeHidden
     *
     * @return LengthAwarePaginator
     */
    public function index(int $perPage, int $page, int $userId, bool $includeHidden = false): LengthAwarePaginator
    {
        $query = Portfolio::query()
            ->where('user_id', '=', $userId)
            ->withCount('favorites')
            ->with(['category', 'subCategory', 'attachments', 'skills', 'skills.skill']);

        if (!$includeHidden) {
            $query->where('hidden', false);
        }

        return $query->orderByDesc('created_at')
            ->paginate($perPage, ['*'], 'page', $page);
    }


    /**
     * Get one portfolio
     *
     * @param Portfolio $portfolio
     * @param User $user
     *
     * @return Portfolio
     * @throws Exception
     */
    public function show(Portfolio $portfolio, User $user): Portfolio
    {
        if ($portfolio->user_id !== $user->id && $portfolio->hidden) {
            throw new Exception(__('Portfolio is not available'));
        }

        return $portfolio->loadCount('favorites')
            ->load(['attachments', 'category', 'subCategory', 'skills', 'skills.skill']);
    }

    /**
     * Store new portfolio
     *
     * @param User $user
     * @param array $data
     *
     * @return Portfolio
     *
     * @throws Exception
     */
    public function store(User $user, array $data): Portfolio
    {
        DB::beginTransaction();
        try {
            $portfolio = Portfolio::create([
                'user_id' => $user->getAttribute('id'),
                'title' => $data['title'],
                'description' => $data['description'],
                'hidden' => $data['hidden'] ?? false,
                'category_id' => Category::where('uuid', $data['category_id'])->value('id'),
                'sub_category_id' => SubCategory::where('uuid', $data['sub_category_id'])->value('id'),
            ]);

            StoreAttachmentAction::store($portfolio, $data['attachments'], 'attachments', false);

            $this->storePortfolioSkills($portfolio, $data['skill_ids']);

            DB::commit();

            $user->notify(new NewPortfolioAddedNotification);

            return $portfolio->load(['attachments', 'category', 'subCategory', 'skills', 'skills.skill']);

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Update portfolio
     *
     * @param Portfolio $portfolio
     * @param array $data
     *
     * @return Portfolio
     *
     * @throws Exception
     */
    public function update(Portfolio $portfolio, array $data): Portfolio
    {
        DB::beginTransaction();

        try {
            if (!empty($data['category_id'])) {
                $data['category_id'] = Category::where('uuid', $data['category_id'])->value('id');
            }

            if (!empty($data['sub_category_id'])) {
                $data['sub_category_id'] = SubCategory::where('uuid', $data['sub_category_id'])->value('id');
            }

            $portfolio->update([
                'title' => $data['title'] ?? $portfolio->title,
                'description' => $data['description'] ?? $portfolio->description,
                'hidden' => $data['hidden'] ?? $portfolio->hidden,
                'category_id' => $data['category_id'] ?? $portfolio->category_id,
                'sub_category_id' => $data['sub_category_id'] ?? $portfolio->sub_category_id,
            ]);

            if (!empty($data['skill_ids'])) {
                $this->storePortfolioSkills($portfolio, $data['skill_ids']);
            }

            $portfolio->load(['attachments', 'category', 'subCategory', 'skills']);

            DB::commit();

            return $portfolio;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Delete portfolio attachment
     *
     * @param Portfolio $portfolio
     * @param Attachment $attachment
     *
     * @return Portfolio
     *
     * @throws Exception
     */
    public function deleteAttachment(Portfolio $portfolio, Attachment $attachment): Portfolio
    {
        DB::beginTransaction();
        try {
            // Verify the attachment belongs to this portfolio
            $portfolioAttachment = $portfolio->attachments()->where('id', $attachment->id)->first();

            if (!$portfolioAttachment) {
                throw new Exception(__('Attachment not found for this portfolio'));
            }

            if (Storage::disk($portfolioAttachment->disk)->exists($portfolioAttachment->path)) {
                Storage::disk($portfolioAttachment->disk)->delete($portfolioAttachment->path);
            }

            $portfolioAttachment->delete();

            $portfolio->load(['attachments', 'category', 'subCategory', 'skills']);

            DB::commit();

            return $portfolio;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Add an attachment to the portfolio
     *
     * @param User $user
     * @param Portfolio $portfolio
     * @param array $data
     *
     * @return Portfolio
     *
     * @throws Exception
     */
    public function addAttachment(User $user, Portfolio $portfolio, array $data): Portfolio
    {
        DB::beginTransaction();
        try {
            StoreAttachmentAction::store($portfolio, $data['attachment'], 'attachments', false);

            $portfolio->load(['attachments', 'category', 'subCategory', 'skills']);

            DB::commit();

            return $portfolio;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
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

    /**
     * Store portfolio skills without detaching the old ones.
     *
     *
     *
     * @throws Exception
     */
    public function storePortfolioSkills(Portfolio $portfolio, array $skills): Portfolio
    {
        DB::beginTransaction();
        try {
            $skillIds = Skill::whereIn('uuid', $skills)->pluck('id')->toArray();

            foreach ($skillIds as $skillId) {
                $portfolio->skills()->firstOrCreate([
                    'skill_id' => $skillId,
                ]);
            }

            DB::commit();

            return $portfolio->load(['attachments', 'category', 'subCategory', 'skills', 'skills.skill']);

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Remove portfolio skills.
     *
     *
     *
     * @throws Exception
     */
    public function removePortfolioSkills(Portfolio $portfolio, array $skills): Portfolio
    {
        DB::beginTransaction();
        try {
            $skillIds = Skill::whereIn('uuid', $skills)->pluck('id')->toArray();

            $portfolio->skills()->whereIn('skill_id', $skillIds)->delete();

            DB::commit();

            return $portfolio->load(['attachments', 'category', 'subCategory', 'skills', 'skills.skill']);

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Toggle favorite portfolio for a user
     *
     * @throws Exception
     */
    public function toggleFavorite(User $user, Portfolio $portfolio): bool
    {
        DB::beginTransaction();
        try {
            $favorite = FavoritePortfolio::where('user_id', $user->id)
                ->where('portfolio_id', $portfolio->id)
                ->first();

            if ($favorite) {
                $favorite->delete();
                $isFavorite = false;
            } else {
                FavoritePortfolio::create([
                    'user_id' => $user->id,
                    'portfolio_id' => $portfolio->id,
                ]);
                $isFavorite = true;
            }

            DB::commit();

            return $isFavorite;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Toggle portfolio hidden status
     *
     * @param Portfolio $portfolio
     * @return Portfolio
     */
    public function toggleHidden(Portfolio $portfolio): Portfolio
    {
        $portfolio->update([
            'hidden' => !$portfolio->hidden
        ]);

        return $portfolio->load(['user', 'category', 'subCategory', 'attachments', 'skills', 'skills.skill']);
    }

    /**
     * Get user's favorite portfolios
     *
     * @param User $user
     * @param int $perPage
     * @param int $page
     *
     * @return LengthAwarePaginator
     */
    public function favorites(User $user, int $perPage, int $page): LengthAwarePaginator
    {
        return Portfolio::query()
            ->whereHas('favorites', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('hidden', false)
            ->withCount('favorites')
            ->with(['user', 'category', 'subCategory', 'attachments', 'skills', 'skills.skill'])
            ->orderByDesc('created_at')
            ->paginate($perPage, ['*'], 'page', $page);
    }
}
