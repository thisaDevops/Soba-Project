<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Portal — Dashboard | Soba Marketplace</title>
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

  /* SVG Icons */
  .icon {
    width: 16px; height: 16px; flex-shrink: 0; stroke: currentColor; fill: none;
    stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round;
  }
  .icon-sm { width: 14px; height: 14px; }
  .icon-lg { width: 20px; height: 20px; }
  .icon-xl { width: 24px; height: 24px; }

  /* Ambient Cinematic Background */
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

  /* Admin Header Nav */
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

  .admin-search-box {
    display: flex; align-items: center;
    background: rgba(255, 255, 255, 0.06); border: 1px solid var(--line);
    border-radius: 12px; padding: 7px 14px; width: 380px; gap: 10px;
    transition: all 0.2s ease;
  }
  .admin-search-box:focus-within {
    border-color: var(--neo); box-shadow: 0 0 15px rgba(184,255,61,0.2);
  }
  .admin-search-box input {
    background: transparent; border: none; outline: none;
    color: #fff; font-size: 13.5px; width: 100%; font-family: 'Work Sans', sans-serif;
  }
  .admin-search-box input::placeholder { color: rgba(255,255,255,0.4); }

  .header-right { display: flex; align-items: center; gap: 16px; }
  
  .live-status-pill {
    display: inline-flex; align-items: center; gap: 7px;
    background: rgba(184,255,61,0.1); border: 1px solid rgba(184,255,61,0.25);
    padding: 6px 12px; border-radius: 99px; font-size: 12px; color: var(--neo); font-weight: 600;
  }
  .pulse-dot {
    width: 7px; height: 7px; border-radius: 50%; background: var(--neo);
    box-shadow: 0 0 8px var(--neo); animation: pulse 1.6s infinite;
  }
  @keyframes pulse { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.4; transform: scale(0.8); } }

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

  /* Layout Frame */
  .admin-shell {
    position: relative; z-index: 10; flex: 1;
    display: grid; grid-template-columns: 260px 1fr;
    max-width: 1600px; width: 100%; margin: 0 auto;
    min-height: calc(100vh - 65px);
  }

  /* Admin Sidebar (Daraz Seller Center Style) */
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
  .sidebar-link:hover {
    background: rgba(255,255,255,0.06); color: #fff;
  }
  .sidebar-link.active {
    background: rgba(184, 255, 61, 0.14); color: var(--neo); font-weight: 700;
    border: 1px solid rgba(184, 255, 61, 0.28);
  }
  .sidebar-link-left { display: flex; align-items: center; gap: 10px; }
  .link-badge {
    background: rgba(255,255,255,0.1); color: #fff; border-radius: 99px;
    font-size: 11px; font-weight: 700; padding: 2px 7px;
  }
  .sidebar-link.active .link-badge {
    background: var(--neo); color: var(--ink);
  }

  .sidebar-footer { margin-top: auto; padding-top: 18px; border-top: 1px solid var(--line); }
  .logout-btn {
    width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px;
    background: rgba(200, 109, 81, 0.12); border: 1px solid rgba(200, 109, 81, 0.35);
    color: #FFA07A; padding: 10px; border-radius: 10px; font-size: 13px; font-weight: 600;
    cursor: pointer; text-decoration: none; transition: all 0.2s;
  }
  .logout-btn:hover { background: rgba(200, 109, 81, 0.25); color: #fff; }

  /* Main Workspace Area */
  .admin-content {
    padding: 30px 36px 60px;
    display: flex; flex-direction: column; gap: 28px;
  }

  /* Page Title Bar */
  .page-title-bar {
    display: flex; justify-content: space-between; align-items: flex-start; gap: 20px;
  }
  .page-title-bar h1 { font-size: 28px; color: #fff; margin-bottom: 4px; }
  .page-title-bar p { margin: 0; font-size: 13.5px; color: rgba(244,242,233,0.6); }

  .header-actions-right { display: flex; gap: 12px; }
  .btn-primary {
    display: inline-flex; align-items: center; gap: 8px;
    background: linear-gradient(135deg, var(--neo), #8fe04f); color: var(--ink);
    border: none; border-radius: 11px; padding: 11px 20px; font-size: 13.5px; font-weight: 700;
    cursor: pointer; text-decoration: none; transition: all 0.2s;
    box-shadow: 0 10px 24px rgba(184,255,61,0.22);
  }
  .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 14px 30px rgba(184,255,61,0.32); }

  /* 4 Metric Top KPI Cards */
  .kpi-grid {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;
  }
  .kpi-card {
    background: linear-gradient(160deg, rgba(18,61,44,.65), rgba(8,26,19,.8));
    border: 1px solid var(--glass-border);
    border-radius: 18px; padding: 22px;
    box-shadow: 0 15px 40px rgba(0,0,0,.35);
    backdrop-filter: blur(16px);
    display: flex; flex-direction: column; gap: 10px; position: relative; overflow: hidden;
  }
  .kpi-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, var(--neo), transparent);
  }
  .kpi-top { display: flex; justify-content: space-between; align-items: center; }
  .kpi-label { font-size: 12.5px; color: rgba(244,242,233,0.65); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }
  .kpi-icon-box {
    width: 36px; height: 36px; border-radius: 10px;
    background: rgba(184,255,61,0.12); color: var(--neo);
    display: flex; align-items: center; justify-content: center;
  }
  .kpi-val { font-size: 26px; font-weight: 700; color: #fff; line-height: 1.1; }
  .kpi-meta { display: flex; align-items: center; gap: 6px; font-size: 12px; }
  .trend-up { color: var(--neo); font-weight: 600; }
  .trend-sub { color: rgba(244,242,233,0.5); }

  /* Daraz-Style Fast Fulfillment Status Banner */
  .fulfillment-bar {
    background: linear-gradient(135deg, rgba(18, 61, 44, 0.7) 0%, rgba(8, 30, 21, 0.85) 100%);
    border: 1px solid var(--glass-border);
    border-radius: 18px; padding: 22px 26px;
    display: flex; flex-direction: column; gap: 16px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.3);
  }
  .fulfillment-header { display: flex; justify-content: space-between; align-items: center; }
  .fulfillment-header h2 { font-size: 18px; color: #fff; }
  .fulfillment-header a { font-size: 13px; color: var(--neo); text-decoration: none; font-weight: 600; }

  .status-tiles {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px;
  }
  .status-tile {
    background: rgba(255,255,255,0.04); border: 1px solid var(--line);
    border-radius: 14px; padding: 16px; display: flex; align-items: center; justify-content: space-between;
    text-decoration: none; color: inherit; transition: all 0.2s ease;
  }
  .status-tile:hover {
    background: rgba(255,255,255,0.08); border-color: rgba(184,255,61,0.4);
    transform: translateY(-2px);
  }
  .status-tile-left h4 { font-size: 13.5px; color: rgba(244,242,233,0.7); margin-bottom: 4px; font-weight: 600; }
  .status-tile-left .tile-count { font-size: 22px; font-weight: 700; color: #fff; }
  .status-tile .icon-tag { font-size: 26px; }

  /* Grid Layout: Visual Sales Trend & Eco Impact */
  .dashboard-mid-grid {
    display: grid; grid-template-columns: 2fr 1fr; gap: 24px;
  }

  .chart-card {
    background: linear-gradient(160deg, rgba(18,61,44,.6), rgba(8,26,19,.75));
    border: 1px solid var(--glass-border);
    border-radius: 20px; padding: 26px;
    box-shadow: 0 20px 50px rgba(0,0,0,.35);
    display: flex; flex-direction: column; gap: 20px;
  }
  .chart-header { display: flex; justify-content: space-between; align-items: center; }
  .chart-header h3 { font-size: 18px; color: #fff; }

  /* Pure CSS / SVG Glow Chart Simulation */
  .chart-svg-shell {
    width: 100%; height: 220px; position: relative;
    display: flex; align-items: flex-end; justify-content: space-between;
    padding-top: 20px; padding-bottom: 25px;
    border-bottom: 1px dashed var(--line);
  }
  .chart-bar-col {
    display: flex; flex-direction: column; align-items: center; gap: 8px; flex: 1; height: 100%; justify-content: flex-end;
  }
  .chart-bar-fill {
    width: 32px; border-radius: 8px 8px 3px 3px;
    background: linear-gradient(180deg, var(--neo) 0%, rgba(59, 122, 52, 0.4) 100%);
    box-shadow: 0 0 15px rgba(184, 255, 61, 0.2);
    transition: all 0.4s ease;
  }
  .chart-bar-fill:hover {
    background: var(--neo); box-shadow: 0 0 25px rgba(184, 255, 61, 0.6);
  }
  .chart-bar-fill.zero-fill {
    background: rgba(255, 255, 255, 0.08);
    box-shadow: none;
  }
  .chart-bar-fill.zero-fill:hover {
    background: rgba(184, 255, 61, 0.3);
    box-shadow: 0 0 15px rgba(184, 255, 61, 0.2);
  }
  .chart-bar-fill.today-fill {
    border: 1px solid rgba(184, 255, 61, 0.7);
  }
  .chart-bar-day { font-size: 11.5px; color: rgba(244,242,233,0.55); font-weight: 600; }
  .chart-bar-val { font-size: 10.5px; color: var(--neo); font-weight: 700; margin-bottom: 4px; }

  /* Eco Meter Widget */
  .eco-card {
    background: linear-gradient(160deg, rgba(18,61,44,.6), rgba(8,26,19,.75));
    border: 1px solid var(--glass-border);
    border-radius: 20px; padding: 26px;
    box-shadow: 0 20px 50px rgba(0,0,0,.35);
    display: flex; flex-direction: column; gap: 18px; justify-content: space-between;
  }
  .eco-meter-circle {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    width: 130px; height: 130px; border-radius: 50%; margin: 10px auto;
    border: 6px solid var(--neo); box-shadow: 0 0 30px rgba(184, 255, 61, 0.3);
    position: relative;
  }
  .eco-meter-val { font-size: 26px; font-weight: 800; color: #fff; }
  .eco-meter-lbl { font-size: 10.5px; color: var(--neo); text-transform: uppercase; letter-spacing: 0.08em; font-weight: 700; }

  /* Recent Orders Table (Daraz Style) */
  .orders-table-card {
    background: linear-gradient(160deg, rgba(18,61,44,.6), rgba(8,26,19,.75));
    border: 1px solid var(--glass-border);
    border-radius: 20px; padding: 26px;
    box-shadow: 0 20px 50px rgba(0,0,0,.35);
  }
  .table-head-bar {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 20px;
  }
  .table-head-bar h3 { font-size: 18px; color: #fff; }

  .data-table-wrap { overflow-x: auto; width: 100%; }
  table.admin-table {
    width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;
  }
  table.admin-table th {
    color: rgba(244,242,233,0.5); font-size: 11.5px; text-transform: uppercase;
    letter-spacing: 0.08em; font-weight: 700; padding: 12px 14px;
    border-bottom: 1px solid var(--line);
  }
  table.admin-table td {
    padding: 14px; border-bottom: 1px solid rgba(255,255,255,0.06);
    color: rgba(244,242,233,0.85); vertical-align: middle;
  }
  table.admin-table tr:hover td {
    background: rgba(255,255,255,0.03); color: #fff;
  }

  .badge-status {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px; border-radius: 99px; font-size: 11px; font-weight: 700;
  }
  .badge-status.pending { background: rgba(255, 193, 7, 0.15); color: #FFD166; border: 1px solid rgba(255, 193, 7, 0.3); }
  .badge-status.dispatch { background: rgba(56, 189, 248, 0.15); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.3); }
  .badge-status.delivered { background: rgba(184, 255, 61, 0.15); color: var(--neo); border: 1px solid rgba(184, 255, 61, 0.3); }

  .table-action-btn {
    background: rgba(255,255,255,0.06); border: 1px solid var(--line);
    color: #fff; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600;
    text-decoration: none; cursor: pointer; transition: all 0.18s;
  }
  .table-action-btn:hover { border-color: var(--neo); color: var(--neo); }

  /* Toast Notification */
  .toast {
    position: fixed; top: 24px; left: 50%; transform: translateX(-50%) translateY(-20px);
    display: inline-flex; align-items: center; gap: 9px;
    background: rgba(184,255,61,.96); border: 1px solid var(--neo); color: var(--ink);
    font-weight: 700; padding: 12px 24px; border-radius: 99px; font-size: 14px; z-index: 100;
    opacity: 0; pointer-events: none; transition: opacity .3s ease, transform .3s ease;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
  }
  .toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }

  @media (max-width: 1200px) {
    .kpi-grid { grid-template-columns: 1fr 1fr; }
    .status-tiles { grid-template-columns: 1fr 1fr; }
    .dashboard-mid-grid { grid-template-columns: 1fr; }
  }
  @media (max-width: 900px) {
    .admin-shell { grid-template-columns: 1fr; width: 100%; max-width: 100vw; overflow-x: hidden; }
    .admin-sidebar { display: none; }
    .admin-search-box { display: none; }
    .admin-content { padding: 14px 12px 60px; width: 100%; max-width: 100vw; overflow-x: hidden; box-sizing: border-box; }
    .header-bar { padding: 0 4px; flex-wrap: wrap; gap: 8px; }
    .kpi-grid, .status-tiles { grid-template-columns: 1fr; gap: 12px; }
    .dashboard-mid-grid { grid-template-columns: 1fr; gap: 16px; }
    .orders-table-card { padding: 14px 10px; border-radius: 14px; width: 100%; box-sizing: border-box; overflow-x: hidden; }
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
  <span id="toastMsg">Action executed successfully</span>
</div>

<!-- Header -->
<header>
  <div class="header-bar">
    <a href="dashboard.php" class="logo-admin">
      <span class="mark">Soba</span>
      <span class="badge">Seller Portal</span>
    </a>

    <div class="admin-search-box">
      <svg class="icon" viewBox="0 0 24 24" style="color:rgba(255,255,255,0.4);"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" placeholder="Search orders, SKU, customer name or waybill..." onkeydown="if(event.key==='Enter') quickSearch(this.value)">
    </div>

    <div class="header-right">
      <div class="live-status-pill">
        <span class="pulse-dot"></span>
        <span>Eco Network Live</span>
      </div>

      <a href="../customer/products.php" target="_blank" class="view-store-btn" title="Open customer store in new tab">
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

<!-- Main Shell -->
<div class="admin-shell">

  <!-- Left Sidebar Navigator -->
  <aside class="admin-sidebar">
    <div class="nav-group">
      <div class="nav-heading">Overview</div>
      <a href="dashboard.php" class="sidebar-link active">
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
        <span class="link-badge" id="dashSideSkuBadge">10</span>
      </a>
      <a href="inventory_stock.php" class="sidebar-link">
        <div class="sidebar-link-left">
          <svg class="icon" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
          <span>Inventory & Stock</span>
        </div>
        <span class="link-badge" style="background:rgba(200,109,81,0.25); color:#FFA07A;" id="dashSideStockBadge">0 Low</span>
      </a>
    </div>

    <div class="nav-group">
      <div class="nav-heading">Fulfillment</div>
      <a href="order_dispatch.php" class="sidebar-link">
        <div class="sidebar-link-left">
          <svg class="icon" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
          <span>Order Dispatch</span>
        </div>
        <span class="link-badge" style="background:var(--neo); color:var(--ink);" id="dashSideDispatchBadge">0 Active</span>
      </a>
    </div>

    <div class="sidebar-footer">
      <button type="button" class="logout-btn" onclick="handleAdminLogout()">
        <svg class="icon icon-sm" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        <span>Sign Out Admin</span>
      </button>
    </div>
  </aside>

  <!-- Right Main Admin Content -->
  <main class="admin-content">

    <div class="page-title-bar">
      <div>
        <h1>Store Performance & Operations</h1>
        <p>Real-time analytics, order dispatch statuses, and catalog overview for Soba Zero-Waste Marketplace.</p>
      </div>
      <div class="header-actions-right">
        <a href="products_manage.php" class="btn-primary">
          <svg class="icon" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          <span>Add New Product</span>
        </a>
      </div>
    </div>

    <!-- 4 KPI Cards -->
    <div class="kpi-grid">
      <div class="kpi-card">
        <div class="kpi-top">
          <span class="kpi-label">Gross Revenue</span>
          <div class="kpi-icon-box">
            <svg class="icon" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
          </div>
        </div>
        <div class="kpi-val mono" id="kpiRevenue">Rs. 7,900</div>
        <div class="kpi-meta">
          <span class="trend-up">↑ Live Sync</span>
          <span class="trend-sub">from orders DB</span>
        </div>
      </div>

      <div class="kpi-card">
        <div class="kpi-top">
          <span class="kpi-label">Total Orders</span>
          <div class="kpi-icon-box">
            <svg class="icon" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-2z"/></svg>
          </div>
        </div>
        <div class="kpi-val mono" id="kpiOrders">3 Orders</div>
        <div class="kpi-meta">
          <span class="trend-up" id="kpiDispatchToday">↑ Active</span>
          <span class="trend-sub">in marketplace</span>
        </div>
      </div>

      <div class="kpi-card">
        <div class="kpi-top">
          <span class="kpi-label">Active SKUs</span>
          <div class="kpi-icon-box">
            <svg class="icon" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
          </div>
        </div>
        <div class="kpi-val mono" id="kpiSkus">10 Items</div>
        <div class="kpi-meta">
          <span style="color:#FFA07A; font-weight:600;" id="kpiLowStock">0 Low stock</span>
          <span class="trend-sub">inventory status</span>
        </div>
      </div>

      <div class="kpi-card">
        <div class="kpi-top">
          <span class="kpi-label">Plastic Saved</span>
          <div class="kpi-icon-box">
            <svg class="icon" viewBox="0 0 24 24"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 7 18 7c1 8-6 11-7 13z"/></svg>
          </div>
        </div>
        <div class="kpi-val mono" id="kpiPlastic">0.42 kg</div>
        <div class="kpi-meta">
          <span class="trend-up">100% Zero-Waste</span>
          <span class="trend-sub">packaging standard</span>
        </div>
      </div>
    </div>

    <!-- Fulfillment Speed Banner (Daraz Seller Center Style) -->
    <div class="fulfillment-bar">
      <div class="fulfillment-header">
        <h2>Order Fulfillment Flow</h2>
        <a href="order_dispatch.php">Manage Dispatch Queue &rarr;</a>
      </div>
      <div class="status-tiles">
        <a href="order_dispatch.php?filter=pending" class="status-tile">
          <div class="status-tile-left">
            <h4>Pending Confirmation</h4>
            <div class="tile-count mono" id="tilePending">01</div>
          </div>
          <span class="icon-tag">⏳</span>
        </a>
        <a href="order_dispatch.php?filter=packing" class="status-tile">
          <div class="status-tile-left">
            <h4>Ready to Pack</h4>
            <div class="tile-count mono" id="tilePacking">01</div>
          </div>
          <span class="icon-tag">📦</span>
        </a>
        <a href="order_dispatch.php?filter=dispatch" class="status-tile">
          <div class="status-tile-left">
            <h4>Out for Dispatch</h4>
            <div class="tile-count mono" id="tileDispatch">01</div>
          </div>
          <span class="icon-tag">🚚</span>
        </a>
        <a href="order_dispatch.php?filter=delivered" class="status-tile">
          <div class="status-tile-left">
            <h4>Delivered / Completed</h4>
            <div class="tile-count mono" id="tileDelivered">01</div>
          </div>
          <span class="icon-tag">✅</span>
        </a>
      </div>
    </div>

    <!-- Mid Grid: Chart & Eco Compliance -->
    <div class="dashboard-mid-grid">
      <!-- Sales Chart -->
      <div class="chart-card">
        <div class="chart-header">
          <div>
            <h3>Weekly Sales Revenue (LKR)</h3>
            <p style="font-size:12px; color:rgba(244,242,233,0.5); margin-top:2px;">Gross revenue performance over the current 7-day cycle</p>
          </div>
          <span style="font-size:12px; color:var(--neo); font-weight:700;" id="chartPeriodBadge">Oct 20 – Oct 26, 2026</span>
        </div>
        <div class="chart-svg-shell" id="weeklyChartContainer">
          <div class="chart-bar-col">
            <span class="chart-bar-val mono">...</span>
            <div class="chart-bar-fill" style="height: 10%;"></div>
            <span class="chart-bar-day">Loading</span>
          </div>
        </div>
      </div>

      <!-- Eco Impact Meter -->
      <div class="eco-card">
        <div>
          <h3>Zero-Waste Compliance</h3>
          <p style="font-size:12.5px; color:rgba(244,242,233,0.6); margin-top:4px;">Percentage of orders shipped strictly with plastic-free biodegradable packaging materials.</p>
        </div>
        <div class="eco-meter-circle">
          <span class="eco-meter-val mono" id="complianceVal">100%</span>
          <span class="eco-meter-lbl">Certified</span>
        </div>
        <div style="font-size:12px; text-align:center; color:rgba(244,242,233,0.7);" id="complianceMeta">
          🌱 3 of 3 orders certified 100% plastic-free
        </div>
      </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="orders-table-card">
      <div class="table-head-bar">
        <h3>Live Orders Awaiting Action</h3>
        <a href="order_dispatch.php" class="table-action-btn">View All Orders</a>
      </div>

      <div class="data-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Order ID</th>
              <th>Customer</th>
              <th>City / Address</th>
              <th>Items</th>
              <th>Total Amount</th>
              <th>Delivery Type</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody id="recentOrdersBody">
            <tr>
              <td class="mono" style="font-weight:700; color:#fff;">#SOBA-74921</td>
              <td>Thisaru Ranathunga</td>
              <td>Colombo 03</td>
              <td>Organic Bamboo Toothbrush (x2), Solid Shampoo Bar (x1)</td>
              <td class="mono" style="color:var(--neo); font-weight:700;">Rs. 2,450</td>
              <td>Carbon-Neutral Express</td>
              <td><span class="badge-status dispatch">In Transit</span></td>
              <td><a href="order_dispatch.php" class="table-action-btn">Manage</a></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </main>

</div>

<script>
  async function loadDashboardStats() {
    try {
      const res = await fetch('/php/web/Soba-Project/backend/api/admin/dashboard_stats.php');
      const data = await res.json();
      if (data && data.success) {
        const m = data.metrics;
        document.getElementById('kpiRevenue').textContent = 'Rs. ' + Number(m.gross_revenue).toLocaleString();
        document.getElementById('kpiOrders').textContent = m.total_orders + ' Orders';
        document.getElementById('kpiSkus').textContent = m.active_skus + ' Items';
        const sideSku = document.getElementById('dashSideSkuBadge');
        if (sideSku) sideSku.textContent = m.active_skus;
        const sideStock = document.getElementById('dashSideStockBadge');
        if (sideStock) sideStock.textContent = m.low_stock + ' Low';
        const sideDispatch = document.getElementById('dashSideDispatchBadge');
        if (sideDispatch) sideDispatch.textContent = (m.in_dispatch + m.pending_packing) + ' Active';
        document.getElementById('kpiLowStock').textContent = m.low_stock + ' Low stock';
        document.getElementById('kpiPlastic').textContent = m.plastic_saved_kg + ' kg';

        document.getElementById('tilePending').textContent = String(m.pending_packing).padStart(2, '0');
        document.getElementById('tilePacking').textContent = String(m.pending_packing).padStart(2, '0');
        document.getElementById('tileDispatch').textContent = String(m.in_dispatch).padStart(2, '0');
        document.getElementById('tileDelivered').textContent = String(m.delivered).padStart(2, '0');

        // Render Dynamic Weekly Chart
        if (data.weekly_chart) {
          const badge = document.getElementById('chartPeriodBadge');
          if (badge && data.weekly_chart.period_label) {
            badge.textContent = data.weekly_chart.period_label;
          }

          const chartContainer = document.getElementById('weeklyChartContainer');
          if (chartContainer && Array.isArray(data.weekly_chart.days)) {
            chartContainer.innerHTML = '';
            data.weekly_chart.days.forEach(item => {
              const col = document.createElement('div');
              col.className = 'chart-bar-col';
              const revFormatted = Number(item.revenue).toLocaleString();
              col.title = `${item.display_day} (${item.short_date}): Rs. ${revFormatted} • ${item.order_count} order(s)`;

              let fillClass = 'chart-bar-fill';
              if (item.revenue === 0) fillClass += ' zero-fill';
              if (item.is_today) fillClass += ' today-fill';

              const valColor = item.revenue > 0 ? 'var(--neo)' : 'rgba(244,242,233,0.35)';
              const dayStyle = item.is_today ? 'style="color:var(--neo); font-weight:700;"' : '';

              col.innerHTML = `
                <span class="chart-bar-val mono" style="color:${valColor};">${item.label}</span>
                <div class="${fillClass}" style="height: ${item.height_pct}%;"></div>
                <span class="chart-bar-day" ${dayStyle}>${item.display_day}</span>
              `;
              chartContainer.appendChild(col);
            });
          }
        }

        // Render Dynamic Eco Compliance Meter
        if (data.compliance) {
          const compVal = document.getElementById('complianceVal');
          if (compVal) compVal.textContent = data.compliance.rate_pct + '%';
          const compMeta = document.getElementById('complianceMeta');
          if (compMeta && data.compliance.summary) {
            compMeta.textContent = data.compliance.summary;
          }
        }

        if (Array.isArray(data.recent_orders) && data.recent_orders.length > 0) {
          const tbody = document.getElementById('recentOrdersBody');
          tbody.innerHTML = '';
          data.recent_orders.forEach(ord => {
            let badgeClass = 'pending';
            const s = (ord.status || '').toLowerCase();
            if (s.includes('transit') || s.includes('dispatch')) badgeClass = 'dispatch';
            else if (s.includes('delivered')) badgeClass = 'delivered';

            const tr = document.createElement('tr');
            tr.innerHTML = `
              <td class="mono" style="font-weight:700; color:#fff;">#${ord.order_number}</td>
              <td>${ord.customer_name || 'Customer'}</td>
              <td>${ord.shipping_address || 'Colombo, Sri Lanka'}</td>
              <td>${ord.items_summary || 'Zero-Waste Eco Products'}</td>
              <td class="mono" style="color:var(--neo); font-weight:700;">Rs. ${Number(ord.total).toLocaleString()}</td>
              <td>Carbon-Neutral Express</td>
              <td><span class="badge-status ${badgeClass}">${ord.status}</span></td>
              <td><a href="order_dispatch.php" class="table-action-btn">Manage</a></td>
            `;
            tbody.appendChild(tr);
          });
        }
      }
    } catch(err) {
      console.warn("Could not load stats from DB API", err);
    }
  }

  // Load logged-in admin details from localStorage or Session
  window.addEventListener('DOMContentLoaded', () => {
    loadDashboardStats();
    // Auto refresh live stats every 10 seconds
    setInterval(loadDashboardStats, 10000);

    try {
      const stored = localStorage.getItem('soba_user');
      if (stored) {
        const user = JSON.parse(stored);
        if (user.name) {
          const nameEl = document.querySelector('.admin-name');
          const avatarEl = document.querySelector('.admin-avatar');
          if (nameEl) nameEl.textContent = user.name;
          if (avatarEl) avatarEl.textContent = user.name.charAt(0).toUpperCase();
        }
      }
    } catch(e) {}
  });

  function showToast(msg) {
    const t = document.getElementById('toast');
    document.getElementById('toastMsg').textContent = msg;
    t.classList.add('show');
    clearTimeout(window._toastTimer);
    window._toastTimer = setTimeout(() => t.classList.remove('show'), 2800);
  }

  function quickSearch(q) {
    if(!q.trim()) return;
    showToast(`Filtering admin system for: "${q.trim()}"`);
  }

  function handleAdminLogout() {
    localStorage.removeItem('soba_admin');
    localStorage.removeItem('soba_user');
    sessionStorage.removeItem('soba_admin');
    sessionStorage.removeItem('soba_user');
    window.location.href = '/php/web/Soba-Project/backend/api/logout.php';
  }
</script>
<script src="../../assets/js/main.js"></script>
</body>
</html>