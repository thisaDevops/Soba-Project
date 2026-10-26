<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Portal — Order Dispatch & Logistics | Soba Marketplace</title>
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

  /* Daraz-Style Status Tabs */
  .dispatch-tabs-bar {
    display: flex; gap: 12px; border-bottom: 1px solid var(--line); padding-bottom: 14px; overflow-x: auto;
  }
  .dispatch-tab-btn {
    background: rgba(255,255,255,0.04); border: 1px solid var(--line);
    color: rgba(244,242,233,0.7); padding: 8px 18px; border-radius: 99px;
    font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s ease;
    display: inline-flex; align-items: center; gap: 8px; white-space: nowrap;
  }
  .dispatch-tab-btn:hover { background: rgba(255,255,255,0.08); color: #fff; }
  .dispatch-tab-btn.active {
    background: rgba(184,255,61,0.15); border-color: var(--neo); color: var(--neo);
  }
  .dispatch-tab-btn .tab-count {
    background: rgba(255,255,255,0.1); border-radius: 99px; font-size: 11px; padding: 1px 6px;
  }
  .dispatch-tab-btn.active .tab-count { background: var(--neo); color: var(--ink); font-weight: 700; }

  /* Courier Partner Quick Stats */
  .courier-metrics {
    display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px;
  }
  .courier-card {
    background: linear-gradient(160deg, rgba(18,61,44,.65), rgba(8,26,19,.8));
    border: 1px solid var(--glass-border);
    border-radius: 16px; padding: 18px 20px; display: flex; align-items: center; justify-content: space-between;
  }
  .courier-info h4 { font-size: 14px; color: #fff; margin-bottom: 3px; font-weight: 600; }
  .courier-info p { font-size: 12px; color: rgba(244,242,233,0.6); margin: 0; }
  .courier-badge {
    background: rgba(184,255,61,0.12); border: 1px solid rgba(184,255,61,0.3);
    color: var(--neo); font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 6px;
  }

  /* Dispatch Table Card */
  .table-card {
    background: linear-gradient(160deg, rgba(18,61,44,.6), rgba(8,26,19,.75));
    border: 1px solid var(--glass-border);
    border-radius: 20px; padding: 24px;
    box-shadow: 0 20px 50px rgba(0,0,0,.35);
  }
  .table-top-actions {
    display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 12px;
  }
  .search-dispatch {
    display: flex; align-items: center; background: rgba(255,255,255,0.05);
    border: 1px solid var(--line); border-radius: 10px; padding: 7px 14px; gap: 8px; width: 280px;
  }
  .search-dispatch input {
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

  .status-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 10px; border-radius: 99px; font-size: 11.5px; font-weight: 700;
  }
  .status-badge.to-pack { background: rgba(255, 193, 7, 0.15); color: #FFD166; border: 1px solid rgba(255, 193, 7, 0.3); }
  .status-badge.ready { background: rgba(56, 189, 248, 0.15); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.3); }
  .status-badge.in-transit { background: rgba(184, 255, 61, 0.15); color: var(--neo); border: 1px solid rgba(184, 255, 61, 0.3); }
  .status-badge.delivered { background: rgba(74, 222, 128, 0.15); color: #4ade80; border: 1px solid rgba(74, 222, 128, 0.3); }

  .btn-action-icon {
    background: rgba(255,255,255,0.06); border: 1px solid var(--line);
    color: #fff; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600;
    cursor: pointer; transition: all 0.18s; display: inline-flex; align-items: center; gap: 4px;
  }
  .btn-action-icon:hover { border-color: var(--neo); color: var(--neo); }

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

  @media (max-width: 1024px) {
    .courier-metrics { grid-template-columns: 1fr; }
  }
  @media (max-width: 900px) {
    .admin-shell { grid-template-columns: 1fr; width: 100%; max-width: 100vw; overflow-x: hidden; }
    .admin-sidebar { display: none; }
    .admin-content { padding: 14px 12px 60px; width: 100%; max-width: 100vw; overflow-x: hidden; box-sizing: border-box; }
    .header-bar { padding: 0 4px; flex-wrap: wrap; gap: 8px; }
    .table-card { padding: 12px 8px; border-radius: 14px; width: 100%; box-sizing: border-box; overflow-x: hidden; }
    .dispatch-tabs { overflow-x: auto; -webkit-overflow-scrolling: touch; width: 100%; }
    .data-table-wrap { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; display: block; }
    table.admin-table { min-width: 650px; }
  }
</style>
</head>
<body>

<div class="bg-scene"></div>
<div class="bg-noise"></div>

<!-- Toast -->
<div class="toast" id="toast">
  <svg class="icon" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
  <span id="toastMsg">Dispatch status updated</span>
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
      <a href="inventory_stock.php" class="sidebar-link">
        <div class="sidebar-link-left">
          <svg class="icon" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
          <span>Inventory & Stock</span>
        </div>
        <span class="link-badge" style="background:rgba(200,109,81,0.25); color:#FFA07A;" id="sideStockBadge">...</span>
      </a>
    </div>

    <div class="nav-group">
      <div class="nav-heading">Fulfillment</div>
      <a href="order_dispatch.php" class="sidebar-link active">
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
        <h1>Logistics & Order Dispatch Hub</h1>
        <p>Coordinate plastic-free warehouse packing, courier manifests, and carbon-neutral express deliveries.</p>
      </div>
      <div>
        <button type="button" class="btn-action-icon" style="padding: 10px 18px; border-color:var(--neo); color:var(--neo);" onclick="showToast('Printing All Ready Manifests (6 labels)...')">
          🖨️ Print Batch Waybills
        </button>
      </div>
    </div>

    <!-- Quick Status Filter Tabs (Daraz Style) -->
    <div class="dispatch-tabs-bar">
      <button type="button" class="dispatch-tab-btn active" onclick="filterDispatch('ALL', this)">
        <span>All Active Orders</span>
        <span class="tab-count" id="tabCountAll">0</span>
      </button>
      <button type="button" class="dispatch-tab-btn" onclick="filterDispatch('TO_PACK', this)">
        <span>⏳ To Pack</span>
        <span class="tab-count" id="tabCountToPack">0</span>
      </button>
      <button type="button" class="dispatch-tab-btn" onclick="filterDispatch('READY', this)">
        <span>📦 Ready to Dispatch</span>
        <span class="tab-count" id="tabCountReady">0</span>
      </button>
      <button type="button" class="dispatch-tab-btn" onclick="filterDispatch('IN_TRANSIT', this)">
        <span>🚚 In Transit</span>
        <span class="tab-count" id="tabCountTransit">0</span>
      </button>
      <button type="button" class="dispatch-tab-btn" onclick="filterDispatch('DELIVERED', this)">
        <span>✅ Delivered</span>
        <span class="tab-count" id="tabCountDelivered">0</span>
      </button>
    </div>

    <!-- Courier Cards -->
    <div class="courier-metrics">
      <div class="courier-card">
        <div class="courier-info">
          <h4>Soba Green Express</h4>
          <p>Next Courier Pickup: Today, 2:30 PM</p>
        </div>
        <span class="courier-badge">8 Parcels Ready</span>
      </div>
      <div class="courier-card">
        <div class="courier-info">
          <h4>CityPak Carbon Neutral</h4>
          <p>Scheduled Delivery Rate: 99.4%</p>
        </div>
        <span class="courier-badge">4 In Transit</span>
      </div>
      <div class="courier-card">
        <div class="courier-info">
          <h4>Island-Wide Zero-Waste</h4>
          <p>Avg Delivery: 24 - 48 Hours</p>
        </div>
        <span class="courier-badge" style="background:rgba(255,255,255,0.06); color:#fff; border-color:var(--line);">Active Fleet</span>
      </div>
    </div>

    <!-- Dispatch Orders Table -->
    <div class="table-card">
      <div class="table-top-actions">
        <div class="search-dispatch">
          <svg class="icon" viewBox="0 0 24 24" style="color:rgba(255,255,255,0.4);"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input type="text" id="searchOrdersInput" placeholder="Search Order ID, Waybill or Customer..." oninput="searchDispatchTable()">
        </div>
        <div style="font-size:12.5px; color:rgba(244,242,233,0.6);">
          Fulfillment SLA: <strong style="color:var(--neo);">Same Day Dispatch Guaranteed</strong>
        </div>
      </div>

      <div class="data-table-wrap">
        <table class="admin-table" id="dispatchTable">
          <thead>
            <tr>
              <th>Order ID</th>
              <th>Customer & City</th>
              <th>Order Items</th>
              <th>Courier & Waybill</th>
              <th>Shipping Mode</th>
              <th>Dispatch Status</th>
              <th>Fulfillment Action</th>
            </tr>
          </thead>
          <tbody id="dispatchBody">
            <tr data-status="IN_TRANSIT">
              <td class="mono" style="font-weight:700; color:#fff;">#SOBA-991204</td>
              <div>
                <td>
                  <strong>Nimal Perera</strong>
                  <div style="font-size:11.5px; color:rgba(244,242,233,0.5);">Colombo 04, Bambalapitiya</div>
                </td>
              </div>
              <td>Bamboo Cutlery Set (x2)</td>
              <td>
                <div>Soba Green Express</div>
                <div class="mono" style="font-size:11px; color:var(--neo);">WB-778921</div>
              </td>
              <td><span style="font-size:11.5px; color:#dfffd0;">⚡ Carbon-Neutral Express</span></td>
              <td><span class="status-badge in-transit">🚚 In Transit</span></td>
              <td>
                <button type="button" class="btn-action-icon" onclick="markDelivered(this)">Mark Delivered</button>
              </td>
            </tr>

            <tr data-status="READY">
              <td class="mono" style="font-weight:700; color:#fff;">#SOBA-991205</td>
              <td>
                <strong>Kavinda Silva</strong>
                <div style="font-size:11.5px; color:rgba(244,242,233,0.5);">Nugegoda, Western Prov.</div>
              </td>
              <td>Neem Castile Soap (x2), Coconut Bowl (x2)</td>
              <td>
                <div>CityPak Green</div>
                <div class="mono" style="font-size:11px; color:rgba(244,242,233,0.6);">WB-Pending</div>
              </td>
              <td><span style="font-size:11.5px; color:#dfffd0;">🌱 Standard Eco</span></td>
              <td><span class="status-badge ready">📦 Ready to Dispatch</span></td>
              <td>
                <button type="button" class="btn-action-icon" onclick="dispatchOrder(this)">Handover Courier</button>
              </td>
            </tr>

            <tr data-status="TO_PACK">
              <td class="mono" style="font-weight:700; color:#fff;">#SOBA-991206</td>
              <td>
                <strong>Mahela Perera</strong>
                <div style="font-size:11.5px; color:rgba(244,242,233,0.5);">Kandy Central</div>
              </td>
              <td>Bamboo Toothbrush (4-Pack)</td>
              <td>
                <div>Unassigned</div>
                <div class="mono" style="font-size:11px; color:rgba(244,242,233,0.4);">-</div>
              </td>
              <td><span style="font-size:11.5px; color:#dfffd0;">⚡ Carbon-Neutral Express</span></td>
              <td><span class="status-badge to-pack">⏳ To Pack</span></td>
              <td>
                <button type="button" class="btn-action-icon" onclick="packOrder(this)">Complete Packing</button>
              </td>
            </tr>

            <tr data-status="DELIVERED">
              <td class="mono" style="font-weight:700; color:#fff;">#SOBA-991198</td>
              <td>
                <strong>Dilani Senaratne</strong>
                <div style="font-size:11.5px; color:rgba(244,242,233,0.5);">Galle Fort</div>
              </td>
              <td>Organic Cotton Grocery Bag (x3)</td>
              <td>
                <div>Soba Green Express</div>
                <div class="mono" style="font-size:11px; color:rgba(244,242,233,0.6);">WB-665012</div>
              </td>
              <td><span style="font-size:11.5px; color:#dfffd0;">⚡ Carbon-Neutral Express</span></td>
              <td><span class="status-badge delivered">✅ Delivered</span></td>
              <td>
                <button type="button" class="btn-action-icon" onclick="showToast('Invoice & Carbon Certificate generated for #SOBA-991198')">Invoice</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </main>
</div>

<script>
  let dispatchOrders = [];

  function showToast(msg) {
    const t = document.getElementById('toast');
    document.getElementById('toastMsg').textContent = msg;
    t.classList.add('show');
    clearTimeout(window._toastTimer);
    window._toastTimer = setTimeout(() => t.classList.remove('show'), 2800);
  }

  async function loadDispatchOrders() {
    try {
      const res = await fetch('/php/web/Soba-Project/backend/api/admin/manage_orders.php');
      const data = await res.json();
      if (data && data.success && Array.isArray(data.orders)) {
        dispatchOrders = data.orders;
        renderDispatchTable(dispatchOrders);
      }
    } catch(err) {
      console.warn("Could not load orders from DB", err);
    }

    try {
      const statsRes = await fetch('/php/web/Soba-Project/backend/api/admin/dashboard_stats.php');
      const statsData = await statsRes.json();
      if (statsData && statsData.success && statsData.metrics) {
        const m = statsData.metrics;
        const sideProd = document.getElementById('sideProdBadge');
        if (sideProd) sideProd.textContent = m.active_skus;
        const sideStock = document.getElementById('sideStockBadge');
        if (sideStock) sideStock.textContent = m.low_stock + ' Low';
        const sideDispatch = document.getElementById('sideDispatchBadge');
        if (sideDispatch) sideDispatch.textContent = (m.in_dispatch + m.pending_packing) + ' Active';
      }
    } catch(err) {
      console.warn("Could not load stats from DB API", err);
    }
  }

  function renderDispatchTable(items) {
    const tbody = document.getElementById('dispatchBody');
    tbody.innerHTML = '';

    let cntToPack = 0;
    let cntReady = 0;
    let cntTransit = 0;
    let cntDelivered = 0;

    items.forEach(ord => {
      const tr = document.createElement('tr');
      
      let normStatus = 'READY';
      let badgeClass = 'ready';
      let badgeLabel = '📦 Ready to Dispatch';
      let actionBtn = `<button type="button" class="btn-action-icon" onclick="updateOrderStatus(${ord.o_id}, 'In Transit', 'Handed over to Eco Courier')">Handover Courier</button>`;

      const s = (ord.status || '').toLowerCase();
      if (s.includes('transit')) {
        normStatus = 'IN_TRANSIT';
        badgeClass = 'in-transit';
        badgeLabel = '🚚 In Transit';
        actionBtn = `<button type="button" class="btn-action-icon" onclick="updateOrderStatus(${ord.o_id}, 'Delivered', 'Order marked as Delivered')">Mark Delivered</button>`;
        cntTransit++;
      } else if (s.includes('delivered')) {
        normStatus = 'DELIVERED';
        badgeClass = 'delivered';
        badgeLabel = '✅ Delivered';
        actionBtn = `<button type="button" class="btn-action-icon" onclick="showToast('Invoice generated for #' + ${JSON.stringify(ord.order_number)})">Invoice</button>`;
        cntDelivered++;
      } else if (s.includes('pack') || s.includes('pending')) {
        normStatus = 'TO_PACK';
        badgeClass = 'to-pack';
        badgeLabel = '⏳ To Pack';
        actionBtn = `<button type="button" class="btn-action-icon" onclick="updateOrderStatus(${ord.o_id}, 'Ready to Dispatch', 'Package sealed plastic-free')">Complete Packing</button>`;
        cntToPack++;
      } else {
        cntReady++;
      }

      tr.setAttribute('data-status', normStatus);
      tr.setAttribute('data-oid', ord.o_id);

      tr.innerHTML = `
        <td class="mono" style="font-weight:700; color:#fff;">#${ord.order_number}</td>
        <td>
          <strong>${ord.customer_name || 'Customer'}</strong>
          <div style="font-size:11.5px; color:rgba(244,242,233,0.5);">${ord.shipping_address || 'Colombo, Sri Lanka'}</div>
        </td>
        <td>${ord.items_summary || 'Zero-Waste Eco Products'}</td>
        <td>
          <div>Soba Green Express</div>
          <div class="mono" style="font-size:11px; color:var(--neo);">${ord.courier_tracking || 'EV-' + ord.order_number.replace('SOBA-', '')}</div>
        </td>
        <td><span style="font-size:11.5px; color:#dfffd0;">⚡ Carbon-Neutral Express</span></td>
        <td><span class="status-badge ${badgeClass}">${badgeLabel}</span></td>
        <td>${actionBtn}</td>
      `;
      tbody.appendChild(tr);
    });

    const elAll = document.getElementById('tabCountAll');
    if (elAll) elAll.textContent = items.length;
    const elToPack = document.getElementById('tabCountToPack');
    if (elToPack) elToPack.textContent = cntToPack;
    const elReady = document.getElementById('tabCountReady');
    if (elReady) elReady.textContent = cntReady;
    const elTransit = document.getElementById('tabCountTransit');
    if (elTransit) elTransit.textContent = cntTransit;
    const elDelivered = document.getElementById('tabCountDelivered');
    if (elDelivered) elDelivered.textContent = cntDelivered;
  }

  async function updateOrderStatus(orderId, newStatus, toastMsg) {
    try {
      const res = await fetch('/php/web/Soba-Project/backend/api/admin/manage_orders.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ order_id: orderId, status: newStatus })
      });
      const data = await res.json();
      if (data && data.success) {
        showToast(toastMsg || `Order status updated to ${newStatus}`);
        await loadDispatchOrders();
      } else {
        alert(data.error || 'Failed to update order status');
      }
    } catch(err) {
      console.error(err);
      showToast('Error connecting to database');
    }
  }

  function filterDispatch(status, btn) {
    document.querySelectorAll('.dispatch-tab-btn').forEach(b => b.classList.remove('active'));
    if(btn) btn.classList.add('active');

    const rows = document.querySelectorAll('#dispatchBody tr');
    rows.forEach(tr => {
      const rowStatus = tr.getAttribute('data-status');
      if(status === 'ALL' || rowStatus === status) {
        tr.style.display = '';
      } else {
        tr.style.display = 'none';
      }
    });
  }

  function searchDispatchTable() {
    const q = document.getElementById('searchOrdersInput').value.toLowerCase();
    const rows = document.querySelectorAll('#dispatchBody tr');
    rows.forEach(tr => {
      tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
  }

  function handleAdminLogout() {
    localStorage.removeItem('soba_admin');
    sessionStorage.removeItem('soba_admin');
    window.location.href = '/php/web/Soba-Project/backend/api/logout.php';
  }

  window.addEventListener('DOMContentLoaded', () => {
    loadDispatchOrders();
  });
</script>
<script src="../../assets/js/main.js"></script>
</body>
</html>
