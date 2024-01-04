<?php

namespace App\Listeners;

use App\Events\UpdateUserEvent;
use App\Models\User;
use App\Traits\Keycloak;
use Illuminate\Support\Facades\Http;

class UpdateKeycloakListener
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
    public function handle(UpdateUserEvent $event): void
    {
        $user = User::findOrFail($event->userId);
        if ($user) {
            $userIdKeycloak = $user->user_id_keycloak;
            Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $event->token,
            ])->put(env('KETLOAK_URL') . '/admin/realms/' . env('KEYLOAK_REALM_NAME') . '/users/' . $userIdKeycloak, [
                'email' => $event->email
            ]);
        }
    }
}
