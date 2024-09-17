<?php

namespace App\Http\Controllers\v1;

use Carbon\Carbon;
use App\Models\v1\User;
use App\Models\v1\Branch;
use App\Models\v1\Device;
use App\Models\v1\Work_Days;
use Illuminate\Http\Request;
use App\Models\v1\Attendance;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Branch\BranchAddRequest;
use App\Http\Requests\v1\Branch\BranchUpdateRequest;
use App\Http\Resources\v1\Branch\BranchsResource;
use Exception;

class BranchController extends Controller
{
    public function add(BranchAddRequest $request)
    {
        $data = $request->validated();

        $data = $request->validated();
        $branch = Branch::create([
            'name' => $data['name'],
            'location' => $data['location'],
        ]);

        return response()->json([
            'success' => true,
            'data' => $branch
        ], 201);
    }


    public function update(Request $request, Branch $branch)
    {

        if ($branch) {
            $branch->update([
                'name' => $request->input('name', $branch->name),
                'location' => $request->input('location', $branch->location),
            ]);
            return response()->json([
                'success' => true,
            ]);
        }
    }

    public function all(Request $request)
    {
        $branches = Branch::latest()->get();

        return response()->json([
            'success' => true,
            'data' => BranchsResource::collection($branches)
        ]);
    }

    
    public function delete(Branch $branch)
    {
        if ($branch) {
            if ($branch->users()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu filialda foydalanuvchilar mavjud'
                ], 400);
            }
            $branch->delete();
            return response()->json([
                'success' => true,
            ]);
        }

        }
 
}
