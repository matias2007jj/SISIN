<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CatalogoController extends Controller
{
    public function index($catalogo)
    {
        $programaId   = session('programa_estudio_id');
        $esSuperadmin = session('es_superadmin', false) ? 1 : 0;

        if ($catalogo === 'marca') {
            $marcas = DB::select('CALL sp_listar_marcas(?)', [$programaId]);
            return view('almacen.catalogo.marca', compact('marcas', 'catalogo'));
        }

        if ($catalogo === 'tipo') {
            $tipos = DB::select('CALL sp_listar_tipos(?)', [$programaId]);
            return view('almacen.catalogo.tipo', compact('tipos', 'catalogo'));
        }

        if ($catalogo === 'modelo') {
            $modelos = DB::select('CALL sp_listar_modelos(?)', [$programaId]);
            return view('almacen.catalogo.modelo', compact('modelos', 'catalogo'));
        }

        if ($catalogo === 'estado') {
            $estados = DB::select('CALL sp_listar_estados()');
            return view('almacen.catalogo.estado', compact('estados', 'catalogo'));
        }

        if ($catalogo === 'grupo') {
            // Se envía $programaId y $esSuperadmin para permitir al SUPERADMIN ver todos los grupos
            $grupos = DB::select('CALL sp_listar_grupos(?, ?)', [$programaId, $esSuperadmin]);
            $programasEstudio = DB::select('SELECT id, nombre FROM programas_estudio WHERE deleted_at IS NULL ORDER BY nombre ASC');
            return view('almacen.catalogo.grupo', compact('grupos', 'programasEstudio', 'catalogo'));
        }

        if ($catalogo === 'sala') {
            $salas = DB::select('CALL sp_listar_salas(?)', [$programaId]);
            return view('almacen.catalogo.sala', compact('salas', 'catalogo'));
        }

        return redirect()->back()->with('warning', 'Módulo en desarrollo');
    }

    public function store(Request $request, $catalogo)
    {   
        $userId = Auth::id() ?? 1;
        $programaId = session('programa_estudio_id');

        if (in_array($catalogo, ['marca', 'tipo', 'modelo', 'estado', 'sala'])) {
            $request->validate(['nombre' => 'required|string|max:60']);

            if ($catalogo === 'marca')  DB::statement('CALL sp_guardar_marca(?, ?, ?)', [$request->nombre, $programaId, $userId]);
            if ($catalogo === 'tipo')   DB::statement('CALL sp_guardar_tipo(?, ?, ?)', [$request->nombre, $programaId, $userId]);
            if ($catalogo === 'modelo') DB::statement('CALL sp_guardar_modelo(?, ?, ?)', [$request->nombre, $programaId, $userId]);
            if ($catalogo === 'estado') DB::statement('CALL sp_guardar_estado(?, ?)', [$request->nombre, $userId]);
            if ($catalogo === 'sala')   DB::statement('CALL sp_guardar_sala(?, ?, ?, ?)', [$request->nombre, $request->descripcion ?? null, $programaId, $userId]);

            return redirect()->back()->with('success', ucfirst($catalogo) . ' registrado correctamente');
        }

        if ($catalogo === 'grupo') {
            $request->validate([
                'nombre' => 'required|string|max:60',
                'programa_estudio_id' => 'required|exists:programas_estudio,id'
            ]);

            DB::statement('CALL sp_guardar_grupo(?, ?, ?)', [$request->nombre, $request->programa_estudio_id, $userId]);
            return redirect()->back()->with('success', 'Grupo registrado correctamente');
        }

        return redirect()->back();
    }

    public function update(Request $request, $catalogo, $id)
    {
        $userId = Auth::id() ?? 1;

        if (in_array($catalogo, ['marca', 'tipo', 'modelo', 'estado', 'sala'])) {
            $request->validate(['nombre' => 'required|string|max:60']);

            if ($catalogo === 'marca')  DB::statement('CALL sp_actualizar_marca(?, ?, ?)', [$id, $request->nombre, $userId]);
            if ($catalogo === 'tipo')   DB::statement('CALL sp_actualizar_tipo(?, ?, ?)', [$id, $request->nombre, $userId]);
            if ($catalogo === 'modelo') DB::statement('CALL sp_actualizar_modelo(?, ?, ?)', [$id, $request->nombre, $userId]);
            if ($catalogo === 'estado') DB::statement('CALL sp_actualizar_estado(?, ?, ?)', [$id, $request->nombre, $userId]);
            if ($catalogo === 'sala')   DB::statement('CALL sp_actualizar_sala(?, ?, ?, ?)', [$id, $request->nombre, $request->descripcion ?? null, $userId]);

            return redirect()->back()->with('success', ucfirst($catalogo) . ' actualizado correctamente');
        }

        if ($catalogo === 'grupo') {
            $request->validate([
                'nombre' => 'required|string|max:60',
                'programa_estudio_id' => 'required|exists:programas_estudio,id'
            ]);

            DB::statement('CALL sp_actualizar_grupo(?, ?, ?, ?)', [$id, $request->nombre, $request->programa_estudio_id, $userId]);
            return redirect()->back()->with('success', 'Grupo actualizado correctamente');
        }

        return redirect()->back();
    }

    public function destroy($catalogo, $id)
    {
        $userId = Auth::id() ?? 1;

        if (in_array($catalogo, ['marca', 'tipo', 'modelo', 'estado', 'grupo', 'sala'])) {
            if ($catalogo === 'marca')  DB::statement('CALL sp_eliminar_marca(?, ?)', [$id, $userId]);
            if ($catalogo === 'tipo')   DB::statement('CALL sp_eliminar_tipo(?, ?)', [$id, $userId]);
            if ($catalogo === 'modelo') DB::statement('CALL sp_eliminar_modelo(?, ?)', [$id, $userId]);
            if ($catalogo === 'estado') DB::statement('CALL sp_eliminar_estado(?, ?)', [$id, $userId]);
            if ($catalogo === 'grupo')  DB::statement('CALL sp_eliminar_grupo(?, ?)', [$id, $userId]);
            if ($catalogo === 'sala')   DB::statement('CALL sp_eliminar_sala(?, ?)', [$id, $userId]);

            return redirect()->back()->with('success', ucfirst($catalogo) . ' eliminado correctamente');
        }

        return redirect()->back();
    }
}