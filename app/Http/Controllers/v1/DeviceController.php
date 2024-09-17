<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Device\DeviceAddRequest;
use App\Http\Requests\v1\Device\DeviceUpdateRequest;
use App\Http\Resources\v1\Branch\GetAllBranchesResource;
use App\Models\BaseModel;
use App\Models\v1\Branch;
use App\Models\v1\Device;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeviceController extends Controller
{
    public function all(){
        $data = Carbon::now();
        return gettype($data);
    }

    public function add(DeviceAddRequest $deviceAddRequest)
    {
        $data = $deviceAddRequest->validated();
        
        $create = Device::create([
            'name' => $data['name'],
            'branch_id' => $data['branch_id']
        ]);

        return response()->json([
            'success' => true,
            'data' => $create
        ], 201);
    }

    public function update($id, DeviceUpdateRequest $request)
    {
        $data = $request->validated();
        $device = Device::findOrFail($id);
        $branch_id = $device->branch_id;
        $result = $device->where('id', $id)->where('branch_id', $branch_id)->update([
            'name' => $data['name'] ?? $device->name
        ]);

        return response()->json([
            'success' => true,
            'data' => $result
        ]);

    }

    public function delete($id)
    {
        $device = Device::findOrFail($id);
        if ($device->attendances()->count() > 0 || $device->clientAttendances()->count() > 0) {
            
            return response()->json([
                'success' => false,
                'message' => 'Bu camerada odamlar mavjud!'
            ]);
        }

        $device->delete();
    }

}
