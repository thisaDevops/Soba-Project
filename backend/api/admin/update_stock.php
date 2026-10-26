<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../config/db_connection.php';

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    $data = $_POST;
}

$id = isset($data['id']) ? intval($data['id']) : null;
$addQty = isset($data['add_qty']) ? intval($data['add_qty']) : 0;

if (!$id || $addQty <= 0) {
    echo json_encode(['success' => false, 'error' => 'Product ID and valid restock quantity required.']);
    exit;
}

$stmt = $conn->prepare("UPDATE products SET stock_qty = stock_qty + ? WHERE p_id = ?");
$stmt->bind_param("ii", $addQty, $id);
$success = $stmt->execute();

$getStmt = $conn->prepare("SELECT stock_qty FROM products WHERE p_id = ?");
$getStmt->bind_param("i", $id);
$getStmt->execute();
$newStock = $getStmt->get_result()->fetch_assoc()['stock_qty'];

echo json_encode(['success' => $success, 'p_id' => $id, 'new_stock' => (int)$newStock]);
