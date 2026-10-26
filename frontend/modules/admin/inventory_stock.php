<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Portal — Inventory & Stock Management | Soba Marketplace</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..700;1,9..144,400..600&family=Work+Sans:wght@400;500;600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../../assets/css/style.css">
<style>
  :root {
    --neo: #B8FF3D;
    --neo-glow: rgba(184, 255, 61, 0.28);
    --forest: #3B7A34;
    --forest-dim: #2C5D27;
    --deep: #071c15;
    --deep2: #0e3023;
    --ink: #0c1f14;
    --paper: #F9F8F3;
    --sand-deep: #F1F0E9;
    --line: rgba(255, 255, 255, 0.12);
    --line-light: rgba(184, 255, 61, 0.2);
    --clay: #C86D51;
    --glass: rgba(18, 61, 44, 0.65);
    --glass-border: rgba(184, 255, 61, 0.18);
  }

  * { box-sizing: border-box; }
  html { scroll-behavior: smooth; }

  body {
    margin: 0;
    min-height: 100vh;
    font-family: 'Work Sans', sans-serif;
    color: #F4F2E9;
    background: #061912;
    -webkit-font-smoothing: antialiased;
    display: flex;
    flex-direction: column;
    overflow-x: hidden;
  }

  h1, h2, h3, h4 { font-family: 'Fraunces', serif; font-weight: 500; letter-spacing: -0.01em; margin: 0; }
  .mono { font-family: 'IBM Plex Mono', monospace; }

  .icon {
    width: 16px; height: 16px; flex-shrink: 0; stroke: currentColor; fill: none;
    stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round;
  }
  .icon-sm { width: 14px; height: 14px; }
  .icon-lg { width: 20px; height: 20px; }

  /* Background */
  .bg-scene {
    position: fixed; inset: 0; z-index: 0; overflow: hidden; pointer-events: none;
    background:
      radial-gradient(1200px 700px at 15% -8%, rgba(184,255,61,.14), transparent 55%),
      radial-gradient(900px 600px at 85% 85%, rgba(184,255,61,.09), transparent 55%),
      linear-gradient(180deg, #05150f 0%, #0a251b 50%, #061912 100%);
  }
  .bg-noise {
    position: fixed; inset: 0; z-index: 0; pointer-events: none; opacity: .04; mix-blend-mode: overlay;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='140' height='140'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
  }

  /* Admin Header */
  header {
    position: sticky; top: 0; z-index: 40;
    background: rgba(7, 28, 20, 0.92);
    backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
    border-bottom: 1px solid var(--line);
    padding: 12px 28px;
  }
  .header-bar {
    display: flex; align-items: center; justify-content: space-between; gap: 20px;
    max-width: 1600px; margin: 0 auto; width: 100%;
  }
  .logo-admin { display: inline-flex; align-items: baseline; gap: 10px; text-decoration: none; }
  .logo-admin .mark { font-family: 'Fraunces', serif; font-weight: 700; font-size: 26px; color: #F4F8F1; letter-spacing: -0.02em; line-height: 1; }
  .logo-admin .badge {
    background: var(--neo); color: var(--ink); font-size: 10px; font-weight: 800;
    padding: 2px 7px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.08em;
  }

  .header-right { display: flex; align-items: center; gap: 16px; }
  .view-store-btn {
    display: inline-flex; align-items: center; gap: 6px;
    background: rgba(255, 255, 255, 0.06); border: 1px solid var(--line);
    color: rgba(244,242,233,0.85); padding: 7px 14px; border-radius: 10px;
    font-size: 13px; font-weight: 600; text-decoration: none; transition: all 0.2s;
  }
  .view-store-btn:hover { border-color: var(--neo); color: #fff; background: rgba(184,255,61,0.12); }

  .admin-profile-pill {
    display: flex; align-items: center; gap: 10px;
    background: rgba(255,255,255,0.06); border: 1px solid var(--line);
    padding: 4px 12px 4px 6px; border-radius: 99px;
  }
  .admin-avatar {
    width: 28px; height: 28px; border-radius: 50%; background: var(--neo);
    color: var(--ink); font-weight: 800; font-size: 12px; display: flex; align-items: center; justify-content: center;
  }
  .admin-info { display: flex; flex-direction: column; }
  .admin-name { font-size: 12.5px; font-weight: 700; color: #fff; line-height: 1.1; }
  .admin-role { font-size: 10.5px; color: var(--neo); text-transform: uppercase; letter-spacing: 0.04em; }

  /* Frame */
  .admin-shell {
    position: relative; z-index: 10; flex: 1;
    display: grid; grid-template-columns: 260px 1fr;
    max-width: 1600px; width: 100%; margin: 0 auto;
    min-height: calc(100vh - 65px);
  }

  /* Sidebar */
  .admin-sidebar {
    background: rgba(8, 30, 21, 0.85);
    border-right: 1px solid var(--line);
    padding: 24px 16px; display: flex; flex-direction: column; gap: 24px;
    backdrop-filter: blur(16px);
  }
  .nav-group { display: flex; flex-direction: column; gap: 4px; }
  .nav-heading {
    font-size: 11px; text-transform: uppercase; letter-spacing: 0.12em;
    color: rgba(244,242,233,0.4); font-weight: 700; padding: 8px 12px 4px;
  }
  .sidebar-link {
    display: flex; align-items: center; justify-content: space-between;
    padding: 10px 14px; border-radius: 12px; color: rgba(244,242,233,0.78);
    font-size: 13.5px; font-weight: 500; text-decoration: none; transition: all 0.18s ease;
  }
  .sidebar-link:hover { background: rgba(255,255,255,0.06); color: #fff; }
  .sidebar-link.active {
    background: rgba(184, 255, 61, 0.14); color: var(--neo); font-weight: 700;
    border: 1px solid rgba(184, 255, 61, 0.28);
  }
  .sidebar-link-left { display: flex; align-items: center; gap: 10px; }
  .link-badge {
    background: rgba(255,255,255,0.1); color: #fff; border-radius: 99px;
    font-size: 11px; font-weight: 700; padding: 2px 7px;
  }
  .sidebar-link.active .link-badge { background: var(--neo); color: var(--ink); }

  .sidebar-footer { margin-top: auto; padding-top: 18px; border-top: 1px solid var(--line); }
  .logout-btn {
    width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px;
    background: rgba(200, 109, 81, 0.12); border: 1px solid rgba(200, 109, 81, 0.35);
    color: #FFA07A; padding: 10px; border-radius: 10px; font-size: 13px; font-weight: 600;
    cursor: pointer; text-decoration: none; transition: all 0.2s;
  }
  .logout-btn:hover { background: rgba(200, 109, 81, 0.25); color: #fff; }

  /* Content */
  .admin-content {
    padding: 30px 36px 60px;
    display: flex; flex-direction: column; gap: 24px;
  }

  .page-title-bar {
    display: flex; justify-content: space-between; align-items: flex-start; gap: 20px;
  }
  .page-title-bar h1 { font-size: 28px; color: #fff; margin-bottom: 4px; }
  .page-title-bar p { margin: 0; font-size: 13.5px; color: rgba(244,242,233,0.6); }

  /* KPI Grid */
  .kpi-grid {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px;
  }
  .kpi-card {
    background: linear-gradient(160deg, rgba(18,61,44,.65), rgba(8,26,19,.8));
    border: 1px solid var(--glass-border);
    border-radius: 18px; padding: 20px; display: flex; flex-direction: column; gap: 8px;
  }
  .kpi-label { font-size: 12px; color: rgba(244,242,233,0.6); font-weight: 600; text-transform: uppercase; }
  .kpi-val { font-size: 24px; font-weight: 700; color: #fff; }
  .kpi-meta { font-size: 11.5px; }

  /* Low Stock Urgency Banner */
  .low-stock-alert {
    background: rgba(200, 109, 81, 0.15); border: 1px solid rgba(200, 109, 81, 0.38);
    border-radius: 16px; padding: 18px 24px;
    display: flex; align-items: center; justify-content: space-between; gap: 16px;
  }
  .alert-left { display: flex; align-items: center; gap: 14px; }
  .alert-icon { font-size: 26px; }
  .alert-text h4 { font-size: 14.5px; color: #FFA07A; margin-bottom: 2px; font-weight: 700; }
  .alert-text p { margin: 0; font-size: 12.5px; color: rgba(244,242,233,0.75); }

  /* Table Card */
  .table-card {
    background: linear-gradient(160deg, rgba(18,61,44,.6), rgba(8,26,19,.75));
    border: 1px solid var(--glass-border);
    border-radius: 20px; padding: 24px;
    box-shadow: 0 20px 50px rgba(0,0,0,.35);
  }
  .table-top-actions {
    display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; gap: 12px;
  }
  .search-stock {
    display: flex; align-items: center; background: rgba(255,255,255,0.05);
    border: 1px solid var(--line); border-radius: 10px; padding: 7px 14px; gap: 8px; width: 280px;
  }
  .search-stock input {
    background: transparent; border: none; outline: none; color: #fff; font-size: 13px; width: 100%;
  }

  .data-table-wrap { overflow-x: auto; width: 100%; }
  table.admin-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13px; }
  table.admin-table th {
    color: rgba(244,242,233,0.5); font-size: 11.5px; text-transform: uppercase;
    letter-spacing: 0.08em; font-weight: 700; padding: 12px 14px; border-bottom: 1px solid var(--line);
  }
  table.admin-table td {
    padding: 14px; border-bottom: 1px solid rgba(255,255,255,0.06);
    color: rgba(244,242,233,0.85); vertical-align: middle;
  }
  table.admin-table tr:hover td { background: rgba(255,255,255,0.03); color: #fff; }

  .stock-bar-wrap {
    width: 100px; height: 6px; background: rgba(255,255,255,0.1); border-radius: 99px; overflow: hidden; margin-top: 4px;
  }
  .stock-bar-fill { height: 100%; border-radius: 99px; }

  .btn-action-icon {
    background: rgba(255,255,255,0.06); border: 1px solid var(--line);
    color: #fff; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600;
    cursor: pointer; transition: all 0.18s; display: inline-flex; align-items: center; gap: 4px;
  }
  .btn-action-icon:hover { border-color: var(--neo); color: var(--neo); }

  /* Restock Modal */
  .modal-overlay {
    position: fixed; inset: 0; z-index: 100;
    background: rgba(5, 20, 15, 0.85); backdrop-filter: blur(12px);
    display: none; align-items: center; justify-content: center; padding: 20px;
  }
  .modal-overlay.open { display: flex; }
  .modal-card {
    background: linear-gradient(160deg, rgba(18,61,44,.95), rgba(8,26,19,.98));
    border: 1px solid var(--glass-border); border-radius: 20px;
    width: 100%; max-width: 440px; padding: 28px;
    box-shadow: 0 30px 80px rgba(0,0,0,0.6);
  }
  .modal-header {
    display: flex; justify-content: space-between; align-items: center;
    border-bottom: 1px solid var(--line); padding-bottom: 14px; margin-bottom: 18px;
  }
  .modal-header h3 { font-size: 18px; color: #fff; }
  .modal-close-btn { background: none; border: none; color: #fff; font-size: 22px; cursor: pointer; }
  .modal-input {
    width: 100%; background: rgba(255,255,255,0.06); border: 1px solid var(--line);
    border-radius: 10px; padding: 10px 14px; color: #fff; font-size: 14px; outline: none; margin-top: 8px;
  }
  .modal-footer {
    display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;
  }

  /* Toast Notification */
  .toast {
    position: fixed; top: 24px; left: 50%; transform: translateX(-50%) translateY(-20px);
    display: inline-flex; align-items: center; gap: 9px;
    background: rgba(184,255,61,.96); border: 1px solid var(--neo); color: var(--ink);
    font-weight: 700; padding: 12px 24px; border-radius: 99px; font-size: 14px; z-index: 200;
    opacity: 0; pointer-events: none; transition: opacity .3s ease, transform .3s ease;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
  }
  .toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }

  @media (max-width: 1100px) {
    .kpi-grid { grid-template-columns: 1fr 1fr; }
  }
  @media (max-width: 900px) {
    .admin-shell { grid-template-columns: 1fr; width: 100%; max-width: 100vw; overflow-x: hidden; }
    .admin-sidebar { display: none; }
    .admin-content { padding: 14px 12px 60px; width: 100%; max-width: 100vw; overflow-x: hidden; box-sizing: border-box; }
    .header-bar { padding: 0 4px; flex-wrap: wrap; gap: 8px; }
    .kpi-grid { grid-template-columns: 1fr; gap: 12px; }
    .table-card { padding: 12px 8px; border-radius: 14px; width: 100%; box-sizing: border-box; overflow-x: hidden; }
    .table-top-actions { flex-direction: column; align-items: stretch; gap: 10px; }
    .search-stock { width: 100%; box-sizing: border-box; }
    .data-table-wrap { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; display: block; }
    table.admin-table { min-width: 600px; }
  }
</style>
</head>
<body>

<div class="bg-scene"></div>
<div class="bg-noise"></div>

<!-- Toast -->
<div class="toast" id="toast">
  <svg class="icon" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
  <span id="toastMsg">Stock updated successfully</span>
</div>

<!-- Header -->
<header>
  <div class="header-bar">
    <a href="dashboard.php" class="logo-admin">
      <span class="mark">Soba</span>
      <span class="badge">Seller Portal</span>
    </a>

    <div class="header-right">
      <a href="../customer/products.php" target="_blank" class="view-store-btn">
        <svg class="icon icon-sm" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
        <span>View Live Store</span>
      </a>

      <div class="admin-profile-pill">
        <div class="admin-avatar">A</div>
        <div class="admin-info">
          <span class="admin-name">Admin Officer</span>
          <span class="admin-role">Super Admin</span>
        </div>
      </div>
    </div>
  </div>
</header>

<div class="admin-shell">

  <!-- Sidebar -->
  <aside class="admin-sidebar">
    <div class="nav-group">
      <div class="nav-heading">Overview</div>
      <a href="dashboard.php" class="sidebar-link">
        <div class="sidebar-link-left">
          <svg class="icon" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
          <span>Dashboard</span>
        </div>
      </a>
    </div>

    <div class="nav-group">
      <div class="nav-heading">Catalog & Stock</div>
      <a href="products_manage.php" class="sidebar-link">
        <div class="sidebar-link-left">
          <svg class="icon" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-2z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
          <span>Products Manage</span>
        </div>
        <span class="link-badge" id="sideProdBadge">...</span>
      </a>
      <a href="inventory_stock.php" class="sidebar-link active">
        <div class="sidebar-link-left">
          <svg class="icon" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
          <span>Inventory & Stock</span>
        </div>
        <span class="link-badge" style="background:rgba(200,109,81,0.25); color:#FFA07A;" id="sideStockBadge">...</span>
      </a>
    </div>

    <div class="nav-group">
      <div class="nav-heading">Fulfillment</div>
      <a href="order_dispatch.php" class="sidebar-link">
        <div class="sidebar-link-left">
          <svg class="icon" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
          <span>Order Dispatch</span>
        </div>
        <span class="link-badge" style="background:var(--neo); color:var(--ink);" id="sideDispatchBadge">...</span>
      </a>
    </div>

    <div class="sidebar-footer">
      <button type="button" class="logout-btn" onclick="handleAdminLogout()">
        <svg class="icon icon-sm" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        <span>Sign Out Admin</span>
      </button>
    </div>
  </aside>

  <!-- Content -->
  <main class="admin-content">

    <div class="page-title-bar">
      <div>
        <h1>Inventory Control & Safety Stock</h1>
        <p>Monitor real-time warehouse inventory, safety stock alerts, and trigger immediate supplier restock.</p>
      </div>
      <div>
        <button type="button" class="btn-action-icon" style="padding: 10px 18px; border-color:var(--neo); color:var(--neo);" onclick="showToast('Exported Warehouse Inventory Audit (CSV)')">
          📥 Export Inventory Report
        </button>
      </div>
    </div>

    <!-- KPI Grid -->
    <div class="kpi-grid">
      <div class="kpi-card">
        <span class="kpi-label">Total Units in Stock</span>
        <div class="kpi-val mono" id="totalUnitsVal">--</div>
        <div class="kpi-meta" style="color:var(--neo);" id="activeSkusMeta">Across -- Active SKUs</div>
      </div>
      <div class="kpi-card">
        <span class="kpi-label">Low Stock Alerts</span>
        <div class="kpi-val mono" style="color:#FFA07A;" id="lowStockVal">--</div>
        <div class="kpi-meta" style="color:#FFA07A;">Requires Supplier PO</div>
      </div>
      <div class="kpi-card">
        <span class="kpi-label">Out of Stock</span>
        <div class="kpi-val mono" id="outOfStockVal">0 SKUs</div>
        <div class="kpi-meta" style="color:var(--neo);" id="outOfStockRate">0% Stockout Rate</div>
      </div>
      <div class="kpi-card">
        <span class="kpi-label">Total Inventory Value</span>
        <div class="kpi-val mono" id="totalInventoryVal">Rs. --</div>
        <div class="kpi-meta" style="color:rgba(244,242,233,0.6);">Cost basis valuation</div>
      </div>
    </div>

    <!-- Alert Banner -->
    <div class="low-stock-alert" id="lowStockAlertBanner">
      <div class="alert-left">
        <span class="alert-icon">⚠️</span>
        <div class="alert-text">
          <h4 id="alertBannerTitle">Urgent Safety Stock Reorder Needed</h4>
          <p id="alertBannerDesc">Low stock detected below safety threshold. Restock now to prevent checkout stockouts.</p>
        </div>
      </div>
      <button type="button" class="btn-action-icon" style="background:rgba(200,109,81,0.2); border-color:#FFA07A; color:#FFA07A; padding:8px 16px;" id="alertBannerBtn" onclick="showToast('Supplier purchase order draft generated!')">
        Bulk Reorder
      </button>
    </div>

    <!-- Inventory Table -->
    <div class="table-card">
      <div class="table-top-actions">
        <div class="search-stock">
          <svg class="icon" viewBox="0 0 24 24" style="color:rgba(255,255,255,0.4);"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input type="text" id="stockSearch" placeholder="Search SKU, Product or Bin..." oninput="searchStockTable()">
        </div>
        <div style="font-size:12.5px; color:rgba(244,242,233,0.6);">
          Warehouse Location: <strong style="color:#fff;">Colombo Central Distribution (Bin A-D)</strong>
        </div>
      </div>

      <div class="data-table-wrap">
        <table class="admin-table" id="stockTable">
          <thead>
            <tr>
              <th>Product & SKU</th>
              <th>Bin Location</th>
              <th>Current Stock</th>
              <th>Re-order Threshold</th>
              <th>Status</th>
              <th>Supplier Lead Time</th>
              <th>Restock Action</th>
            </tr>
          </thead>
          <tbody id="stockBody">
            <tr>
              <td>
                <strong style="color:#fff;">Neem & Turmeric Castile Soap</strong>
                <div class="mono" style="font-size:11.5px; color:rgba(244,242,233,0.5);">SKU: ECO-SOP-0211</div>
              </td>
              <td class="mono">Shelf B-04</td>
              <td>
                <span class="current-stock-val mono" style="color:#FFA07A; font-weight:700;">4 units</span>
                <div class="stock-bar-wrap">
                  <div class="stock-bar-fill" style="width: 20%; background: #FFA07A;"></div>
                </div>
              </td>
              <td class="mono">15 units</td>
              <td><span style="color:#FFA07A; font-weight:700; font-size:11.5px;">⚠️ Critical Low</span></td>
              <td>2 Days · BioHerbs Lanka</td>
              <td>
                <button type="button" class="btn-action-icon" onclick="openRestockModal('Neem & Turmeric Castile Soap', this)">+ Restock</button>
              </td>
            </tr>

            <tr>
              <td>
                <strong style="color:#fff;">Organic Cotton Mesh Grocery Bag (Set of 3)</strong>
                <div class="mono" style="font-size:11.5px; color:rgba(244,242,233,0.5);">SKU: ECO-BAG-0502</div>
              </td>
              <td class="mono">Shelf C-12</td>
              <td>
                <span class="current-stock-val mono" style="color:#FFA07A; font-weight:700;">6 units</span>
                <div class="stock-bar-wrap">
                  <div class="stock-bar-fill" style="width: 30%; background: #FFA07A;"></div>
                </div>
              </td>
              <td class="mono">20 units</td>
              <td><span style="color:#FFA07A; font-weight:700; font-size:11.5px;">⚠️ Low Stock</span></td>
              <td>3 Days · Ceylon Cotton Co.</td>
              <td>
                <button type="button" class="btn-action-icon" onclick="openRestockModal('Organic Cotton Mesh Grocery Bag', this)">+ Restock</button>
              </td>
            </tr>

            <tr>
              <td>
                <strong style="color:#fff;">Organic Bamboo Cutlery Set</strong>
                <div class="mono" style="font-size:11.5px; color:rgba(244,242,233,0.5);">SKU: ECO-BAM-0104</div>
              </td>
              <td class="mono">Shelf A-01</td>
              <td>
                <span class="current-stock-val mono" style="color:var(--neo); font-weight:700;">48 units</span>
                <div class="stock-bar-wrap">
                  <div class="stock-bar-fill" style="width: 80%; background: var(--neo);"></div>
                </div>
              </td>
              <td class="mono">15 units</td>
              <td><span style="color:var(--neo); font-weight:700; font-size:11.5px;">● Healthy</span></td>
              <td>5 Days · Green Bamboo Ltd</td>
              <td>
                <button type="button" class="btn-action-icon" onclick="openRestockModal('Organic Bamboo Cutlery Set', this)">+ Restock</button>
              </td>
            </tr>

            <tr>
              <td>
                <strong style="color:#fff;">Charcoal Bamboo Toothbrush (4-Pack)</strong>
                <div class="mono" style="font-size:11.5px; color:rgba(244,242,233,0.5);">SKU: ECO-TB-0309</div>
              </td>
              <td class="mono">Shelf A-08</td>
              <td>
                <span class="current-stock-val mono" style="color:var(--neo); font-weight:700;">85 units</span>
                <div class="stock-bar-wrap">
                  <div class="stock-bar-fill" style="width: 95%; background: var(--neo);"></div>
                </div>
              </td>
              <td class="mono">25 units</td>
              <td><span style="color:var(--neo); font-weight:700; font-size:11.5px;">● Healthy</span></td>
              <td>4 Days · CleanSmile Eco</td>
              <td>
                <button type="button" class="btn-action-icon" onclick="openRestockModal('Charcoal Bamboo Toothbrush', this)">+ Restock</button>
              </td>
            </tr>

            <tr>
              <td>
                <strong style="color:#fff;">Handcrafted Coconut Shell Bowl</strong>
                <div class="mono" style="font-size:11.5px; color:rgba(244,242,233,0.5);">SKU: ECO-CK-0418</div>
              </td>
              <td class="mono">Shelf D-02</td>
              <td>
                <span class="current-stock-val mono" style="color:var(--neo); font-weight:700;">24 units</span>
                <div class="stock-bar-wrap">
                  <div class="stock-bar-fill" style="width: 60%; background: var(--neo);"></div>
                </div>
              </td>
              <td class="mono">10 units</td>
              <td><span style="color:var(--neo); font-weight:700; font-size:11.5px;">● Healthy</span></td>
              <td>2 Days · Southern Coconut Crafts</td>
              <td>
                <button type="button" class="btn-action-icon" onclick="openRestockModal('Handcrafted Coconut Shell Bowl', this)">+ Restock</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </main>
</div>

<!-- Restock Quantity Modal -->
<div class="modal-overlay" id="restockModal">
  <div class="modal-card">
    <div class="modal-header">
      <h3 id="modalProductName">Restock Product</h3>
      <button type="button" class="modal-close-btn" onclick="closeRestockModal()">&times;</button>
    </div>
    <div>
      <label style="font-size:12.5px; color:rgba(244,242,233,0.8); font-weight:600;">Units Received from Supplier</label>
      <input type="number" id="addQtyInput" class="modal-input" placeholder="e.g. 50" min="1" value="50">
    </div>
    <div class="modal-footer">
      <button type="button" class="btn-action-icon" onclick="closeRestockModal()">Cancel</button>
      <button type="button" class="btn-action-icon" style="background:var(--neo); color:var(--ink); font-weight:700;" onclick="submitRestock()">Confirm Inbound Stock</button>
    </div>
  </div>
</div>

<script>
  let activeRestockRow = null;
  let activeProductId = null;
  let stockProducts = [];

  function showToast(msg) {
    const t = document.getElementById('toast');
    document.getElementById('toastMsg').textContent = msg;
    t.classList.add('show');
    clearTimeout(window._toastTimer);
    window._toastTimer = setTimeout(() => t.classList.remove('show'), 2800);
  }

  async function loadStockInventory() {
    try {
      const res = await fetch('/php/web/Soba-Project/backend/api/customer/get_products.php');
      const data = await res.json();
      if (data && data.success && Array.isArray(data.products)) {
        stockProducts = data.products;
        renderStockTable(stockProducts);
      }
    } catch(err) {
      console.warn("Could not load inventory from DB", err);
    }

    try {
      const statsRes = await fetch('/php/web/Soba-Project/backend/api/admin/dashboard_stats.php');
      const statsData = await statsRes.json();
      if (statsData && statsData.success && statsData.metrics) {
        const m = statsData.metrics;

        // Sidebar Badges
        const sideProd = document.getElementById('sideProdBadge');
        if (sideProd) sideProd.textContent = m.active_skus;
        const sideStock = document.getElementById('sideStockBadge');
        if (sideStock) sideStock.textContent = m.low_stock + ' Low';
        const sideDispatch = document.getElementById('sideDispatchBadge');
        if (sideDispatch) sideDispatch.textContent = (m.in_dispatch + m.pending_packing) + ' Active';

        // KPI Cards
        const totalUnits = document.getElementById('totalUnitsVal');
        if (totalUnits) totalUnits.textContent = Number(m.total_units).toLocaleString();

        const activeMeta = document.getElementById('activeSkusMeta');
        if (activeMeta) activeMeta.textContent = `Across ${m.active_skus} Active SKUs`;

        const lowVal = document.getElementById('lowStockVal');
        if (lowVal) lowVal.textContent = `${m.low_stock} SKUs`;

        const outVal = document.getElementById('outOfStockVal');
        if (outVal) outVal.textContent = `${m.out_of_stock} SKUs`;

        const outRate = document.getElementById('outOfStockRate');
        if (outRate) {
          const rate = m.active_skus > 0 ? Math.round((m.out_of_stock / m.active_skus) * 100) : 0;
          outRate.textContent = `${rate}% Stockout Rate`;
        }

        const invVal = document.getElementById('totalInventoryVal');
        if (invVal) {
          const formatted = m.total_inventory_val >= 1000000 
            ? 'Rs. ' + (m.total_inventory_val / 1000000).toFixed(2) + 'M'
            : 'Rs. ' + Number(m.total_inventory_val).toLocaleString('en-LK', {maximumFractionDigits: 0});
          invVal.textContent = formatted;
        }

        // Urgency Alert Banner
        const bannerTitle = document.getElementById('alertBannerTitle');
        const bannerDesc = document.getElementById('alertBannerDesc');
        const bannerBtn = document.getElementById('alertBannerBtn');
        if (m.low_stock > 0) {
          if (bannerTitle) bannerTitle.textContent = 'Urgent Safety Stock Reorder Needed';
          if (bannerDesc) bannerDesc.textContent = `${m.low_stock} product(s) currently below the safety threshold of 20 units. Restock now to prevent checkout stockouts.`;
          if (bannerBtn) bannerBtn.textContent = `Bulk Reorder (${m.low_stock} Items)`;
        } else {
          if (bannerTitle) bannerTitle.textContent = 'Warehouse Stock Levels Optimal';
          if (bannerDesc) bannerDesc.textContent = 'All active inventory items are healthy and above safety threshold levels.';
          if (bannerBtn) bannerBtn.textContent = 'Audit Catalog';
        }
      }
    } catch(err) {
      console.warn("Could not load stats from DB API", err);
    }
  }

  function renderStockTable(items) {
    const tbody = document.getElementById('stockBody');
    tbody.innerHTML = '';

    items.forEach(p => {
      const tr = document.createElement('tr');
      tr.setAttribute('data-id', p.id);

      const threshold = 20;
      const pct = Math.min(100, Math.round((p.stock_qty / 50) * 100));
      let statusColor = 'var(--neo)';
      let statusLabel = '● Healthy';

      if (p.stock_qty <= 0) {
        statusColor = '#FF4D4D';
        statusLabel = '❌ Out of Stock';
      } else if (p.stock_qty <= 10) {
        statusColor = '#FF6B6B';
        statusLabel = '⚠️ Critical Low';
      } else if (p.stock_qty <= 20) {
        statusColor = '#FFA07A';
        statusLabel = '⚠️ Low Stock';
      }

      tr.innerHTML = `
        <td>
          <strong style="color:#fff;">${p.name}</strong>
          <div class="mono" style="font-size:11.5px; color:rgba(244,242,233,0.5);">SKU: ${p.sku || 'ECO-00' + p.id}</div>
        </td>
        <td class="mono">Shelf ${(p.id % 4 === 1 ? 'A' : (p.id % 4 === 2 ? 'B' : 'C'))}-0${p.id}</td>
        <td>
          <span class="current-stock-val mono" style="color:${statusColor}; font-weight:700;">${p.stock_qty} units</span>
          <div class="stock-bar-wrap">
            <div class="stock-bar-fill" style="width: ${pct}%; background: ${statusColor};"></div>
          </div>
        </td>
        <td class="mono">${threshold} units</td>
        <td><span style="color:${statusColor}; font-weight:700; font-size:11.5px;">${statusLabel}</span></td>
        <td>2-4 Days · Ceylon BioHub</td>
        <td>
          <button type="button" class="btn-action-icon" onclick="openRestockModal(${p.id}, '${p.name.replace(/'/g, "\\'")}', this)">+ Restock</button>
        </td>
      `;
      tbody.appendChild(tr);
    });
  }

  function searchStockTable() {
    const q = document.getElementById('stockSearch').value.toLowerCase();
    const rows = document.querySelectorAll('#stockBody tr');
    rows.forEach(tr => {
      tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
  }

  function openRestockModal(pId, pName, btn) {
    activeRestockRow = btn.closest('tr');
    activeProductId = pId;
    document.getElementById('modalProductName').textContent = `Restock: ${pName}`;
    document.getElementById('addQtyInput').value = 50;
    document.getElementById('restockModal').classList.add('open');
  }

  function closeRestockModal() {
    document.getElementById('restockModal').classList.remove('open');
    activeProductId = null;
  }

  async function submitRestock() {
    const addQty = parseInt(document.getElementById('addQtyInput').value) || 0;
    if(activeProductId && addQty > 0) {
      try {
        const res = await fetch('/php/web/Soba-Project/backend/api/admin/update_stock.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: activeProductId, add_qty: addQty })
        });
        const data = await res.json();
        if (data && data.success) {
          closeRestockModal();
          showToast(`Added +${addQty} units to warehouse inventory!`);
          await loadStockInventory();
        } else {
          alert(data.error || 'Failed to update stock in database');
        }
      } catch(err) {
        console.error(err);
        showToast('Error saving stock to database');
      }
    }
  }

  function handleAdminLogout() {
    localStorage.removeItem('soba_admin');
    sessionStorage.removeItem('soba_admin');
    window.location.href = '/php/web/Soba-Project/backend/api/logout.php';
  }

  window.addEventListener('DOMContentLoaded', () => {
    loadStockInventory();
  });
</script>
<script src="../../assets/js/main.js"></script>
</body>
</html>
