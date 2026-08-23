<?php

namespace App\Http\Resources\Api\Service;

use App\Http\Resources\Api\Chat\ChatResource;
use App\Http\Resources\Api\Attachment\AttachmentResource;
use App\Http\Resources\Api\Category\CategoryResource;
use App\Http\Resources\Api\Package\PackageResource;
use App\Http\Resources\Api\SubCategory\SubCategoryResource;
use App\Http\Resources\Api\User\UserMiniResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defines the service resource
 */
class ServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'title' => $this->title,
            'description' => $this->description,
            'client_guidelines' => $this->client_guidelines,
            'type' => $this->type,
            'category_id' => $this->category?->uuid,
            'sub_category_id' => $this->subCategory?->uuid,
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'sub_category' => SubCategoryResource::make($this->whenLoaded('subCategory')),
            'skills' => ServiceSkillResource::collection($this->whenLoaded('skills')),
            'attachments' => AttachmentResource::collection($this->whenLoaded('attachments')),
            'packages' => PackageResource::collection($this->whenLoaded('packages')),
            'rate' => $this->averageRate(),
            'rates' => $this->ratingsCount(),
            'is_favorite' => $this->isFavorite(),
            'is_approved' => $this->is_approved,
            'purchase_count' => $this->when(isset($this->purchase_count), $this->purchase_count),
            'total_revenue' => $this->when(isset($this->total_revenue), $this->total_revenue),
            'first_package_price' => $this->when(isset($this->first_package_price), $this->first_package_price),
            'user' => UserMiniResource::make($this->whenLoaded('user')),
            'seekers' => $this->when(isset($this->seekers), UserMiniResource::collection($this->seekers ?? [])),
            'ordered_at' => $this->when(isset($this->order_date), $this->order_date),
            'completed_at' => $this->when(isset($this->completed_at), $this->completed_at),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'chats' =>  ChatResource::collection($this->whenLoaded('chats'))
        ];
    }
}
