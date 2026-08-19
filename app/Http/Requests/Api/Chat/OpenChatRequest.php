<?php

namespace App\Http\Requests\Api\Chat;

use App\Enums\ChatTypeEnum;
use App\Traits\RequestFailedValidationJsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'type' => ['required', Rule::in(ChatTypeEnum::toArray())],
            'user_uuid' => ['required_if:type,user', 'string', 'exists:users,uuid'],
            'team_uuid' => ['required_if:type,team', 'string', 'exists:teams,uuid'],
            'service_uuid' => ['required_if:type,service', 'string', 'exists:services,uuid'],
            'part_time_job_uuid' => ['required_if:type,part_time_job', 'string', 'exists:part_time_jobs,uuid'],
            'project_uuid' => ['required_if:type,project', 'string', 'exists:projects,uuid'],
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
            'type.required' => __('Chat type is required'),
            'type.in' => __('Invalid chat type selected'),
            'user_uuid.required_if' => __('User is required for user chat'),
            'user_uuid.exists' => __('The selected user does not exist'),
            'team_uuid.required_if' => __('Team is required for team chat'),
            'team_uuid.exists' => __('The selected team does not exist'),
            'service_uuid.required_if' => __('Service is required for service chat'),
            'service_uuid.exists' => __('The selected service does not exist'),
            'part_time_job_uuid.required_if' => __('Part time job is required for part time job chat'),
            'part_time_job_uuid.exists' => __('The selected part time job does not exist'),
            'project_uuid.required_if' => __('Project is required for project chat'),
            'project_uuid.exists' => __('The selected project does not exist'),
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

            // Check team membership
            if ($this->type === 'team' && $this->team_uuid) {
                $team = \App\Models\Team::where('uuid', $this->team_uuid)->first();
                if ($team && !$team->members()->where('user_id', auth()->id())->exists()) {
                    $validator->errors()->add('team_uuid', __('You are not a member of this team.'));
                }
            }
        });
    }
}
