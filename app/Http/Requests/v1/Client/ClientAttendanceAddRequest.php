<?php

namespace App\Http\Requests\v1\Client;

use App\Models\BaseModel;
use App\Models\v1\Device;
use DB;
use Illuminate\Foundation\Http\FormRequest;

class ClientAttendanceAddRequest extends FormRequest
{
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'client_id' =>'required',
            'gender' => 'required|string',
            'age' => 'required|integer',
            //'device_id' => 'required|exists:devices,id',
            'device_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    $branchId = null;
                    try {
                        BaseModel::setConnectionByBranchId(1);
                        $device = DB::connection('mysql_branch_1')->table('devices')->where('id', $value)->first();

                        if ($device) {
                            $branchId = $device->branch_id;
                        } else {
                            BaseModel::setConnectionByBranchId(2);
                            $device = DB::connection('mysql_branch_2')->table('devices')->where('id', $value)->first();

                            if ($device) {
                                $branchId = $device->branch_id;
                            }
                        }

                        if (!$branchId) {
                            $fail('Device ID ' . $value . ' не найден ни в одной из баз данных.');
                        } else {
                            BaseModel::setConnectionByBranchId($branchId);
                        }

                    } catch (\Exception $e) {
                        $fail('Не удалось установить соединение с базой данных для device_id: ' . $value);
                    }
                },
            ],
            'date' => 'required',
            'score' => 'required|numeric'
        ];
    }

    // protected function prepareForValidation()
    // {
    //     $device_id = $this->get('device_id');
    //     $device = Device::findOrFail($device_id);
    //     $branchId = $device->branch_id;
    //     if ($branch_id) {
    //         BaseModel::setConnectionByBranchId($branch_id);
    //     }

    // }
}
