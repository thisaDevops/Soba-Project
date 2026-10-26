<?php

ini_set('display_errors', '0');
error_reporting(E_ALL);

$DB_HOST = '127.0.0.1';
$DB_PORT = 3307;
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'eco_marketplace';  

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);
    $conn->set_charset('utf8mb4');
} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $e->getMessage(),
    ]);
    exit;
}