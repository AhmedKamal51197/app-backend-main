<?php

namespace App\Http\Resources\Api\Provider;

use App\Http\Resources\Admin\Country\CountryResource;
use App\Http\Resources\Api\Attachment\AttachmentResource;
use App\Http\Resources\Api\Category\CategoryResource;
use App\Http\Resources\Api\SubCategory\SubCategoryResource;
use App\Http\Resources\Api\Education\EducationResource;
use App\Http\Resources\Api\Experience\ExperienceResource;
use App\Http\Resources\Api\Portfolio\PortfolioResource;
use App\Http\Resources\Api\Rate\RateResource;
use App\Http\Resources\Api\Service\ServiceResource;
use App\Http\Resources\Api\UserCertificate\UserCertificateResource;
use App\Http\Resources\Api\UserSkillResource\UserSkillResource;
use App\Http\Resources\Api\UserSubCategory\UserSubCategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A class defines provider resource
 */
class ProviderResource extends JsonResource
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
            'about' => $this->about,
            'status' => $this->status,
            'avatar' => AttachmentResource::make($this->whenLoaded('avatar')),
            'rating_avg' => $this->averageRate(),
            'rating_count' => $this->ratingsCount(),
            'completed_projects' => $this->completedProjects(),
            'portfolios' => PortfolioResource::collection($this->whenLoaded('portfolios')),
            'services' => ServiceResource::collection($this->whenLoaded('services')),
            'role' => $this->getRoleNames()[0],
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'sub_category' => SubCategoryResource::make($this->whenLoaded('subCategory')),
            'country' => CountryResource::make($this->whenLoaded('country')),
            'sub_categories' => UserSubCategoryResource::collection($this->whenLoaded('userSubCategories')),
            'skills' => UserSkillResource::collection($this->whenLoaded('userSkills')),
            'educations' => EducationResource::collection($this->whenLoaded('educations')),
            'experiences' => ExperienceResource::collection($this->whenLoaded('experiences')),
            'certificates' => UserCertificateResource::collection($this->whenLoaded('userCertificates')),
            'rates' => RateResource::collection($this->whenLoaded('rates')),
        ];
    }
}
