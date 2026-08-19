<?php

namespace App\Http\Controllers\Admin\Country;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Requests\Admin\Country\StoreCountryRequest;
use App\Http\Resources\Admin\Country\CountryResource;
use App\Models\Country;
use App\Models\Setting;
use App\Services\Country\CountryService;
use Exception;
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
        $countries = $this->service->index($request->input('limit', Setting::PAGE_RESULT_LIMIT));

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

    /**
     * Store country
     *
     * @param StoreCountryRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function store(StoreCountryRequest $request): JsonResponse
    {
        $country = $this->service->store($request->validated());

        return $this->jsonSuccess(
            CountryResource::make($country),
            __('Country created successfully')
        );
    }

    /**
     * Delete specific country
     *
     * @param Country $country
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function delete(Country $country): JsonResponse
    {
        $this->service->delete($country);

        return $this->jsonSuccess([], __('Country deleted successfully'));
    }
}
