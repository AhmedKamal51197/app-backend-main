<?php
//
//namespace App\Services\User;
//
//use App\Models\User;
//use App\Services\Sms\SmsService;
//use Exception;
//
///**
// * A class defines the user mobile service
// */
//class MobileService
//{
//    /**
//     * Load sms verification
//     *
//     * @param SmsService $smsVerificationService
//     */
//    public function __construct(protected SmsService $smsVerificationService)
//    {
//    }
//
//    /**
//     * Set mobile with verified
//     *
//     * @param User $user
//     * @param string $mobile
//     *
//     * @return void
//     *
//     * @throws Exception
//     */
//    public function store(User $user, string $mobile): void
//    {
//        $user->update(
//            [
//                'mobile' => $mobile,
//                'mobile_verified_at' => null
//            ]
//        );
//        $this->smsVerificationService->send($user);
//    }
//}
