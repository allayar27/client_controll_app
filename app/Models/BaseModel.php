<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BaseModel extends Model
{
    use HasFactory;

    public static function setConnectionByBranchId($branchId)
    {
        switch ($branchId) {
            case 1:
                DB::setDefaultConnection('mysql_branch_1');
                break;
            case 2:
                DB::setDefaultConnection('mysql_branch_2');
                break;
            default:
                throw new \Exception("Invalid branch ID");
        }
    }
}
