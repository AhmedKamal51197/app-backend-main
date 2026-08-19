<?php

namespace App\Http\Requests\Admin\Chat;

use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;

/**
 * A class define for opening/creating chat
 */
class OpenChatRequest extends FormRequest
{
    use RequestFailedValidationJsonResponse;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'user_uuid' => ['required', 'string', 'exists:users,uuid'],
            'title' => ['nullable', 'string', 'max:255'],
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
            'user_uuid.required' => __('User is required for user chat'),
            'user_uuid.exists' => __('The selected user does not exist'),
        ];
    }

    /**
     * Additional validation after basic rules
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Prevent user from chatting with themselves
            if ($this->type === 'user' && $this->user_uuid === auth()->user()->uuid) {
                $validator->errors()->add('user_uuid', __('You cannot create a chat with yourself.'));
            }

        });
    }
}
