<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{
    public function login()
    {
        $login = Http::withHeaders([
            'Content-Type' => 'application/x-www-form-urlencoded'
        ])->asForm()->post(env('KETLOAK_URL') . "/realms/" . env('KEYLOAK_REALM_NAME') . "/protocol/openid-connect/token", [
            'grant_type' => env('KEYLOAK_CLIENT_GRANT_TYPE'),
            'client_id' => env('KEYLOAK_CLIENT_ID'),
            'client_secret' => env('KEYLOAK_CLIENT_SECRET')
        ]);
        $token = $login['access_token'];

        if ($token) {
            DB::table('token_login')->insert([
                'access_token' => $token
            ]);
        }
        return response([
            'message' => 'Đăng nhập thành công !'
        ]);
    }

    public function register(Request $request)
    {
        $token  =  DB::table('token_login')->orderBy('id', 'desc')->first();
        Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token->access_token,

        ])->post('https://sso.toprate.io/admin/realms/PHP-intern/users', [
            'username' => $request->username,
            'email' => $request->email,
            'firstName' => $request->firstName,
            'lastName' => $request->lastName
        ]);


        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password
        ]);



        return response([
            'message' => 'Đăng ký thành công !'
        ]);
    }
}
