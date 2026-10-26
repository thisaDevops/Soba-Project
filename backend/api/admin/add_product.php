<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../config/db_connection.php';

// Accept both multipart/form-data ($_POST, $_FILES) and JSON payload
$data = [];
$rawInput = file_get_contents('php://input');
if (!empty($rawInput)) {
    $decoded = json_decode($rawInput, true);
    if (is_array($decoded)) {
        $data = $decoded;
    }
}
if (empty($data)) {
    $data = $_POST;
}

$id = isset($data['id']) && $data['id'] !== '' ? intval($data['id']) : null;
$name = isset($data['name']) ? trim($data['name']) : '';
$sku = isset($data['sku']) ? trim($data['sku']) : '';
$categoryName = isset($data['category']) ? trim($data['category']) : '';
$price = isset($data['price']) ? floatval($data['price']) : 0.00;
$stock = isset($data['stock']) ? intval($data['stock']) : 0;
$ecoBadge = isset($data['eco']) ? trim($data['eco']) : '';
$icon = isset($data['icon']) ? trim($data['icon']) : '🌱';
$material = isset($data['material']) ? trim($data['material']) : '100% Eco-Friendly Materials';
$description = isset($data['description']) ? trim($data['description']) : 'Zero-waste certified sustainably made product.';

if (empty($name) || $price <= 0) {
    echo json_encode(['success' => false, 'error' => 'Product name and valid price are required.']);
    exit;
}

try {
    // Map or lookup category
    $catStmt = $conn->prepare("SELECT cat_id FROM categories WHERE name = ? OR slug = ? LIMIT 1");
$catStmt->bind_param("ss", $categoryName, $categoryName);
$catStmt->execute();
$catRes = $catStmt->get_result();
$cat_id = 1; // default
if ($cRow = $catRes->fetch_assoc()) {
    $cat_id = (int)$cRow['cat_id'];
} else {
    // Insert new category
    $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $categoryName));
    $inCat = $conn->prepare("INSERT INTO categories (name, slug, icon) VALUES (?, ?, ?)");
    $inCat->bind_param("sss", $categoryName, $slug, $icon);
    $inCat->execute();
    $cat_id = $conn->insert_id;
}

$adminUid = 8; // default admin

if ($id) {
    // Update existing product
    $stmt = $conn->prepare("UPDATE products SET cat_id = ?, name = ?, sku = ?, material = ?, price = ?, stock_qty = ?, cert_badge = ?, icon = ?, description = ? WHERE p_id = ?");
    $stmt->bind_param("isssdisssi", $cat_id, $name, $sku, $material, $price, $stock, $ecoBadge, $icon, $description, $id);
    $success = $stmt->execute();
    $targetProductId = $id;
} else {
    // Insert new product
    if (empty($sku)) {
        $sku = 'ECO-' . strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $name), 0, 3)) . '-' . rand(1000, 9999);
    }
    $plasticSaved = 50;
    $originalPrice = round($price * 1.35, 2);
    $rating = 5.0;
    $reviewsCount = 1;

    $stmt = $conn->prepare("INSERT INTO products (cat_id, u_id, name, sku, material, price, original_price, stock_qty, plastic_saved_g, cert_badge, icon, description, is_active, rating, reviews_count) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?, ?)");
    $stmt->bind_param("iisssddiisssdi", $cat_id, $adminUid, $name, $sku, $material, $price, $originalPrice, $stock, $plasticSaved, $ecoBadge, $icon, $description, $rating, $reviewsCount);
    $success = $stmt->execute();
    $targetProductId = $conn->insert_id;
}

if (!$success || !$targetProductId) {
    echo json_encode(['success' => false, 'error' => 'Database product save failed: ' . $conn->error]);
    exit;
}

// Handle Image URLs and file uploads
$newImageUrls = [];

// 1. Process image URLs submitted (URLs kept by admin or manually added)
if (isset($data['image_urls'])) {
    if (is_array($data['image_urls'])) {
        foreach ($data['image_urls'] as $url) {
            $trimUrl = trim($url);
            if (!empty($trimUrl)) {
                $newImageUrls[] = $trimUrl;
            }
        }
    } elseif (is_string($data['image_urls'])) {
        $urlsArr = explode("\n", str_replace(["\r", ","], "\n", $data['image_urls']));
        foreach ($urlsArr as $u) {
            $trimU = trim($u);
            if (!empty($trimU)) {
                $newImageUrls[] = $trimU;
            }
        }
    }
}

// 2. Process uploaded image files (if any)
if (!empty($_FILES['images'])) {
    $uploadDir = __DIR__ . '/../../../frontend/assets/images/products/';
    if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0777, true);
    }

    $files = $_FILES['images'];
    if (is_array($files['name'])) {
        $fileCount = count($files['name']);
        for ($i = 0; $i < $fileCount; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];
                if (in_array($ext, $allowed)) {
                    $uniqueName = 'prod_' . $targetProductId . '_' . time() . '_' . rand(100, 999) . '.' . $ext;
                    $targetPath = $uploadDir . $uniqueName;
                    if (move_uploaded_file($files['tmp_name'][$i], $targetPath)) {
                        $webUrl = '/php/web/Soba-Project/frontend/assets/images/products/' . $uniqueName;
                        $newImageUrls[] = $webUrl;
                    }
                }
            }
        }
    } else if (!empty($files['name']) && $files['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($files['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];
        if (in_array($ext, $allowed)) {
            $uniqueName = 'prod_' . $targetProductId . '_' . time() . '_' . rand(100, 999) . '.' . $ext;
            $targetPath = $uploadDir . $uniqueName;
            if (move_uploaded_file($files['tmp_name'], $targetPath)) {
                $webUrl = '/php/web/Soba-Project/frontend/assets/images/products/' . $uniqueName;
                $newImageUrls[] = $webUrl;
            }
        }
    }
}

// 3. Save images: When updating, replace images so removed ones stay deleted
$shouldReplace = ($id !== null) || (!empty($data['replace_images']) && ($data['replace_images'] === '1' || $data['replace_images'] === true || $data['replace_images'] === 'true'));

if ($shouldReplace) {
    $delImg = $conn->prepare("DELETE FROM product_images WHERE p_id = ?");
    $delImg->bind_param("i", $targetProductId);
    $delImg->execute();
    $startOrder = 0;
} else {
    // Find current max sort_order
    $orderStmt = $conn->prepare("SELECT COALESCE(MAX(sort_order), -1) as max_order FROM product_images WHERE p_id = ?");
    $orderStmt->bind_param("i", $targetProductId);
    $orderStmt->execute();
    $oRes = $orderStmt->get_result();
    $maxO = ($oRow = $oRes->fetch_assoc()) ? (int)$oRow['max_order'] : -1;
    $startOrder = $maxO + 1;
}

if (!empty($newImageUrls)) {
    $insImg = $conn->prepare("INSERT INTO product_images (p_id, url, sort_order) VALUES (?, ?, ?)");
    foreach ($newImageUrls as $idx => $imgUrl) {
        $order = $startOrder + $idx;
        $insImg->bind_param("isi", $targetProductId, $imgUrl, $order);
        $insImg->execute();
    }
}

// Return updated product images
$allImages = [];
$fetchImgs = $conn->prepare("SELECT url FROM product_images WHERE p_id = ? ORDER BY sort_order ASC");
$fetchImgs->bind_param("i", $targetProductId);
$fetchImgs->execute();
$imgsRes = $fetchImgs->get_result();
while ($r = $imgsRes->fetch_assoc()) {
    $allImages[] = $r['url'];
}

    echo json_encode([
        'success' => true,
        'p_id' => $targetProductId,
        'sku' => $sku,
        'images' => $allImages,
        'action' => $id ? 'updated' : 'created'
    ]);
} catch (Throwable $t) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $t->getMessage()
    ]);
}

