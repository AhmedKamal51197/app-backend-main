<?php

namespace App\Services\Favorite;

use App\Enums\ServiceTypeEnum;
use App\Events\LogExceptionEvent;
use App\Models\FavoriteService;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * A class defines the favorite service
 */
class FavoriteServiceService
{
    /**
     * Get all favorites
     *
     * @param User $user
     * @param int $perPage
     *
     * @return array
     */
    public function index(User $user, int $perPage = Setting::PAGE_RESULT_LIMIT): array
    {
        $partTime = Service::query()
            ->with(['attachments', 'category', 'packages', 'user'])
            ->whereHas('favorites', function ($query) use ($user) {
                $query->where('user_id', $user->getAttribute('id'));
            })->where('type', '=', ServiceTypeEnum::PART_TIME->value)
            ->orderByDesc('created_at')
            ->paginate($perPage);

        $oneTime = Service::query()
            ->with(['attachments', 'category', 'packages', 'user'])
            ->whereHas('favorites', function ($query) use ($user) {
                $query->where('user_id', $user->getAttribute('id'));
            })
            ->where('type', '=', ServiceTypeEnum::ONE_TIME->value)
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return [
            'one_time' => $oneTime,
            'part_time' => $partTime,
        ];
    }

    /**
     * Add service to favorites
     *
     * @param User $user
     * @param Service $service
     *
     * @return FavoriteService
     *
     * @throws Exception
     */
    public function store(User $user, Service $service): FavoriteService
    {
        DB::beginTransaction();
        try {
            $existing = $this->getFavorite($user, $service);

            if ($existing) {
                return $existing->load('service'); // Return existing with relation
            }
            $favorite = FavoriteService::create([
                'user_id' => $user->getAttribute('id'),
                'service_id' => $service->getAttribute('id'),
            ]);

            DB::commit();

            return $favorite->load(['service']);

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Remove service from favorites
     *
     * @param User $user
     * @param Service $service
     *
     * @return bool
     *
     * @throws Exception
     */
    public function delete(User $user, Service $service): bool
    {
        DB::beginTransaction();
        try {
            $result = FavoriteService::
            where('service_id', '=', $service->getAttribute('id'))
                ->where('user_id', '=', $user->getAttribute('id'))
                ->delete();

            DB::commit();

            return $result;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Check if the service is already added to the user's favorites.
     *
     * @param User $user
     * @param Service $service
     *
     * @return FavoriteService|null
     */
    public function getFavorite(User $user, Service $service): ?FavoriteService
    {
        return FavoriteService::where('user_id', $user->id)
            ->where('service_id', $service->id)
            ->first();
    }
}
