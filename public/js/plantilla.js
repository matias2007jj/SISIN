$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

document.querySelectorAll('.submenu-toggle').forEach(function (toggle) {
    toggle.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        toggle.parentElement.classList.toggle('open');
    });
});

document.querySelectorAll('.navbar-custom .dropdown').forEach(function (dd) {
    dd.addEventListener('hidden.bs.dropdown', function () {
        dd.querySelectorAll('.dropdown-submenu.open').forEach(function (s) {
            s.classList.remove('open');
        });
    });
});