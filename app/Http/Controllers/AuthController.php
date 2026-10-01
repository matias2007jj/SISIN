<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'usu' => 'required',
            'pass' => 'required',
        ]);

        $credentials = [
            'username' => $request->input('usu'),
            'password' => $request->input('pass'),
        ];

        // Intentamos autenticar
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return response()->json([
                'status' => 'success',
                'message' => '¡Acceso correcto!'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Usuario o contraseña incorrectos.'
        ], 401);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}