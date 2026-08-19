<?php

namespace App\Http\Requests\Admin\Service;

use App\Enums\OrderDirectionEnum;
use App\Enums\TimePeriodEnum;
use App\Models\Setting;
use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * A class define for admin service index request
 */
class ServiceIndexRequest extends FormRequest
{
    use RequestFailedValidationJsonResponse;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'limit' => ['nullable', 'integer', 'min:1',],
            'page' => ['nullable', 'integer', 'min:1'],
            'status' => ['nullable', 'boolean'],
            'order_by' => ['nullable', Rule::enum(OrderDirectionEnum::class)],
            'services_categories_rate' => ['nullable', Rule::enum(TimePeriodEnum::class)],
        ];
    }
}
