<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::orderBy('id', 'desc')->get();
        return response([
            'data' => $user
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ]);
        }

        $user = User::create($request->all());

        return response([
            'user' => $user,
            'status' => 200
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if ($id) {
            $user = User::find($id);
            return response([
                'user' => $user
            ]);
        }
        return response([
            'message' => 'Người dùng này không tồn tại'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all()
            ]);
        }

        $user = User::where('id', $id)->update($request->all());
        return response([
            'user' => $user
        ]);

        return response(['message' => 'Người dùng này không tồn tại']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
            return response([
                'message' => 'Xóa thành công !'
            ]);
        }
        return response([
            'error' => 'Người dùng không tồn tại !'
        ]);
    }
}
