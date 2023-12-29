<?php

namespace App\Listeners;

use App\Events\DeleteEvent;
use App\Models\User;
use App\Traits\Keycloak;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;

class DeleteListener
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
        $user = User::findOrFail($event->user_id);
        if ($user) {
            $user_id_keycloak = $user->user_id_keycloak;
            $token = $this->getTokenKeycloak();

            $http_delete_user_keycloak = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ])->delete(env('KETLOAK_URL') . '/admin/realms/' . env('KEYLOAK_REALM_NAME') . '/users/' . $user_id_keycloak);

            if ($http_delete_user_keycloak->successful()) {
                $user->delete();
            }
        }
    }
}
