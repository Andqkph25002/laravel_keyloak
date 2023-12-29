<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

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
            return null;
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
}
