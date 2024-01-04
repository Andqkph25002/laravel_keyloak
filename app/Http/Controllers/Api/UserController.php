<?php

namespace App\Http\Controllers\Api;

use App\Events\DeleteUserEvent;
use App\Events\UpdateUserEvent;
use App\Http\Controllers\Controller;
use App\Jobs\ImportUsersJob;
use App\Jobs\SendEmailJob;
use App\Models\User;
use App\Traits\Keycloak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use Keycloak;
    public function validateRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|min:6',
            'email' => 'required|email|unique:users,email',
            'role_name' => 'required',
            'password' => 'required',
        ]);

        return $validator;
    }
    public function register(Request $request)
    {
        $validator =  $this->validateRegister($request);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all(),
            ]);
        }
        try {
            $accessToken = $request->bearerToken();
            $userIdKeyCloak = $this->createUserKeyCloak($accessToken, $request->username, $request->email);
            $user = new User();
            $user->username = $request->username;
            $user->email  = $request->email;
            $user->password = bcrypt($request->password);
            $user->assignRole($request->role_name);
            $user->save();
            User::find($user->id)->update([
                'user_id_keycloak' => $userIdKeyCloak
            ]);
            return response([
                'message' => 'Đăng ký thành công !',
            ]);
        } catch (\Exception $e) {
            return response([
                'message' => 'Đăng ký thất bại !',
            ]);
        }
    }
    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'role_name' => 'required',
        ]);
        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all(),
            ]);
        }
        try {
            $accessToken = $request->bearerToken();
            event(new UpdateUserEvent($accessToken, $request->email, $id));
            $user = User::findOrFail($id);
            $user->username = $request->username;
            $user->email = $request->email;
            $user->password = $request->password;
            $user->save();
            $user->syncRoles($request->role_name);
            return response([
                'message' => 'Cập nhật thành công',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response([
                'error' => 'Cập nhật thất bại'
            ]);
        }
    }
    public function destroy($id)
    {
        try {
            event(new DeleteUserEvent($id));
            $user = User::findOrFail($id);
            $user->delete();
            return response([
                'message' => 'Xóa thành công'
            ]);
        } catch (\Exception $e) {
            return response([
                'error' => 'Xóa thất bại'
            ]);
        }
    }

    public function index(Request $request)
    {
        $query = User::query();
        if ($request->username) {
            $query->where('username', 'like', '%' . $request->username . '%');
        } else if ($request->email) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }
        if ($request->role_name) {
            $query->role($request->role_name);
        }
        $users = $query->paginate(10);
        return response()->json($users);
    }

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
