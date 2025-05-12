<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request){
        $request->validate([
                'full_name' => 'required|string',
                'username' => 'required|unique:users,username|regex:/^[a-zA-Z0-9\-.]+$/',
                'password' => 'required',
                'bio' => 'required',
                'is_private' => 'required|boolean'
        ]);

        $user = User::create([
            'full_name' => $request->full_name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'bio' => $request->bio,
            'is_private' => $request->is_private,
            'created_at' => date('Y-m-d,H:i:s')
        ]);

        return response([
            'message' => 'Register Successfull',
            'token' => $user->createToken('login_tokens')->plainTextToken,
            'user' => $user,
        ]);
    }

    public function login(Request $request){
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);
    
        $user = User::where('username', $request->username)->first();
    
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => 'Invalid Username Or Password'
            ], 401);  
        }
    
        return response([
            'message' => 'Login Successful',
            'user' => $user,
            'token' => $user->createToken('login_tokens')->plainTextToken
        ], 200); 
    }
    
    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        
        return response([
            'message' => 'Logout Succesfull'
        ]);
    }

}
