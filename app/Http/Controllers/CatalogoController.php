<?php

namespace App\Http\Controllers;

use App\Models\Tipo;
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

        if ($catalogo === 'tipo') {
            $tipos = Tipo::with('programaEstudio')->orderBy('nombre', 'asc')->get();
            return view('almacen.catalogo.tipo', compact('tipos', 'catalogo'));
        }
        if ($catalogo === 'sala') {
            $salas = collect();
            return view('almacen.catalogo.sala', compact('salas', 'catalogo'));
        }

        if ($catalogo === 'modelo') {
            $modelos = collect();
            return view('almacen.catalogo.modelo', compact('modelos', 'catalogo'));
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
        if ($catalogo === 'tipo') {
            $request->validate(['nombre' => 'required|string|max:100']);
         
           Tipo::create([
                'nombre' => mb_strtoupper(trim($request->nombre)),
                'programa_estudio_id' => session('programa_estudio_id'),
            ]);

            return redirect()->back()->with('success', 'Tipo registrado correctamente');
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

        if ($catalogo === 'tipo') {
            $request->validate(['nombre' => 'required|string|max:100']);

            Tipo::findOrFail($id)->update([
                'nombre' => mb_strtoupper(trim($request->nombre)),
            ]);

            return redirect()->back()->with('success', 'Tipo actualizado correctamente');
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

        if ($catalogo === 'tipo') {
            $tipo = Tipo::findOrFail($id);
            $tipo->delete();

            return redirect()->back()->with('success', 'Tipo eliminado correctamente');
        }
        if ($catalogo === 'tipo') {
            Tipo::findOrFail($id)->delete();

            return redirect()->back()->with('success', 'Tipo eliminado correctamente');
        }

        return redirect()->back();
    }
}
