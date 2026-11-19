document.addEventListener('DOMContentLoaded', () => {
    // Confirmación antes de pasar a INACTIVO (también para roles que no ven el formulario de registro)
    document.addEventListener('submit', (e) => {
        const formDesactivar = e.target.closest('.form-desactivar');
        if (!formDesactivar) return;

        if (!confirm('¿Seguro que deseas pasar este bien a estado INACTIVO?')) {
            e.preventDefault();
        }
    });

    const modalEl = document.getElementById('modalBien');
    if (!modalEl) return;

    const modal = new bootstrap.Modal(modalEl);
    const form = document.getElementById('formBien');
    const titulo = document.getElementById('tituloModalBien');
    const metodo = document.getElementById('metodoForm');
    const storeUrl = form.dataset.storeUrl;

    // 1. Abrir modal para Crear
    const btnNuevo = document.getElementById('btnNuevoBien');
    if (btnNuevo) {
        btnNuevo.addEventListener('click', () => {
            form.reset();
            form.action = storeUrl;
            metodo.value = 'POST';
            titulo.textContent = 'Registrar Nuevo Bien';
            modal.show();
        });
    }

    // 2. Abrir modal para Editar (delegación de eventos por si filtras/paginación)
    document.addEventListener('click', (e) => {
        const btnEditar = e.target.closest('.btn-editar');
        if (!btnEditar) return;

        const b = JSON.parse(btnEditar.dataset.bien);
        form.action = btnEditar.dataset.url;
        metodo.value = 'PUT';
        titulo.textContent = `Editar Bien: ${b.cbi}`;

        document.getElementById('cbi').value = b.cbi ?? '';
        document.getElementById('descripcion').value = b.descripcion ?? '';
        document.getElementById('sala_id').value = b.sala_id ?? '';
        document.getElementById('grupo_id').value = b.grupo_id ?? '';
        document.getElementById('tipo_id').value = b.tipo_id ?? '';
        document.getElementById('marca_id').value = b.marca_id ?? '';
        document.getElementById('modelo_id').value = b.modelo_id ?? '';
        document.getElementById('estado_id').value = b.estado_id ?? '';
        document.getElementById('serie_medida').value = b.serie_medida ?? '';
        document.getElementById('otras_especificaciones').value = b.otras_especificaciones ?? '';

        modal.show();
    });

});
