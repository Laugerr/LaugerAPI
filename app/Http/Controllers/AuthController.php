<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request){
        $validate = $request->validate([
            'username' => 'required|string|unique:users,username|min:3|max:25',
            'first_name' => 'required|regex:/^[\pL\s\-]+$/u|max:50|min:3',
            'family_name' => 'required|regex:/^[\pL\s\-]+$/u|max:50|min:3',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed|min:6',
        ]);

        $user = User::create([
            'username' => $validate['username'],
            'first_name' => $validate['first_name'],
            'family_name' => $validate['family_name'],
            'email' => $validate['email'],
            'password' => Hash::make($validate['password'])
        ]);

        $response = [
            'user' => $user
        ];

        return response($response, 201);
    }
    
}
