<?php

namespace App\Listeners;

use App\Events\UpdateEvent;
use App\Models\User;
use App\Traits\Keycloak;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;

class UpdateListener
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
    public function handle(UpdateEvent $event): void
    {
        $token = $this->getTokenKeycloak();
        $user = User::findOrFail($event->user_id);
        if ($user) {
            $user_id_keycloak = $user->user_id_keycloak;

            // event laravel
            $httpUpdateKeycloak = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ])->put(env('KETLOAK_URL') . '/admin/realms/' . env('KEYLOAK_REALM_NAME') . '/users/' . $user_id_keycloak, [
                'email' => $event->email
            ]);

            if ($httpUpdateKeycloak->successful()) { // check lai điều kiện
                $user->update([
                    'name' => $event->name,
                    'email' => $event->email,
                    'password' => $event->password,
                ]);
            }
        }
    }
}
