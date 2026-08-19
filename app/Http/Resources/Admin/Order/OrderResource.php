<?php

namespace App\Http\Resources\Admin\Order;

use App\Http\Resources\Admin\Attachment\AttachmentResource;
use App\Http\Resources\Admin\Category\CategoryResource;
use App\Http\Resources\Admin\Chat\ChatResource;
use App\Http\Resources\Admin\Project\ProjectResource;
use App\Http\Resources\Api\Order\OrderHistoryResource;
use App\Http\Resources\Api\Package\PackageResource;
use App\Http\Resources\Admin\User\UserMiniResource;
use App\Http\Resources\Admin\SubCategory\SubCategoryResource;
use App\Http\Resources\Api\Rate\RateResource;
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
            'disputed_at' => $this->disputed_at?->format('Y-m-d H:i:s'),
            'provider' => UserMiniResource::make($this->whenLoaded('provider')),
            'seeker' => UserMiniResource::make($this->whenLoaded('seeker')),
            'orderable' => $this->whenLoaded('orderable', fn() => $this->resolveOrderable()),
            'orderable_type' => $this->getOrderableType(),
            'attachments' => AttachmentResource::collection($this->whenLoaded('attachments')),
            'histories' => OrderHistoryResource::collection($this->whenLoaded('histories')),
            'renewals' => OrderResource::collection($this->whenLoaded('renewals')),
            'main_order' => $this->main_order,
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'sub_category' => SubCategoryResource::make($this->whenLoaded('subCategory')),
            'completed_at' => $this->completed_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'rates' => RateResource::collection($this->whenLoaded('rates')),
            'chat' => ChatResource::make($this->whenLoaded('chat')),
        ];
    }

    /**
     * Resolve the orderable resource based on type
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

    /**
     * Get the orderable type
     *
     * @return string|null
     */
    protected function getOrderableType(): ?string
    {
        return match (true) {
            $this->orderable instanceof Project => 'project',
            $this->orderable instanceof ServicePackage => 'service',
            default => null,
        };
    }
}
