<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Portal — Product Catalog | Soba Marketplace</title>
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

  /* Admin Frame */
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

  .btn-primary {
    display: inline-flex; align-items: center; gap: 8px;
    background: linear-gradient(135deg, var(--neo), #8fe04f); color: var(--ink);
    border: none; border-radius: 11px; padding: 11px 22px; font-size: 13.5px; font-weight: 700;
    cursor: pointer; text-decoration: none; transition: all 0.2s;
    box-shadow: 0 10px 24px rgba(184,255,61,0.22);
  }
  .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 14px 30px rgba(184,255,61,0.32); }

  /* Filters & Action Bar */
  .catalog-controls {
    background: linear-gradient(160deg, rgba(18,61,44,.6), rgba(8,26,19,.75));
    border: 1px solid var(--glass-border);
    border-radius: 18px; padding: 18px 22px;
    display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;
    box-shadow: 0 15px 40px rgba(0,0,0,.35);
  }
  .filter-group-left { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; flex: 1; }
  
  .filter-search {
    display: flex; align-items: center;
    background: rgba(255, 255, 255, 0.05); border: 1px solid var(--line);
    border-radius: 10px; padding: 8px 14px; gap: 8px; min-width: 260px;
  }
  .filter-search input {
    background: transparent; border: none; outline: none;
    color: #fff; font-size: 13px; width: 100%; font-family: 'Work Sans', sans-serif;
  }
  .filter-search input::placeholder { color: rgba(255,255,255,0.4); }

  .filter-select {
    background: rgba(255, 255, 255, 0.05); border: 1px solid var(--line);
    border-radius: 10px; padding: 8px 14px; color: #fff; font-size: 13px;
    outline: none; font-family: 'Work Sans', sans-serif; cursor: pointer;
  }
  .filter-select option { background: #0c2b20; color: #fff; }

  /* Products Data Table */
  .table-card {
    background: linear-gradient(160deg, rgba(18,61,44,.6), rgba(8,26,19,.75));
    border: 1px solid var(--glass-border);
    border-radius: 20px; padding: 24px;
    box-shadow: 0 20px 50px rgba(0,0,0,.35);
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

  .prod-cell { display: flex; align-items: center; gap: 12px; }
  .prod-thumb {
    width: 48px; height: 48px; border-radius: 10px; background: rgba(184,255,61,0.12);
    display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;
    overflow: hidden; border: 1px solid rgba(255,255,255,0.08);
  }
  .prod-thumb img {
    width: 100%; height: 100%; object-fit: cover;
  }
  .prod-info-title { font-weight: 600; color: #fff; margin-bottom: 2px; }
  .prod-info-sku { font-size: 11.5px; color: rgba(244,242,233,0.5); font-family: 'IBM Plex Mono', monospace; }

  .eco-cert-badge {
    background: rgba(184, 255, 61, 0.12); border: 1px solid rgba(184, 255, 61, 0.3);
    color: var(--neo); font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 6px;
    display: inline-block;
  }

  .stock-status-pill {
    display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600;
  }
  .stock-status-pill.in-stock { color: var(--neo); }
  .stock-status-pill.low-stock { color: #FFA07A; }

  .btn-action-icon {
    background: rgba(255,255,255,0.06); border: 1px solid var(--line);
    color: #fff; padding: 6px 10px; border-radius: 8px; font-size: 12px; font-weight: 600;
    cursor: pointer; transition: all 0.18s; display: inline-flex; align-items: center; gap: 4px;
  }
  .btn-action-icon:hover { border-color: var(--neo); color: var(--neo); }
  .btn-action-icon.delete:hover { border-color: #FFA07A; color: #FFA07A; }

  /* Modal Add / Edit Product */
  .modal-overlay {
    position: fixed; inset: 0; z-index: 100;
    background: rgba(5, 20, 15, 0.85); backdrop-filter: blur(12px);
    display: none; align-items: center; justify-content: center; padding: 20px;
  }
  .modal-overlay.open { display: flex; }

  .modal-card {
    background: linear-gradient(160deg, rgba(18,61,44,.95), rgba(8,26,19,.98));
    border: 1px solid var(--glass-border); border-radius: 22px;
    width: 100%; max-width: 620px; padding: 32px;
    box-shadow: 0 30px 80px rgba(0,0,0,0.6);
    position: relative; max-height: 90vh; overflow-y: auto;
  }
  .modal-header {
    display: flex; justify-content: space-between; align-items: center;
    border-bottom: 1px solid var(--line); padding-bottom: 16px; margin-bottom: 22px;
  }
  .modal-header h2 { font-size: 22px; color: #fff; }
  .modal-close-btn {
    background: none; border: none; color: rgba(255,255,255,0.6); font-size: 24px;
    cursor: pointer; line-height: 1; padding: 4px;
  }
  .modal-close-btn:hover { color: var(--neo); }

  .form-grid-modal { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
  .modal-group { display: flex; flex-direction: column; gap: 6px; }
  .modal-group.full { grid-column: 1 / -1; }
  .modal-label { font-size: 12px; font-weight: 600; color: rgba(244,242,233,0.85); }
  .modal-input {
    background: rgba(255,255,255,0.06); border: 1px solid var(--line);
    border-radius: 10px; padding: 10px 14px; color: #fff; font-size: 13.5px;
    font-family: 'Work Sans', sans-serif; outline: none;
  }
  .modal-input:focus { border-color: var(--neo); box-shadow: 0 0 12px rgba(184,255,61,0.2); }
  select.modal-input option { background: #0c2b20; color: #fff; }

  .modal-footer {
    display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;
    border-top: 1px solid var(--line); padding-top: 18px;
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

  @media (max-width: 900px) {
    .admin-shell { grid-template-columns: 1fr; width: 100%; max-width: 100vw; overflow-x: hidden; }
    .admin-sidebar { display: none; }
    .admin-content { padding: 14px 12px 60px; width: 100%; max-width: 100vw; overflow-x: hidden; box-sizing: border-box; }
    .header-bar { padding: 0 4px; flex-wrap: wrap; gap: 8px; }
    .catalog-controls { flex-direction: column; align-items: stretch; padding: 14px 12px; gap: 10px; }
    .filter-group-left { flex-direction: column; align-items: stretch; width: 100%; }
    .filter-search { min-width: 100%; box-sizing: border-box; }
    .filter-select { width: 100%; box-sizing: border-box; }
    .table-card { padding: 12px 8px; border-radius: 14px; width: 100%; box-sizing: border-box; overflow-x: hidden; }
    .data-table-wrap { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; display: block; }
    table.admin-table { min-width: 620px; }
  }
  @media (max-width: 600px) {
    .page-title-bar { flex-direction: column; align-items: stretch; gap: 12px; }
    .page-title-bar h1 { font-size: 22px; }
    .btn-primary { width: 100%; justify-content: center; }
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
      <a href="products_manage.php" class="sidebar-link active">
        <div class="sidebar-link-left">
          <svg class="icon" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-2z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
          <span>Products Manage</span>
        </div>
        <span class="link-badge" id="productCountBadge">38</span>
      </a>
      <a href="inventory_stock.php" class="sidebar-link">
        <div class="sidebar-link-left">
          <svg class="icon" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
          <span>Inventory & Stock</span>
        </div>
        <span class="link-badge" style="background:rgba(200,109,81,0.25); color:#FFA07A;" id="sideStockBadge">0 Low</span>
      </a>
    </div>

    <div class="nav-group">
      <div class="nav-heading">Fulfillment</div>
      <a href="order_dispatch.php" class="sidebar-link">
        <div class="sidebar-link-left">
          <svg class="icon" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
          <span>Order Dispatch</span>
        </div>
        <span class="link-badge" style="background:var(--neo); color:var(--ink);" id="sideDispatchBadge">0 Active</span>
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
        <h1>Zero-Waste Product Catalog</h1>
        <p>Add, edit, categorise and manage inventory pricing for sustainable lifestyle items.</p>
      </div>
      <button type="button" class="btn-primary" onclick="openAddProductModal()">
        <svg class="icon" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        <span>Add New Eco Product</span>
      </button>
    </div>

    <!-- Filter & Search Controls -->
    <div class="catalog-controls">
      <div class="filter-group-left">
        <div class="filter-search">
          <svg class="icon" viewBox="0 0 24 24" style="color:rgba(255,255,255,0.4);"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input type="text" id="prodSearchInput" placeholder="Filter product name or SKU..." oninput="filterTable()">
        </div>

        <select id="categorySelect" class="filter-select" onchange="filterTable()">
          <option value="ALL">All Categories</option>
          <option value="Personal Care">Personal Care</option>
          <option value="Kitchen & Dining">Kitchen & Dining</option>
          <option value="Eco Lifestyle">Eco Lifestyle</option>
        </select>

        <select id="stockSelect" class="filter-select" onchange="filterTable()">
          <option value="ALL">All Stock Levels</option>
          <option value="IN_STOCK">In Stock (> 10 units)</option>
          <option value="LOW_STOCK">Low Stock (≤ 10 units)</option>
        </select>
      </div>

      <div style="font-size: 13px; color: rgba(244,242,233,0.65);">
        Showing <strong style="color:var(--neo);" id="showingCount">5</strong> products
      </div>
    </div>

    <!-- Products Table -->
    <div class="table-card">
      <div class="data-table-wrap">
        <table class="admin-table" id="productsTable">
          <thead>
            <tr>
              <th>Product Details</th>
              <th>Category</th>
              <th>Eco Standard</th>
              <th>Price (LKR)</th>
              <th>Available Stock</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="productTableBody">
            <tr>
              <td colspan="7" style="text-align:center; padding:30px; color:rgba(244,242,233,0.5);">
                Loading zero-waste products catalog from database...
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </main>
</div>

<!-- Add / Edit Product Modal -->
<div class="modal-overlay" id="productModal">
  <div class="modal-card">
    <div class="modal-header">
      <h2 id="modalTitle">Add New Eco Product</h2>
      <button type="button" class="modal-close-btn" onclick="closeProductModal()">&times;</button>
    </div>

    <form id="productForm" onsubmit="saveProductForm(event)">
      <input type="hidden" id="mId" value="">
      <div class="form-grid-modal">

        <div class="modal-group full">
          <label class="modal-label" for="mName">Product Name</label>
          <input type="text" class="modal-input" id="mName" placeholder="e.g. Copper Water Bottle 750ml" required>
        </div>

        <div class="modal-group">
          <label class="modal-label" for="mCategory">Category</label>
          <select class="modal-input" id="mCategory" required>
            <option value="Personal Care">Personal Care</option>
            <option value="Kitchen & Dining">Kitchen & Dining</option>
            <option value="Eco Lifestyle">Eco Lifestyle</option>
          </select>
        </div>

        <div class="modal-group">
          <label class="modal-label" for="mSku">SKU Code</label>
          <input type="text" class="modal-input" id="mSku" placeholder="e.g. ECO-BOT-0610" required>
        </div>

        <div class="modal-group">
          <label class="modal-label" for="mPrice">Regular Price (LKR)</label>
          <input type="number" class="modal-input" id="mPrice" placeholder="e.g. 2400" min="50" required>
        </div>

        <div class="modal-group">
          <label class="modal-label" for="mStock">Stock Quantity</label>
          <input type="number" class="modal-input" id="mStock" placeholder="e.g. 35" min="0" required>
        </div>

        <div class="modal-group">
          <label class="modal-label" for="mEco">Eco Certification Tag</label>
          <input type="text" class="modal-input" id="mEco" placeholder="e.g. 100% Recyclable Pure Copper" required>
          <input type="hidden" id="mIcon" value="🌿">
        </div>

        <div class="modal-group full" style="border-top:1px dashed var(--line); padding-top:14px; margin-top:6px;">
          <label class="modal-label" style="display:flex; justify-content:space-between; align-items:center;">
            <span>📸 Upload Product Images (Hold Ctrl/Shift to choose multiple)</span>
            <span style="font-size:11px; color:var(--neo); font-weight:700;" id="fileCountIndicator">0 files selected</span>
          </label>
          <div style="display:flex; gap:10px; align-items:center;">
            <input type="file" class="modal-input" id="mImageFiles" accept="image/*" multiple onchange="handleImageFileSelect(this)" style="padding:8px 12px; flex:1;">
            <button type="button" class="btn-action-icon" onclick="clearSelectedUploads()" style="padding:9px 12px; white-space:nowrap;">Clear Files</button>
          </div>
          <div style="font-size:11px; color:rgba(244,242,233,0.55); margin-top:3px;">
            💡 Tip: Hold <kbd style="background:rgba(255,255,255,0.15); padding:1px 5px; border-radius:3px;">Ctrl</kbd> or <kbd style="background:rgba(255,255,255,0.15); padding:1px 5px; border-radius:3px;">Shift</kbd> in file dialog to select multiple images at once.
          </div>
        </div>

        <div class="modal-group full">
          <label class="modal-label" for="mImageUrls">
            🌐 Or Paste Image URLs <span style="font-size:11px; color:rgba(255,255,255,0.45); font-weight:normal;">(One link per line for gallery)</span>
          </label>
          <textarea class="modal-input" id="mImageUrls" rows="2" placeholder="https://images.unsplash.com/...&#10;https://example.com/item.jpg" style="resize:vertical;" oninput="syncUrlsFromTextarea()"></textarea>
        </div>

        <div class="modal-group full">
          <label class="modal-label" style="display:flex; justify-content:space-between;">
            <span>Images Preview & Active Gallery</span>
            <span id="previewCountBadge" style="font-size:11px; color:var(--neo);"></span>
          </label>
          <div id="modalImagePreviewStrip" style="display:flex; gap:10px; flex-wrap:wrap; min-height:56px; background:rgba(0,0,0,0.25); border:1px solid var(--line); border-radius:10px; padding:10px; align-items:center;">
            <span style="font-size:12px; color:rgba(244,242,233,0.4);">No images selected yet.</span>
          </div>
        </div>

      <div class="modal-footer">
        <button type="button" class="btn-action-icon" style="padding:10px 18px;" onclick="closeProductModal()">Cancel</button>
        <button type="submit" class="btn-primary">Save to Catalog</button>
      </div>
    </form>
  </div>
</div>

<script>
  let adminProducts = [];

  function showToast(msg) {
    const t = document.getElementById('toast');
    document.getElementById('toastMsg').textContent = msg;
    t.classList.add('show');
    clearTimeout(window._toastTimer);
    window._toastTimer = setTimeout(() => t.classList.remove('show'), 2800);
  }

  async function loadAdminProducts() {
    try {
      const res = await fetch('/php/web/Soba-Project/backend/api/customer/get_products.php');
      const data = await res.json();
      if (data && data.success && Array.isArray(data.products)) {
        adminProducts = data.products;
        renderAdminTable(adminProducts);
        const badge = document.getElementById('productCountBadge');
        if (badge) badge.textContent = adminProducts.length;
      }
    } catch(err) {
      console.warn("Failed to fetch products from DB", err);
    }

    try {
      const statsRes = await fetch('/php/web/Soba-Project/backend/api/admin/dashboard_stats.php');
      const statsData = await statsRes.json();
      if (statsData && statsData.success && statsData.metrics) {
        const m = statsData.metrics;
        const sideStock = document.getElementById('sideStockBadge');
        if (sideStock) sideStock.textContent = m.low_stock + ' Low';
        const sideDispatch = document.getElementById('sideDispatchBadge');
        if (sideDispatch) sideDispatch.textContent = (m.in_dispatch + m.pending_packing) + ' Active';
      }
    } catch(err) {
      console.warn("Failed to fetch dashboard stats", err);
    }
  }

  let selectedFileObjects = [];
  let currentGalleryUrls = [];

  function handleImageFileSelect(input) {
    if (input.files && input.files.length > 0) {
      const incoming = Array.from(input.files);
      incoming.forEach(f => {
        if (!selectedFileObjects.some(existing => existing.name === f.name && existing.size === f.size)) {
          selectedFileObjects.push(f);
        }
      });
    }
    renderGalleryPreview();
  }

  function removeSelectedFile(index) {
    if (index >= 0 && index < selectedFileObjects.length) {
      selectedFileObjects.splice(index, 1);
      renderGalleryPreview();
    }
  }

  function removeGalleryUrl(index) {
    if (index >= 0 && index < currentGalleryUrls.length) {
      currentGalleryUrls.splice(index, 1);
      document.getElementById('mImageUrls').value = currentGalleryUrls.join('\n');
      renderGalleryPreview();
    }
  }

  function clearSelectedUploads() {
    selectedFileObjects = [];
    const fileInput = document.getElementById('mImageFiles');
    if (fileInput) fileInput.value = '';
    renderGalleryPreview();
  }

  function syncUrlsFromTextarea() {
    const rawUrls = document.getElementById('mImageUrls')?.value || '';
    currentGalleryUrls = rawUrls.split('\n')
      .map(u => u.trim())
      .filter(u => u.length > 0);
    renderGalleryPreview();
  }

  function renderGalleryPreview() {
    const strip = document.getElementById('modalImagePreviewStrip');
    if (!strip) return;
    strip.innerHTML = '';

    let totalCount = 0;

    // Update count indicator
    const fileCountBadge = document.getElementById('fileCountIndicator');
    if (fileCountBadge) {
      fileCountBadge.textContent = selectedFileObjects.length > 0 
        ? `${selectedFileObjects.length} file(s) queued for upload`
        : '0 files selected';
    }

    // 1. Existing or pasted Image URLs
    currentGalleryUrls.forEach((url, i) => {
      totalCount++;
      const box = document.createElement('div');
      box.style.cssText = 'position:relative; width:62px; height:62px; border-radius:8px; overflow:hidden; border:2px solid var(--neo); background:#0c2b20; flex-shrink:0;';
      box.innerHTML = `
        <img src="${url}" alt="Image ${i+1}" style="width:100%; height:100%; object-fit:cover;" onerror="this.src=''; this.alt='Failed'; this.style.display='none'; this.nextElementSibling.style.display='flex';">
        <span style="display:none; width:100%; height:100%; align-items:center; justify-content:center; font-size:10px; color:#ff8080; text-align:center; padding:2px;">⚠️ Broken</span>
        <span style="position:absolute; bottom:0; left:0; right:0; background:rgba(0,0,0,0.7); font-size:9px; text-align:center; color:var(--neo); font-weight:700;">#${i+1}</span>
      `;

      // Delete button for this image URL
      const delBtn = document.createElement('button');
      delBtn.type = 'button';
      delBtn.innerHTML = '✕';
      delBtn.title = 'Remove this image';
      delBtn.style.cssText = 'position:absolute; top:2px; right:2px; width:18px; height:18px; background:rgba(220,38,38,0.9); color:#fff; border:none; border-radius:50%; font-size:10px; cursor:pointer; display:flex; align-items:center; justify-content:center; line-height:1; font-weight:bold; z-index:2;';
      delBtn.onclick = (e) => {
        e.stopPropagation();
        removeGalleryUrl(i);
      };
      box.appendChild(delBtn);

      strip.appendChild(box);
    });

    // 2. Newly selected local files for upload
    selectedFileObjects.forEach((file, i) => {
      totalCount++;
      const box = document.createElement('div');
      box.style.cssText = 'position:relative; width:62px; height:62px; border-radius:8px; overflow:hidden; border:2px dashed #FFA07A; background:#0c2b20; flex-shrink:0;';
      
      const img = document.createElement('img');
      img.style.cssText = 'width:100%; height:100%; object-fit:cover;';
      img.title = 'Upload: ' + file.name;
      img.src = URL.createObjectURL(file);
      box.appendChild(img);

      const label = document.createElement('span');
      label.style.cssText = 'position:absolute; bottom:0; left:0; right:0; background:rgba(200,80,30,0.85); font-size:9px; text-align:center; color:#fff; font-weight:700;';
      label.textContent = 'Upload';
      box.appendChild(label);

      // Delete button for this upload
      const delBtn = document.createElement('button');
      delBtn.type = 'button';
      delBtn.innerHTML = '✕';
      delBtn.title = 'Remove ' + file.name;
      delBtn.style.cssText = 'position:absolute; top:2px; right:2px; width:18px; height:18px; background:rgba(220,38,38,0.9); color:#fff; border:none; border-radius:50%; font-size:10px; cursor:pointer; display:flex; align-items:center; justify-content:center; line-height:1; font-weight:bold; z-index:2;';
      delBtn.onclick = (e) => {
        e.stopPropagation();
        removeSelectedFile(i);
      };
      box.appendChild(delBtn);

      strip.appendChild(box);
    });

    const previewBadge = document.getElementById('previewCountBadge');
    if (previewBadge) {
      previewBadge.textContent = totalCount > 0 ? `Total: ${totalCount} image(s)` : '';
    }

    if (totalCount === 0) {
      strip.innerHTML = '<span style="font-size:12px; color:rgba(244,242,233,0.4);">No images selected yet. Choose files or paste image URLs.</span>';
    }
  }

  function renderAdminTable(items) {
    const tbody = document.getElementById('productTableBody');
    tbody.innerHTML = '';

    items.forEach(p => {
      const tr = document.createElement('tr');
      tr.setAttribute('data-id', p.id);
      tr.setAttribute('data-cat', p.category_name || p.category);
      tr.setAttribute('data-stock', p.stock_qty);

      const stockHtml = p.stock_qty <= 10 
        ? `<span class="stock-status-pill low-stock"><span>⚠️ ${p.stock_qty} units left</span></span>`
        : `<span class="stock-status-pill in-stock"><span>● ${p.stock_qty} units</span></span>`;

      // Check if product has real images in DB
      const primaryImg = (p.images && p.images.length > 0) ? p.images[0] : (p.image || null);
      const thumbContent = primaryImg
        ? `<img src="${primaryImg}" alt="${p.name}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"><span style="display:none;">${p.icon || '🌿'}</span>`
        : `<span>${p.icon || '🌿'}</span>`;

      tr.innerHTML = `
        <td>
          <div class="prod-cell">
            <div class="prod-thumb">${thumbContent}</div>
            <div>
              <div class="prod-info-title">${p.name}</div>
              <div class="prod-info-sku">SKU: ${p.sku || 'ECO-00' + p.id}</div>
            </div>
          </div>
        </td>
        <td>${p.category_name || p.category}</td>
        <td><span class="eco-cert-badge">${p.cert || 'Eco Friendly'}</span></td>
        <td class="mono" style="color:var(--neo); font-weight:700;">Rs. ${Number(p.price).toLocaleString()}</td>
        <td>${stockHtml}</td>
        <td><span style="color:var(--neo); font-weight:700; font-size:12px;">Active</span></td>
        <td>
          <button type="button" class="btn-action-icon" onclick="editProductRow(this)">Edit</button>
          <button type="button" class="btn-action-icon delete" onclick="deleteProductRow(this)">Delete</button>
        </td>
      `;
      tbody.appendChild(tr);
    });

    filterTable();
  }

  function openAddProductModal() {
    document.getElementById('modalTitle').textContent = 'Add New Eco Product';
    document.getElementById('productForm').reset();
    document.getElementById('mId').value = '';
    currentGalleryUrls = [];
    selectedFileObjects = [];
    document.getElementById('mImageUrls').value = '';
    const fileInput = document.getElementById('mImageFiles');
    if (fileInput) fileInput.value = '';
    renderGalleryPreview();
    document.getElementById('productModal').classList.add('open');
  }

  function closeProductModal() {
    document.getElementById('productModal').classList.remove('open');
  }

  async function saveProductForm(e) {
    e.preventDefault();
    const id = document.getElementById('mId').value;
    const name = document.getElementById('mName').value.trim();
    const cat = document.getElementById('mCategory').value;
    const sku = document.getElementById('mSku').value.trim();
    const price = document.getElementById('mPrice').value;
    const stock = parseInt(document.getElementById('mStock').value) || 0;
    const eco = document.getElementById('mEco').value.trim();
    const icon = document.getElementById('mIcon').value.trim() || '🌿';

    // Use FormData to support multipart file uploads seamlessly
    const formData = new FormData();
    if (id) formData.append('id', id);
    formData.append('name', name);
    formData.append('category', cat);
    formData.append('sku', sku);
    formData.append('price', price);
    formData.append('stock', stock);
    formData.append('eco', eco);
    formData.append('icon', icon);
    formData.append('image_urls', currentGalleryUrls.join('\n'));
    formData.append('replace_images', '1');

    // Append all selected files from queue
    if (selectedFileObjects.length > 0) {
      for (let i = 0; i < selectedFileObjects.length; i++) {
        formData.append('images[]', selectedFileObjects[i]);
      }
    }

    try {
      const res = await fetch('/php/web/Soba-Project/backend/api/admin/add_product.php', {
        method: 'POST',
        body: formData
      });
      const data = await res.json();
      if (data && data.success) {
        showToast(`Product "${name}" saved to database successfully!`);
        closeProductModal();
        await loadAdminProducts();
      } else {
        showToast(data.error || 'Failed to save product');
      }
    } catch(err) {
      console.error(err);
      showToast('Error communicating with database');
    }
  }

  async function deleteProductRow(btn) {
    const row = btn.closest('tr');
    const id = row.getAttribute('data-id');
    const name = row.querySelector('.prod-info-title').textContent;

    const confirmed = await sobaConfirm(
      `Are you sure you want to remove "${name}" from the database catalog? This action will permanently remove all associated gallery images.`,
      "Delete Product",
      true
    );

    if (confirmed) {
      try {
        const res = await fetch('/php/web/Soba-Project/backend/api/admin/delete_product.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: parseInt(id) })
        });
        const data = await res.json();
        if (data && data.success) {
          showToast(`"${name}" successfully deleted from catalog.`);
          await loadAdminProducts();
        } else {
          showToast(data.error || 'Failed to delete from database');
        }
      } catch(err) {
        console.error(err);
        row.remove();
        filterTable();
        showToast('Product removed locally.');
      }
    }
  }

  function editProductRow(btn) {
    const row = btn.closest('tr');
    const id = row.getAttribute('data-id');
    const p = adminProducts.find(item => item.id == id);

    const name = p ? p.name : row.querySelector('.prod-info-title').textContent;
    const sku = p ? (p.sku || '') : row.querySelector('.prod-info-sku').textContent.replace('SKU: ', '');
    const cat = p ? (p.category_name || p.category) : row.getAttribute('data-cat');
    const price = p ? p.price : row.querySelector('.mono').textContent.replace('Rs. ', '').replace(/,/g, '');
    const stock = p ? p.stock_qty : row.getAttribute('data-stock');
    const eco = p ? p.cert : row.querySelector('.eco-cert-badge').textContent;
    const icon = p ? p.icon : (row.querySelector('.prod-thumb span')?.textContent || '🌿');

    document.getElementById('modalTitle').textContent = 'Edit Eco Product';
    document.getElementById('mId').value = id || '';
    document.getElementById('mName').value = name;
    
    // Select category if option exists
    const catSelect = document.getElementById('mCategory');
    let matched = false;
    for (let opt of catSelect.options) {
      if (opt.value.toLowerCase() === cat.toLowerCase()) {
        opt.selected = true;
        matched = true;
        break;
      }
    }
    if (!matched) {
      const opt = new Option(cat, cat);
      catSelect.add(opt);
      opt.selected = true;
    }

    document.getElementById('mSku').value = sku;
    document.getElementById('mPrice').value = price;
    document.getElementById('mStock').value = stock;
    document.getElementById('mEco').value = eco;
    document.getElementById('mIcon').value = icon;

    // Load existing images
    selectedFileObjects = [];
    const fileInput = document.getElementById('mImageFiles');
    if (fileInput) fileInput.value = '';

    currentGalleryUrls = (p && Array.isArray(p.images)) ? [...p.images] : [];
    document.getElementById('mImageUrls').value = currentGalleryUrls.join('\n');
    renderGalleryPreview();

    document.getElementById('productModal').classList.add('open');
  }

  function filterTable() {
    const q = document.getElementById('prodSearchInput').value.toLowerCase();
    const cat = document.getElementById('categorySelect').value;
    const stockMode = document.getElementById('stockSelect').value;

    const rows = document.querySelectorAll('#productTableBody tr');
    let visible = 0;

    rows.forEach(tr => {
      const text = tr.textContent.toLowerCase();
      const rowCat = tr.getAttribute('data-cat') || '';
      const rowStock = parseInt(tr.getAttribute('data-stock')) || 0;

      let matchText = text.includes(q);
      let matchCat = (cat === 'ALL' || rowCat.toLowerCase().includes(cat.toLowerCase()) || cat.toLowerCase().includes(rowCat.toLowerCase()));
      let matchStock = true;

      if(stockMode === 'IN_STOCK') matchStock = (rowStock > 10);
      else if(stockMode === 'LOW_STOCK') matchStock = (rowStock <= 10);

      if(matchText && matchCat && matchStock) {
        tr.style.display = '';
        visible++;
      } else {
        tr.style.display = 'none';
      }
    });

    const showing = document.getElementById('showingCount');
    if (showing) showing.textContent = visible;
  }

  function handleAdminLogout() {
    localStorage.removeItem('soba_admin');
    sessionStorage.removeItem('soba_admin');
    window.location.href = '/php/web/Soba-Project/backend/api/logout.php';
  }

  window.addEventListener('DOMContentLoaded', () => {
    loadAdminProducts();
  });
</script>
<script src="../../assets/js/main.js"></script>
</body>
</html>
