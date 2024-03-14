<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function login(Request $request)
    {
        $request->validate([
        'email' => 'required|email',
        'password' => 'required', 
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
         
        $token = $user->createToken('api-token')->plainTextToken;
        return response()->json([
            'token' => $token
        ]);
    }

    

    /**
     * Display the specified resource.
     */
    public function logout(Request $request)
    {
        //
    }

    
}