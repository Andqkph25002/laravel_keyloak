<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleInPermissionController extends Controller
{
    public function addRolesInUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userId' => 'required|numeric',
            'role' => 'required'
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()]);
        }
        try {
            $user = User::find($request->id);
            $user->assignRole($request->role);
            return response(['message' => 'Thêm role cho user thành công !']);
        } catch (\Exception $th) {
            return response(['errors' => 'Có lỗi xảy ra']);
        }
    }
    public function updateRoleInUser(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required'
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()]);
        }
        try {
            $user = User::find($id);
            $user->syncRoles($request->role);
            return response(['message' => 'Thêm role cho user thành công !']);
        } catch (\Exception $th) {
            return response(['errors' => 'Có lỗi xảy ra']);
        }
    }
}
