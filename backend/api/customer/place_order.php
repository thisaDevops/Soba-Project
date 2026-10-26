<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../config/db_connection.php';

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) $data = $_POST;

$orderNumber = isset($data['order_number']) ? trim($data['order_number']) : 'SOBA-' . rand(10000, 99999);
$customerName = isset($data['customer_name']) ? trim($data['customer_name']) : 'Customer';
$customerPhone = isset($data['customer_phone']) ? trim($data['customer_phone']) : '';
$customerEmail = isset($data['customer_email']) ? trim($data['customer_email']) : '';
$shippingAddress = isset($data['shipping_address']) ? trim($data['shipping_address']) : '';
$paymentMethod = isset($data['payment_method']) ? trim($data['payment_method']) : 'Cash on Delivery';
$subtotal = isset($data['subtotal']) ? floatval($data['subtotal']) : 0.00;
$shippingFee = isset($data['shipping_fee']) ? floatval($data['shipping_fee']) : 350.00;
$discountAmount = isset($data['discount_amount']) ? floatval($data['discount_amount']) : 0.00;
$total = isset($data['total']) ? floatval($data['total']) : ($subtotal + $shippingFee - $discountAmount);
$items = isset($data['items']) && is_array($data['items']) ? $data['items'] : [];

// Find user if exists
$uId = 3; // default customer Thisaru
if (!empty($customerEmail)) {
    $uStmt = $conn->prepare("SELECT u_id FROM users WHERE email = ? LIMIT 1");
    $uStmt->bind_param("s", $customerEmail);
    $uStmt->execute();
    $uRes = $uStmt->get_result();
    if ($row = $uRes->fetch_assoc()) {
        $uId = (int)$row['u_id'];
    }
}

$status = ($paymentMethod === 'Cash on Delivery') ? 'Pending Dispatch' : 'Processing / Paid';
$courierTracking = 'EV-' . rand(10000, 99999);
$plasticSavedG = 100; // base calculation

$stmt = $conn->prepare("
    INSERT INTO orders 
    (u_id, customer_name, customer_phone, customer_email, shipping_address, order_number, status, payment_method, courier_tracking, subtotal, shipping_fee, discount_amount, total, plastic_saved_g, created_at)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
");
$stmt->bind_param(
    "issssssssddddi",
    $uId,
    $customerName,
    $customerPhone,
    $customerEmail,
    $shippingAddress,
    $orderNumber,
    $status,
    $paymentMethod,
    $courierTracking,
    $subtotal,
    $shippingFee,
    $discountAmount,
    $total,
    $plasticSavedG
);

$success = $stmt->execute();
$orderId = $conn->insert_id;

if ($success && !empty($items)) {
    $itemStmt = $conn->prepare("INSERT INTO order_items (o_id, p_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
    foreach ($items as $item) {
        $pId = isset($item['id']) ? intval($item['id']) : (isset($item['p_id']) ? intval($item['p_id']) : 1);
        $qty = isset($item['quantity']) ? intval($item['quantity']) : 1;
        $unitPrice = isset($item['price']) ? floatval($item['price']) : 0.00;

        $itemStmt->bind_param("iiid", $orderId, $pId, $qty, $unitPrice);
        $itemStmt->execute();

        // Decrement product stock
        $stkStmt = $conn->prepare("UPDATE products SET stock_qty = GREATEST(0, stock_qty - ?) WHERE p_id = ?");
        $stkStmt->bind_param("ii", $qty, $pId);
        $stkStmt->execute();
    }
}

echo json_encode([
    'success' => $success,
    'order_id' => $orderId,
    'order_number' => $orderNumber,
    'tracking' => $courierTracking,
    'status' => $status
]);
