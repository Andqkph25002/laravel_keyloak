<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ImportUsersJob;
use App\Jobs\SendEmailJob;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function importUser(Request $request)
    {
        $request->validate([
            'fileExcel' => 'required|max:50000|mimes:xlsx,application/excel'
        ]);
        $accessToken = $request->bearerToken();
        $file = $request->file('fileExcel');
        $filePath = $file->storeAs('imports', 'imported_file.xlsx');
        ImportUsersJob::dispatch($filePath);

        $decodedToken = json_decode(base64_decode(explode('.', $accessToken)[1]), true);

        $subject = "Email từ laravel";
        $message = "Import thành công !";
        SendEmailJob::dispatch($decodedToken['email'], $subject, $message);
        return "done";
    }
}
