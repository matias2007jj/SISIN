<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Marca;

class CatalogoController extends Controller
{
    public function index($catalogo)
    {
        if ($catalogo === 'marca') {
            $marcas = Marca::with('programaEstudio')->orderBy('nombre', 'asc')->get();
            return view('almacen.catalogo.marca', compact('marcas', 'catalogo'));
        }

        return redirect()->back()->with('warning', 'Módulo en desarrollo');
    }

    public function store(Request $request, $catalogo)
    {
        if ($catalogo === 'marca') {
            $request->validate([
                'nombre' => 'required|string|max:100',
            ]);

            Marca::create([
                'nombre' => strtoupper(trim($request->nombre)),
                'programa_estudio_id' => session('programa_estudio_id'),
            ]);

            return redirect()->back()->with('success', 'Marca registrada correctamente');
        }

        return redirect()->back();
    }

    public function update(Request $request, $catalogo, $id)
    {
        if ($catalogo === 'marca') {
            $request->validate([
                'nombre' => 'required|string|max:100',
            ]);

            $marca = Marca::findOrFail($id);
            $marca->update([
                'nombre' => strtoupper(trim($request->nombre)),
            ]);

            return redirect()->back()->with('success', 'Marca actualizada correctamente');
        }

        return redirect()->back();
    }

    public function destroy($catalogo, $id)
    {
        if ($catalogo === 'marca') {
            $marca = Marca::findOrFail($id);
            $marca->delete();

            return redirect()->back()->with('success', 'Marca eliminada correctamente');
        }

        return redirect()->back();
    }
}