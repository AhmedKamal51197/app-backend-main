<?php

namespace App\Http\Resources\Api\Order;

use App\Http\Resources\Api\Attachment\AttachmentResource;
use App\Http\Resources\Api\Category\CategoryResource;
use App\Http\Resources\Api\Package\PackageResource;
use App\Http\Resources\Api\Project\ProjectResource;
use App\Http\Resources\Api\Rate\RateResource;
use App\Http\Resources\Api\SubCategory\SubCategoryResource;
use App\Http\Resources\Api\User\UserMiniResource;
use App\Models\Project;
use App\Models\ServicePackage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defines the order resource
 */
class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'time' => $this->time,
            'code' => $this->code,
            'price' => $this->price,
            'commissions' => $this->commissions,
            'status' => $this->status,
            'approved_at' => $this->approved_at?->format('Y-m-d H:i:s'),
            'released_at' => $this->released_at?->format('Y-m-d H:i:s'),
            'cancelled_at' => $this->cancelled_at?->format('Y-m-d H:i:s'),
            'cancellation_reason' => $this->cancellation_reason,
            'cancelled_by' => $this->cancelled_by,
            'disputed_reason' => $this->disputed_reason,
            'disputed_by' => $this->disputed_by,
            'provider' => UserMiniResource::make($this->whenLoaded('provider')),
            'seeker' => UserMiniResource::make($this->whenLoaded('seeker')),
            'orderable' => $this->whenLoaded('orderable', fn() => $this->resolveOrderable()),
            'messages' => OrderMessageResource::collection($this->whenLoaded('messages')),
            'attachments' => AttachmentResource::collection($this->whenLoaded('attachments')),
            'histories' => OrderHistoryResource::collection($this->whenLoaded('histories')),
            'renewals' => OrderResource::collection($this->whenLoaded('renewals')),
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'sub_category' => SubCategoryResource::make($this->whenLoaded('subCategory')),
            'rates' => RateResource::collection($this->whenLoaded('rates')),
            'main_order' => $this->main_order,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Resolve the orderable resource
     *
     * @return ProjectResource|PackageResource|null
     */
    protected function resolveOrderable()
    {
        return match (true) {
            $this->orderable instanceof Project => ProjectResource::make($this->orderable),
            $this->orderable instanceof ServicePackage => PackageResource::make($this->orderable),
            default => null,
        };
    }
}
