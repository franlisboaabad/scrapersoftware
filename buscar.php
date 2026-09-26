<?php
// buscar.php - Llamada a Google Places API
require_once __DIR__ . '/config/persist.php';

header('Content-Type: application/json');

// ============================================
// CONFIGURACIÓN - ¡PON AQUÍ TU API KEY!
// ============================================
$API_KEY = 'AIzaSyBRgLt2e4V8kOvfMIzC_7QlwVosEqOTxQ0'; // <-- REEMPLAZA ESTO

// Recibir datos del frontend
$data = json_decode(file_get_contents('php://input'), true);
$keyword = $data['keyword'] ?? 'agencias de viajes';
$location = $data['location'] ?? 'Piura, Perú';
$limit = min(intval($data['limit'] ?? 20), 60); // Máximo 60 por API

try {
    // Construir la consulta
    $query = $keyword . ' en ' . $location;

    // Llamar a Google Places API (Text Search)
    $url = "https://maps.googleapis.com/maps/api/place/textsearch/json";
    $params = [
        'query' => $query,
        'key' => $API_KEY,
        'language' => 'es'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        throw new Exception("Error en API de Google: HTTP $httpCode");
    }

    $result = json_decode($response, true);

    if ($result['status'] !== 'OK') {
        throw new Exception("Error de Google Places: " . $result['status']);
    }

    $empresas = [];
    $count = 0;

    foreach ($result['results'] as $place) {
        if ($count >= $limit) break;

        // Obtener detalles adicionales del lugar (website, teléfono)
        $placeId = $place['place_id'];
        $detailsUrl = "https://maps.googleapis.com/maps/api/place/details/json";
        $detailsParams = [
            'place_id' => $placeId,
            'fields' => 'name,website,formatted_phone_number,formatted_address,rating,user_ratings_total',
            'key' => $API_KEY
        ];

        $ch2 = curl_init();
        curl_setopt($ch2, CURLOPT_URL, $detailsUrl . '?' . http_build_query($detailsParams));
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);

        $detailsResponse = curl_exec($ch2);
        curl_close($ch2);

        $details = json_decode($detailsResponse, true);
        $placeDetails = $details['result'] ?? [];

        $empresas[] = [
            'nombre' => $place['name'] ?? 'N/A',
            'website' => $placeDetails['website'] ?? null,
            'telefono' => $placeDetails['formatted_phone_number'] ?? 'N/A',
            'direccion' => $placeDetails['formatted_address'] ?? $place['formatted_address'] ?? 'N/A',
            'rating' => ($placeDetails['rating'] ?? $place['rating'] ?? 'N/A') . ' ★',
            'place_id' => $placeId,
            'emails' => [],
            'todos_emails' => []
        ];

        $count++;
    }

    $busquedaId = null;
    $saveError = null;

    try {
        $busquedaId = guardarResultadosBusqueda($keyword, $location, $limit, $empresas);
    } catch (Throwable $exception) {
        $saveError = $exception->getMessage();
    }

    echo json_encode([
        'success' => true,
        'empresas' => $empresas,
        'total' => count($empresas),
        'busqueda_id' => $busquedaId,
        'saved' => $busquedaId !== null,
        'save_error' => $saveError,
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
