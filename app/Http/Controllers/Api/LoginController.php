<?php

namespace App\Http\Controllers\Api;

use App\Events\DeleteEvent;
use App\Events\RegisterEvent;
use App\Events\UpdateEvent;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\Keycloak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
    // public function register(Request $request)
    // {
    //     $this->validateRegister($request);

    //     $token = $this->getTokenKeycloak();
    //     if ($token == "") {
    //         return response(['errors' => 'Đăng ký thất bại']);
    //     } else {
    //         // biến để dạng carmel case
    //         $httpRegisterKeycloak =  Http::withHeaders([
    //             'Content-Type' => 'application/json',
    //             'Authorization' => 'Bearer ' . $token,

    //         ])->post(env('KETLOAK_URL') . '/admin/realms/' . env('KEYLOAK_REALM_NAME') . '/users', [
    //             'username' => $request->username,
    //             'email' => $request->email,
    //             'firstName' => $request->firstName,
    //             'lastName' => $request->lastName,
    //         ]);



    //         if ($httpRegisterKeycloak->successful()) { // check lai theo status code
    //             $user =  User::create([
    //                 'name' => $request->name,
    //                 'email' => $request->email,
    //                 'password' => $request->password,
    //             ]);
    //         }
    //         return response([
    //             'message' => 'Đăng ký thành công !',
    //             'data' => $httpRegisterKeycloak->header('location'),
    //             'user' => User::where('id', $user->id)->update(['user_id_keycloak' => $this->getUserApiKeycloak($httpRegisterKeycloak->header('location'), $token)]),
    //         ]);
    //     }


    //     return response(['errors' => 'Đăng ký thất bại']);
    // }



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
            return response([
                'message' => 'Đăng ký thành công !',
            ]);
        } catch (\Exception $e) {
            return response([
                'message' => 'Đăng ký thất bại !',
            ]);
        }
    }


    // public function update(Request $request, $id)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|min:10',
    //         'username' => 'required|min:6',
    //         'email' => 'required|email',
    //         'password' => 'required',
    //     ]);
    //     if ($validator->fails()) {
    //         return response([
    //             'errors' => $validator->errors()->all(),
    //         ]);
    //     }
    //     $token = $this->getTokenKeycloak();
    //     if ($token == "") {
    //         return response(['errors' => 'Đăng ký thất bại']);
    //     } else {
    //         $user = User::findOrFail($id); // firstOrFalse
    //         if ($user) {
    //             $user_id_keycloak = $user->user_id_keycloak;

    //             // event laravel
    //             $httpUpdateKeycloak = Http::withHeaders([
    //                 'Content-Type' => 'application/json',
    //                 'Authorization' => 'Bearer ' . $token,
    //             ])->put(env('KETLOAK_URL') . '/admin/realms/' . env('KEYLOAK_REALM_NAME') . '/users/' . $user_id_keycloak, [
    //                 'email' => $request->email,
    //                 'username' => $request->username,
    //                 'firstName' => $request->firstName,
    //                 'lastName' => $request->lastName,
    //             ]);

    //             if ($httpUpdateKeycloak->successful()) { // check lai điều kiện
    //                 $user->update([
    //                     'name' => $request->name,
    //                     'email' => $request->email,
    //                     'password' => $request->password,
    //                 ]);
    //             }
    //             return response(['message' => 'Cập nhật thành công']);
    //         }
    //     }

    //     return response(['error' => 'Cập nhật thất bại']);
    // }


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
            return response([
                'message' => 'Cập nhật thành công'
            ]);
        } catch (\Exception $e) {
            return response([
                'error' => 'Cập nhật thất bại'
            ]);
        }
    }





    // public function destroy($id)
    // {
    //     $user = User::findOrFail($id);
    //     if ($user) {
    //         $user_id_keycloak = $user->user_id_keycloak;
    //         $token = $this->getTokenKeycloak();
    //         if ($token == null) {
    //             return response(['errors' => 'Xóa thất bại (Chưa lấy được token)']);
    //         }

    //         $http_delete_user_keycloak = Http::withHeaders([
    //             'Content-Type' => 'application/json',
    //             'Authorization' => 'Bearer ' . $token,
    //         ])->delete(env('KETLOAK_URL') . '/admin/realms/' . env('KEYLOAK_REALM_NAME') . '/users/' . $user_id_keycloak);

    //         if ($http_delete_user_keycloak->successful()) {
    //             $user->delete();
    //         }
    //         return response(['message' => 'Xóa thành công !']);
    //     }

    //     return response(['error' => 'Không tìm thấy người dùng này']);
    // }



    public function destroy($id)
    {
        try {
            event(new DeleteEvent($id));
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
