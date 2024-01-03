<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;

trait Keycloak
{

    public function getTokenKeycloak()
    {
        $httpLoginKeycloak = Http::withHeaders([
            'Content-Type' => 'application/x-www-form-urlencoded'
        ])->asForm()->post(env('KETLOAK_URL') . "/realms/" . env('KEYLOAK_REALM_NAME') . "/protocol/openid-connect/token", [
            'grant_type' => env('KEYLOAK_CLIENT_GRANT_TYPE'),
            'client_id' => env('KEYLOAK_CLIENT_ID'),
            'client_secret' => env('KEYLOAK_CLIENT_SECRET')
        ]);
        if ($httpLoginKeycloak['access_token'] == "") {
            return response(['error' => 'Lỗi access token']);
        }

        return $httpLoginKeycloak['access_token'];
    }
    public function getUserIdKeycloak($httpGetUser, $token)
    {
        $httpGetUserKeycloak = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->get($httpGetUser);
        if ($httpGetUserKeycloak['id'] == null) {
            return null;
        }
        $userId = $httpGetUserKeycloak['id'];
        return $userId;
    }
    public function createUserKeyCloak($token, $username, $email)
    {
        $httpRegisterKeycloak =  Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,

        ])->post(env('KETLOAK_URL') . '/admin/realms/' . env('KEYLOAK_REALM_NAME') . '/users', [
            'username' => $username,
            'email' => $email,
        ]);
        if ($httpRegisterKeycloak->successful()) {
            $httpUserKeycloak = $httpRegisterKeycloak->header('location');
            $length = strlen($httpUserKeycloak);
            $userIdKeyCloak = substr($httpUserKeycloak, $length - 36, $length);
            return $userIdKeyCloak;
        }
        return null;
    }
}
