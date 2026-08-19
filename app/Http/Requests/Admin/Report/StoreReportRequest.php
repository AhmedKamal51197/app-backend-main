<?php

namespace App\Http\Requests\Admin\Report;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * A class defines the store report request for admin
 */
class StoreReportRequest extends FormRequest
{
    use RequestFailedValidationJsonResponse;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,uuid'],
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'body' => ['required', 'string', 'min:10', 'max:5000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'user_id.required' => __('User is required'),
            'user_id.exists' => __('User not found'),
            'title.required' => __('Report title is required'),
            'title.min' => __('Report title must be at least 3 characters'),
            'title.max' => __('Report title cannot exceed 255 characters'),
            'body.required' => __('Report body is required'),
            'body.min' => __('Report body must be at least 10 characters'),
            'body.max' => __('Report body cannot exceed 5000 characters'),
        ];
    }
}
