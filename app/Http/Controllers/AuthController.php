<?php

namespace App\Http\Controllers;
use App\Models\Usuario;
use App\Models\ProgramaEstudio;
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

            $programa = $usuario->programas()
                ->wherePivot('acceso', true)
                ->orderBy('programas_estudio.nombre')
                ->first();

   
            if (!$programa && $usuario->esAdministrador()) {
                $programa = ProgramaEstudio::where('activo', true)->orderBy('nombre')->first();
            }

            if ($programa) {
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