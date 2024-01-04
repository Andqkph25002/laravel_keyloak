<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function addRolesInUser(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'role_name' => 'required'
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()]);
        }
        try {
            $user = User::findOrFail($id);
            $user->assignRole($request->role_name);
            return response(['message' => 'Thêm role name cho user thành công !']);
        } catch (\Exception $th) {
            return response(['errors' => 'Có lỗi xảy ra']);
        }
    }
    public function updateRoleInUser(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'role_name' => 'required'
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()]);
        }
        try {
            $user = User::findOrFail($id);
            $user->syncRoles($request->role_name);
            return response(['message' => 'Cập nhật role name cho user thành công !']);
        } catch (\Exception $th) {
            return response(['errors' => 'Có lỗi xảy ra']);
        }
    }
}
