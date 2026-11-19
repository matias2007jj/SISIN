<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use Illuminate\Support\Facades\Auth;

class InventarioController extends Controller
{
  
    public function index()
    {
        $user = Auth::user();

        $inventarios = Inventario::with('programaEstudio.institucion')
            ->withCount('detalles')
            ->when(! $user->veTodaLaInstitucion(),
                fn ($q) => $q->whereIn('programa_estudio_id', $user->idsProgramasPermitidos()))
            ->orderByDesc('created_at')
            ->get();

        return view('almacen.inventario.index', compact('inventarios'));
    }

    public function show(Inventario $inventario)
    {
        $user = Auth::user();

        abort_unless(
            $user->veTodaLaInstitucion() || $user->puedeUsarPrograma((int) $inventario->programa_estudio_id),
            404
        );

        $inventario->load(['programaEstudio.institucion', 'detalles']);

        return view('almacen.inventario.show', compact('inventario'));
    }
}
