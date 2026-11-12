<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
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
            'activo'   => true,
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            /** @var Usuario $usuario */
            $usuario = Auth::user();

            // Un usuario de institución queda fijo en ella (se lee de usuarios.institucion_id);
            // el superusuario empieza viendo todas.
            $request->session()->forget(['institucion_id', 'programa_estudio_id']);

            if ($usuario->institucion_id && ! $usuario->institucion) {
                Auth::logout();
                return response()->json([
                    'status'  => 'error',
                    'message' => 'La institución de este usuario ya no está disponible.',
                ], 403);
            }

            if ($programa = $usuario->programaPorDefecto()) {
                $request->session()->put('programa_estudio_id', $programa->id);
            }

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
