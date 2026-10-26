<?php
header('Content-Type: application/json');

$merchant_id = "1238336"; 
$merchant_secret = "Mzc2MTU0NDgzNzY2ODA2NzEzNDE2MTkxNDExMzUxMDg0OTQwOTg0"; 

$order_id = isset($_POST['order_id']) ? trim($_POST['order_id']) : '';
$amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
$currency = isset($_POST['currency']) ? trim($_POST['currency']) : 'LKR';

if (empty($order_id) || $amount <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid order_id or amount']);
    exit;
}

$amount_formatted = number_format((float)$amount, 2, '.', '');

$hash = strtoupper(
    md5(
        $merchant_id . $order_id . 
        $amount_formatted . $currency . 
        strtoupper(md5($merchant_secret))
    )
);

echo json_encode([
    'status' => 'success',
    'merchant_id' => $merchant_id,
    'order_id' => $order_id,
    'amount' => $amount_formatted,
    'currency' => $currency,
    'hash' => $hash
]);
