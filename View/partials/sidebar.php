<aside class="sidebar" id="sidebar">
    <div class="brand">
        <span class="brand-mark" aria-hidden="true">
            <i class="fa-solid fa-building"></i>
        </span>
        <div>
            <strong>Scraper</strong>
            <span>Empresas por rubro</span>
        </div>
    </div>

    <nav class="nav" aria-label="Principal">
        <a class="nav-link<?= $page === 'dashboard' ? ' is-active' : '' ?>" href="<?= e(app_url('dashboard')) ?>">
            <i class="fa-solid fa-table-columns" aria-hidden="true"></i>
            Panel
        </a>
        <a class="nav-link<?= $page === 'buscar' ? ' is-active' : '' ?>" href="<?= e(app_url('buscar')) ?>">
            <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
            Buscar
        </a>
    </nav>
</aside>
