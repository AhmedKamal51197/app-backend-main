<?php

namespace App\Http\Requests\Admin\Chat;

use App\Enums\ChatTypeEnum;
use App\Enums\OrderStatusEnum;
use App\Enums\ProjectStatusEnum;
use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * A class define for admin chat index request
 */
class ChatIndexRequest extends FormRequest
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
            'type' => ['nullable', Rule::in([
                ChatTypeEnum::USER->value,
                ChatTypeEnum::PROJECT->value,
                ChatTypeEnum::SERVICE->value,
            ])],
            'project_status' => ['nullable', Rule::in(ProjectStatusEnum::toArray())],
            'order_status' => ['nullable', Rule::in(OrderStatusEnum::toArray())],
            'search' => ['nullable', 'string', 'max:255'],
            'limit' => ['nullable', 'integer', 'min:1'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
