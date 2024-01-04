<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ImportUsersJob;
use App\Jobs\SendEmailJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function importUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fileExcel' => 'required|mimes:xlsx,application/excel'
        ]);
        if ($validator->fails()) {
            return response(['error' => $validator->errors()->all()]);
        }
        if (Auth::check()) {
            $email = Auth::user()->email;
            $file = $request->file('fileExcel');
            $filePath = $file->storeAs('imports', 'imported_file.xlsx');
            ImportUsersJob::dispatch($filePath);
            $subject = "Email từ laravel";
            $message = "Import thành công !";
            SendEmailJob::dispatch($email, $subject, $message);
            return response(['message' => 'Import thành công !']);
        }
        return response(['error' => 'Import thất bại']);
    }
}
