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

        try {
            DB::connection('mysql_branch_1')->transaction(function () use ($data) {
                DB::connection('mysql_branch_1')->table('branches')->insert($data);
            });

            DB::connection('mysql_branch_2')->transaction(function () use ($data) {
                DB::connection('mysql_branch_2')->table('branches')->insert($data);
            });

            return response()->json([
                'success' => true,
            ], 201);

        } catch (\Exception $e) {

            return response()->json([
                'error' => 'An error occurred while recording branches.',
                'details' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 500);
        }
    }


    public function update($id)
    {
        
        $branch1 = DB::connection('mysql_branch_1')->table('branches')->find($id);
        $branch2 = DB::connection('mysql_branch_2')->table('branches')->find($id);

        if ($branch1 && $branch2) {
            try {
                DB::connection('mysql_branch_1')->transaction(function () use ($branch1, $id) {
                    DB::connection('mysql_branch_1')->table('branches')->where('id', $id)->update([
                        'name' => request()->input('name', $branch1->name),
                        'location' => request()->input('location', $branch1->location),
                    ]);
                });

                DB::connection('mysql_branch_2')->transaction(function () use ($branch2, $id) {
                    DB::connection('mysql_branch_2')->table('branches')->where('id', $id)->update([
                        'name' => request()->input('name', $branch2->name),
                        'location' => request()->input('location', $branch2->location),
                    ]);
                });

                return response()->json([
                    'success' => true,
                ], 200);

            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'An error occurred while updating branches.',
                    'details' => $e->getMessage(),
                    'line' => $e->getLine(),
                ], 500);
            }
        } else {
            return response()->json([
                'error' => 'Branch not found.',
            ], 404);
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

    
    public function delete($id)
    {
        $branch1 = DB::connection('mysql_branch_1')->table('branches')->find($id);
        $branch2 = DB::connection('mysql_branch_2')->table('branches')->find($id);

        // Проверка наличия пользователей
        $usersInBranch1 = DB::connection('mysql_branch_1')->table('users')
            ->where('branch_id', $id)
            ->count();

        $usersInBranch2 = DB::connection('mysql_branch_2')->table('users')
            ->where('branch_id', $id)
            ->count();

        if ($usersInBranch1 > 0 || $usersInBranch2 > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Bu filialda foydalanuvchilar mavjud'
            ], 400);
        }
        
        $branch1->delete();
        $branch2->delete();

        return response()->json([
                'success' => true,
        ]);

        }
 
}
