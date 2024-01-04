<?php

namespace App\Listeners;

use App\Events\DeleteEvent;
use App\Models\User;
use App\Traits\Keycloak;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
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
    public function handle(DeleteEvent $event): void
    {
        $user = User::findOrFail($event->userId);
        if ($user) {
            $userIdKeycloak = $user->user_id_keycloak;
            Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $event->token,
            ])->delete(env('KETLOAK_URL') . '/admin/realms/' . env('KEYLOAK_REALM_NAME') . '/users/' . $userIdKeycloak);
        }
    }
}
