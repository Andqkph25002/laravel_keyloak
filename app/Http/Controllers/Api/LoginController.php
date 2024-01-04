<?php

namespace App\Http\Controllers\Api;

use App\Events\DeleteEvent;
use App\Events\UpdateEvent;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\Keycloak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    use Keycloak;
    public function validateRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|min:6',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
        ]);

        return $validator;
    }
    public function register(Request $request)
    {
        $validator =  $this->validateRegister($request);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all(),
            ]);
        }
        try {
            $accessToken = $request->bearerToken();
            $userIdKeyCloak = $this->createUserKeyCloak($accessToken, $request->username, $request->email);
            $user = new User();
            $user->username = $request->username;
            $user->email  = $request->email;
            $user->password = bcrypt($request->password);
            $user->assignRole($request->role);
            $user->save();
            User::find($user->id)->update([
                'user_id_keycloak' => $userIdKeyCloak
            ]);
            return response([
                'message' => 'Đăng ký thành công !',
            ]);
        } catch (\Exception $e) {
            return response([
                'message' => 'Đăng ký thất bại !',
            ]);
        }
    }
    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all(),
            ]);
        }
        try {
            $accessToken = $request->bearerToken();
            event(new UpdateEvent($accessToken, $request->email, $id));
            $user = User::findOrFail($id);
            $user->username = $request->username;
            $user->email = $request->email;
            $user->password = $request->password;
            $user->save();
            $user->syncRoles($request->role);
            return response([
                'message' => 'Cập nhật thành công',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response([
                'error' => 'Cập nhật thất bại'
            ]);
        }
    }



    public function destroy(Request $request, $id)
    {
        try {
            $accessToken = $request->bearerToken();
            event(new DeleteEvent($accessToken, $id));
            $user = User::findOrFail($id);
            $roles = $user->getRoleNames();
            if (is_array($roles)) {
                foreach ($roles as $role) {
                    $user->removeRole($role);
                }
            }
            $user->removeRole($roles);
            $user->delete();

            return response([
                'message' => 'Xóa thành công'
            ]);
        } catch (\Exception $e) {
            return response([
                'error' => 'Xóa thất bại'
            ]);
        }
    }

    public function index()
    {
        $user = User::all();
        return response(['data' => $user]);
    }
}
