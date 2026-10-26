<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../config/db_connection.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'GET') {
    $sql = "
        SELECT 
            o.o_id,
            o.order_number,
            o.customer_name,
            o.customer_phone,
            o.customer_email,
            o.shipping_address,
            o.status,
            o.payment_method,
            o.courier_tracking,
            o.subtotal,
            o.shipping_fee,
            o.discount_amount,
            o.total,
            o.plastic_saved_g,
            o.created_at,
            GROUP_CONCAT(CONCAT(p.name, ' (x', oi.quantity, ')') SEPARATOR ', ') as items_summary
        FROM orders o
        LEFT JOIN order_items oi ON o.o_id = oi.o_id
        LEFT JOIN products p ON oi.p_id = p.p_id
        GROUP BY o.o_id
        ORDER BY o.o_id DESC
    ";

    $res = $conn->query($sql);
    $orders = [];
    while ($row = $res->fetch_assoc()) {
        $orders[] = $row;
    }

    echo json_encode(['success' => true, 'count' => count($orders), 'orders' => $orders]);
    exit;
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) $data = $_POST;

    $orderId = isset($data['order_id']) ? intval($data['order_id']) : null;
    $orderNumber = isset($data['order_number']) ? trim($data['order_number']) : null;
    $newStatus = isset($data['status']) ? trim($data['status']) : null;

    if ((!$orderId && !$orderNumber) || !$newStatus) {
        echo json_encode(['success' => false, 'error' => 'Order identifier and new status required.']);
        exit;
    }

    if ($orderId) {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE o_id = ?");
        $stmt->bind_param("si", $newStatus, $orderId);
    } else {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_number = ?");
        $stmt->bind_param("ss", $newStatus, $orderNumber);
    }

    $success = $stmt->execute();
    echo json_encode(['success' => $success, 'status' => $newStatus]);
    exit;
}
