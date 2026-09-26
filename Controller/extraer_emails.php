<?php

require_once dirname(__DIR__) . '/config/persist.php';

use Peterujah\NanoBlock\EmailCrawl;

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$url = $data['url'] ?? null;
$empresa = $data['empresa'] ?? 'Desconocida';
$placeId = $data['place_id'] ?? null;

if (!$url) {
    echo json_encode(['success' => false, 'error' => 'URL no proporcionada']);
    exit;
}

try {
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        throw new Exception('URL inválida');
    }

    $craw = new EmailCrawl($url, 20);
    $response = $craw->craw()->getResponse();
    $todosEmails = $response->asArray();

    $excludedPatterns = [
        '/^info@/i',
        '/^ventas@/i',
        '/^soporte@/i',
        '/^admin@/i',
        '/^webmaster@/i',
        '/^noreply@/i',
        '/^no-reply@/i'
    ];

    $filteredEmails = array_values(array_filter($todosEmails, function ($email) use ($excludedPatterns) {
        foreach ($excludedPatterns as $pattern) {
            if (preg_match($pattern, $email)) {
                return false;
            }
        }
        return true;
    }));

    $emailsGuardados = 0;
    $saveError = null;

    if ($placeId) {
        try {
            $emailsGuardados = guardarEmailsEmpresa($placeId, $filteredEmails, $todosEmails);
        } catch (Throwable $exception) {
            $saveError = $exception->getMessage();
        }
    } else {
        $saveError = 'Falta place_id para guardar los emails.';
    }

    echo json_encode([
        'success' => true,
        'emails' => $filteredEmails,
        'todos_emails' => $todosEmails,
        'empresa' => $empresa,
        'url' => $url,
        'saved' => $saveError === null,
        'emails_guardados' => $emailsGuardados,
        'save_error' => $saveError,
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'empresa' => $empresa,
        'url' => $url
    ]);
}
