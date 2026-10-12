<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class CatalogoController extends Controller
{
    /**
     * Tablas generales de Almacén. Ajusta 'tabla' y 'pk' si en tu base de datos se llaman distinto.
     */
    protected array $catalogos = [
        'grupo'  => ['titulo' => 'Grupo',  'tabla' => 'grupos',  'pk' => 'id'],
        'estado' => ['titulo' => 'Estado', 'tabla' => 'estados', 'pk' => 'id'],
        'marca'  => ['titulo' => 'Marca',  'tabla' => 'marcas',  'pk' => 'id'],
        'modelo' => ['titulo' => 'Modelo', 'tabla' => 'modelos', 'pk' => 'id'],
        'sala'   => ['titulo' => 'Sala',   'tabla' => 'salas',   'pk' => 'id'],
        'tipo'   => ['titulo' => 'Tipo',   'tabla' => 'tipos',   'pk' => 'id'],
    ];

    protected function config(string $catalogo): array
    {
        abort_unless(isset($this->catalogos[$catalogo]), 404);

        return $this->catalogos[$catalogo];
    }

    protected function usuarioActual(): string
    {
        $u = auth()->user();

        return mb_strtoupper($u?->username ?? $u?->name ?? 'SISTEMA');
    }

    protected function mensajes(): array
    {
        return [
            'nombre.required'  => 'El nombre es obligatorio.',
            'nombre.max'       => 'El nombre no puede superar los 100 caracteres.',
            'nombre.unique'    => 'Ya existe un registro con ese nombre.',
            'descripcion.max'  => 'La descripción no puede superar los 255 caracteres.',
        ];
    }

    public function index(string $catalogo)
    {
        $cfg = $this->config($catalogo);

        return view('catalogo', [
            'catalogo' => $catalogo,
            'titulo'   => $cfg['titulo'],
            'pk'       => $cfg['pk'],
            'filas'    => DB::table($cfg['tabla'])->orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request, string $catalogo)
    {
        $cfg = $this->config($catalogo);
        $request->merge(['nombre' => mb_strtoupper(trim((string) $request->input('nombre')))]);

        $datos = $request->validate([
            'nombre'      => ['required', 'string', 'max:100', Rule::unique($cfg['tabla'], 'nombre')],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ], $this->mensajes());

        $registro = [
            'nombre'        => $datos['nombre'],
            'fechaCreacion' => now(),
            'idUsuarioCrea' => $this->usuarioActual(),
        ];

        // La descripción se guarda solo si la tabla tiene esa columna
        if (Schema::hasColumn($cfg['tabla'], 'descripcion')) {
            $registro['descripcion'] = $datos['descripcion'] ?? null;
        }

        DB::table($cfg['tabla'])->insert($registro);

        return redirect()->route('catalogo.index', $catalogo)
            ->with('mensaje', 'Registro guardado correctamente.');
    }

    public function update(Request $request, string $catalogo, $id)
    {
        $cfg = $this->config($catalogo);
        $request->merge(['nombre' => mb_strtoupper(trim((string) $request->input('nombre')))]);

        $datos = $request->validate([
            'nombre'      => ['required', 'string', 'max:100', Rule::unique($cfg['tabla'], 'nombre')->ignore($id, $cfg['pk'])],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ], $this->mensajes());

        $cambios = [
            'nombre'            => $datos['nombre'],
            'fechaModificacion' => now(),
            'idUsuarioModifica' => $this->usuarioActual(),
        ];

        if (Schema::hasColumn($cfg['tabla'], 'descripcion')) {
            $cambios['descripcion'] = $datos['descripcion'] ?? null;
        }

        DB::table($cfg['tabla'])->where($cfg['pk'], $id)->update($cambios);

        return redirect()->route('catalogo.index', $catalogo)
            ->with('mensaje', 'Registro actualizado correctamente.');
    }

    public function destroy(string $catalogo, $id)
    {
        $cfg = $this->config($catalogo);

        try {
            DB::table($cfg['tabla'])->where($cfg['pk'], $id)->delete();
        } catch (QueryException $e) {
            return redirect()->route('catalogo.index', $catalogo)
                ->withErrors(['eliminar' => 'No se puede eliminar el registro porque está siendo utilizado.']);
        }

        return redirect()->route('catalogo.index', $catalogo)
            ->with('mensaje', 'Registro eliminado correctamente.');
    }
}
