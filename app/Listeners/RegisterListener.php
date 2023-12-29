<?php

namespace App\Listeners;

use App\Events\RegisterEvent;
use App\Models\User;
use App\Traits\Keycloak;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;

class RegisterListener
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
    public function handle(RegisterEvent $event)
    {

        $token = $this->getTokenKeycloak();
        $httpRegisterKeycloak =  Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,

        ])->post(env('KETLOAK_URL') . '/admin/realms/' . env('KEYLOAK_REALM_NAME') . '/users', [
            'username' => $event->username,
            'email' => $event->email,
        ]);
        if ($httpRegisterKeycloak->successful()) {
            $user = User::create([
                'name' => $event->name,
                'email' => $event->email,
                'password' => $event->password,

            ]);
            return response([
                'message' => 'Đăng ký thành công !',
                'data' => $httpRegisterKeycloak->header('location'),
                'user' => User::where('id', $user->id)->update(['user_id_keycloak' => $this->getUserApiKeycloak($httpRegisterKeycloak->header('location'), $token)]),
            ]);
        }
    }
}
