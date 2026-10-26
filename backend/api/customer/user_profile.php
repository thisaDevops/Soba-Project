<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../config/db_connection.php';
require_once __DIR__ . '/../../includes/session_handler.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userId = isset($_GET['u_id']) ? intval($_GET['u_id']) : null;
$userEmail = isset($_GET['email']) ? trim($_GET['email']) : null;

// Resolve user from session if not explicitly provided
if (!$userId && !$userEmail && !empty($_SESSION['u_id'])) {
    $userId = (int)$_SESSION['u_id'];
}
if (!$userId && !$userEmail && !empty($_SESSION['email'])) {
    $userEmail = trim($_SESSION['email']);
}

// If no user is identified, reject unauthenticated access
if (!$userId && !$userEmail) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not authenticated. Please log in.']);
    exit;
}

$user = null;
if ($userId) {
    $uStmt = $conn->prepare("
        SELECT 
            u.u_id, u.name, u.email, u.phone, u.role, u.lifetime_grams_saved, 
            el.name as tier_name, el.discount_percent, el.icon as tier_icon
        FROM users u
        LEFT JOIN eco_levels el ON u.el_id = el.el_id
        WHERE u.u_id = ?
        LIMIT 1
    ");
    $uStmt->bind_param("i", $userId);
} else {
    $uStmt = $conn->prepare("
        SELECT 
            u.u_id, u.name, u.email, u.phone, u.role, u.lifetime_grams_saved, 
            el.name as tier_name, el.discount_percent, el.icon as tier_icon
        FROM users u
        LEFT JOIN eco_levels el ON u.el_id = el.el_id
        WHERE u.email = ?
        LIMIT 1
    ");
    $uStmt->bind_param("s", $userEmail);
}
$uStmt->execute();
$userRes = $uStmt->get_result();
if ($uRow = $userRes->fetch_assoc()) {
    $user = $uRow;
}

if (!$user) {
    echo json_encode(['success' => false, 'error' => 'User not found']);
    exit;
}

$uId = (int)$user['u_id'];

// Fetch user addresses
$addrStmt = $conn->prepare("SELECT * FROM addresses WHERE u_id = ? ORDER BY is_default DESC, addr_id DESC LIMIT 1");
$addrStmt->bind_param("i", $uId);
$addrStmt->execute();
$addr = $addrStmt->get_result()->fetch_assoc();

// Fetch orders
$ordStmt = $conn->prepare("
    SELECT 
        o.o_id,
        o.order_number,
        o.status,
        o.payment_method,
        o.courier_tracking,
        o.subtotal,
        o.shipping_fee,
        o.discount_amount,
        o.total,
        o.plastic_saved_g,
        o.created_at,
        o.shipping_address
    FROM orders o
    WHERE o.u_id = ? OR o.customer_email = ?
    ORDER BY o.o_id DESC
");
$ordStmt->bind_param("is", $uId, $user['email']);
$ordStmt->execute();
$ordRes = $ordStmt->get_result();

$orders = [];
while ($ord = $ordRes->fetch_assoc()) {
    $o_id = (int)$ord['o_id'];
    $itemStmt = $conn->prepare("
        SELECT 
            oi.quantity,
            oi.unit_price,
            p.p_id,
            p.name,
            p.icon,
            p.cert_badge,
            (SELECT url FROM product_images WHERE p_id = p.p_id ORDER BY sort_order ASC LIMIT 1) as image_url
        FROM order_items oi
        LEFT JOIN products p ON oi.p_id = p.p_id
        WHERE oi.o_id = ?
    ");
    $itemStmt->bind_param("i", $o_id);
    $itemStmt->execute();
    $items = [];
    $itemRes = $itemStmt->get_result();
    while ($it = $itemRes->fetch_assoc()) {
        $items[] = $it;
    }
    $ord['items'] = $items;
    $orders[] = $ord;
}

echo json_encode([
    'success' => true,
    'user' => [
        'id' => $uId,
        'name' => $user['name'],
        'email' => $user['email'],
        'phone' => $user['phone'] ?? '+94 77 123 4567',
        'tier' => $user['tier_name'] ?? 'Seedling Saver',
        'tier_icon' => $user['tier_icon'] ?? '🌱',
        'lifetime_grams_saved' => (int)($user['lifetime_grams_saved'] ?? 0),
        'eco_points' => (int)($user['lifetime_grams_saved'] ?? 0) * 5,
        'address' => $addr ? $addr['street'] : 'No. 45, Temple Road, Colombo 03',
        'city' => $addr ? $addr['city'] : 'Colombo',
        'postal' => $addr ? $addr['postal_code'] : '00300'
    ],
    'orders' => $orders
]);
