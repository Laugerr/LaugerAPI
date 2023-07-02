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

        $ip = $request->ip(); // Get the IP address of the user registring
        
        $user = User::create([
            'username' => $validate['username'],
            'first_name' => $validate['first_name'],
            'family_name' => $validate['family_name'],
            'email' => $validate['email'],
            'password' => Hash::make($validate['password']),
            'ip_register' => $ip // Set the IP address of the user registring 
        ]);

        $token = $user->createToken('mylaugertoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }
    
    public function login(Request $request){
        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string'
        ]);

        // Checking either login using email or username
        $field = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials[$field] = $credentials['login'];
        unset($credentials['login']);

        //Get user current IP address
        $ip = $request->ip();
        //Get user current Machine ID
        $machineID = request()->header('User-Agent');

        if (Auth::attempt($credentials)){
            $user = Auth::user();
            //Update the current IP address and Machine ID for the logged-in user
            $user->update([
                'ip_current' => $ip,
                'machine_id' => $machineID
            ]);
            
            $request->session()->regenerate();
            // Token creation
            $token = $user->createToken('mylaugertoken')->plainTextToken;

            $response = [
                'message' => 'Login Successful!',
                'user' => $user,
                'token' => $token
            ];

            // Authentication Success
            return response($response, 201);
        } else {
            return response(['message' => 'Invalide Credentials.'], 401);
        }
    }

    public function logout(Request $request){
        $user = Auth::user();

        if($user){
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response(['message' => 'Logged out!'], 200);
        }else{
            return response(['message' => 'No account is logged in. Login first!'], 401);
        }
    }
}
