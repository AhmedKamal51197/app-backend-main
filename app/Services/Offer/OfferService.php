<?php

namespace App\Services\Offer;

use App\Enums\MessageTypeEnum;
use App\Events\LogExceptionEvent;
use App\Models\Category;
use App\Models\Chat;
use App\Models\Feature;
use App\Models\Message;
use App\Models\PackageFeature;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Models\Setting;
use App\Models\SubCategory;
use App\Models\User;
use App\Notifications\Offer\OfferAddedNotification;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * A class defines the service offers
 */
class OfferService
{
    /**
     * Get all services
     *
     * @param User $user
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function index(User $user, int $perPage = Setting::PAGE_RESULT_LIMIT): LengthAwarePaginator
    {
        return Service::query()
            ->with(['attachments', 'category', 'skills', 'skills.skill', 'packages', 'user'])
            ->where('user_id', '=', $user->getAttribute('id'))
            ->where('custom_offer', '=', true)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Store new service
     *
     * @param Chat $chat
     * @param User $user
     * @param array $data
     *
     * @return Service
     *
     * @throws Exception
     */
    public function store(Chat $chat, User $user, array $data): Service
    {
        // Validate user is participant
        if (!$chat->participants()->where('user_id', $user->getAttribute('id'))->exists()) {
            throw new Exception(__('You are not a participant in this chat'));
        }

        $category = Category::where('uuid', '=', $data['category_id'])->firstOrFail();
        $subCategory = SubCategory::where('uuid', '=', $data['sub_category_id'])->firstOrFail();

        DB::beginTransaction();
        try {
            $service = Service::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'category_id' => $category->getAttribute('id'),
                'sub_category_id' => $subCategory->getAttribute('id'),
                'type' => $data['type'],
                'hidden' => true,
                'custom_offer' => true,
                'user_id' => $user->getAttribute('id'),
            ]);

            $package = ServicePackage::create([
                'service_id' => $service->getAttribute('id'),
                'price' => $data['price'],
                'days' => $data['time'],
                'revisions' => $data['revisions'],
            ]);


            if (!empty($data['feature_ids'])) {

                self::storePackageFeatures($package, $data['feature_ids']);

                $featureIds = $data['feature_ids'];
                $features = Feature::whereIn('uuid', $featureIds)
                    ->get()
                    ->map(fn($f) => $f->title())
                    ->toArray();

                $featuresString = implode(',', $features);
            } else {
                $featuresString = '';
            }

            $content = sprintf(
                "/%s/service_uuid/%s/service_description/%s/%s/%s/service_title/%s/service_features/%s",
                $package->price,
                $package->uuid,
                Str::limit($service->description, 40, ''),
                $package->days,
                $package->revisions,
                $service->title,
                urlencode($featuresString)
            );

            Message::create([
                'chat_id' => $chat->getAttribute('id'),
                'sender_id' => $user->getAttribute('id'),
                'content' => $content,
                'type' => MessageTypeEnum::OFFER->value,
                'messageable_type' => Chat::class,
                'messageable_id' => $chat->id,
            ]);

            DB::commit();

            $user->notify(new OfferAddedNotification());

            return $service->load(['category', 'packages', 'subCategory']);

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
}
