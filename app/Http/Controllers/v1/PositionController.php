<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Position\PositionAddRequest;
use App\Http\Requests\v1\Position\PositionUpdateRequest;
use App\Http\Resources\v1\Position\PositionsResource;
use App\Models\v1\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PositionController extends Controller
{
    public function add(PositionAddRequest $request){
        $data = $request->validated();

        try {
            DB::connection('mysql_branch_1')->transaction(function () use ($data) {
                DB::connection('mysql_branch_1')->table('positions')->insert($data);
            });

            DB::connection('mysql_branch_2')->transaction(function () use ($data) {
                DB::connection('mysql_branch_2')->table('posiitons')->insert($data);
            });

            return response()->json([
                'success' => true,
            ], 201);

        } catch (\Exception $e) {

            return response()->json([
                'error' => 'An error occurred while recording positions.',
                'details' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 500);
        }
        // Position::create([ 
        //     'name' => $data['name'],
        // ]);
        // return response()->json([
        //     'success' => true,
        // ],201);
    }

    public function update($id,PositionUpdateRequest $request){
        // if($position){
        //     $position->update([
        //         'name' => $request->name
        //     ]);
        //     return response()->json([
        //         'success' => true,
        //     ]);
        // }
        $data = $request->validated();
        $branch1 = DB::connection('mysql_branch_1')->table('positions')->find($id);
        $branch2 = DB::connection('mysql_branch_2')->table('positions')->find($id);

        if ($branch1 && $branch2) {
            try {
                DB::connection('mysql_branch_1')->transaction(function () use ($branch1, $id, $data) {
                    DB::connection('mysql_branch_1')->table('positions')->where('id', $id)->update([
                        'name' => $data['name'] ?? $branch1->name,
                    ]);
                });

                DB::connection('mysql_branch_2')->transaction(function () use ($branch2, $id, $data) {
                    DB::connection('mysql_branch_2')->table('positions')->where('id', $id)->update([
                        'name' => $data['name'] ?? $branch2->name,

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
    public function delete(Position $position){
        if($position){
            if($position->name =='unknown'){
                return response()->json([
                   'success' => false,
                   'message' => 'You can not delete this position'
                ],400);
            }
            $position_id = Position::where('name','unknown')->first()->id;
            $users = $position->users;
            $users->each(function($user) use ($position_id) {
                $user->position_id = $position_id;
                $user->save();
            });

            $branch1 = DB::connection('mysql_branch_1')->table('positions')->find($position->id);
            $branch2 = DB::connection('mysql_branch_2')->table('positions')->find($position->id);
            $branch1->delete();
            $branch2->delete();
            return response()->json([
               'success' => true,
            ]);
        }
    }

    public function all_positions(){
        $positions = Position::latest()->get();
        return response()->json([
            'success' => true,
            'data' => PositionsResource::collection($positions)
        ]);
    }

}
