<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropertiesController extends Controller
{
    public function me(){
        $user = User::select('name', 'rut as id', 'preferred_color')
            ->where('id', auth()->user()->id)
            ->get();

        return response()->json($user);
    }

    public function users(){
        $users = DB::table("model_has_permissions")
            ->select('users.name', 'users.rut as id', 'users.preferred_color')
            ->join("permissions","model_has_permissions.permission_id", "=", "permissions.id")
            ->join("users","model_has_permissions.model_id","=", "users.id")
            ->where("permissions.name", "=", "user_login")
            ->get();

        return response()->json($users);
    }


    public function myPermissions( $sufix = null ){
        if(auth()->check()) {
            $current_user = auth()->user()->id;

            $permissions = DB::table("model_has_permissions")
                ->select(DB::raw("permissions.name"))
                ->join("permissions", "model_has_permissions.permission_id", "=", "permissions.id")
                ->join("users", "model_has_permissions.model_id", "=", "users.id")
                ->where('users.id', "=", $current_user)
                ->where("permissions.name", "LIKE", $sufix . "%")
                ->orderBy('permissions.name')
                ->get();

            return response()->json($permissions);
        }
        abort(403);
    }
}
