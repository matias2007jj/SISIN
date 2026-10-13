document.addEventListener('DOMContentLoaded', function () {
    const modales = document.querySelectorAll('.modal');
    modales.forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function () {
            const form = modal.querySelector('form');
            if (form && !modal.id.includes('Editar')) {
                form.reset();
            }
        });
    });
});