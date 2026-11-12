<?php

namespace App\Http\Controllers;

use App\Models\Inventario;

class InventarioController extends Controller
{
    /** Tomas de inventario de la institución activa (el scope ya filtra). */
    public function index()
    {
        $inventarios = Inventario::with('programaEstudio.institucion')
            ->withCount('detalles')
            ->orderByDesc('created_at')
            ->get();

        return view('almacen.inventario.index', compact('inventarios'));
    }

    /** Si el inventario es de otra institución, el scope lo oculta y Laravel responde 404. */
    public function show(Inventario $inventario)
    {
        $inventario->load(['programaEstudio.institucion', 'detalles']);

        return view('almacen.inventario.show', compact('inventario'));
    }
}
