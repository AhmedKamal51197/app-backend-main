<?php

namespace App\Http\Controllers\Api\BankAccount;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\BankAccount\StoreBankAccountRequest;
use App\Http\Requests\Api\BankAccount\UpdateBankAccountRequest;
use App\Http\Resources\Api\BankAccount\BankAccountResource;
use App\Services\BankAccount\BankAccountService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A class defines the BankAccount controller
 */
class BankAccountController extends BaseApiController
{
    /**
     * Load BankAccount service
     *
     * @param BankAccountService $service
     */
    public function __construct(protected BankAccountService $service)
    {
    }

    /**
     * Get user's bank account
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function userBankAccount(Request $request): JsonResponse
    {
        $user = request()->user();

        if (!($user->hasBankAccount())) {
            return $this->jsonError(
                __('User doesnt have a bank account')
            );
        }
        $bankAccount = $this->service->userBankAccount(request()->user());

        return $this->jsonSuccess(BankAccountResource::make($bankAccount));
    }

    /**
     * Store BankAccount data
     *
     * @param StoreBankAccountRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function store(StoreBankAccountRequest $request): JsonResponse
    {
        $user = request()->user();

        $bankAccount = $this->service->store($user, $request->validated());

        return $this->jsonSuccess(
            BankAccountResource::make($bankAccount),
            __('Bank account updated successfully')
        );
    }

    /**
     * Update BankAccount data
     *
     * @param UpdateBankAccountRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function update(UpdateBankAccountRequest $request): JsonResponse
    {
        $user = request()->user();

        if (!($user->hasBankAccount())) {
            return $this->jsonError(
                __('User doesnt have a bank account')
            );
        }

        $bankAccount = $this->service->update($user, $request->validated());

        return $this->jsonSuccess(
            BankAccountResource::make($bankAccount),
            __('Bank account updated successfully')
        );
    }
}
