<?php

namespace App\Http\Resources\Api\User;

use App\Http\Resources\Admin\Country\CountryResource;
use App\Http\Resources\Api\Attachment\AttachmentResource;
use App\Http\Resources\Api\BankAccount\BankAccountResource;
use App\Http\Resources\Api\Category\CategoryResource;
use App\Http\Resources\Api\SubCategory\SubCategoryResource;
use App\Http\Resources\Api\Education\EducationResource;
use App\Http\Resources\Api\PayPal\PayPalResource;
use App\Http\Resources\Api\Portfolio\PortfolioResource;
use App\Http\Resources\Api\UserCertificate\UserCertificateResource;
use App\Http\Resources\Api\UserSkillResource\UserSkillResource;
use App\Http\Resources\Api\UserSubCategory\UserSubCategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defines user resource
 */
class UserResource extends JsonResource
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
            'kyc_verified' => $this->isKycVerified(),
            'is_active' => true,
            'status' => $this->status,
            'has_bank_account' => $this->hasBankAccount(),
            'has_category' => $this->hasCategory(),
            'avatar' => AttachmentResource::make($this->whenLoaded('avatar')),
            'rating_avg' => $this->averageRate(),
            'rating_count' => $this->ratingsCount(),
            'completed_projects' => $this->completedProjects(),
            'portfolios' => PortfolioResource::collection($this->whenLoaded('portfolios')),
            'services' => [],
            'bank_details' => BankAccountResource::make($this->whenLoaded('bankAccount')),
            'paypal_details' => PayPalResource::make($this->whenLoaded('paypal')),
            'role' => $this->getRoleNames()[0],
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'sub_category' => SubCategoryResource::make($this->whenLoaded('subCategory')),
            'country' => CountryResource::make($this->whenLoaded('country')),
            'sub_categories' => UserSubCategoryResource::collection($this->whenLoaded('userSubCategories')),
            'skills' => UserSkillResource::collection($this->whenLoaded('userSkills')),
            'educations' => EducationResource::collection($this->whenLoaded('educations')),
            'certificates' => UserCertificateResource::collection($this->whenLoaded('userCertificates')),
            'last_login_at' => $this->last_login_at?->format('Y-m-d H:i:s'),
            'fcm_token' => $this->fcm_token,
        ];
    }
}
