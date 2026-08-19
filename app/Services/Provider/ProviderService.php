<?php

namespace App\Services\Provider;


use App\Models\User;

/**
 * A class defines the provider service
 */
class ProviderService
{
    /**
     * Provider Profile
     *
     * @param User $user
     *
     * @return User
     */
    public function profile(User $user): User
    {
        $user->load([
            'category',
            'country',
            'userSubCategories', 'userSubCategories.subCategory',
            'userSkills', 'userSkills.skill',
            'userCertificates', 'userCertificates.certificate',
            'avatar',
            'bankAccount',
            'paypal',
            'educations', 'educations.educationMajor', 'educations.educationDegree', 'educations.image',
            'portfolios', 'portfolios.attachments', 'portfolios.duration', 'portfolios.industry',
            'services', 'services.attachments', 'services.packages'
        ]);

        return $user;
    }
}
