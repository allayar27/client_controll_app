<?php

namespace App\Http\Requests\v1\User;

use App\Models\BaseModel;
use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'string',
            'branch_id' => 'exists:branches,id',
            'position_id' => 'exists:positions,id',
            'schedule_id' => 'exists:schedules,id',
            'phone' => 'string',
            'images' => 'array',
            'images.*' => 'file',
        ];
    }

}
