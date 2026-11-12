@extends('layouts.sisin')

@section('title', 'Menú Principal - SISIN')

@section('content')
@php
    // Resumen: pásalo desde el controlador con view('menu', compact('stats')).
    // Si no lo pasas, se muestran ceros.
    $stats = $stats ?? [];
    $summary = [
        ['label' => 'Productos',        'value' => $stats['productos'] ?? 0],
        ['label' => 'Clientes',         'value' => $stats['clientes'] ?? 0],
        ['label' => 'Facturas del mes', 'value' => $stats['facturas'] ?? 0],
        ['label' => 'Con stock bajo',   'value' => $stats['stock_bajo'] ?? 0, 'warn' => true],
    ];
@endphp


@endsection