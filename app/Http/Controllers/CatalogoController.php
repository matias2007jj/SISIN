<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use App\Models\grupo;
use App\Models\ProgramaEstudio;
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
            $modelos = Modelo::with('programaEstudio')->orderBy('nombre', 'asc')->get();
            return view('almacen.catalogo.modelo', compact('modelos', 'catalogo'));
        }

        if ($catalogo === 'grupo') {
            $grupos = Grupo::with('programaEstudio')->orderBy('nombre', 'asc')->get();
            $programasEstudio = ProgramaEstudio::orderBy('nombre', 'asc')->get();
            return view('almacen.catalogo.grupo', compact('grupos', 'programasEstudio', 'catalogo'));
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
        if ($catalogo === 'modelo') {
            $request->validate(['nombre' => 'required|string|max:100']);
         
           Modelo::create([
                'nombre' => mb_strtoupper(trim($request->nombre)),
                'programa_estudio_id' => session('programa_estudio_id'),
            ]);

            return redirect()->back()->with('success', 'Modelo registrado correctamente');
        }

        if ($catalogo === 'estado') {
            $request->validate(['nombre' => 'required|string|max:100']);

            Estado::create([
                'nombre' => mb_strtoupper(trim($request->nombre)),
                'programa_estudio_id' => session('programa_estudio_id'),
            ]);

            return redirect()->back()->with('success', 'Estado registrado correctamente');
        }

        if ($catalogo === 'grupo') {
            $request->validate([
                'nombre' => 'required|string|max:60|unique:grupos,nombre',
                'programa_estudio_id' => 'required|exists:programas_estudio,id'
            ]);

            Grupo::create([
                'nombre' => strtoupper(trim($request->nombre)),
                'programa_estudio_id' => $request->programa_estudio_id,
            ]);

            return redirect()->back()->with('success', 'Grupo registrado correctamente');
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

        if ($catalogo === 'grupo') {
            $request->validate([
                'nombre' => 'required|string|max:60|unique:grupos,nombre,' . $id,
                'programa_estudio_id' => 'required|exists:programas_estudio,id'
            ]);

            $grupo = Grupo::findOrFail($id);
            $grupo->update([
                'nombre' => strtoupper(trim($request->nombre)),
                'programa_estudio_id' => $request->programa_estudio_id,
            ]);

            return redirect()->back()->with('success', 'Grupo actualizado correctamente');
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

        if ($catalogo === 'grupo') {
            $grupo = Grupo::findOrFail($id);
            $grupo->delete();

            return redirect()->back()->with('success', 'Grupo eliminado correctamente');
        }

        return redirect()->back();
    }
}