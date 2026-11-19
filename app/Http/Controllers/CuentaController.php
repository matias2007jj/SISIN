<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/** Cada usuario, sea cual sea su rol, puede cambiar su propia contraseña. */
class CuentaController extends Controller
{
    public function edit()
    {
        return view('cuenta.edit');
    }

    public function update(Request $request)
    {
        $datos = $request->validate([
            'password_actual' => ['required', 'string'],
            'password'        => ['required', 'string', 'min:6', 'max:60', 'confirmed'],
        ], [
            'password.confirmed' => 'La confirmación de la nueva contraseña no coincide.',
            'password.min'       => 'La nueva contraseña debe tener al menos 6 caracteres.',
        ]);

        $usuario = $request->user();

        if (! Hash::check($datos['password_actual'], $usuario->password)) {
            return redirect()->back()->withErrors(['password_actual' => 'La contraseña actual no es correcta.']);
        }

        $usuario->update(['password' => $datos['password']]);   // el cast 'hashed' la encripta

        return redirect()->route('cuenta.edit')->with('success', 'Contraseña actualizada correctamente');
    }
}
