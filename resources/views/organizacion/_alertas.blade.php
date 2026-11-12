@if (session('success'))
    <div class="alerta-sisin exito" role="alert">{{ session('success') }}</div>
@endif

@if (session('error'))
    <div class="alerta-sisin error" role="alert">{{ session('error') }}</div>
@endif

@if ($errors->any())
    <div class="alerta-sisin error" role="alert">
        Revise los datos ingresados:
        <ul>
            @foreach ($errors->all() as $mensaje)
                <li>{{ $mensaje }}</li>
            @endforeach
        </ul>
    </div>
@endif
