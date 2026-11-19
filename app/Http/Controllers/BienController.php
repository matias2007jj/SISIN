<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BienController extends Controller
{
    public function index()
    {
        /** @var Usuario $user */
        $user = Auth::user();
        $esSuperadmin = $user->esSuperusuario() ? 1 : 0;
        $institucionId = $user->institucionActiva()?->id;
        $programaId = session('programa_estudio_id');

        // 1. Listar bienes filtrados por programa de estudio (o institución/global si es superadmin)
        $bienes = DB::select('CALL sp_listar_bienes(?, ?, ?)', [
            $programaId,
            $institucionId,
            $esSuperadmin,
        ]);

        // 2. Cargar combos aislados por el programa de estudio activo en sesión
        $salas   = $programaId ? DB::select('CALL sp_listar_salas(?)', [$programaId]) : [];
        $grupos  = $programaId ? DB::select('CALL sp_listar_grupos(?, 0)', [$programaId]) : [];
        $tipos   = $programaId ? DB::select('CALL sp_listar_tipos(?)', [$programaId]) : [];
        $marcas  = $programaId ? DB::select('CALL sp_listar_marcas(?)', [$programaId]) : [];
        $modelos = $programaId ? DB::select('CALL sp_listar_modelos(?)', [$programaId]) : [];

        // 3. Cargar estados excluyendo INACTIVO (id=6) para que solo se asigne con el botón de baja
        $estados = collect(DB::select('CALL sp_listar_estados()'))
            ->where('id', '!=', 6);

        return view('almacen.bien.index', compact(
            'bienes', 'salas', 'grupos', 'tipos', 'marcas', 'modelos', 'estados'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sala_id'                => ['required', 'integer', 'exists:salas,id'],
            'grupo_id'               => ['required', 'integer', 'exists:grupos,id'],
            'cbi'                    => ['required', 'string', 'max:10', Rule::unique('bienes', 'cbi')->whereNull('deleted_at')],
            'tipo_id'                => ['required', 'integer', 'exists:tipos,id'],
            'descripcion'            => ['required', 'string', 'max:100'],
            'marca_id'               => ['nullable', 'integer', 'exists:marcas,id'],
            'modelo_id'              => ['nullable', 'integer', 'exists:modelos,id'],
            'serie_medida'           => ['nullable', 'string', 'max:100'],
            'estado_id'              => ['required', 'integer', 'exists:estados,id'],
            'otras_especificaciones' => ['nullable', 'string', 'max:100'],
        ], [
            'cbi.unique' => 'El código CBI ya se encuentra registrado en otro bien.',
        ]);

        DB::statement('CALL sp_guardar_bien(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
            $data['sala_id'],
            $data['grupo_id'],
            $data['cbi'],
            $data['tipo_id'],
            $data['descripcion'],
            $data['marca_id'] ?? null,
            $data['modelo_id'] ?? null,
            $data['serie_medida'] ?? null,
            $data['estado_id'],
            $data['otras_especificaciones'] ?? null,
            Auth::id(),
        ]);

        return redirect()->route('bienes.index')->with('success', 'Bien registrado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'sala_id'                => ['required', 'integer', 'exists:salas,id'],
            'grupo_id'               => ['required', 'integer', 'exists:grupos,id'],
            'cbi'                    => ['required', 'string', 'max:10', Rule::unique('bienes', 'cbi')->ignore($id)->whereNull('deleted_at')],
            'tipo_id'                => ['required', 'integer', 'exists:tipos,id'],
            'descripcion'            => ['required', 'string', 'max:100'],
            'marca_id'               => ['nullable', 'integer', 'exists:marcas,id'],
            'modelo_id'              => ['nullable', 'integer', 'exists:modelos,id'],
            'serie_medida'           => ['nullable', 'string', 'max:100'],
            'estado_id'              => ['required', 'integer', 'exists:estados,id'],
            'otras_especificaciones' => ['nullable', 'string', 'max:100'],
        ], [
            'cbi.unique' => 'El código CBI ya pertenece a otro bien.',
        ]);

        DB::statement('CALL sp_actualizar_bien(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
            $id,
            $data['sala_id'],
            $data['grupo_id'],
            $data['cbi'],
            $data['tipo_id'],
            $data['descripcion'],
            $data['marca_id'] ?? null,
            $data['modelo_id'] ?? null,
            $data['serie_medida'] ?? null,
            $data['estado_id'],
            $data['otras_especificaciones'] ?? null,
            Auth::id(),
        ]);

        return redirect()->route('bienes.index')->with('success', 'Bien actualizado correctamente.');
    }

    public function destroy($id)
    {
        // Pasa el bien a estado INACTIVO (id = 6) sin borrar el registro
        DB::statement('CALL sp_desactivar_bien(?, ?)', [
            $id,
            Auth::id(),
        ]);

        return redirect()->route('bienes.index')->with('success', 'El bien pasó a estado INACTIVO.');
    }
}