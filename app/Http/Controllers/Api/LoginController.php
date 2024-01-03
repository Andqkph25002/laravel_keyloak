<?php

namespace App\Http\Controllers\Api;

use App\Events\DeleteEvent;
use App\Events\RegisterEvent;
use App\Events\UpdateEvent;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\Keycloak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    use Keycloak;



    public function validateRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:10',
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
            event(new RegisterEvent($request->email, $request->username, $request->password, $request->name));
            if (session()->has('userIdKeycloak')) {
                $userIdKeycloak = session()->get('userIdKeycloak');
                User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => $request->password,
                    'user_id_keycloak' => $userIdKeycloak
                ]);
                session()->forget('userIdKeycloak');
            }

            return response([
                'message' => 'Đăng ký thành công !',
            ]);
        } catch (\Exception $e) {
            return response([
                'message' => 'Đăng ký thất bại !',
            ]);
        }
    }
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:10',
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all(),
            ]);
        }
        try {
            event(new UpdateEvent($request->email, $request->password, $request->name, $id));
            $user = User::findOrFail($id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);
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



    public function destroy($id)
    {
        try {
            event(new DeleteEvent($id));
            $user = User::findOrFail($id);
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
}
