<?php

namespace App\Http\Resources\Admin\Users;

use App\Http\Resources\Admin\Country\CountryResource;
use App\Http\Resources\Admin\Kyc\KycResource;
use App\Http\Resources\Admin\PaymentRequest\PaymentRequestResource;
use App\Http\Resources\Admin\Report\ReportResource;
use App\Http\Resources\Api\Attachment\AttachmentResource;
use App\Http\Resources\Api\Category\CategoryResource;
use App\Http\Resources\Api\Order\OrderResource;
use App\Http\Resources\Api\Portfolio\PortfolioResource;
use App\Http\Resources\Api\Project\ProjectResource;
use App\Http\Resources\Admin\Service\ServiceResource;
use App\Http\Resources\Api\UserSkillResource\UserSkillResource;
use App\Http\Resources\Api\UserSubCategory\UserSubCategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defines users resource
 */
class UsersResource extends JsonResource
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
            'name' => $this->name,
            'username' => $this->username,
            'about' => $this->about,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'active' => $this->active,
            'kyc_verified' => $this->isKycVerified(),
            'email_verified' => !is_null($this->email_verified_at),
            'status' => $this->status,
            'rating_avg' => $this->averageRate(),
            'rating_count' => $this->ratingsCount(),
            'country' => CountryResource::make($this->whenLoaded('country')),
            'avatar' => AttachmentResource::make($this->whenLoaded('avatar')),
            'role' => $this->getRoleNames()[0],
            'wallet' => $this->walletBalance(),
            'total_spending' => $this->totalSpending(),
            'total_profit' => $this->totalProfit(),
            'total_budget' => $this->totalBudget(),
            'total_withdrawn' => $this->totalWithdrawn(),
            'total_projects' => $this->projects_count,
            'total_services' => $this->services_count,
            'total_portfolios' => $this->portfolios_count,
            'analytics' => $this->when(isset($this->analytics), $this->analytics),
            'email_verified_at' => $this->email_verified_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'kyc' => KycResource::make($this->whenLoaded('latestKyc')),
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'sub_categories' => UserSubCategoryResource::collection($this->whenLoaded('userSubCategories')),
            'skills' => UserSkillResource::collection($this->whenLoaded('userSkills')),
            'owned_projects' => ProjectResource::collection($this->whenLoaded('projects')),
            'worked_projects' => ProjectResource::collection($this->whenLoaded('workedProjects')),
            'seeker_orders' => OrderResource::collection($this->whenLoaded('seekerOrders')),
            'provider_orders' => OrderResource::collection($this->whenLoaded('providerOrders')),
            'payment_requests' => PaymentRequestResource::collection($this->whenLoaded('paymentRequests')),
            'owned_services' => ServiceResource::collection($this->whenLoaded('services')),
            'ordered_services' => ServiceResource::collection($this->when(isset($this->ordered_services), $this->ordered_services)) ?? [],
            'portfolios' => PortfolioResource::collection($this->whenLoaded('portfolios')),
            'reports' => ReportResource::collection($this->whenLoaded('reports')),
            'reports_count' => $this->reports()->count(),
            'open_tickets_count' => 0, // Placeholder until the Tickets module is implemented
        ];
    }
}
