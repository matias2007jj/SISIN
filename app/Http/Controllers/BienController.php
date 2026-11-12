<?php

namespace App\Http\Controllers;

use App\Models\Bien;

class BienController extends Controller
{
    /** Bienes de las salas de la institución activa. */
    public function index()
    {
        $bienes = Bien::with(['sala.programaEstudio.institucion', 'grupo', 'tipo', 'marca', 'modelo', 'estado'])
            ->orderBy('cbi')
            ->get();

        return view('almacen.bien.index', compact('bienes'));
    }
}
