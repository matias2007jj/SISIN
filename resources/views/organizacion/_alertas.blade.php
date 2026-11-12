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

@if (session('credenciales'))
    <div class="alerta-sisin exito" role="alert">
        <strong>Datos de acceso de la institución</strong> (anótelos ahora, no se vuelven a mostrar):<br>
        Usuario: <code>{{ session('credenciales.usuario') }}</code> &nbsp;·&nbsp;
        Contraseña: <code>{{ session('credenciales.clave') }}</code>
    </div>
@endif
