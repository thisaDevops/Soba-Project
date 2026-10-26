<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Products — Soba Zero-Waste Marketplace</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..700;1,9..144,400..600&family=Work+Sans:wght@400;500;600;700;800&family=IBM+Plex+Mono:wght@500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../../assets/css/style.css">
<style>
  :root {
    --forest: #3B7A34;
    --forest-dim: #2C5D27;
    --forest-bright: #5FAE47;
    --sand: #FFFFFF;
    --sand-deep: #F1F0E9;
    --clay: #C86D51;
    --clay-soft: #E9B7A4;
    --lime: #A8E070;
    --sage: #768A6E;
    --ink: #2B2D2F;
    --paper: #F9F8F3;
    --line: #E2DFD7;
    
    /* Eco-Glass V2 Variables */
    --neo: #b8ff6a;
    --deep: #0b2b20;
    --deep2: #123d2c;
    --glass: rgba(255, 255, 255, 0.06);
    --glass-line: rgba(255, 255, 255, 0.14);
    --white-soft: rgba(255, 255, 255, 0.82);
  }

  * { box-sizing: border-box; }
  html { scroll-behavior: smooth; }
  
  body {
    margin: 0;
    background: #071c15;
    color: #fff;
    font-family: 'Work Sans', sans-serif;
    -webkit-font-smoothing: antialiased;
    overflow-x: hidden;
  }

  body::before {
    content: "";
    position: fixed;
    inset: 0;
    z-index: -5;
    background:
      radial-gradient(circle at 15% 15%, rgba(184,255,106,.12), transparent 32%),
      radial-gradient(circle at 85% 25%, rgba(56,189,248,.08), transparent 30%),
      radial-gradient(circle at 50% 90%, rgba(200,109,81,.12), transparent 35%),
      linear-gradient(135deg, #04120e 0%, #082219 48%, #051812 100%);
  }

  #grain {
    position: fixed; inset: 0; z-index: 9998; pointer-events: none; opacity: 0.035; mix-blend-mode: screen;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='140' height='140'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
  }

  #spotlight { display: none !important; }

  h1, h2, h3, .display { font-family: 'Fraunces', serif; font-weight: 500; margin: 0; }
  .mono { font-family: 'IBM Plex Mono', monospace; }

  /* ===== Header Styling ===== */
  header { position: sticky; top: 0; z-index: 50; }
  
  .topbar {
    background: rgba(184, 255, 106, 0.08);
    border-bottom: 1px solid rgba(184, 255, 106, 0.12);
    backdrop-filter: blur(14px);
    height: 34px; display: flex; align-items: center; overflow: hidden;
  }
  .marquee-track {
    display: flex; gap: 60px; white-space: nowrap; animation: marquee 40s linear infinite;
    font-size: 12.5px; color: #dfffd0; padding-left: 100%; font-weight: 500;
  }
  @keyframes marquee { from { transform: translateX(0); } to { transform: translateX(-100%); } }

  .header-top {
    background: rgba(7, 28, 21, 0.65); border-bottom: 1px solid rgba(255, 255, 255, 0.08); backdrop-filter: blur(20px);
  }
  .header-top .bar {
    max-width: 1480px; 
    margin: 0 auto; 
    padding: 14px 28px; 
    display: flex;             
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    flex-wrap: nowrap;
  }
  .logo { 
    display: flex; 
    align-items: baseline; 
    gap: 8px; 
    text-decoration: none; 
    justify-self: start;
  }
  .logo .mark { font-family: 'Fraunces', serif; font-weight: 600; font-size: 26px; color: #f5fff1; text-shadow: 0 0 24px rgba(184,255,106,.18); }
  .logo .sub { font-size: 11px; letter-spacing: 0.14em; text-transform: uppercase; color: var(--neo); font-weight: 600; }

  .search-group {
    width: 100%;
    max-width: 640px; 
    display: flex; 
    height: 46px; 
    border-radius: 14px; 
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.14); 
    background: rgba(255, 255, 255, 0.06);
    justify-self: center;
    transition: border-color 0.2s;
  }
  .search-group:focus-within { border-color: var(--neo); box-shadow: 0 0 15px rgba(184, 255, 106, 0.2); }
  .search-group input {
    flex: 1; border: none; padding: 0 18px; font-size: 14px; color: #fff; background: transparent; font-family: 'Work Sans', sans-serif;
  }
  .search-group input::placeholder { color: rgba(255, 255, 255, 0.45); }
  .search-group input:focus { outline: none; }
  .search-go { background: var(--neo); border: none; width: 56px; cursor: pointer; font-size: 16px; color: #102117; transition: opacity 0.2s; }
  .search-go:hover { opacity: 0.88; }

  .bar-right { 
    display: flex; 
    align-items: center; 
    gap: 16px; 
    justify-self: end;
  }
  .cart-btn {
    background: rgba(255, 255, 255, 0.06); color: #fff; border: 1px solid rgba(184, 255, 106, 0.35);
    border-radius: 999px; padding: 10px 20px; font-size: 14px; font-weight: 600; cursor: pointer;
    display: flex; align-items: center; gap: 8px; font-family: 'Work Sans', sans-serif; transition: all 0.2s;
    text-decoration: none;
  }
  .cart-btn:hover { background: rgba(184, 255, 106, 0.15); border-color: var(--neo); transform: translateY(-1px); }
  .cart-count {
    background: var(--neo); color: #102117; border-radius: 999px; font-size: 11px;
    min-width: 20px; height: 20px; display: flex; align-items: center; justify-content: center;
    padding: 0 6px; font-weight: 800; font-family: 'IBM Plex Mono', monospace;
  }

  .header-nav {
    background: rgba(6, 23, 17, 0.55);
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    backdrop-filter: blur(16px);
  }
  .header-nav .bar {
    max-width: 1480px;
    margin: 0 auto;
    padding: 0 28px;
    display: flex;
    align-items: center;
    height: 44px;
  }
  .header-nav nav {
    position: relative;
    display: flex;
    gap: 32px;
    align-items: center;
    height: 100%;
  }
  .header-nav nav a {
    color: rgba(255, 255, 255, 0.65);
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    position: relative;
    padding-bottom: 6px;
    transition: color 0.2s;
  }
  .header-nav nav a:hover, .header-nav nav a.active {
    color: var(--neo);
  }
  .nav-indicator {
    position: absolute;
    bottom: 0px;
    left: 0;
    height: 2.5px;
    background: var(--neo, #b8ff6a);
    box-shadow: 0 0 14px rgba(184,255,106,.5);
    border-radius: 2px;
    pointer-events: none;
    transition: transform 0.28s cubic-bezier(.4,0,.2,1), width 0.28s cubic-bezier(.4,0,.2,1), opacity 0.2s ease;
    z-index: 10;
    width: 0;
    transform: translateX(0);
    opacity: 0;
  }

  /* ===== Hero Banner ===== */
  .page-banner { max-width: 1420px; margin: 30px auto 15px; padding: 0 28px; }
  .page-banner-card {
    position: relative; overflow: hidden;
    background: linear-gradient(135deg, rgba(184, 255, 106, 0.12) 0%, rgba(12, 45, 33, 0.4) 50%, rgba(255, 255, 255, 0.02) 100%);
    border: 1px solid rgba(184, 255, 106, 0.2); border-radius: 28px; padding: 46px 48px; backdrop-filter: blur(20px);
    box-shadow: 0 30px 60px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.15);
  }
  .page-banner-card::after {
    content: "SOBA"; position: absolute; right: -20px; bottom: -30px; font-size: 160px; font-weight: 900;
    color: rgba(255,255,255,0.02); font-family: 'Fraunces', serif; pointer-events: none; letter-spacing: -0.05em;
  }
  .page-banner-card .eyebrow {
    display: inline-flex; align-items: center; gap: 6px;
    background: rgba(184, 255, 106, 0.15); border: 1px solid rgba(184, 255, 106, 0.3);
    padding: 6px 14px; border-radius: 999px; font-size: 11px; letter-spacing: 0.16em; text-transform: uppercase; color: var(--neo); font-weight: 700; margin-bottom: 14px;
  }
  .page-banner-card h1 { font-size: 48px; color: #f5fff1; font-weight: 600; line-height: 1.1; }
  .page-banner-card p { color: rgba(240, 255, 246, 0.78); max-width: 620px; font-size: 15px; margin-top: 12px; line-height: 1.6; }

  /* ===== Shop & Filter Shell ===== */
  .shop-shell { max-width: 1420px; margin: 0 auto; padding: 20px 28px 100px; }
  
  .filter-row {
    display: flex; gap: 16px; flex-wrap: wrap; align-items: center; justify-content: space-between;
    margin-bottom: 35px; border-top: 1px solid rgba(255,255,255,0.10); border-bottom: 1px solid rgba(255,255,255,0.10);
    padding: 18px 0;
  }
  .filter-group { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
  .filter-group-label { font-size: 11px; text-transform: uppercase; letter-spacing: 0.1em; color: rgba(255,255,255,0.5); font-weight: 700; margin-right: 6px; }
  
  .chip {
    border: 1px solid rgba(255,255,255,0.14); background: rgba(255,255,255,0.04); color: rgba(255,255,255,0.85);
    border-radius: 999px; padding: 9px 20px; font-size: 13px; cursor: pointer; backdrop-filter: blur(10px);
    font-family: 'Work Sans', sans-serif; font-weight: 600; transition: all 0.25s ease;
  }
  .chip:hover { border-color: var(--neo); color: #fff; background: rgba(184, 255, 106, 0.1); }
  .chip.active { background: var(--neo); color: #081d14; border-color: var(--neo); font-weight: 700; box-shadow: 0 0 20px rgba(184, 255, 106, 0.35); }

  .select-filter {
    border: 1px solid rgba(255,255,255,0.14); background: rgba(7, 28, 21, 0.85); color: rgba(255,255,255,0.9);
    border-radius: 12px; padding: 10px 16px; font-size: 13px; font-family: 'Work Sans', sans-serif; cursor: pointer; font-weight: 500; outline: none;
  }
  .select-filter:focus { border-color: var(--neo); }

  .section-head { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 28px; }
  .section-head h2 { font-size: 34px; color: #f5fff2; letter-spacing: -0.02em; }
  .section-head .count { font-size: 13px; color: rgba(255,255,255,0.5); font-family: 'IBM Plex Mono', monospace; font-weight: 500; }

  /* ===== Product Grid ===== */
  .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 28px; }
  
  .card {
    position: relative;
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.07) 0%, rgba(255, 255, 255, 0.03) 100%);
    border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 22px;
    overflow: hidden; display: flex; flex-direction: column; backdrop-filter: blur(16px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.25);
    transition: transform 0.35s cubic-bezier(0.2, 0.8, 0.2, 1), box-shadow 0.35s ease, border-color 0.35s ease;
    cursor: pointer;
  }
  .card:hover {
    transform: translateY(-8px) scale(1.01);
    box-shadow: 0 30px 60px rgba(0,0,0,0.4), 0 0 30px rgba(184, 255, 106, 0.18);
    border-color: rgba(184, 255, 106, 0.45);
  }

  .card-art {
    height: 220px; display: flex; align-items: center; justify-content: center; font-size: 72px;
    background: radial-gradient(circle at 50% 50%, rgba(184, 255, 106, 0.12), transparent 70%),
                linear-gradient(160deg, rgba(255,255,255,0.05), rgba(8, 32, 24, 0.4));
    position: relative; overflow: hidden;
  }
  .card-art img {
    width: 100%; height: 100%; object-fit: cover;
    transition: transform 0.4s cubic-bezier(.4, 0, .2, 1);
  }
  .card:hover .card-art img { transform: scale(1.08); }
  .card-art .emoji-icon {
    filter: drop-shadow(0 15px 25px rgba(0,0,0,0.5));
    transition: transform 0.35s ease;
  }
  .card:hover .card-art .emoji-icon { transform: scale(1.12) rotate(-3deg); }

  .card-cert {
    position: absolute; top: 14px; left: 14px;
    background: rgba(7, 28, 21, 0.75); color: var(--neo);
    border: 1px solid rgba(184, 255, 106, 0.4); font-size: 10.5px; font-weight: 700; padding: 5px 10px; border-radius: 8px;
    letter-spacing: 0.05em; backdrop-filter: blur(10px); text-transform: uppercase;
  }
  .card-wish {
    position: absolute; top: 14px; right: 14px; background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.18);
    width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
    cursor: pointer; font-size: 15px; transition: all 0.25s; color: #fff; backdrop-filter: blur(10px);
    z-index: 2;
  }
  .card-wish:hover, .card-wish.active { background: var(--clay); border-color: var(--clay); transform: scale(1.1); }

  .poster-price-tag {
    position: absolute; bottom: 12px; right: 14px;
    background: var(--neo); color: #081d14;
    padding: 6px 14px; border-radius: 999px;
    font-family: 'IBM Plex Mono', monospace; font-weight: 700; font-size: 14px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.3);
  }

  .card-body { padding: 22px; display: flex; flex-direction: column; gap: 10px; flex: 1; background: rgba(6, 22, 16, 0.3); }
  .card-body h3 { font-size: 18px; font-weight: 700; font-family: 'Work Sans', sans-serif; color: #fff; line-height: 1.3; }
  .card-meta { font-size: 11px; color: rgba(255,255,255,0.45); text-transform: uppercase; letter-spacing: 0.08em; font-weight: 600; }
  
  .card-saved {
    display: inline-flex; align-items: center; gap: 6px; font-size: 12px; color: var(--neo); font-weight: 600;
    background: rgba(184, 255, 106, 0.08); padding: 5px 10px; border-radius: 6px; border: 1px solid rgba(184, 255, 106, 0.15); width: fit-content;
  }
  
  .card-foot { display: flex; align-items: center; justify-content: space-between; margin-top: auto; padding-top: 14px; }
  
  .add-btn {
    width: 100%;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.12), rgba(255, 255, 255, 0.05));
    color: #fff; border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px; padding: 12px; font-size: 14px; font-weight: 700; cursor: pointer;
    font-family: 'Work Sans', sans-serif; transition: all 0.25s ease; text-transform: uppercase; letter-spacing: 0.05em;
    display: flex; align-items: center; justify-content: center; gap: 6px;
    z-index: 2;
  }
  .add-btn:hover { background: var(--neo); color: #081d14; border-color: var(--neo); box-shadow: 0 0 20px rgba(184, 255, 106, 0.4); }
  .add-btn.added { background: var(--sage); color: #fff; border-color: var(--sage); }

  /* ===== Footer ===== */
  footer { background: #04110c; border-top: 1px solid rgba(255,255,255,0.08); padding: 60px 28px 24px; font-size: 13px; color: rgba(255,255,255,0.55); margin-top: 90px; }
  .footer-inner { max-width: 1420px; margin: 0 auto; padding-bottom: 40px; display: grid; grid-template-columns: 1.6fr 1fr 1fr 1fr; gap: 36px; }
  .footer-col { display: flex; flex-direction: column; gap: 12px; }
  .footer-col a { color: rgba(255,255,255,0.65); text-decoration: none; font-size: 13.5px; transition: color 0.2s; }
  .footer-col a:hover { color: var(--neo); }
  .footer-title { font-size: 12px; text-transform: uppercase; letter-spacing: 0.12em; color: var(--neo); font-weight: 700; margin-bottom: 6px; }
</style>
</head>
<body>

<div id="grain"></div>
<div id="spotlight"></div>

<!-- Top Bar -->
<div class="topbar">
  <div class="marquee-track">
    <span>🌱 100% Plastic-Free Packaging</span>
    <span>⚡ Carbon-Neutral Delivery Sri Lanka Wide</span>
    <span>♻️ Zero-Waste Certified Suppliers</span>
    <span>🌱 100% Plastic-Free Packaging</span>
  </div>
</div>

<!-- Header -->
<header id="site-header">
  <div class="header-top">
    <div class="bar">
      <a href="../../home.php" class="logo">
        <span class="mark">Soba</span>
        <span class="sub">sobadahama · nature</span>
      </a>
      
      <div class="search-group">
        <input type="text" id="productSearch" placeholder="Search eco products (e.g. Toothbrush, Soap)..." oninput="filterProducts()" onkeydown="if(event.key==='Enter') filterProducts()">
        <button class="search-go">🔍</button>
      </div>

      <div class="bar-right">
        <a href="user_dashboard.php" class="cart-btn" style="text-decoration:none;" id="productsHeaderAccount">
          👤 <span id="productsHeaderUserName">Account</span>
        </a>
        <a href="cart.php" class="cart-btn">
          🛒 <span>Cart</span>
          <span class="cart-count" id="cartCount">0</span>
        </a>
      </div>
    </div>
  </div>

  <div class="header-nav">
    <div class="bar" style="display:flex; justify-content:space-between; align-items:center;">
      <nav id="navbar">
        <a class="nav-link" href="../../home.php" data-nav="home">Home</a>
        <a class="nav-link active" href="products.php" data-nav="shop">Shop Products</a>
        <a class="nav-link" href="cart.php" data-nav="cart">My Cart</a>
        <a class="nav-link" href="../../home.php#about-section" data-nav="about">About</a>
        <a class="nav-link" href="../../home.php#reviews-section" data-nav="reviews">Reviews</a>
        <a class="nav-link" href="../../home.php#contact-section" data-nav="contact">Contact</a>
        <div class="nav-indicator" id="nav-indicator"></div>
      </nav>
      <a href="../../home.php" style="display:inline-flex; align-items:center; gap:6px; color:var(--neo); font-size:12.5px; font-weight:700; text-decoration:none; padding:5px 12px; border-radius:8px; background:rgba(184,255,106,0.08); border:1px solid rgba(184,255,106,0.25);">
        &larr; Back to Home
      </a>
    </div>
  </div>
</header>

<!-- Hero Poster Banner -->
<section class="page-banner">
  <div class="page-banner-card">
    <span class="eyebrow">✦ Sustainable Collection V2</span>
    <h1>Explore Eco Essentials</h1>
    <p>Every single item is hand-picked for maximum sustainability impact—100% biodegradable, ethically sourced, and zero-plastic guaranteed.</p>
  </div>
</section>

<!-- Main Products Section -->
<main class="shop-shell">
  <!-- Filters -->
  <div class="filter-row">
    <div class="filter-group">
      <span class="filter-group-label">Category:</span>
      <button class="chip active" onclick="setCategory('all', this)">All Items</button>
      <button class="chip" onclick="setCategory('personal', this)">Personal Care</button>
      <button class="chip" onclick="setCategory('kitchen', this)">Kitchen & Dining</button>
      <button class="chip" onclick="setCategory('stationery', this)">Stationery</button>
      <button class="chip" onclick="setCategory('lifestyle', this)">Lifestyle</button>
    </div>

    <div class="filter-group">
      <span class="filter-group-label">Sort By:</span>
      <select class="select-filter" id="sortSelect" onchange="filterProducts()">
        <option value="featured">Featured</option>
        <option value="low">Price: Low to High</option>
        <option value="high">Price: High to Low</option>
      </select>
    </div>
  </div>

  <div class="section-head">
    <h2>Catalog Showcase</h2>
    <span class="count" id="itemCount">Showing all items</span>
  </div>

  <!-- Dynamic Product Grid -->
  <div class="grid" id="productGrid"></div>
</main>

<!-- Footer -->
<footer>
  <div class="footer-inner">
    <div class="footer-col">
      <span class="footer-title">Soba Zero-Waste</span>
      <p style="color: rgba(255,255,255,0.6); line-height: 1.6;">Empowering sustainable living with high quality, plastic-free everyday essentials.</p>
    </div>
    <div class="footer-col">
      <span class="footer-title">Shop</span>
      <a href="products.php?category=personal">Personal Care</a>
      <a href="products.php?category=kitchen">Zero Waste Kitchen</a>
      <a href="products.php?category=lifestyle">Home & Living</a>
      <a href="products.php?category=stationery">Stationery</a>
    </div>
    <div class="footer-col">
      <span class="footer-title">Impact</span>
      <a href="#">Our Mission</a>
      <a href="#">Carbon Offsets</a>
      <a href="#">Supplier Ethics</a>
    </div>
    <div class="footer-col">
      <span class="footer-title">Help</span>
      <a href="#">Shipping Policy</a>
      <a href="#">Returns</a>
      <a href="#">Contact Us</a>
    </div>
  </div>
</footer>

<script>
  // Product Catalog Data - Loaded from Database
  let products = [];
  let cart = [];
  let selectedCategory = 'all';

  async function fetchProductsFromDB() {
    try {
      const res = await fetch('/php/web/Soba-Project/backend/api/customer/get_products.php');
      const data = await res.json();
      if (data && data.success && Array.isArray(data.products)) {
        products = data.products;
      }
    } catch(err) {
      console.warn("Could not fetch products from DB API, using fallback data", err);
    }
    filterProducts();
  }

  function loadSavedCart() {
    try {
      const stored = localStorage.getItem('soba_cart');
      if (stored) cart = JSON.parse(stored);
    } catch(e) {
      cart = [];
    }
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    const countEl = document.getElementById('cartCount');
    if (countEl) countEl.innerText = totalItems;
  }

  function renderProducts(items) {
    const grid = document.getElementById('productGrid');
    grid.innerHTML = '';
    
    document.getElementById('itemCount').innerText = `Showing ${items.length} items`;

    items.forEach(p => {
      const card = document.createElement('div');
      card.className = 'card';
      card.dataset.productId = p.id;
      card.setAttribute('title', 'Click to view product details');
      const primaryImg = (p.images && p.images.length > 0) ? p.images[0] : (p.image || null);
      const artMedia = primaryImg
        ? `<img src="${primaryImg}" alt="${p.name}" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';"><span class="emoji-icon" style="display:none;">${p.icon || '🌿'}</span>`
        : `<span class="emoji-icon">${p.icon || '🌿'}</span>`;

      card.innerHTML = `
        <div class="card-art">
          <span class="card-cert">${p.cert}</span>
          <button class="card-wish" onclick="toggleWish(event, this)">♥</button>
          ${artMedia}
          <span class="poster-price-tag">LKR ${p.price.toLocaleString()}</span>
        </div>
        <div class="card-body">
          <div class="card-meta">${p.category}</div>
          <h3>${p.name}</h3>
          <div class="card-saved">🌱 Saves ${p.saved}</div>
          <div class="card-foot">
            <button class="add-btn" onclick="addToCart(event, ${p.id}, this)">
              <span>Add to Cart</span> +
            </button>
          </div>
        </div>
      `;
      grid.appendChild(card);
    });
  }

  // Clicking a card directly redirects to product_details.php
  document.getElementById('productGrid').addEventListener('click', function(e) {
    // If clicked Add to Cart button or Wishlist button, don't navigate
    if (e.target.closest('button')) return;

    const card = e.target.closest('.card');
    if (!card) return;
    const id = Number(card.dataset.productId);
    if (id) {
      window.location.href = `product_details.php?id=${id}`;
    }
  });

  function setCategory(cat, btn) {
    selectedCategory = cat;
    document.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
    btn.classList.add('active');
    filterProducts();
  }

  function filterProducts() {
    const query = document.getElementById('productSearch').value.toLowerCase();
    const sort = document.getElementById('sortSelect').value;

    let filtered = products.filter(p => {
      const matchCat = selectedCategory === 'all' || p.category === selectedCategory;
      const matchSearch = p.name.toLowerCase().includes(query);
      return matchCat && matchSearch;
    });

    if (sort === 'low') filtered.sort((a,b) => a.price - b.price);
    if (sort === 'high') filtered.sort((a,b) => b.price - a.price);

    renderProducts(filtered);
  }

  function addToCart(event, id, btn) {
    if (event) event.stopPropagation(); // Stop card click navigation
    const product = products.find(p => p.id === id);
    const existing = cart.find(item => item.id === id);

    if (existing) {
      existing.quantity += 1;
    } else {
      cart.push({ ...product, quantity: 1, selected: true });
    }

    try {
      localStorage.setItem('soba_cart', JSON.stringify(cart));
    } catch(e) {}

    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    const countEl = document.getElementById('cartCount');
    if (countEl) countEl.innerText = totalItems;

    if (btn) {
      btn.classList.add('added');
      btn.innerHTML = '<span>Added ✓</span>';
      setTimeout(() => {
        btn.classList.remove('added');
        btn.innerHTML = '<span>Add to Cart</span> +';
      }, 1200);
    }
  }

  function toggleWish(event, btn) {
    if (event) event.stopPropagation(); // Stop card click navigation
    btn.classList.toggle('active');
  }

  // Mouse Spotlight Motion disabled

  // Nav indicator tracking script
  function moveNavIndicator(targetLink, isSmooth = true) {
    const indicator = document.getElementById('nav-indicator');
    const nav = document.getElementById('navbar');
    if (!indicator || !nav) return;

    const link = targetLink || nav.querySelector('.nav-link.active') || nav.querySelector('.nav-link');
    if (!link) {
      indicator.style.opacity = '0';
      return;
    }

    indicator.style.transition = isSmooth 
      ? 'transform 0.28s cubic-bezier(.4,0,.2,1), width 0.28s cubic-bezier(.4,0,.2,1), opacity 0.2s ease'
      : 'none';

    const left = link.offsetLeft;
    const width = link.offsetWidth;

    indicator.style.transform = `translateX(${left}px)`;
    indicator.style.width = `${width}px`;
    indicator.style.opacity = '1';
  }

  document.querySelectorAll('#navbar .nav-link').forEach(link => {
    link.addEventListener('mouseenter', () => moveNavIndicator(link, true));
  });

  document.getElementById('navbar')?.addEventListener('mouseleave', () => {
    const activeLink = document.querySelector('#navbar .nav-link.active');
    moveNavIndicator(activeLink, true);
  });

  window.addEventListener('DOMContentLoaded', () => {
    loadSavedCart();
    fetchProductsFromDB();
    moveNavIndicator(null, false);

    const urlParams = new URLSearchParams(window.location.search);
    const categoryQuery = urlParams.get('category');
    const searchQuery = urlParams.get('search');
    
    if (categoryQuery) {
      const cleanCat = categoryQuery.toLowerCase().trim();
      const chips = document.querySelectorAll('.chip');
      let matched = false;
      
      chips.forEach(chip => {
        const chipText = chip.textContent.trim().toLowerCase();
        if (chipText.includes(cleanCat) || cleanCat.includes(chipText)) {
          chip.click();
          matched = true;
        }
      });

      if (!matched && typeof setCategory === 'function') {
        selectedCategory = cleanCat;
        filterProducts();
      }
    }
    
    if (searchQuery) {
      const searchInput = document.getElementById('productSearch');
      if (searchInput) {
        searchInput.value = searchQuery;
        filterProducts();
      }
    }

    try {
      const stored = localStorage.getItem('soba_user');
      if (stored) {
        const u = JSON.parse(stored);
        if (u.name) {
          const accLabel = document.getElementById('productsHeaderUserName');
          if (accLabel) accLabel.textContent = u.name.split(' ')[0];
        }
        if (u.role && u.role.toLowerCase() === 'admin') {
          const accLink = document.getElementById('productsHeaderAccount');
          if (accLink) accLink.href = '../admin/dashboard.php';
        }
      }
    } catch(e) {}
  });

  window.addEventListener('resize', () => moveNavIndicator(null, false));
</script>
<script src="../../assets/js/main.js"></script>
</body>
</html>