<?php

namespace App\Http\Requests\Admin\DashboardNotification;

use App\Enums\DashboardNotificationTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

/**
 * A class defines the update dashboard notification request
 */
class UpdateDashboardNotificationRequest extends FormRequest
{
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
            'title' => ['nullable', 'string', 'max:255'],
            'details' => ['nullable', 'string'],
            'type' => ['nullable', new Enum(DashboardNotificationTypeEnum::class)],
            'resolved' => ['nullable', 'boolean'],
            'is_seen' => ['nullable', 'boolean'],
        ];
    }
}
