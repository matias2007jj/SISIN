<?php

namespace App\Http\Controllers;

use App\Models\Tipo;
use App\Models\Marca;
use App\Models\Estado; // <- Importar Estado
use Illuminate\Http\Request;

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

        if ($catalogo === 'estado') {
            $estados = Estado::with('programaEstudio')->orderBy('nombre', 'asc')->get();
            return view('almacen.catalogo.estado', compact('estados', 'catalogo'));
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
            $request->validate(['nombre' => 'required|string|max:100']);

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

        if ($catalogo === 'estado') {
            $request->validate(['nombre' => 'required|string|max:100']);

            Estado::create([
                'nombre' => mb_strtoupper(trim($request->nombre)),
                'programa_estudio_id' => session('programa_estudio_id'),
            ]);

            return redirect()->back()->with('success', 'Estado registrado correctamente');
        }

        return redirect()->back();
    }

    public function update(Request $request, $catalogo, $id)
    {
        if ($catalogo === 'marca') {
            $request->validate(['nombre' => 'required|string|max:100']);

            Marca::findOrFail($id)->update([
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

        if ($catalogo === 'estado') {
            $request->validate(['nombre' => 'required|string|max:100']);

            Estado::findOrFail($id)->update([
                'nombre' => mb_strtoupper(trim($request->nombre)),
            ]);

            return redirect()->back()->with('success', 'Estado actualizado correctamente');
        }

        return redirect()->back();
    }

    public function destroy($catalogo, $id)
    {
        if ($catalogo === 'marca') {
            Marca::findOrFail($id)->delete();
            return redirect()->back()->with('success', 'Marca eliminada correctamente');
        }

        if ($catalogo === 'tipo') {
            Tipo::findOrFail($id)->delete();
            return redirect()->back()->with('success', 'Tipo eliminado correctamente');
        }

        if ($catalogo === 'estado') {
            Estado::findOrFail($id)->delete();
            return redirect()->back()->with('success', 'Estado eliminado correctamente');
        }

        return redirect()->back();
    }
}