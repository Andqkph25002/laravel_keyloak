<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{
    public function loginView()
    {
        return view('login');
    }
    public function loginUser(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $httpLoginKeycloak = Http::withHeaders([
            'Content-Type' => 'application/x-www-form-urlencoded'
        ])->asForm()->post(env('KETLOAK_URL') . "/realms/" . env('KEYLOAK_REALM_NAME') . "/protocol/openid-connect/token", [
            'grant_type' => 'password',
            'client_id' => env('KEYLOAK_CLIENT_ID'),
            'client_secret' => env('KEYLOAK_CLIENT_SECRET'),
            'username' => $request->username,
            'password' => $request->password,
        ]);
        if ($httpLoginKeycloak->successful()) {
            $tokenData = $httpLoginKeycloak->json();
            session(['access_token' => $tokenData['access_token']]);
            return redirect()->route('index');
        }

        return redirect()->route('login')->with('error', 'Đăng nhập không thành công');
    }
}
