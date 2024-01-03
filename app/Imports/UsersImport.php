<?php

namespace App\Imports;

use App\Models\User;
use App\Traits\Keycloak;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
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
            $userIdKeyCloak = $this->createUserKeyCloak($accessToken, $row[0], $row[1]);
            if ($userIdKeyCloak == null) {
                abort(404);
            }
            $data = [
                'username' => $row[0],
                'email' => $row[1],
                'password' => $row[2],
            ];
            DB::table('users')->updateOrInsert($data);
            $id = DB::table('users')->where($data)->value('id');
            User::find($id)->update([
                'user_id_keycloak' => $userIdKeyCloak
            ]);
        }
    }
}
