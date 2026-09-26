<?php

require_once __DIR__ . '/connection.php';

function guardarResultadosBusqueda(string $keyword, string $location, int $limite, array $empresas): int
{
    $pdo = db();
    $pdo->beginTransaction();

    try {
        $busquedaId = insertarBusqueda($pdo, $keyword, $location, $limite, count($empresas));

        foreach ($empresas as $empresa) {
            $empresaId = upsertEmpresa($pdo, $empresa);
            vincularBusquedaEmpresa($pdo, $busquedaId, $empresaId);
        }

        $pdo->commit();

        return $busquedaId;
    } catch (Throwable $exception) {
        $pdo->rollBack();
        throw $exception;
    }
}

function guardarEmailsEmpresa(string $placeId, array $emailsFiltrados, array $todosEmails): int
{
    $pdo = db();
    $empresaId = buscarEmpresaIdPorPlaceId($pdo, $placeId);

    if ($empresaId === null) {
        throw new RuntimeException('La empresa no está guardada. Ejecuta una búsqueda primero.');
    }

    $emails = array_values(array_unique(array_filter($todosEmails)));

    if ($emails === []) {
        return 0;
    }

    $pdo->beginTransaction();

    try {
        $guardados = insertarEmailsEmpresa($pdo, $empresaId, $emails, $emailsFiltrados);
        $pdo->commit();

        return $guardados;
    } catch (Throwable $exception) {
        $pdo->rollBack();
        throw $exception;
    }
}

function insertarBusqueda(PDO $pdo, string $keyword, string $location, int $limite, int $total): int
{
    $stmt = $pdo->prepare(
        'INSERT INTO busquedas (keyword, location, limite, total_encontradas)
         VALUES (?, ?, ?, ?)'
    );
    $stmt->execute([$keyword, $location, $limite, $total]);

    return (int) $pdo->lastInsertId();
}

function upsertEmpresa(PDO $pdo, array $empresa): int
{
    $stmt = $pdo->prepare(
        'INSERT INTO empresas (place_id, nombre, website, telefono, direccion, rating)
         VALUES (?, ?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE
            nombre = VALUES(nombre),
            website = VALUES(website),
            telefono = VALUES(telefono),
            direccion = VALUES(direccion),
            rating = VALUES(rating),
            id = LAST_INSERT_ID(id)'
    );
    $stmt->execute([
        $empresa['place_id'],
        $empresa['nombre'] ?? 'N/A',
        valorOpcional($empresa['website'] ?? null),
        valorOpcional($empresa['telefono'] ?? null),
        valorOpcional($empresa['direccion'] ?? null),
        parseRating($empresa['rating'] ?? null),
    ]);

    return (int) $pdo->lastInsertId();
}

function vincularBusquedaEmpresa(PDO $pdo, int $busquedaId, int $empresaId): void
{
    $stmt = $pdo->prepare(
        'INSERT IGNORE INTO busqueda_empresa (busqueda_id, empresa_id) VALUES (?, ?)'
    );
    $stmt->execute([$busquedaId, $empresaId]);
}

function buscarEmpresaIdPorPlaceId(PDO $pdo, string $placeId): ?int
{
    $stmt = $pdo->prepare('SELECT id FROM empresas WHERE place_id = ?');
    $stmt->execute([$placeId]);
    $id = $stmt->fetchColumn();

    return $id === false ? null : (int) $id;
}

function insertarEmailsEmpresa(PDO $pdo, int $empresaId, array $emails, array $emailsFiltrados): int
{
    $stmt = $pdo->prepare(
        'INSERT INTO empresa_emails (empresa_id, email, es_filtrado)
         VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE es_filtrado = VALUES(es_filtrado)'
    );
    $filtrados = array_flip($emailsFiltrados);

    foreach ($emails as $email) {
        $stmt->execute([
            $empresaId,
            $email,
            isset($filtrados[$email]) ? 1 : 0,
        ]);
    }

    return count($emails);
}

function valorOpcional($value): ?string
{
    $value = trim((string) $value);

    if ($value === '' || strcasecmp($value, 'N/A') === 0) {
        return null;
    }

    return $value;
}

function parseRating($rating): ?float
{
    if ($rating === null || $rating === '' || $rating === 'N/A') {
        return null;
    }

    if (is_numeric($rating)) {
        return round((float) $rating, 1);
    }

    if (preg_match('/(\d+(?:\.\d+)?)/', (string) $rating, $matches)) {
        return round((float) $matches[1], 1);
    }

    return null;
}
