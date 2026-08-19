<?php

namespace App\Http\Controllers\Api\Country;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Resources\Api\Country\CountryResource;
use App\Models\Country;
use App\Models\Setting;
use App\Services\Country\CountryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A class defined for the countries controller
 */
class CountryController extends BaseAdminController
{
    /**
     * Load the service
     *
     * @param CountryService $service
     */
    public function __construct(protected CountryService $service)
    {
    }

    /**
     * Index all system countries
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $countries = $this->service->index(300);

        return $this->jsonSuccess(CountryResource::collection($countries));
    }

    /**
     * Show a country
     *
     * @param Country $country
     *
     * @return JsonResponse
     */
    public function show(Country $country): JsonResponse
    {
        return $this->jsonSuccess(CountryResource::make($country));
    }
}
