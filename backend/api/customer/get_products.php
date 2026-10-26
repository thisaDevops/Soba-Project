<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../config/db_connection.php';

$categoryId = isset($_GET['cat_id']) ? intval($_GET['cat_id']) : null;
$categorySlug = isset($_GET['category']) ? trim($_GET['category']) : null;
$search = isset($_GET['search']) ? trim($_GET['search']) : null;
$productId = isset($_GET['id']) ? intval($_GET['id']) : null;

$sql = "
    SELECT 
        p.p_id as id,
        p.name,
        p.sku,
        p.material,
        p.price,
        p.original_price,
        p.stock_qty,
        p.plastic_saved_g,
        p.cert_badge as cert,
        p.icon,
        p.description,
        p.rating,
        p.reviews_count,
        c.cat_id,
        c.name as category_name,
        c.slug as category
    FROM products p
    LEFT JOIN categories c ON p.cat_id = c.cat_id
    WHERE p.is_active = 1
";

$params = [];
$types = "";

if ($productId) {
    $sql .= " AND p.p_id = ?";
    $params[] = $productId;
    $types .= "i";
}

if ($categorySlug && $categorySlug !== 'all') {
    $sql .= " AND c.slug = ?";
    $params[] = $categorySlug;
    $types .= "s";
}

if ($search) {
    $sql .= " AND (p.name LIKE ? OR p.material LIKE ? OR p.description LIKE ?)";
    $like = "%" . $search . "%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= "sss";
}

$sql .= " ORDER BY p.p_id ASC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    $row['id'] = (int)$row['id'];
    $row['price'] = (float)$row['price'];
    $row['original_price'] = $row['original_price'] !== null ? (float)$row['original_price'] : null;
    $row['originalPrice'] = $row['original_price'];
    $row['stock_qty'] = (int)$row['stock_qty'];
    $row['plastic_saved_g'] = (int)$row['plastic_saved_g'];
    $row['saved'] = $row['plastic_saved_g'] . 'g Plastic';
    $row['rating'] = (float)$row['rating'];
    $row['reviews_count'] = (int)$row['reviews_count'];

    // Fetch images
    $imgStmt = $conn->prepare("SELECT url FROM product_images WHERE p_id = ? ORDER BY sort_order ASC");
    $imgStmt->bind_param("i", $row['id']);
    $imgStmt->execute();
    $imgRes = $imgStmt->get_result();
    $images = [];
    while ($img = $imgRes->fetch_assoc()) {
        $images[] = $img['url'];
    }
    $row['images'] = $images;

    $products[] = $row;
}

if ($productId && count($products) === 1) {
    echo json_encode(['success' => true, 'product' => $products[0]]);
} else {
    echo json_encode(['success' => true, 'count' => count($products), 'products' => $products]);
}
