<?php
require_once 'config.php';

function calculateDelivery($city, $weight) {
    $url = DELIVERY_SERVICE_URL . '?' . http_build_query([
            'city' => $city,
            'weight' => (int)$weight
        ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 && $response) {
        return json_decode($response, true);
    }

    return [
        'status' => 'error',
        'message' => 'Ошибка соединения с сервисом расчета доставки',
        'price' => 0
    ];
}
?>