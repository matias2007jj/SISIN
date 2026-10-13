@extends('layouts.sisin')

@section('title', 'Menú Principal - SISIN')

@section('content')
@php
    // Resumen: pásalo desde el controlador con view('menu', compact('stats')).
    // Si no lo pasas, se muestran ceros.
    $stats = $stats ?? [];
    $summary = [
        ['label' => 'Productos',        'value' => $stats['productos'] ?? 0,  'icon' => 'fa-cart-shopping'],
        ['label' => 'Clientes',         'value' => $stats['clientes'] ?? 0,   'icon' => 'fa-user-group'],
        ['label' => 'Facturas del mes', 'value' => $stats['facturas'] ?? 0,   'icon' => 'fa-receipt'],
        ['label' => 'Con stock bajo',   'value' => $stats['stock_bajo'] ?? 0, 'icon' => 'fa-triangle-exclamation', 'warn' => true],
    ];
@endphp


@endsection