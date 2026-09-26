(() => {
    const toggle = document.querySelector('.menu-toggle');
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.querySelector('.nav-backdrop');

    if (!toggle || !sidebar || !backdrop) {
        return;
    }

    const setOpen = (open) => {
        sidebar.classList.toggle('is-open', open);
        backdrop.hidden = !open;
        toggle.setAttribute('aria-expanded', String(open));
    };

    toggle.addEventListener('click', () => {
        setOpen(!sidebar.classList.contains('is-open'));
    });

    backdrop.addEventListener('click', () => setOpen(false));
})();
