<?php

namespace App\Listeners;

use App\Events\DeleteUserEvent;
use App\Models\User;
use App\Traits\Keycloak;
use Illuminate\Support\Facades\Http;

class DeleteKeycloakListener
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
    public function handle(DeleteUserEvent $event): void
    {
       
        $user = User::findOrFail($event->userId);
        if ($user) {
            $userIdKeycloak = $user->user_id_keycloak;
            $token = $this->getTokenKeycloak();
            Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ])->delete(env('KETLOAK_URL') . '/admin/realms/' . env('KEYLOAK_REALM_NAME') . '/users/' . $userIdKeycloak);
        }
    }
}
