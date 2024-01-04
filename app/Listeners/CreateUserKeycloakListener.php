<?php

namespace App\Listeners;

use App\Events\RegisterEvent;
use App\Events\RegisterUserEvent;
use App\Traits\Keycloak;
use Illuminate\Support\Facades\Http;

class CreateUserKeycloakListener
{
    use Keycloak;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RegisterUserEvent $event)
    {

       
        $httpRegisterKeycloak =  Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $event->token,

        ])->post(env('KETLOAK_URL') . '/admin/realms/' . env('KEYLOAK_REALM_NAME') . '/users', [
            'username' => $event->username,
            'email' => $event->email,
        ]);
        if ($httpRegisterKeycloak->successful()) {
            $httpUserKeycloak = $httpRegisterKeycloak->header('location');
            session()->put('userIdKeycloak', $this->getUserIdKeycloak($httpUserKeycloak, $event->token));
        }
    }
}
