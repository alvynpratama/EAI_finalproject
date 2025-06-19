<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{

    public function index()
    {
        return response()->json(User::all(), 200);
    }


    public function store(Request $request)
    {

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);


        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
        ]);


        return response()->json($user, 201);
    }


    public function show($id)
    {

        $user = User::findOrFail($id);
        return response()->json($user);
    }


    public function update(Request $request, $id)
    {

        $user = User::findOrFail($id);


        $validated = $request->validate([
            'name'  => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id, // validasi email dengan pengecualian pada user yang sedang diupdate
            'password' => 'sometimes|required|string|min:8',
        ]);


        if ($request->has('password')) {

            $validated['password'] = bcrypt($request->password);
        }


        $user->update($validated);

        return response()->json($user);
    }


    public function destroy($id)
    {

        $user = User::findOrFail($id);


        $user->delete();


        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
