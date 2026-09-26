<?php
// extraer_emails.php - Extrae emails de un sitio web usando EmailCrawl
require_once 'vendor/autoload.php';

use Peterujah\NanoBlock\EmailCrawl;

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$url = $data['url'] ?? null;
$empresa = $data['empresa'] ?? 'Desconocida';

if (!$url) {
    echo json_encode(['success' => false, 'error' => 'URL no proporcionada']);
    exit;
}

try {
    // Validar URL
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        throw new Exception('URL inválida');
    }

    // Extraer emails con EmailCrawl
    $craw = new EmailCrawl($url, 20); // Profundidad 20 páginas
    $response = $craw->craw()->getResponse();
    $todosEmails = $response->asArray();

    // Filtrar emails (excluir correos genéricos si deseas)
    $excludedPatterns = [
        '/^info@/i',
        '/^ventas@/i',
        '/^soporte@/i',
        '/^admin@/i',
        '/^webmaster@/i',
        '/^noreply@/i',
        '/^no-reply@/i'
    ];

    $filteredEmails = array_filter($todosEmails, function ($email) use ($excludedPatterns) {
        foreach ($excludedPatterns as $pattern) {
            if (preg_match($pattern, $email)) {
                return false;
            }
        }
        return true;
    });

    echo json_encode([
        'success' => true,
        'emails' => array_values($filteredEmails),
        'todos_emails' => $todosEmails,
        'empresa' => $empresa,
        'url' => $url
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'empresa' => $empresa,
        'url' => $url
    ]);
}
