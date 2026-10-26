<?php
require_once __DIR__ . '/../config/db_connection.php';

// Disable foreign key checks temporarily during seed
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// 1. Seed eco_levels
$conn->query("TRUNCATE TABLE eco_levels");
$eco_levels = [
    [1, 'Seedling Saver', 0, 0.00, '🌱'],
    [2, 'Canopy Guardian', 500, 5.00, '🌿'],
    [3, 'Earth Champion', 2000, 10.00, '🌍']
];
$stmt = $conn->prepare("INSERT INTO eco_levels (el_id, name, min_grams, discount_percent, icon) VALUES (?, ?, ?, ?, ?)");
foreach ($eco_levels as $lvl) {
    $stmt->bind_param("isids", $lvl[0], $lvl[1], $lvl[2], $lvl[3], $lvl[4]);
    $stmt->execute();
}

// 2. Seed categories
$conn->query("TRUNCATE TABLE categories");
$categories = [
    [1, 'Personal Care', 'personal', '🪥'],
    [2, 'Kitchen & Dining', 'kitchen', '🍱'],
    [3, 'Eco Lifestyle', 'lifestyle', '🍾'],
    [4, 'Eco Stationery', 'stationery', '📓']
];
$stmt = $conn->prepare("INSERT INTO categories (cat_id, name, slug, icon) VALUES (?, ?, ?, ?)");
foreach ($categories as $c) {
    $stmt->bind_param("isss", $c[0], $c[1], $c[2], $c[3]);
    $stmt->execute();
}

// 3. Seed products
$conn->query("TRUNCATE TABLE products");
$conn->query("TRUNCATE TABLE product_images");

$products = [
    [
        'id' => 1,
        'cat_id' => 1,
        'name' => 'Organic Bamboo Toothbrush',
        'sku' => 'ECO-TB-0309',
        'material' => '100% Biodegradable Moso Bamboo',
        'price' => 450.00,
        'original_price' => 650.00,
        'stock_qty' => 85,
        'plastic_saved_g' => 30,
        'cert_badge' => '100% Compostable',
        'icon' => '🪥',
        'description' => 'A durable bamboo toothbrush crafted for a cleaner dental routine without the plastic pollution. The ergonomic handle is shaped from wild, organically grown bamboo and sealed with plant wax for maximum longevity.',
        'rating' => 4.90,
        'reviews_count' => 128,
        'images' => [
            'https://images.unsplash.com/photo-1607613009820-a29f7bb81c04?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1590439471364-192aa70c0b53?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1589365278144-c9e705f843ba?auto=format&fit=crop&w=800&q=80'
        ]
    ],
    [
        'id' => 2,
        'cat_id' => 1,
        'name' => 'Solid Shampoo Bar (Citrus)',
        'sku' => 'ECO-SH-0112',
        'material' => 'Cold-Pressed Plant Oils & Botanicals',
        'price' => 1200.00,
        'original_price' => 1600.00,
        'stock_qty' => 42,
        'plastic_saved_g' => 70,
        'cert_badge' => 'Zero Waste',
        'icon' => '🧼',
        'description' => 'Concentrated citrus botanical shampoo bar equivalent to 3 bottles of liquid shampoo. Free of sulfates, silicones, and synthetic parabens.',
        'rating' => 4.85,
        'reviews_count' => 94,
        'images' => [
            'https://images.unsplash.com/photo-1608248597359-0021b369c0d3?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1607006310452-95f2a1b92e76?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=800&q=80'
        ]
    ],
    [
        'id' => 3,
        'cat_id' => 2,
        'name' => 'Stainless Steel Bento Box',
        'sku' => 'ECO-BX-0994',
        'material' => 'Food-Grade 304 Stainless Steel',
        'price' => 3400.00,
        'original_price' => 4200.00,
        'stock_qty' => 30,
        'plastic_saved_g' => 180,
        'cert_badge' => 'Plastic-Free',
        'icon' => '🍱',
        'description' => 'Double-tier leak-resistant stainless steel bento box for work, travel, and campus. Dishwasher safe, rust-proof, and designed to last a lifetime.',
        'rating' => 4.95,
        'reviews_count' => 67,
        'images' => [
            'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1512058564366-18510be2db19?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1584269600464-37b1b58a9fe7?auto=format&fit=crop&w=800&q=80'
        ]
    ],
    [
        'id' => 4,
        'cat_id' => 2,
        'name' => 'Reusable Beeswax Food Wraps',
        'sku' => 'ECO-BW-0410',
        'material' => 'GOTS Organic Cotton & Local Beeswax',
        'price' => 1800.00,
        'original_price' => 2400.00,
        'stock_qty' => 55,
        'plastic_saved_g' => 90,
        'cert_badge' => 'Organic Cotton',
        'icon' => '🐝',
        'description' => 'Natural alternative to cling wrap. Washable, compostable, and infused with jojoba oil and tree resin for superior freshness preservation.',
        'rating' => 4.80,
        'reviews_count' => 83,
        'images' => [
            'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=800&q=80'
        ]
    ],
    [
        'id' => 5,
        'cat_id' => 3,
        'name' => 'Insulated Glass Water Bottle',
        'sku' => 'ECO-BT-0221',
        'material' => 'Borosilicate Glass & Bamboo Cap',
        'price' => 2900.00,
        'original_price' => 3800.00,
        'stock_qty' => 18,
        'plastic_saved_g' => 250,
        'cert_badge' => 'BPA-Free',
        'icon' => '🍾',
        'description' => 'Thermal shock resistant borosilicate glass with protective silicone sleeve and real bamboo leak-proof twist lid. 750ml capacity.',
        'rating' => 4.90,
        'reviews_count' => 110,
        'images' => [
            'https://images.unsplash.com/photo-1602143407151-7111542de6e8?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1523362628745-0c100150b504?auto=format&fit=crop&w=800&q=80'
        ]
    ],
    [
        'id' => 6,
        'cat_id' => 3,
        'name' => 'Heavy Duty Hemp Grocery Tote',
        'sku' => 'ECO-BAG-0502',
        'material' => '100% Raw Hemp Canvas',
        'price' => 1450.00,
        'original_price' => 1950.00,
        'stock_qty' => 64,
        'plastic_saved_g' => 120,
        'cert_badge' => 'Fair Trade',
        'icon' => '🛍️',
        'description' => 'Unbleached natural hemp fiber tote bag reinforced with cross-stitching. Holds up to 25kg of grocery produce with zero plastic footprint.',
        'rating' => 4.75,
        'reviews_count' => 52,
        'images' => [
            'https://images.unsplash.com/photo-1597484661643-2f5fef640dd1?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=800&q=80'
        ]
    ],
    [
        'id' => 7,
        'cat_id' => 2,
        'name' => 'Natural Loofah Dish Scrubber',
        'sku' => 'ECO-LF-0671',
        'material' => '100% Sun-Dried Loofah Plant',
        'price' => 350.00,
        'original_price' => 500.00,
        'stock_qty' => 120,
        'plastic_saved_g' => 20,
        'cert_badge' => '100% Plant Based',
        'icon' => '🧽',
        'description' => 'Fully home-compostable natural loofah scrubber. Non-scratch on delicate nonstick pans and glassware, completely shedding-free.',
        'rating' => 4.65,
        'reviews_count' => 41,
        'images' => [
            'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1585751119414-ef2636f8aede?auto=format&fit=crop&w=800&q=80'
        ]
    ],
    [
        'id' => 8,
        'cat_id' => 1,
        'name' => 'Refillable Liquid Soap Container',
        'sku' => 'ECO-DS-0889',
        'material' => 'Amber Glass & Recycled Brass Pump',
        'price' => 2200.00,
        'original_price' => 2800.00,
        'stock_qty' => 22,
        'plastic_saved_g' => 80,
        'cert_badge' => 'Zero Waste',
        'icon' => '🧴',
        'description' => 'Aesthetic apothecary-style amber glass soap dispenser with anti-corrosion matte pump. Refill infinitely with bulk castile soaps.',
        'rating' => 4.88,
        'reviews_count' => 39,
        'images' => [
            'https://images.unsplash.com/photo-1585751119414-ef2636f8aede?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1608248597359-0021b369c0d3?auto=format&fit=crop&w=800&q=80'
        ]
    ],
    [
        'id' => 9,
        'cat_id' => 4,
        'name' => 'Handmade Elephant Dung Notebook',
        'sku' => 'ECO-NB-0914',
        'material' => 'Eco Maximus Recycled Fiber',
        'price' => 680.00,
        'original_price' => 950.00,
        'stock_qty' => 48,
        'plastic_saved_g' => 40,
        'cert_badge' => 'Tree-Free',
        'icon' => '📓',
        'description' => 'Odorless, acid-free artisanal textured paper crafted entirely from sterilized elephant dung and recycled cotton paper pulp in Kegalle, Sri Lanka.',
        'rating' => 4.92,
        'reviews_count' => 76,
        'images' => [
            'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=800&q=80'
        ]
    ],
    [
        'id' => 10,
        'cat_id' => 4,
        'name' => 'Plantable Seed Pencils (5pk)',
        'sku' => 'ECO-PN-0723',
        'material' => 'Recycled Newspaper & Non-GMO Herb Seeds',
        'price' => 520.00,
        'original_price' => 750.00,
        'stock_qty' => 95,
        'plastic_saved_g' => 50,
        'cert_badge' => 'Plantable',
        'icon' => '✏️',
        'description' => 'Graphite writing pencils rolled with discarded newspapers with biodegradable seed capsules (Basil, Tomato, Chilli, Marigold) at the tip.',
        'rating' => 4.98,
        'reviews_count' => 115,
        'images' => [
            'https://images.unsplash.com/photo-1585336261026-7f573f055375?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1583485088034-697b5bc54ccd?auto=format&fit=crop&w=800&q=80'
        ]
    ]
];

$prodStmt = $conn->prepare("INSERT INTO products (p_id, cat_id, u_id, name, sku, material, price, original_price, stock_qty, plastic_saved_g, cert_badge, icon, description, is_active, rating, reviews_count) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?, ?)");
$imgStmt = $conn->prepare("INSERT INTO product_images (p_id, url, sort_order) VALUES (?, ?, ?)");

$adminUid = 8;

foreach ($products as $p) {
    $prodStmt->bind_param(
        "iiisssddiisssdi",
        $p['id'],
        $p['cat_id'],
        $adminUid,
        $p['name'],
        $p['sku'],
        $p['material'],
        $p['price'],
        $p['original_price'],
        $p['stock_qty'],
        $p['plastic_saved_g'],
        $p['cert_badge'],
        $p['icon'],
        $p['description'],
        $p['rating'],
        $p['reviews_count']
    );
    $prodStmt->execute();

    $sort = 0;
    foreach ($p['images'] as $url) {
        $imgStmt->bind_param("isi", $p['id'], $url, $sort);
        $imgStmt->execute();
        $sort++;
    }
}

// 4. Seed sample Orders & Order Items
$conn->query("TRUNCATE TABLE orders");
$conn->query("TRUNCATE TABLE order_items");

$orders = [
    [
        'order_number' => 'SOBA-74921',
        'u_id' => 3,
        'name' => 'Thisaru Ranathunga',
        'phone' => '+94 77 123 4567',
        'email' => 'thisaruranathunga06@gmail.com',
        'address' => 'No. 45, Temple Road, Colombo 03',
        'status' => 'In Transit',
        'payment_method' => 'Card / PayHere',
        'courier_tracking' => 'EV-89241',
        'subtotal' => 2050.00,
        'shipping_fee' => 400.00,
        'discount_amount' => 0.00,
        'total' => 2450.00,
        'plastic_saved_g' => 120,
        'items' => [
            ['p_id' => 1, 'qty' => 2, 'price' => 450.00],
            ['p_id' => 2, 'qty' => 1, 'price' => 1200.00]
        ]
    ],
    [
        'order_number' => 'SOBA-68102',
        'u_id' => 3,
        'name' => 'Thisaru Ranathunga',
        'phone' => '+94 77 123 4567',
        'email' => 'thisaruranathunga06@gmail.com',
        'address' => 'No. 45, Temple Road, Colombo 03',
        'status' => 'Delivered',
        'payment_method' => 'Card / PayHere',
        'courier_tracking' => 'EV-71029',
        'subtotal' => 3400.00,
        'shipping_fee' => 400.00,
        'discount_amount' => 200.00,
        'total' => 3600.00,
        'plastic_saved_g' => 180,
        'items' => [
            ['p_id' => 3, 'qty' => 1, 'price' => 3400.00]
        ]
    ],
    [
        'order_number' => 'SOBA-59124',
        'u_id' => 3,
        'name' => 'Anura Kumara',
        'phone' => '+94 71 889 9123',
        'email' => 'anura@example.com',
        'address' => '12 Lotus Lane, Kandy',
        'status' => 'Pending Dispatch',
        'payment_method' => 'Cash on Delivery',
        'courier_tracking' => 'EV-64219',
        'subtotal' => 1450.00,
        'shipping_fee' => 400.00,
        'discount_amount' => 0.00,
        'total' => 1850.00,
        'plastic_saved_g' => 120,
        'items' => [
            ['p_id' => 6, 'qty' => 1, 'price' => 1450.00]
        ]
    ]
];

$ordStmt = $conn->prepare("INSERT INTO orders (u_id, customer_name, customer_phone, customer_email, shipping_address, order_number, status, payment_method, courier_tracking, subtotal, shipping_fee, discount_amount, total, plastic_saved_g, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
$oiStmt = $conn->prepare("INSERT INTO order_items (o_id, p_id, quantity, unit_price) VALUES (?, ?, ?, ?)");

foreach ($orders as $ord) {
    $ordStmt->bind_param(
        "issssssssddddi",
        $ord['u_id'],
        $ord['name'],
        $ord['phone'],
        $ord['email'],
        $ord['address'],
        $ord['order_number'],
        $ord['status'],
        $ord['payment_method'],
        $ord['courier_tracking'],
        $ord['subtotal'],
        $ord['shipping_fee'],
        $ord['discount_amount'],
        $ord['total'],
        $ord['plastic_saved_g']
    );
    $ordStmt->execute();
    $o_id = $conn->insert_id;

    foreach ($ord['items'] as $item) {
        $oiStmt->bind_param("iiid", $o_id, $item['p_id'], $item['qty'], $item['price']);
        $oiStmt->execute();
    }
}

// 5. Update user lifetime grams saved
$conn->query("UPDATE users SET lifetime_grams_saved = 300, el_id = 1 WHERE u_id = 3");

$conn->query("SET FOREIGN_KEY_CHECKS = 1");

echo "DATABASE SEEDED SUCCESSFULLY!\n";
