<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../config/db_connection.php';

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    $data = $_POST;
}

$id = isset($data['id']) ? intval($data['id']) : (isset($_GET['id']) ? intval($_GET['id']) : null);

if (!$id) {
    echo json_encode(['success' => false, 'error' => 'Product ID is required']);
    exit;
}

// 1. Delete associated images from product_images
$delImg = $conn->prepare("DELETE FROM product_images WHERE p_id = ?");
$delImg->bind_param("i", $id);
$delImg->execute();

// 2. Delete associated cart items from cart_items
$delCart = $conn->prepare("DELETE FROM cart_items WHERE p_id = ?");
$delCart->bind_param("i", $id);
$delCart->execute();

// 3. Delete associated reviews from reviews
$delRev = $conn->prepare("DELETE FROM reviews WHERE p_id = ?");
$delRev->bind_param("i", $id);
$delRev->execute();

// 4. Delete associated order items if any
$delOi = $conn->prepare("DELETE FROM order_items WHERE p_id = ?");
$delOi->bind_param("i", $id);
$delOi->execute();

// 5. Completely delete product from products table
$stmt = $conn->prepare("DELETE FROM products WHERE p_id = ?");
$stmt->bind_param("i", $id);
$success = $stmt->execute();

if ($success) {
    echo json_encode([
        'success' => true,
        'p_id' => $id,
        'message' => 'Product permanently deleted from database'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => $conn->error ?: 'Failed to delete product from database'
    ]);
}
