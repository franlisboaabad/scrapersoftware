<?php

require_once __DIR__ . '/connection.php';

function dashboardResumen(): array
{
    $vacio = [
        'ok' => false,
        'error' => null,
        'busquedas' => 0,
        'empresas' => 0,
        'con_web' => 0,
        'con_email' => 0,
        'ultimas_busquedas' => [],
        'ultimas_empresas' => [],
    ];

    try {
        $pdo = db();

        return [
            'ok' => true,
            'error' => null,
            'busquedas' => (int) $pdo->query('SELECT COUNT(*) FROM busquedas')->fetchColumn(),
            'empresas' => (int) $pdo->query('SELECT COUNT(*) FROM empresas')->fetchColumn(),
            'con_web' => (int) $pdo->query('SELECT COUNT(*) FROM empresas WHERE website IS NOT NULL')->fetchColumn(),
            'con_email' => (int) $pdo->query('SELECT COUNT(DISTINCT empresa_id) FROM empresa_emails')->fetchColumn(),
            'ultimas_busquedas' => $pdo->query(
                'SELECT id, keyword, location, limite, total_encontradas, created_at
                 FROM busquedas
                 ORDER BY id DESC
                 LIMIT 8'
            )->fetchAll(),
            'ultimas_empresas' => $pdo->query(
                'SELECT id, nombre, website, telefono, direccion, rating, created_at
                 FROM empresas
                 ORDER BY updated_at DESC
                 LIMIT 8'
            )->fetchAll(),
        ];
    } catch (Throwable $exception) {
        $vacio['error'] = $exception->getMessage();

        return $vacio;
    }
}

function formatearFecha(?string $datetime): string
{
    if ($datetime === null || $datetime === '') {
        return '—';
    }

    try {
        return (new DateTime($datetime))->format('d/m/Y H:i');
    } catch (Exception $exception) {
        return $datetime;
    }
}
