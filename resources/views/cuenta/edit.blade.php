@extends('plantilla.plantilla')

@section('title', 'SISIN - Mi cuenta')
@section('titulo', 'Mi cuenta')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/organizacion.css') }}">
@endpush

@section('contenido')
@include('organizacion._alertas')

@php $u = auth()->user(); @endphp

<div class="ficha-institucion">
    <div class="datos">
        <div><strong>Usuario:</strong> {{ $u->username }}</div>
        <div><strong>Rol:</strong> {{ $u->rol_etiqueta }}</div>
        <div><strong>Institución:</strong> {{ $u->institucionActiva()->nombre ?? 'Todas las instituciones' }}</div>
    </div>
</div>

<div class="mi-modal" style="max-width: 460px; position: static;">
    <form method="POST" action="{{ route('cuenta.update') }}">
        @csrf
        @method('PUT')

        <div class="modal-header">
            <div class="titulo">
                <span class="punto"></span>
                <h2>Cambiar contraseña</h2>
            </div>
        </div>

        <div class="modal-body">
            <div class="campo mb-3">
                <label for="password_actual">Contraseña actual</label>
                <input type="password" id="password_actual" name="password_actual" autocomplete="current-password" required>
            </div>
            <div class="campo mb-3">
                <label for="password">Nueva contraseña</label>
                <input type="password" id="password" name="password" minlength="6" maxlength="60" autocomplete="new-password" required>
            </div>
            <div class="campo mb-3">
                <label for="password_confirmation">Repetir nueva contraseña</label>
                <input type="password" id="password_confirmation" name="password_confirmation" minlength="6" maxlength="60" autocomplete="new-password" required>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-guardar">Guardar</button>
        </div>
    </form>
</div>
@endsection
