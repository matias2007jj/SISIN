$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

document.querySelectorAll('.sidebar-group-toggle, .submenu-toggle').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
        e.preventDefault();
        const parent = btn.parentElement;
        const isOpen = parent.classList.toggle('open');
        btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
});

(function () {
    const sidebar = document.getElementById('sidebarSisin');
    const backdrop = document.getElementById('sidebarBackdrop');
    const btnOpen = document.getElementById('btnToggleSidebar');
    const btnClose = document.getElementById('btnCloseSidebar');

    function toggleSidebar(open) {
        if (!sidebar) return;
        sidebar.classList.toggle('open', open);
        if (backdrop) backdrop.classList.toggle('show', open);
        if (btnOpen) btnOpen.setAttribute('aria-expanded', open ? 'true' : 'false');
    }

    if (btnOpen) btnOpen.addEventListener('click', () => toggleSidebar(true));
    if (btnClose) btnClose.addEventListener('click', () => toggleSidebar(false));
    if (backdrop) backdrop.addEventListener('click', () => toggleSidebar(false));
})();