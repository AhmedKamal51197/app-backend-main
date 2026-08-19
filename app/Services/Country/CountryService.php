<?php

namespace App\Services\Country;

use App\Events\LogExceptionEvent;
use App\Models\Country;
use App\Models\Setting;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * A class defines the country service
 */
class CountryService
{
    /**
     * Get all countries
     *
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function index(int $perPage = Setting::PAGE_RESULT_LIMIT): LengthAwarePaginator
    {
        return Country::query()
            ->orderBy('id')
            ->paginate($perPage);
    }

    /**
     *
     * Store new country
     *
     * @param array $data
     *
     * @return Country
     *
     * @throws Exception
     */
    public function store(array $data): Country
    {
        DB::beginTransaction();
        try {
            $country = Country::create($data);

            DB::commit();

            return $country;
        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));
            throw $exception;
        }
    }

    /**
     * Delete country
     *
     * @param Country $country
     *
     * @return bool
     *
     * @throws Exception
     */
    public function delete(Country $country): bool
    {
        DB::beginTransaction();
        try {
            $result = $country->delete();

            DB::commit();

            return $result;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }
}
