<?php

namespace App\Http\Requests\v1\User;

use App\Models\BaseModel;
use Illuminate\Foundation\Http\FormRequest;

class UserAddRequest extends FormRequest
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
            'name' => 'required|string',
            'position_id' => 'required|exists:positions,id',
            'branch_id' => 'required|exists:branches,id',
            'schedule_id' => 'required|exists:schedules,id',
            'phone' => 'required',
            'images' => 'array',
            'images.*' => 'file',
        ];
    }
}
