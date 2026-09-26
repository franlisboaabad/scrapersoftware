<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> · Scraper de Empresas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="<?= e(asset_url('assets/css/app.css')) ?>">
</head>

<body data-base="<?= e($appBase) ?>" data-page="<?= e($page) ?>">
    <div class="app">
        <?php require __DIR__ . '/partials/sidebar.php'; ?>

        <div class="workspace">
            <header class="topbar">
                <button class="icon-btn menu-toggle" type="button" aria-label="Abrir menú" aria-controls="sidebar" aria-expanded="false">
                    <i class="fa-solid fa-bars" aria-hidden="true"></i>
                </button>
                <h1><?= e($pageTitle) ?></h1>
                <?php if ($page !== 'buscar'): ?>
                    <a class="btn btn-primary" href="<?= e(app_url('buscar')) ?>">
                        <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                        <span class="btn-label">Nueva búsqueda</span>
                    </a>
                <?php endif; ?>
            </header>

            <main class="main">
                <?php require $viewFile; ?>
            </main>
        </div>
    </div>

    <div class="nav-backdrop" hidden></div>
    <script src="<?= e(asset_url('assets/js/app.js')) ?>"></script>
    <?php if ($page === 'buscar'): ?>
        <script src="<?= e(asset_url('assets/js/buscar.js')) ?>"></script>
    <?php endif; ?>
</body>

</html>
