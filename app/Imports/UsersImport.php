<?php

namespace App\Imports;

use App\Events\CreateUserKeyCloak;
use App\Jobs\CreateUserKeyCloakJob;
use App\Jobs\SendEmailJob;
use App\Models\User;
use App\Traits\Keycloak;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class UsersImport implements ToCollection
{
    use Keycloak;

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {

        $accessToken = $this->getTokenKeycloak();

        foreach ($collection as $row) {
            $getApiUser = $this->createUserKeyCloak($accessToken, $row[0], $row[1]);
            $userIdKeyCloak = $this->getUserIdKeycloak($getApiUser, $accessToken);
            $user = User::updateOrInsert([
                'username' => $row[0],
                'email' => $row[1],
                'password' => $row[2],
                'user_id_keycloak' => $userIdKeyCloak
            ]);
        }
    }
}
