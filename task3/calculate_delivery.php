<?php
require_once 'includes/config.php';
require_once 'includes/delivery_service.php';

header('Content-Type: application/json');

if (!isset($_GET['city']) || !isset($_GET['weight'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Не указаны обязательные параметры',
        'price' => 0
    ]);
    exit;
}

$city = $_GET['city'];
$weight = (int)$_GET['weight'];

$result = calculateDelivery($city, $weight);
echo json_encode($result);
?>