<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    // public function login()
    // {
    //     $login = Http::withHeaders([
    //         'Content-Type' => 'application/x-www-form-urlencoded'
    //     ])->asForm()->post(env('KETLOAK_URL') . "/realms/" . env('KEYLOAK_REALM_NAME') . "/protocol/openid-connect/token", [
    //         'grant_type' => env('KEYLOAK_CLIENT_GRANT_TYPE'),
    //         'client_id' => env('KEYLOAK_CLIENT_ID'),
    //         'client_secret' => env('KEYLOAK_CLIENT_SECRET')
    //     ]);
    //     $token = $login['access_token'];

    //     if ($token) {
    //         DB::table('token_login')->insert([
    //             'access_token' => $token
    //         ]);
    //     }
    //     return response([
    //         'message' => 'Đăng nhập thành công !'
    //     ]);
    // }

    //Hàm lấy token từ api login keycloak
    public function getTokenKeycloak()
    {
        // Lấy token từ login
        $http_login_keycloak = Http::withHeaders([
            'Content-Type' => 'application/x-www-form-urlencoded'
        ])->asForm()->post(env('KETLOAK_URL') . "/realms/" . env('KEYLOAK_REALM_NAME') . "/protocol/openid-connect/token", [
            'grant_type' => env('KEYLOAK_CLIENT_GRANT_TYPE'),
            'client_id' => env('KEYLOAK_CLIENT_ID'),
            'client_secret' => env('KEYLOAK_CLIENT_SECRET')
        ]);
        if ($http_login_keycloak['access_token'] == null) {
            return null;
        }
        
        return $$http_login_keycloak['access_token'];
    }

    public function getUserApiKeycloak($httpGetUser, $token)
    {
        $http_getUser_keycloak = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->get($httpGetUser);
        if ($http_getUser_keycloak['id'] == null) {
            return null;
        }
        $user_id = $http_getUser_keycloak['id'];
        return $user_id;
    }


    public function validateRegister(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:10',
            'username' => 'required|min:6',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all(),
            ]);
        }
    }
    public function register(Request $request)
    {
        $this->validateRegister($request);

        $token = $this->getTokenKeycloak();
        if ($token == "") {
            return response(['errors' => 'Đăng ký thất bại']);
        } else {
            // biến để dạng carmel case
            $http_register_keycloak =  Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token,

            ])->post(env('KETLOAK_URL') . '/admin/realms/' . env('KEYLOAK_REALM_NAME') . '/users', [
                'username' => $request->username,
                'email' => $request->email,
                'firstName' => $request->firstName,
                'lastName' => $request->lastName,
            ]);


            if ($http_register_keycloak ) { // check lai theo status code
                $user =  User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => $request->password,
                ]);
            }
            return response([
                'message' => 'Đăng ký thành công !',
                'data' => $http_register_keycloak->header('location'),
                'user' => User::where('id', $user->id)->update(['user_id_keycloak' => $this->getUserApiKeycloak($http_register_keycloak->header('location'), $token)]),
            ]);
        }


        return response(['errors' => 'Đăng ký thất bại']);
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:10',
            'username' => 'required|min:6',
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all(),
            ]);
        }
        $token = $this->getTokenHttpLogin();
        if ($token == "") {
            return response(['errors' => 'Đăng ký thất bại']);
        } else {
            $user = User::find($id); // firstOrFalse
            if ($user) {
                $user_id_keycloak = $user->user_id_keycloak;

                // event laravel
                $http_update_keycloak = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ])->put(env('KETLOAK_URL') . '/admin/realms/' . env('KEYLOAK_REALM_NAME') . '/users/' . $user_id_keycloak, [
                    'email' => $request->email,
                    'username' => $request->username,
                    'firstName' => $request->firstName,
                    'lastName' => $request->lastName,
                ]);

                if ($http_update_keycloak) { // check lai điều kiện
                    $user->update([
                        'name' => $request->name,
                        'email' => $request->email,
                        'password' => $request->password,
                    ]);
                }
                return response(['message' => 'Cập nhật thành công']);
            }
        }

        return response(['error' => 'Cập nhật thất bại']);
    }





    public function destroy($id)
    {
        $user = User::find($id);
        if ($user) {
            $user_id_keycloak = $user->user_id_keycloak;
            $token = $this->getTokenHttpLogin();
            if ($token == null) {
                return response(['errors' => 'Xóa thất bại (Chưa lấy được token)']);
            }

            $http_delete_user_keycloak = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ])->delete(env('KETLOAK_URL') . '/admin/realms/' . env('KEYLOAK_REALM_NAME') . '/users/' . $user_id_keycloak);

            if ($http_delete_user_keycloak) {
                $user->delete();
            }
            return response(['message' => 'Xóa thành công !']);
        }

        return response(['error' => 'Không tìm thấy người dùng này']);
    }
}
