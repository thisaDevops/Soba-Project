<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../config/db_connection.php';

try {
    // 1. Order & Revenue Stats
    $revRes = $conn->query("
        SELECT 
            COALESCE(SUM(total), 0) as gross_rev, 
            COUNT(*) as total_orders, 
            COALESCE(SUM(plastic_saved_g), 0) as total_plastic_g 
        FROM orders
    ");
    $revRow = $revRes->fetch_assoc();

    $grossRev = (float)$revRow['gross_rev'];
    $totalOrders = (int)$revRow['total_orders'];
    $totalPlasticG = (int)$revRow['total_plastic_g'];

    // Orders placed today
    $todayRes = $conn->query("
        SELECT COUNT(*) as orders_today 
        FROM orders 
        WHERE DATE(created_at) = CURDATE()
    ");
    $todayRow = $todayRes ? $todayRes->fetch_assoc() : null;
    $ordersToday = $todayRow ? (int)$todayRow['orders_today'] : 0;
    if ($ordersToday === 0 && $totalOrders > 0) {
        // Fallback: active non-delivered orders
        $actRes = $conn->query("SELECT COUNT(*) as act FROM orders WHERE status NOT LIKE '%delivered%'");
        $ordersToday = $actRes ? (int)$actRes->fetch_assoc()['act'] : $totalOrders;
    }

    // 2. Active products, stock quantities, and inventory value
    $prodRes = $conn->query("
        SELECT 
            COUNT(*) as active_skus,
            COALESCE(SUM(stock_qty), 0) as total_units,
            COALESCE(SUM(CASE WHEN stock_qty <= 20 THEN 1 ELSE 0 END), 0) as low_stock,
            COALESCE(SUM(CASE WHEN stock_qty = 0 THEN 1 ELSE 0 END), 0) as out_of_stock,
            COALESCE(SUM(price * stock_qty), 0) as total_inventory_val
        FROM products 
        WHERE is_active = 1
    ");
    $prodRow = $prodRes->fetch_assoc();

    $activeSkus = (int)$prodRow['active_skus'];
    $totalUnits = (int)$prodRow['total_units'];
    $lowStock = (int)$prodRow['low_stock'];
    $outOfStock = (int)$prodRow['out_of_stock'];
    $totalInvVal = (float)$prodRow['total_inventory_val'];

    // 3. Order breakdown by status
    $statusRes = $conn->query("
        SELECT 
            COALESCE(SUM(CASE WHEN status LIKE '%pending%' OR status LIKE '%pack%' THEN 1 ELSE 0 END), 0) as pending_pack,
            COALESCE(SUM(CASE WHEN status LIKE '%ready%' OR status LIKE '%dispatch%' OR status LIKE '%transit%' THEN 1 ELSE 0 END), 0) as in_dispatch,
            COALESCE(SUM(CASE WHEN status LIKE '%delivered%' OR status LIKE '%complete%' THEN 1 ELSE 0 END), 0) as delivered_cnt
        FROM orders
    ");
    $statusRow = $statusRes->fetch_assoc();

    // 4. Zero-Waste Eco Compliance stats
    $compRes = $conn->query("
        SELECT 
            COUNT(*) as total_orders,
            COALESCE(SUM(CASE WHEN plastic_saved_g > 0 THEN 1 ELSE 0 END), 0) as certified_orders
        FROM orders
    ");
    $compRow = $compRes ? $compRes->fetch_assoc() : ['total_orders' => 0, 'certified_orders' => 0];
    $cTotal = (int)$compRow['total_orders'];
    $cCert = (int)$compRow['certified_orders'];
    $compPct = $cTotal > 0 ? round(($cCert / $cTotal) * 100, 1) : 100.0;

    // 5. Weekly Sales Revenue (Current 7-Day Cycle ending Today)
    $startDate = date('Y-m-d', strtotime('-6 days'));
    $endDate = date('Y-m-d');

    $salesRes = $conn->query("
        SELECT 
            DATE(created_at) as order_date,
            COALESCE(SUM(total), 0) as daily_revenue,
            COUNT(*) as order_count
        FROM orders
        WHERE DATE(created_at) >= '{$startDate}' AND DATE(created_at) <= '{$endDate}'
        GROUP BY DATE(created_at)
    ");

    $salesByDate = [];
    if ($salesRes) {
        while ($sRow = $salesRes->fetch_assoc()) {
            $salesByDate[$sRow['order_date']] = [
                'revenue' => (float)$sRow['daily_revenue'],
                'orders' => (int)$sRow['order_count']
            ];
        }
    }

    $maxDailyRev = 0;
    for ($i = 6; $i >= 0; $i--) {
        $curDate = date('Y-m-d', strtotime("-$i days"));
        $rev = isset($salesByDate[$curDate]) ? $salesByDate[$curDate]['revenue'] : 0.0;
        if ($rev > $maxDailyRev) {
            $maxDailyRev = $rev;
        }
    }
    $scaleMax = $maxDailyRev > 0 ? $maxDailyRev : 5000;

    $weeklyDays = [];
    $totalWeeklyRev = 0;

    for ($i = 6; $i >= 0; $i--) {
        $curDate = date('Y-m-d', strtotime("-$i days"));
        $rev = isset($salesByDate[$curDate]) ? $salesByDate[$curDate]['revenue'] : 0.0;
        $cnt = isset($salesByDate[$curDate]) ? $salesByDate[$curDate]['orders'] : 0;
        $totalWeeklyRev += $rev;

        if ($rev > 0) {
            $heightPct = min(100, max(20, round(($rev / $scaleMax) * 92)));
        } else {
            $heightPct = 8;
        }

        if ($rev >= 1000) {
            $valFormatted = round($rev / 1000, 1) . 'k';
        } else if ($rev > 0) {
            $valFormatted = number_format($rev);
        } else {
            $valFormatted = '0';
        }

        $dayName = date('D', strtotime("-$i days"));
        $isToday = ($i === 0);

        $weeklyDays[] = [
            'date' => $curDate,
            'day' => $dayName,
            'display_day' => $isToday ? 'Today' : $dayName,
            'short_date' => date('M d', strtotime("-$i days")),
            'revenue' => $rev,
            'label' => $valFormatted,
            'order_count' => $cnt,
            'height_pct' => $heightPct,
            'is_today' => $isToday
        ];
    }

    $weeklyChart = [
        'period_label' => date('M d', strtotime('-6 days')) . ' – ' . date('M d, Y'),
        'total_weekly_rev' => $totalWeeklyRev,
        'days' => $weeklyDays
    ];

    // 6. Recent 5 orders with items summary
    $recRes = $conn->query("
        SELECT 
            o.o_id,
            o.order_number,
            o.customer_name,
            o.shipping_address,
            o.total,
            o.status,
            COALESCE(GROUP_CONCAT(CONCAT(p.name, ' (x', oi.quantity, ')') SEPARATOR ', '), 'Eco Marketplace Items') as items_summary
        FROM orders o
        LEFT JOIN order_items oi ON o.o_id = oi.o_id
        LEFT JOIN products p ON oi.p_id = p.p_id
        GROUP BY o.o_id
        ORDER BY o.o_id DESC
        LIMIT 5
    ");
    $recentOrders = [];
    if ($recRes) {
        while ($row = $recRes->fetch_assoc()) {
            $recentOrders[] = $row;
        }
    }

    echo json_encode([
        'success' => true,
        'metrics' => [
            'gross_revenue' => $grossRev,
            'total_orders' => $totalOrders,
            'orders_today' => $ordersToday,
            'active_skus' => $activeSkus,
            'total_units' => $totalUnits,
            'low_stock' => $lowStock,
            'out_of_stock' => $outOfStock,
            'total_inventory_val' => $totalInvVal,
            'plastic_saved_kg' => round($totalPlasticG / 1000, 2),
            'pending_packing' => (int)$statusRow['pending_pack'],
            'in_dispatch' => (int)$statusRow['in_dispatch'],
            'delivered' => (int)$statusRow['delivered_cnt']
        ],
        'compliance' => [
            'rate_pct' => $compPct,
            'certified_orders' => $cCert,
            'total_orders' => $cTotal,
            'summary' => "🌱 {$cCert} of {$cTotal} orders certified 100% plastic-free"
        ],
        'weekly_chart' => $weeklyChart,
        'recent_orders' => $recentOrders
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database metrics error: ' . $e->getMessage()
    ]);
}
