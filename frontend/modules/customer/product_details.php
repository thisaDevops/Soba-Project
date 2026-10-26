<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Product Details — Soba Zero-Waste Marketplace</title>
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

  /* ===== Breadcrumbs ===== */
  .breadcrumb-shell {
    max-width: 1440px;
    margin: 22px auto 0;
    padding: 0 28px;
  }
  .breadcrumb {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    color: rgba(255, 255, 255, 0.55);
  }
  .breadcrumb a {
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    transition: color 0.2s;
  }
  .breadcrumb a:hover { color: var(--neo); }
  .breadcrumb .separator { color: rgba(255, 255, 255, 0.3); }
  .breadcrumb .current { color: var(--neo); font-weight: 600; }

  /* ===== Main Product Card ===== */
  .details-shell {
    max-width: 1440px;
    margin: 18px auto 60px;
    padding: 0 28px;
  }

  .details-card {
    background:
      radial-gradient(circle at 10% 8%, rgba(184,255,106,.09), transparent 30%),
      linear-gradient(145deg, rgba(16, 45, 34, 0.88) 0%, rgba(7, 27, 20, 0.96) 55%, rgba(6, 21, 15, 0.99) 100%);
    border: 1px solid rgba(184, 255, 106, 0.25);
    border-radius: 28px;
    box-shadow: 0 35px 90px rgba(0,0,0,.65), 0 0 45px rgba(184,255,106,.08);
    backdrop-filter: blur(20px);
    overflow: hidden;
  }

  /* Two Columns: Left Gallery & Right Details + Delivery Box */
  .product-main-grid {
    display: grid;
    grid-template-columns: 50% 50%;
    min-height: 600px;
  }

  /* ========================================================
     DARAZ-STYLE MULTI-IMAGE GALLERY (LEFT)
  ======================================================== */
  .gallery-pane {
    padding: 34px;
    border-right: 1px solid rgba(255,255,255,.09);
    display: flex;
    gap: 20px;
    background:
      radial-gradient(circle at 50% 40%, rgba(184,255,106,.10), transparent 60%),
      linear-gradient(160deg, rgba(255,255,255,0.02), rgba(0,0,0,0.25));
  }

  /* Thumbnail Strip (Left side of the gallery, like Daraz) */
  .gallery-thumbs {
    display: flex;
    flex-direction: column;
    gap: 12px;
    width: 82px;
    flex-shrink: 0;
  }
  .thumb-item {
    width: 80px;
    height: 80px;
    border-radius: 14px;
    background: rgba(255,255,255,0.04);
    border: 2px solid rgba(255,255,255,0.12);
    overflow: hidden;
    cursor: pointer;
    position: relative;
    transition: all 0.22s ease;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .thumb-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.25s;
  }
  .thumb-item .thumb-icon-fallback {
    font-size: 32px;
  }
  .thumb-item:hover {
    border-color: rgba(184, 255, 106, 0.6);
    transform: scale(1.04);
  }
  .thumb-item.active {
    border-color: var(--neo);
    box-shadow: 0 0 16px rgba(184, 255, 106, 0.45);
    background: rgba(184, 255, 106, 0.1);
  }

  /* Main Large Image Stage */
  .gallery-main-stage {
    flex: 1;
    position: relative;
    border-radius: 22px;
    border: 1px solid rgba(255,255,255,0.12);
    background:
      radial-gradient(circle at 50% 50%, rgba(184,255,106,0.12), transparent 70%),
      rgba(4, 19, 13, 0.6);
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 480px;
    cursor: crosshair;
  }
  .main-image-container {
    width: 100%;
    height: 100%;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
  }
  .main-product-img {
    max-width: 88%;
    max-height: 85%;
    object-fit: contain;
    filter: drop-shadow(0 20px 30px rgba(0,0,0,0.6));
    transition: transform 0.25s ease-out, opacity 0.2s ease;
    pointer-events: none;
  }
  .main-product-icon-fallback {
    font-size: 150px;
    filter: drop-shadow(0 25px 35px rgba(0,0,0,.65));
    transition: transform 0.3s ease;
  }

  /* Zoom Lens / Hover Magnifier indicator */
  .zoom-hint {
    position: absolute;
    bottom: 16px;
    right: 16px;
    background: rgba(4, 18, 13, 0.85);
    border: 1px solid rgba(255,255,255,0.18);
    color: rgba(255,255,255,0.85);
    font-size: 11px;
    font-weight: 600;
    padding: 6px 12px;
    border-radius: 999px;
    display: flex;
    align-items: center;
    gap: 6px;
    backdrop-filter: blur(10px);
    pointer-events: none;
  }

  /* Badges inside main image stage */
  .cert-badge-stage {
    position: absolute;
    top: 18px;
    left: 18px;
    background: rgba(7, 28, 21, 0.88);
    color: var(--neo);
    border: 1px solid rgba(184, 255, 106, 0.4);
    font-size: 11px;
    font-weight: 800;
    padding: 7px 14px;
    border-radius: 999px;
    letter-spacing: .08em;
    text-transform: uppercase;
    backdrop-filter: blur(10px);
    z-index: 2;
  }
  .eco-saved-stage {
    position: absolute;
    top: 18px;
    right: 18px;
    background: rgba(184, 255, 106, 0.15);
    color: #e5ffd6;
    border: 1px solid rgba(184, 255, 106, 0.35);
    font-size: 11px;
    font-weight: 700;
    padding: 6px 12px;
    border-radius: 999px;
    backdrop-filter: blur(10px);
    z-index: 2;
  }

  /* Slide Next / Prev buttons */
  .gallery-nav-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(4, 18, 13, 0.7);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #fff;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    transition: all 0.2s;
    z-index: 3;
  }
  .gallery-nav-btn:hover {
    background: var(--neo);
    color: #081d14;
    border-color: var(--neo);
  }
  .gallery-nav-btn.prev { left: 14px; }
  .gallery-nav-btn.next { right: 14px; }

  /* ========================================================
     RIGHT DETAILS + DARAZ SERVICES SECTION
  ======================================================== */
  .details-info-pane {
    padding: 38px 42px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }

  .meta-top-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
  }
  .category-pill {
    color: var(--neo);
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .15em;
  }
  .stock-status-pill {
    background: rgba(184, 255, 106, 0.12);
    color: var(--neo);
    border: 1px solid rgba(184, 255, 106, 0.3);
    font-size: 11px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    gap: 6px;
  }
  .stock-status-pill::before {
    content: "";
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: var(--neo);
    box-shadow: 0 0 8px var(--neo);
  }

  .product-title {
    color: #f5fff1;
    font-size: clamp(28px, 3.2vw, 42px);
    line-height: 1.15;
    margin: 4px 0 10px;
    font-weight: 600;
  }
  .product-tagline {
    color: rgba(255,255,255,.68);
    font-size: 14.5px;
    margin-bottom: 16px;
    line-height: 1.5;
  }

  /* Ratings & Seller Score bar */
  .rating-daraz-bar {
    display: flex;
    align-items: center;
    gap: 16px;
    padding-bottom: 16px;
    border-bottom: 1px solid rgba(255,255,255,0.09);
    margin-bottom: 18px;
    flex-wrap: wrap;
  }
  .stars-group {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #ffb43b;
    font-size: 16px;
  }
  .rating-score {
    font-weight: 700;
    color: #fff;
    font-size: 14px;
    font-family: 'IBM Plex Mono', monospace;
  }
  .rating-count-link {
    color: rgba(255,255,255,0.6);
    font-size: 12.5px;
    text-decoration: underline;
    cursor: pointer;
  }
  .seller-badge {
    margin-left: auto;
    font-size: 12px;
    color: rgba(255,255,255,0.65);
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    padding: 4px 10px;
    border-radius: 8px;
  }
  .seller-badge b { color: var(--neo); font-weight: 600; }

  /* Pricing Block */
  .price-showcase-box {
    background: rgba(4, 19, 13, 0.7);
    border: 1px solid rgba(184, 255, 106, 0.2);
    border-radius: 16px;
    padding: 16px 22px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
  }
  .price-main-display {
    display: flex;
    align-items: baseline;
    gap: 12px;
  }
  .price-amount {
    color: var(--neo);
    font-family: 'IBM Plex Mono', monospace;
    font-size: 34px;
    font-weight: 700;
  }
  .price-original {
    text-decoration: line-through;
    color: rgba(255,255,255,0.4);
    font-family: 'IBM Plex Mono', monospace;
    font-size: 18px;
  }
  .price-discount-tag {
    background: var(--clay);
    color: #fff;
    font-size: 11px;
    font-weight: 800;
    padding: 3px 8px;
    border-radius: 6px;
    text-transform: uppercase;
  }
  .installment-note {
    font-size: 12px;
    color: rgba(255,255,255,0.65);
  }
  .installment-note span { color: var(--neo); font-weight: 600; }

  /* Pack / Variant Selector */
  .variant-section {
    margin-bottom: 20px;
  }
  .variant-label {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: rgba(255,255,255,0.6);
    margin-bottom: 8px;
    display: flex;
    justify-content: space-between;
  }
  .variant-options {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
  }
  .variant-chip {
    padding: 8px 16px;
    border-radius: 10px;
    border: 1px solid rgba(255,255,255,0.15);
    background: rgba(255,255,255,0.04);
    color: rgba(255,255,255,0.85);
    font-size: 13px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.2s;
  }
  .variant-chip:hover {
    border-color: var(--neo);
    color: #fff;
  }
  .variant-chip.active {
    background: rgba(184, 255, 106, 0.15);
    border-color: var(--neo);
    color: var(--neo);
    box-shadow: 0 0 14px rgba(184, 255, 106, 0.25);
  }

  /* Quantity & Buy Actions */
  .actions-box {
    margin-bottom: 22px;
  }
  .qty-and-cart-row {
    display: flex;
    gap: 14px;
    align-items: center;
    flex-wrap: wrap;
  }
  .qty-counter {
    display: flex;
    align-items: center;
    gap: 10px;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.18);
    border-radius: 12px;
    padding: 6px 12px;
  }
  .qty-btn-daraz {
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.12);
    color: #fff;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 18px;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
  }
  .qty-btn-daraz:hover:not(:disabled) {
    background: var(--neo);
    color: #081d14;
    border-color: var(--neo);
  }
  .qty-display-val {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 16px;
    font-weight: 700;
    min-width: 32px;
    text-align: center;
  }

  .btn-add-basket {
    flex: 1;
    min-width: 170px;
    background: rgba(184, 255, 106, 0.15);
    border: 1px solid var(--neo);
    color: var(--neo);
    border-radius: 12px;
    padding: 14px 20px;
    font-family: 'Work Sans', sans-serif;
    font-size: 14px;
    font-weight: 800;
    cursor: pointer;
    text-transform: uppercase;
    letter-spacing: .06em;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
  }
  .btn-add-basket:hover {
    background: var(--neo);
    color: #081d14;
    box-shadow: 0 0 24px rgba(184, 255, 106, 0.4);
    transform: translateY(-2px);
  }
  .btn-add-basket.added {
    background: var(--sage);
    border-color: var(--sage);
    color: #fff;
  }

  .btn-buy-instant {
    flex: 1;
    min-width: 160px;
    background: var(--neo);
    border: none;
    color: #081d14;
    border-radius: 12px;
    padding: 14px 20px;
    font-family: 'Work Sans', sans-serif;
    font-size: 14px;
    font-weight: 800;
    cursor: pointer;
    text-transform: uppercase;
    letter-spacing: .06em;
    transition: all 0.2s;
  }
  .btn-buy-instant:hover {
    box-shadow: 0 0 28px rgba(184, 255, 106, 0.5);
    transform: translateY(-2px);
  }

  /* Daraz-Style Delivery & Service Guarantees Card */
  .daraz-service-card {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 16px;
    padding: 18px 20px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
  }
  .service-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
  }
  .service-item .service-icon {
    font-size: 22px;
    background: rgba(184, 255, 106, 0.1);
    border: 1px solid rgba(184, 255, 106, 0.2);
    border-radius: 10px;
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .service-item .service-text strong {
    display: block;
    font-size: 13px;
    color: #f0fff1;
    margin-bottom: 2px;
  }
  .service-item .service-text span {
    font-size: 11.5px;
    color: rgba(255,255,255,0.6);
    line-height: 1.4;
  }

  /* ========================================================
     TABBED SECTION (SPECS, SUSTAINABILITY, REVIEWS)
  ======================================================== */
  .product-tabs-shell {
    border-top: 1px solid rgba(255,255,255,0.1);
    background: rgba(0,0,0,0.18);
  }
  .tabs-nav {
    display: flex;
    padding: 0 42px;
    border-bottom: 1px solid rgba(255,255,255,0.08);
    gap: 32px;
    overflow-x: auto;
  }
  .tab-btn {
    background: none;
    border: none;
    color: rgba(255,255,255,0.6);
    font-family: 'Work Sans', sans-serif;
    font-size: 14.5px;
    font-weight: 700;
    padding: 20px 0;
    cursor: pointer;
    position: relative;
    transition: color 0.2s;
    white-space: nowrap;
  }
  .tab-btn:hover { color: #fff; }
  .tab-btn.active {
    color: var(--neo);
  }
  .tab-btn.active::after {
    content: "";
    position: absolute;
    bottom: -1px;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--neo);
    border-radius: 3px;
    box-shadow: 0 0 12px var(--neo);
  }

  .tabs-content-body {
    padding: 36px 42px;
  }
  .tab-pane {
    display: none;
    animation: fadeInTab 0.3s ease;
  }
  .tab-pane.active {
    display: block;
  }
  @keyframes fadeInTab {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
  }

  /* Specifications Table */
  .spec-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
  }
  .spec-table tr {
    border-bottom: 1px solid rgba(255,255,255,0.07);
  }
  .spec-table td {
    padding: 12px 16px;
    font-size: 13.5px;
  }
  .spec-table td.spec-name {
    color: rgba(255,255,255,0.55);
    width: 32%;
    font-weight: 600;
  }
  .spec-table td.spec-val {
    color: #e5ffe0;
    font-weight: 500;
  }

  /* Highlights Grid in Tab */
  .tab-highlights-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
  }
  .tab-highlight-card {
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 14px;
    padding: 18px;
  }
  .tab-highlight-card b {
    display: block;
    color: var(--neo);
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .08em;
    margin-bottom: 6px;
  }
  .tab-highlight-card span {
    color: rgba(255,255,255,0.85);
    font-size: 13.5px;
  }

  /* Reviews Grid */
  .reviews-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 18px;
  }
  .review-card {
    border: 1px solid rgba(255,255,255,.10);
    background: rgba(255,255,255,.04);
    border-radius: 16px;
    padding: 22px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }
  .review-card .stars {
    color: #ffb43b;
    font-size: 14px;
    margin-bottom: 10px;
  }
  .review-card p {
    color: rgba(255,255,255,.8);
    font-size: 13.5px;
    line-height: 1.6;
    margin: 0 0 14px;
  }
  .review-card .reviewer-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 12px;
  }
  .review-card strong { color: var(--neo); }
  .review-card .verified-tag { color: rgba(255,255,255,0.5); font-size: 11px; }

  /* ===== Lightbox Modal (For clicking full image) ===== */
  .lightbox-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.88);
    backdrop-filter: blur(12px);
    z-index: 150;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
  }
  .lightbox-overlay.open {
    display: flex;
  }
  .lightbox-img {
    max-width: 90vw;
    max-height: 85vh;
    object-fit: contain;
    border-radius: 18px;
    box-shadow: 0 0 50px rgba(0,0,0,0.8);
    border: 1px solid rgba(184, 255, 106, 0.3);
  }
  .lightbox-close {
    position: absolute;
    top: 24px;
    right: 28px;
    color: #fff;
    font-size: 32px;
    cursor: pointer;
    background: none;
    border: none;
    transition: color 0.2s;
  }
  .lightbox-close:hover { color: var(--neo); }

  /* ===== Related Products ===== */
  .related-shell {
    max-width: 1440px;
    margin: 70px auto 100px;
    padding: 0 28px;
  }
  .related-title {
    font-size: 28px;
    color: #f5fff2;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .related-title a {
    font-size: 13.5px;
    font-family: 'Work Sans', sans-serif;
    color: var(--neo);
    text-decoration: none;
    font-weight: 600;
  }
  .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 28px; }
  
  .card {
    position: relative;
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.07) 0%, rgba(255, 255, 255, 0.03) 100%);
    border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 22px;
    overflow: hidden; display: flex; flex-direction: column; backdrop-filter: blur(16px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.25);
    transition: transform 0.35s cubic-bezier(0.2, 0.8, 0.2, 1), box-shadow 0.35s ease, border-color 0.35s ease;
    text-decoration: none;
    color: inherit;
  }
  .card:hover {
    transform: translateY(-8px) scale(1.01);
    box-shadow: 0 30px 60px rgba(0,0,0,0.4), 0 0 30px rgba(184, 255, 106, 0.18);
    border-color: rgba(184, 255, 106, 0.45);
  }
  .card-art {
    height: 200px; display: flex; align-items: center; justify-content: center; font-size: 68px;
    background: radial-gradient(circle at 50% 50%, rgba(184, 255, 106, 0.12), transparent 70%),
                linear-gradient(160deg, rgba(255,255,255,0.05), rgba(8, 32, 24, 0.4));
    position: relative; overflow: hidden;
  }
  .card-art img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
  }
  .card:hover .card-art img { transform: scale(1.08); }
  .card-cert {
    position: absolute; top: 14px; left: 14px;
    background: rgba(7, 28, 21, 0.75); color: var(--neo);
    border: 1px solid rgba(184, 255, 106, 0.4); font-size: 10.5px; font-weight: 700; padding: 5px 10px; border-radius: 8px;
    letter-spacing: 0.05em; backdrop-filter: blur(10px); text-transform: uppercase;
  }
  .poster-price-tag {
    position: absolute; bottom: 12px; right: 14px;
    background: var(--neo); color: #081d14;
    padding: 6px 14px; border-radius: 999px;
    font-family: 'IBM Plex Mono', monospace; font-weight: 700; font-size: 13.5px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.3);
  }
  .card-body { padding: 20px; display: flex; flex-direction: column; gap: 8px; flex: 1; background: rgba(6, 22, 16, 0.3); }
  .card-body h3 { font-size: 17px; font-weight: 700; font-family: 'Work Sans', sans-serif; color: #fff; line-height: 1.3; }
  .card-meta { font-size: 11px; color: rgba(255,255,255,0.45); text-transform: uppercase; letter-spacing: 0.08em; font-weight: 600; }
  .card-saved {
    display: inline-flex; align-items: center; gap: 6px; font-size: 12px; color: var(--neo); font-weight: 600;
    background: rgba(184, 255, 106, 0.08); padding: 5px 10px; border-radius: 6px; border: 1px solid rgba(184, 255, 106, 0.15); width: fit-content;
  }

  /* ===== Cart Drawer ===== */
  .overlay {
    position: fixed; inset: 0; background: rgba(0,0,0,0.65); opacity: 0; pointer-events: none; transition: opacity 0.3s; z-index: 80; backdrop-filter: blur(6px);
  }
  .overlay.open { opacity: 1; pointer-events: auto; }
  
  .drawer {
    position: fixed; top: 0; right: 0; height: 100%; width: 440px; max-width: 92vw;
    background: #061912; border-left: 1px solid rgba(255,255,255,0.14); z-index: 90;
    transform: translateX(100%); transition: transform 0.35s cubic-bezier(.4,0,.2,1);
    display: flex; flex-direction: column; box-shadow: -10px 0 50px rgba(0,0,0,0.6);
  }
  .drawer.open { transform: translateX(0); }
  
  .drawer-head { padding: 24px 26px; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; justify-content: space-between; align-items: center; }
  .drawer-head h2 { font-size: 22px; color: var(--neo); font-weight: 600; }
  .drawer-close { background: none; border: none; font-size: 24px; cursor: pointer; color: #fff; opacity: 0.7; transition: opacity 0.2s; }
  .drawer-close:hover { opacity: 1; }

  .cart-bulk-bar {
    display: flex; justify-content: space-between; align-items: center;
    padding: 12px 16px; margin-bottom: 12px;
    background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px;
  }
  .cart-bulk-label { display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13px; font-weight: 600; color: rgba(255,255,255,0.85); }
  .cart-bulk-label input[type="checkbox"], .cart-item-checkbox {
    accent-color: var(--neo); width: 17px; height: 17px; cursor: pointer;
  }
  .delete-selected-btn {
    background: rgba(200, 109, 81, 0.2); color: var(--clay-soft); border: 1px solid rgba(200, 109, 81, 0.4);
    padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer; transition: all 0.2s;
    display: flex; align-items: center; gap: 6px;
  }
  .delete-selected-btn:hover:not(:disabled) { background: var(--clay); color: #fff; border-color: var(--clay); }
  .delete-selected-btn:disabled { opacity: 0.35; cursor: not-allowed; border-color: rgba(255,255,255,0.1); color: rgba(255,255,255,0.4); background: transparent; }

  .drawer-body { flex: 1; overflow-y: auto; padding: 20px 26px; }
  .cart-item { display: flex; gap: 12px; padding: 14px 0; border-bottom: 1px solid rgba(255,255,255,0.08); align-items: center; }
  .cart-item .art { width: 50px; height: 50px; background: rgba(255,255,255,0.06); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; border: 1px solid rgba(255,255,255,0.1); flex-shrink: 0; }
  .cart-item .info { flex: 1; }
  .cart-item .info h4 { font-size: 14px; margin: 0 0 4px; color: #fff; font-weight: 600; }
  .cart-item .info .sub { font-size: 13px; color: var(--neo); font-family: 'IBM Plex Mono', monospace; font-weight: 600; }
  
  .qty-controls { display: flex; align-items: center; gap: 8px; margin-top: 6px; }
  .qty-btn {
    background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: #fff;
    width: 24px; height: 24px; border-radius: 6px; cursor: pointer; display: flex;
    align-items: center; justify-content: center; font-weight: bold; font-size: 14px; transition: all 0.2s;
  }
  .qty-btn:hover:not(:disabled) { background: var(--neo); color: #081d14; border-color: var(--neo); }
  .qty-val { font-family: 'IBM Plex Mono', monospace; font-size: 13px; min-width: 20px; text-align: center; }
  
  .remove-btn {
    background: none; border: none; color: rgba(255, 255, 255, 0.4); cursor: pointer;
    font-size: 16px; padding: 6px; transition: color 0.2s, transform 0.2s;
  }
  .remove-btn:hover { color: var(--clay); transform: scale(1.15); }

  .drawer-foot { border-top: 1px solid rgba(255,255,255,0.1); padding: 24px; background: rgba(4, 16, 12, 0.6); }
  .cart-total-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; font-size: 15px; font-weight: 600; }
  .cart-total-price { color: var(--neo); font-family: 'IBM Plex Mono', monospace; font-size: 18px; font-weight: 700; }
  
  .checkout-btn { width: 100%; background: var(--neo); color: #081d14; border: none; border-radius: 12px; padding: 16px; font-size: 16px; font-weight: 800; cursor: pointer; text-transform: uppercase; letter-spacing: 0.05em; transition: all 0.2s; }
  /* ===== Floating Cart Toast ===== */
  .cart-toast {
    position: fixed;
    bottom: 30px;
    right: 30px;
    background: rgba(7, 28, 21, 0.95);
    border: 1px solid var(--neo);
    box-shadow: 0 10px 40px rgba(0,0,0,0.6), 0 0 25px rgba(184, 255, 106, 0.3);
    border-radius: 14px;
    padding: 14px 22px;
    display: flex;
    align-items: center;
    gap: 14px;
    z-index: 100;
    transform: translateY(100px);
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.2, 0.8, 0.2, 1);
    pointer-events: none;
    backdrop-filter: blur(12px);
  }
  .cart-toast.show {
    transform: translateY(0);
    opacity: 1;
    pointer-events: auto;
  }
  .toast-icon {
    background: var(--neo);
    color: #081d14;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 14px;
  }
  .toast-text {
    font-size: 14px;
    color: #f5fff1;
    font-weight: 600;
  }
  .toast-btn {
    background: rgba(184, 255, 106, 0.15);
    color: var(--neo);
    border: 1px solid var(--neo);
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 12.5px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.2s;
  }
  .toast-btn:hover {
    background: var(--neo);
    color: #081d14;
  }

  /* ===== Footer ===== */
  footer { background: #04110c; border-top: 1px solid rgba(255,255,255,0.08); padding: 60px 28px 24px; font-size: 13px; color: rgba(255,255,255,0.55); margin-top: 90px; }
  .footer-inner { max-width: 1420px; margin: 0 auto; padding-bottom: 40px; display: grid; grid-template-columns: 1.6fr 1fr 1fr 1fr; gap: 36px; }
  .footer-col { display: flex; flex-direction: column; gap: 12px; }
  .footer-col a { color: rgba(255,255,255,0.65); text-decoration: none; font-size: 13.5px; transition: color 0.2s; }
  .footer-col a:hover { color: var(--neo); }
  .footer-title { font-size: 12px; text-transform: uppercase; letter-spacing: 0.12em; color: var(--neo); font-weight: 700; margin-bottom: 6px; }

  @media (max-width: 1024px) {
    .product-main-grid { grid-template-columns: 1fr; }
    .gallery-pane { border-right: none; border-bottom: 1px solid rgba(255,255,255,0.1); }
    .daraz-service-card { grid-template-columns: 1fr; }
    .reviews-grid { grid-template-columns: 1fr 1fr; }
  }
  @media (max-width: 768px) {
    .gallery-pane { flex-direction: column-reverse; padding: 20px; }
    .gallery-thumbs { flex-direction: row; width: 100%; overflow-x: auto; padding-bottom: 4px; }
    .gallery-main-stage { min-height: 340px; }
    .details-info-pane { padding: 26px 20px; }
    .reviews-grid { grid-template-columns: 1fr; }
    .tabs-nav { padding: 0 20px; }
    .tabs-content-body { padding: 26px 20px; }
    .footer-inner { grid-template-columns: 1fr 1fr; }
  }
  @media (max-width: 580px) {
    .footer-inner { grid-template-columns: 1fr; }
    .header-top .bar { padding: 12px 16px; }
    .details-shell { padding: 0 14px; }
    .breadcrumb-shell { padding: 0 14px; }
    .qty-and-cart-row { flex-direction: column; align-items: stretch; }
    .btn-add-basket, .btn-buy-instant { width: 100%; }
  }
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
        <input type="text" id="productSearch" placeholder="Search eco products (e.g. Toothbrush, Soap)..." onkeydown="if(event.key==='Enter') doHeaderSearch()">
        <button class="search-go" onclick="doHeaderSearch()">🔍</button>
      </div>

      <div class="bar-right">
        <a href="user_dashboard.php" class="cart-btn" style="text-decoration:none;" id="detailsHeaderAccount">
          👤 <span id="detailsHeaderUserName">Account</span>
        </a>
        <a href="cart.php" class="cart-btn" style="text-decoration: none;">
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
      <a href="products.php" style="display:inline-flex; align-items:center; gap:6px; color:var(--neo); font-size:12.5px; font-weight:700; text-decoration:none; padding:5px 12px; border-radius:8px; background:rgba(184,255,106,0.08); border:1px solid rgba(184,255,106,0.25);">
        &larr; Back to Shop
      </a>
    </div>
  </div>
</header>

<!-- Breadcrumbs -->
<div class="breadcrumb-shell">
  <div class="breadcrumb">
    <a href="../../home.php">Home</a>
    <span class="separator">/</span>
    <a href="products.php">Shop Catalog</a>
    <span class="separator">/</span>
    <span id="breadcrumbCategory">Personal Care</span>
    <span class="separator">/</span>
    <span class="current" id="breadcrumbName">Product Details</span>
  </div>
</div>

<!-- Main Details Section -->
<main class="details-shell">
  <div class="details-card">
    <div class="product-main-grid">
      
      <!-- ==============================================
           LEFT: DARAZ-STYLE INTERACTIVE PICTURE GALLERY
      =============================================== -->
      <div class="gallery-pane">
        
        <!-- Vertical Thumbnails -->
        <div class="gallery-thumbs" id="galleryThumbs">
          <!-- Dynamically generated thumbs -->
        </div>

        <!-- Large Main Image Stage with Zoom -->
        <div class="gallery-main-stage" id="galleryMainStage" onmousemove="handleImageZoom(event)" onmouseleave="resetImageZoom()" onclick="openLightbox()">
          <span class="cert-badge-stage" id="detailCert">100% Compostable</span>
          <span class="eco-saved-stage" id="detailSaved">🌱 Saves 1 Plastic Item</span>
          
          <button class="gallery-nav-btn prev" onclick="event.stopPropagation(); changeImageIndex(-1)">‹</button>
          <button class="gallery-nav-btn next" onclick="event.stopPropagation(); changeImageIndex(1)">›</button>

          <div class="main-image-container" id="mainImageContainer">
            <img src="" alt="Product Picture" class="main-product-img" id="mainProductImg">
            <span class="main-product-icon-fallback" id="mainProductIcon" style="display:none;">🌱</span>
          </div>

          <div class="zoom-hint">
            <span>🔍 Hover to Zoom / Click to Enlarge</span>
          </div>
        </div>

      </div>

      <!-- ==============================================
           RIGHT: PRODUCT DETAILS, PRICING & SERVICES
      =============================================== -->
      <div class="details-info-pane">
        <div>
          <div class="meta-top-row">
            <span class="category-pill" id="detailCategory">Personal Care</span>
            <div class="stock-status-pill">In Stock · Ready to Ship</div>
          </div>

          <h1 class="product-title" id="detailTitle">Organic Bamboo Toothbrush</h1>
          <p class="product-tagline" id="detailSubtitle">Natural daily care with a zero-waste finish.</p>

          <!-- Daraz Style Rating and Seller Bar -->
          <div class="rating-daraz-bar">
            <div class="stars-group">
              <span id="detailStars">★★★★★</span>
              <span class="rating-score" id="detailRatingScore">4.9</span>
            </div>
            <a class="rating-count-link" onclick="switchTab('reviews')">
              <span id="detailRatingCount">128 Ratings & Reviews</span>
            </a>
            <div class="seller-badge">
              Verified Artisan: <b>Soba Direct 🌿</b>
            </div>
          </div>

          <!-- Price & Discounts Box -->
          <div class="price-showcase-box">
            <div>
              <div class="price-main-display">
                <span class="price-amount" id="detailPrice">LKR 450</span>
                <span class="price-original" id="detailOriginalPrice">LKR 650</span>
                <span class="price-discount-tag" id="detailDiscountTag">-30%</span>
              </div>
              <div class="installment-note">
                Or 3 x <span id="installmentPrice">LKR 150</span> with <b>Koko / Mintpay</b> (Interest-Free)
              </div>
            </div>
          </div>

          <!-- Variant / Pack Selection (e.g. 1 Pack, 3 Pack, Eco Bundle) -->
          <div class="variant-section">
            <div class="variant-label">
              <span>Select Pack Option:</span>
              <span id="selectedVariantLabel" style="color:var(--neo); text-transform:none;">Single Item</span>
            </div>
            <div class="variant-options" id="variantOptions">
              <button class="variant-chip active" onclick="selectVariant(this, 1, 'Single Item', 0)">Standard (1x)</button>
              <button class="variant-chip" onclick="selectVariant(this, 3, 'Value Eco Pack (3x)', 0.10)">Pack of 3 (Save 10%)</button>
              <button class="variant-chip" onclick="selectVariant(this, 6, 'Family Bundle (6x)', 0.18)">Family Pack (6x - Save 18%)</button>
            </div>
          </div>

          <!-- Quantity, Add to Cart & Buy Instant Actions -->
          <div class="actions-box">
            <div class="qty-and-cart-row">
              <div class="qty-counter">
                <button class="qty-btn-daraz" onclick="changeDetailQty(-1)">−</button>
                <span class="qty-display-val" id="detailQtyVal">1</span>
                <button class="qty-btn-daraz" onclick="changeDetailQty(1)">+</button>
              </div>

              <button class="btn-add-basket" id="detailAddBtn" onclick="addDetailProductToCart()">
                🛒 <span>Add to Cart</span>
              </button>

              <button class="btn-buy-instant" onclick="buyNow()">
                ⚡ Buy Now
              </button>
            </div>
          </div>

          <!-- Daraz-style Delivery & Assurance Card -->
          <div class="daraz-service-card">
            <div class="service-item">
              <div class="service-icon">🚚</div>
              <div class="service-text">
                <strong>Islandwide Delivery</strong>
                <span>Standard Delivery (2-3 working days) · Free over LKR 5,000</span>
              </div>
            </div>
            <div class="service-item">
              <div class="service-icon">💵</div>
              <div class="service-text">
                <strong>Cash on Delivery Available</strong>
                <span>Pay securely at your doorstep or via online card</span>
              </div>
            </div>
            <div class="service-item">
              <div class="service-icon">📦</div>
              <div class="service-text">
                <strong>100% Zero-Plastic Packing</strong>
                <span>Shipped in biodegradable cartons & paper tape</span>
              </div>
            </div>
            <div class="service-item">
              <div class="service-icon">🔄</div>
              <div class="service-text">
                <strong>7 Days Easy Return</strong>
                <span>100% refund guarantee on damaged or defective items</span>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- ==============================================
         TABBED SPECIFICATIONS, SUSTAINABILITY & REVIEWS
    =============================================== -->
    <div class="product-tabs-shell">
      <div class="tabs-nav">
        <button class="tab-btn active" onclick="switchTab('description', this)">Overview & Features</button>
        <button class="tab-btn" onclick="switchTab('specs', this)">Specifications</button>
        <button class="tab-btn" onclick="switchTab('reviews', this)">Customer Reviews (<span id="tabReviewCount">128</span>)</button>
        <button class="tab-btn" onclick="switchTab('impact', this)">Eco Impact</button>
      </div>

      <div class="tabs-content-body">
        
        <!-- Tab 1: Description -->
        <div class="tab-pane active" id="tab-description">
          <p style="font-size: 15.5px; line-height: 1.8; color: rgba(240,255,246,0.85); max-width: 900px; margin-bottom: 26px;" id="detailDescription">
            Loading product description...
          </p>
          <div class="tab-highlights-grid" id="detailHighlights">
            <!-- Dynamic highlights -->
          </div>
        </div>

        <!-- Tab 2: Specifications Table -->
        <div class="tab-pane" id="tab-specs">
          <table class="spec-table" id="specTable">
            <!-- Dynamically populated -->
          </table>
        </div>

        <!-- Tab 3: Reviews -->
        <div class="tab-pane" id="tab-reviews">
          <div class="reviews-grid" id="detailReviews">
            <!-- Dynamically populated -->
          </div>
        </div>

        <!-- Tab 4: Impact Score -->
        <div class="tab-pane" id="tab-impact">
          <div style="background: rgba(184, 255, 106, 0.08); border: 1px solid rgba(184, 255, 106, 0.25); border-radius: 18px; padding: 28px; max-width: 800px;">
            <h3 style="color: var(--neo); font-size: 20px; margin-bottom: 10px;">🌍 Circular Economy Commitment</h3>
            <p style="color: rgba(255,255,255,0.8); line-height: 1.7; font-size: 14.5px;">
              By purchasing this product instead of standard mass-market petroleum-based items, you actively divert non-biodegradable waste from Sri Lankan landfills and ocean shores.
            </p>
            <div style="display:flex; gap: 20px; margin-top: 18px; flex-wrap:wrap;">
              <div style="background:rgba(0,0,0,0.3); padding:14px 20px; border-radius:12px; border:1px solid rgba(255,255,255,0.1);">
                <span style="font-size:12px; color:rgba(255,255,255,0.5); text-transform:uppercase;">Estimated Plastic Avoided</span>
                <div style="font-size:20px; font-weight:700; color:var(--neo); font-family:'IBM Plex Mono';" id="impactMetric">1 Item Saved</div>
              </div>
              <div style="background:rgba(0,0,0,0.3); padding:14px 20px; border-radius:12px; border:1px solid rgba(255,255,255,0.1);">
                <span style="font-size:12px; color:rgba(255,255,255,0.5); text-transform:uppercase;">Carbon Footprint Reduction</span>
                <div style="font-size:20px; font-weight:700; color:var(--neo); font-family:'IBM Plex Mono';">~0.42 kg CO₂e</div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

  </div>
</main>

<!-- Lightbox Modal for Enlarge Image -->
<div class="lightbox-overlay" id="lightboxModal" onclick="closeLightbox()">
  <button class="lightbox-close" onclick="closeLightbox()">✕</button>
  <img src="" alt="Enlarged View" class="lightbox-img" id="lightboxImg" onclick="event.stopPropagation()">
</div>

<!-- Related Products Section -->
<section class="related-shell">
  <div class="related-title">
    <h2>Recommended For You</h2>
    <a href="products.php">Explore All Items →</a>
  </div>
  <div class="grid" id="relatedGrid">
    <!-- Populated via JS -->
  </div>
</section>

<!-- Floating Toast Notification (Replaces popup window) -->
<div id="cartToast" class="cart-toast">
  <span class="toast-icon">✓</span>
  <span class="toast-text" id="toastText">Item added to your Cart!</span>
  <a href="cart.php" class="toast-btn">View Cart →</a>
</div>

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
  // ========================================================
  // COMPREHENSIVE PRODUCT CATALOG WITH MULTIPLE PICTURES
  // ========================================================
  const products = [
    { 
      id: 1, 
      name: "Organic Bamboo Toothbrush", 
      category: "personal", 
      price: 450, 
      originalPrice: 650,
      icon: "🪥", 
      cert: "100% Compostable", 
      saved: "1 Plastic Brush",
      images: [
        "https://images.unsplash.com/photo-1607613009820-a29f7bb81c04?auto=format&fit=crop&w=800&q=80",
        "https://images.unsplash.com/photo-1590439471364-192aa70c0b53?auto=format&fit=crop&w=800&q=80",
        "https://images.unsplash.com/photo-1589365278144-c9e705f843ba?auto=format&fit=crop&w=800&q=80",
        "https://images.unsplash.com/photo-1612817288484-6f916006741a?auto=format&fit=crop&w=800&q=80"
      ]
    },
    { 
      id: 2, 
      name: "Solid Shampoo Bar (Citrus)", 
      category: "personal", 
      price: 1200, 
      originalPrice: 1600,
      icon: "🧼", 
      cert: "Zero Waste", 
      saved: "2 Shampoo Bottles",
      images: [
        "https://images.unsplash.com/photo-1608248597359-0021b369c0d3?auto=format&fit=crop&w=800&q=80",
        "https://images.unsplash.com/photo-1607006310452-95f2a1b92e76?auto=format&fit=crop&w=800&q=80",
        "https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=800&q=80",
        "https://images.unsplash.com/photo-1601049541289-9b1b7bbbfe19?auto=format&fit=crop&w=800&q=80"
      ]
    },
    { 
      id: 3, 
      name: "Stainless Steel Bento Box", 
      category: "kitchen", 
      price: 3400, 
      originalPrice: 4200,
      icon: "🍱", 
      cert: "Plastic-Free", 
      saved: "150 Plastic Bags",
      images: [
        "https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=800&q=80",
        "https://images.unsplash.com/photo-1512058564366-18510be2db19?auto=format&fit=crop&w=800&q=80",
        "https://images.unsplash.com/photo-1584269600464-37b1b58a9fe7?auto=format&fit=crop&w=800&q=80",
        "https://images.unsplash.com/photo-1505253758473-96b3015f21c9?auto=format&fit=crop&w=800&q=80"
      ]
    },
    { 
      id: 4, 
      name: "Reusable Beeswax Food Wraps", 
      category: "kitchen", 
      price: 1800, 
      originalPrice: 2400,
      icon: "🐝", 
      cert: "Organic Cotton", 
      saved: "3 Cling Wraps",
      images: [
        "https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?auto=format&fit=crop&w=800&q=80",
        "https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=800&q=80",
        "https://images.unsplash.com/photo-1610557892470-55d9e80c0bce?auto=format&fit=crop&w=800&q=80"
      ]
    },
    { 
      id: 5, 
      name: "Insulated Glass Water Bottle", 
      category: "lifestyle", 
      price: 2900, 
      originalPrice: 3800,
      icon: "🍾", 
      cert: "BPA-Free", 
      saved: "300 Plastic Bottles",
      images: [
        "https://images.unsplash.com/photo-1602143407151-7111542de6e8?auto=format&fit=crop&w=800&q=80",
        "https://images.unsplash.com/photo-1523362628745-0c100150b504?auto=format&fit=crop&w=800&q=80",
        "https://images.unsplash.com/photo-1589365278144-c9e705f843ba?auto=format&fit=crop&w=800&q=80"
      ]
    },
    { 
      id: 6, 
      name: "Heavy Duty Hemp Grocery Tote", 
      category: "lifestyle", 
      price: 1450, 
      originalPrice: 1950,
      icon: "🛍️", 
      cert: "Fair Trade", 
      saved: "50 Plastic Bags",
      images: [
        "https://images.unsplash.com/photo-1597484661643-2f5fef640dd1?auto=format&fit=crop&w=800&q=80",
        "https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=800&q=80",
        "https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=800&q=80"
      ]
    },
    { 
      id: 7, 
      name: "Natural Loofah Dish Scrubber", 
      category: "kitchen", 
      price: 350, 
      originalPrice: 500,
      icon: "🧽", 
      cert: "100% Plant Based", 
      saved: "5 Synthetic Sponges",
      images: [
        "https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?auto=format&fit=crop&w=800&q=80",
        "https://images.unsplash.com/photo-1585751119414-ef2636f8aede?auto=format&fit=crop&w=800&q=80"
      ]
    },
    { 
      id: 8, 
      name: "Refillable Liquid Soap Container", 
      category: "personal", 
      price: 2200, 
      originalPrice: 2800,
      icon: "🧴", 
      cert: "Zero Waste", 
      saved: "10 Refill Packs",
      images: [
        "https://images.unsplash.com/photo-1585751119414-ef2636f8aede?auto=format&fit=crop&w=800&q=80",
        "https://images.unsplash.com/photo-1608248597359-0021b369c0d3?auto=format&fit=crop&w=800&q=80"
      ]
    },
    { 
      id: 9, 
      name: "Handmade Elephant Dung Notebook", 
      category: "stationery", 
      price: 680, 
      originalPrice: 950,
      icon: "📓", 
      cert: "Tree-Free", 
      saved: "Tree Saved",
      images: [
        "https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=800&q=80",
        "https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=800&q=80"
      ]
    },
    { 
      id: 10, 
      name: "Plantable Seed Pencils (5pk)", 
      category: "stationery", 
      price: 520, 
      originalPrice: 750,
      icon: "✏️", 
      cert: "Plantable", 
      saved: "5 Plastic Pens",
      images: [
        "https://images.unsplash.com/photo-1585336261026-7f573f055375?auto=format&fit=crop&w=800&q=80",
        "https://images.unsplash.com/photo-1583485088034-697b5bc54ccd?auto=format&fit=crop&w=800&q=80"
      ]
    }
  ];

  // Extended Details, Specs & Verified Reviews Data
  const productDetails = {
    1: {
      subtitle: "Natural daily care with a zero-waste finish.",
      rating: "4.9 / 5 · 128 reviews",
      ratingCount: 128,
      ratingScore: "4.9",
      description: "A durable bamboo toothbrush crafted for a cleaner dental routine without the plastic pollution. The ergonomic handle is shaped from wild, organically grown bamboo and sealed with plant wax for maximum longevity.",
      highlights: [
        ["Material", "100% Biodegradable Moso Bamboo"],
        ["Bristles", "Castor oil-infused bio-nylon (BPA-free)"],
        ["Disposal", "Handle composts in 6 months"],
        ["Best for", "Daily dental hygiene & zero-waste home"]
      ],
      specs: [
        ["Product Type", "Manual Eco Toothbrush"],
        ["Handle Length", "19 cm ergonomic grip"],
        ["Bristle Firmness", "Medium-Soft Gentle Gum Care"],
        ["Packaging", "100% Recycled Kraft paper carton"],
        ["Country of Origin", "Handcrafted in Sri Lanka"]
      ],
      reviews: [
        ["★★★★★", "Feels great in hand and looks beautiful on the sink counter. Love replacing plastic so easily!", "Nimali R."],
        ["★★★★★", "Arrived cleanly packaged with zero plastic tape. Bristles are durable yet gentle on sensitive gums.", "Kasun P."],
        ["★★★★☆", "High quality finish. Handle doesn't get mouldy if kept dry. Highly recommended!", "Ayesha M."]
      ]
    },
    2: {
      subtitle: "Plastic-free hair care, concentrated into one nutrient bar.",
      rating: "4.8 / 5 · 96 reviews",
      ratingCount: 96,
      ratingScore: "4.8",
      description: "A solid citrus shampoo bar formulated with organic coconut oil, lemon peel extract, and shea butter. Cleanses gently while maintaining your hair's natural hydration balance.",
      highlights: [
        ["Scent", "Refreshing Lime & Sweet Orange"],
        ["Usage", "Equivalent to 2 to 3 liquid shampoo bottles"],
        ["Packaging", "Compostable botanical paper box"],
        ["Free From", "Sulfates, Silicones, Palm Oil, Parabens"]
      ],
      specs: [
        ["Weight", "95g solid bar (~65-80 washes)"],
        ["Hair Type", "Normal to oily hair"],
        ["Key Botanicals", "Cold-pressed coconut & citrus oils"],
        ["Storage", "Keep dry between uses on a soap dish"]
      ],
      reviews: [
        ["★★★★★", "Lathers surprisingly rich! Smells heavenly citrusy and leaves my hair soft without conditioner.", "Tharushi K."],
        ["★★★★★", "Travel-friendly, no leaky bottles in my luggage. A must-have!", "Dilan S."],
        ["★★★★☆", "Great product, gentle on hair and no plastic clutter in my bathroom.", "Madu P."]
      ]
    },
    3: {
      subtitle: "Built for everyday meals, engineered to last a lifetime.",
      rating: "4.9 / 5 · 74 reviews",
      ratingCount: 74,
      ratingScore: "4.9",
      description: "Sturdy food-grade 304 stainless steel bento box with smart leak-resistant silicone seals. Great for office lunches, meal prep, leftovers, and outdoor picnics.",
      highlights: [
        ["Steel Grade", "Food-grade 18/8 (304) Stainless Steel"],
        ["Compartments", "2 layers with removable divider"],
        ["Impact", "Replaces 150+ single-use takeaway boxes"],
        ["Care", "100% Dishwasher safe & stain resistant"]
      ],
      specs: [
        ["Capacity", "1200 ml total volume"],
        ["Dimensions", "20cm x 15cm x 6.5cm"],
        ["Closure", "Heavy-duty locking clips with silicone rim"],
        ["Safety", "Toxin-free, lead-free, BPA-free"]
      ],
      reviews: [
        ["★★★★★", "Extremely solid! Clips lock securely with no soup leaks in my office bag.", "Ravindu T."],
        ["★★★★★", "Easy to clean and doesn't hold food odours like plastic Tupperware.", "Sahan D."],
        ["★★★★★", "Super sleek and aesthetic lunch box.", "Nethmi A."]
      ]
    },
    4: {
      subtitle: "Reusable food preservation without single-use plastic wrap.",
      rating: "4.8 / 5 · 61 reviews",
      ratingCount: 61,
      ratingScore: "4.8",
      description: "Organic cotton wraps infused with pure beeswax, jojoba oil and tree resin. Simply mould over bowls, cheese, fresh bread, vegetables and cut fruits using the warmth of your hands.",
      highlights: [
        ["Ingredients", "Organic cotton, Beeswax, Jojoba oil"],
        ["Lifespan", "Wash and reuse up to 150+ times"],
        ["Antimicrobial", "Natural beeswax keeps food fresh longer"],
        ["Pack Contents", "Small, Medium, Large assortment"]
      ],
      specs: [
        ["Sizes Included", "Small (20cm), Medium (26cm), Large (33cm)"],
        ["Cleaning", "Rinse with cold water and mild soap"],
        ["Compostable", "100% biodegradable at end of life"]
      ],
      reviews: [
        ["★★★★★", "Replaced cling film completely in my kitchen. Avocado stayed fresh for 3 days!", "Shalini R."],
        ["★★★★☆", "Moulds easily around ceramic bowls and keeps bread crust crisp.", "Ishan P."],
        ["★★★★★", "Smells like pure honey and looks beautiful.", "Dewmi K."]
      ]
    },
    5: {
      subtitle: "Insulated borosilicate glass bottle for thermal purity.",
      rating: "4.9 / 5 · 109 reviews",
      ratingCount: 109,
      ratingScore: "4.9",
      description: "Double-walled thermal borosilicate glass bottle with an artisan bamboo lid. Keeps herbal teas hot and iced beverages cold with zero metallic aftertaste.",
      highlights: [
        ["Glass", "Thermal shock-resistant Borosilicate"],
        ["Lid", "Natural bamboo with 304 steel liner"],
        ["Infuser", "Includes stainless steel tea filter basket"],
        ["Zero Condensation", "Double-wall prevents sweat rings"]
      ],
      specs: [
        ["Capacity", "500 ml"],
        ["Thermal Retention", "Hot for 4h, Cold for 8h"],
        ["Lid Seal", "Food-grade silicone ring"]
      ],
      reviews: [
        ["★★★★★", "Pure taste! Tea stays steaming hot during my morning commute.", "Chamod L."],
        ["★★★★★", "The bamboo lid looks very classy and the glass feels thick and safe.", "Hiruni S."],
        ["★★★★☆", "Really good bottle, easy to rinse clean.", "Mihiranga P."]
      ]
    },
    6: {
      subtitle: "Heavy duty hemp canvas tote designed for plastic-bag-free shopping.",
      rating: "4.7 / 5 · 52 reviews",
      ratingCount: 52,
      ratingScore: "4.7",
      description: "Constructed with sustainably harvested hemp canvas fibres, known for being four times stronger than conventional cotton. Reinforced cross-stitched handles support up to 20kg.",
      highlights: [
        ["Fabric", "100% Raw Hemp Canvas"],
        ["Strength", "Tested to hold up to 20 kg easily"],
        ["Inner Pocket", "Convenient zipped pocket for keys & wallet"],
        ["Washable", "Machine wash cold & line dry"]
      ],
      specs: [
        ["Dimensions", "42cm x 38cm with 12cm gusset"],
        ["Handle Drop", "28 cm comfortable shoulder hang"],
        ["Certification", "Fair Trade artisan sewn"]
      ],
      reviews: [
        ["★★★★★", "Very strong bag! I carry heavy groceries and coconuts without fear of ripping.", "Praveen J."],
        ["★★★★☆", "Roomy and looks stylish for casual college use too.", "Fathima N."],
        ["★★★★★", "Durable and thick fabric. A real plastic bag saver.", "Yasith P."]
      ]
    },
    7: {
      subtitle: "Natural dish scrubbing fibres straight from the earth.",
      rating: "4.8 / 5 · 43 reviews",
      ratingCount: 43,
      ratingScore: "4.8",
      description: "Made from sun-dried Luffa aegyptiaca fruit vine fibres. Expands when wet to gently lift grease and food residue without scratching pots and non-stick pans.",
      highlights: [
        ["Source", "100% Natural Vine Loofah"],
        ["End of Life", "100% Home compostable in weeks"],
        ["Non-Scratch", "Safe on glass, ceramics & non-stick cookers"],
        ["Zero Microplastics", "No plastic particles washed into oceans"]
      ],
      specs: [
        ["Pack Size", "3 piece scrubber pack"],
        ["Dimensions", "Approx 11cm x 7cm when hydrated"]
      ],
      reviews: [
        ["★★★★★", "Cleans oily pans easily without shedding microplastics.", "Anuki T."],
        ["★★★★☆", "Dries fast between dishwashing sessions.", "Rukshan P."]
      ]
    },
    8: {
      subtitle: "Refill-first approach with brushed recycled glass dispenser.",
      rating: "4.8 / 5 · 68 reviews",
      ratingCount: 68,
      ratingScore: "4.8",
      description: "Durable amber glass dispenser with stainless steel lotion pump. Built to eliminate disposable plastic hand wash bottles forever.",
      highlights: [
        ["Vessel", "Heavy-gauge UV-protective amber glass"],
        ["Pump", "Matte black 304 rust-proof stainless steel"],
        ["Capacity", "Holds 450ml of liquid hand soap or lotion"]
      ],
      specs: [
        ["Volume", "450 ml"],
        ["Pump Output", "2cc smooth dosage"]
      ],
      reviews: [
        ["★★★★★", "Gives my bathroom an upscale spa aesthetic.", "Dilki M."],
        ["★★★★★", "Pump is smooth and durable.", "Kavindu S."]
      ]
    },
    9: {
      subtitle: "100% tree-free artisan paper preserving wildlife habitats.",
      rating: "4.9 / 5 · 39 reviews",
      ratingCount: 39,
      ratingScore: "4.9",
      description: "Sanitized elephant dung mixed with discarded cotton rags to create tree-free deckle edge paper. Smooth writing feel that doesn't bleed ink.",
      highlights: [
        ["Pages", "80 unlined handmade sheets"],
        ["Purity", "Completely odorless & chemical free"],
        ["Cover", "Hardbound with natural twine stitching"]
      ],
      specs: [
        ["Page Size", "A5 (14.8 x 21 cm)"],
        ["Paper Weight", "120 GSM textured sheets"]
      ],
      reviews: [
        ["★★★★★", "An amazing conservation conversation starter!", "Senuri K."],
        ["★★★★★", "Fountain pen ink looks gorgeous on this paper.", "Imran M."]
      ]
    },
    10: {
      subtitle: "Write your thoughts today, harvest fresh herbs tomorrow.",
      rating: "4.8 / 5 · 55 reviews",
      ratingCount: 55,
      ratingScore: "4.8",
      description: "Crafted from tightly rolled recycled newspaper. The top contains a soluble seed capsule. When the pencil becomes too short, plant it in a pot of moist soil!",
      highlights: [
        ["Seeds Included", "Basil, Tomato, Chilli, Marigold"],
        ["Core", "2B smooth natural graphite"],
        ["Body", "Zero wood harvested, 100% recycled paper"]
      ],
      specs: [
        ["Pack Quantity", "5 plantable pencils"],
        ["Germination", "7-14 days after planting"]
      ],
      reviews: [
        ["★★★★★", "Both our tomato pencils sprouted within 10 days! Kids adore it.", "Gayani W."],
        ["★★★★☆", "Writes smoothly and very easy to sharpen.", "Nuwan H."]
      ]
    }
  };

  // State
  let cart = [];
  let currentProduct = null;
  let detailQty = 1;
  let activeImageIdx = 0;
  let currentMultiplier = 1;
  let currentDiscount = 0;

  // Initialize Product Details
  async function initProductDetails() {
    loadSavedCart();

    const urlParams = new URLSearchParams(window.location.search);
    const prodId = parseInt(urlParams.get('id')) || 1;

    try {
      const res = await fetch('/php/web/Soba-Project/backend/api/customer/get_products.php');
      const data = await res.json();
      if (data && data.success && Array.isArray(data.products)) {
        products.splice(0, products.length, ...data.products);
      }
    } catch(err) {
      console.warn("Using local fallback product list", err);
    }

    currentProduct = products.find(p => p.id === prodId) || products[0];
    const details = productDetails[currentProduct.id] || {
      subtitle: currentProduct.material || "Natural eco-friendly craftsmanship.",
      rating: `${currentProduct.rating || 4.9} / 5 · ${currentProduct.reviews_count || 50} reviews`,
      ratingCount: currentProduct.reviews_count || 50,
      ratingScore: (currentProduct.rating || 4.9).toFixed(1),
      description: currentProduct.description || "Zero-waste, sustainable product crafted with renewable natural materials.",
      highlights: [
        ["Material", currentProduct.material || "100% Sustainable Bio-Material"],
        ["Eco Cert", currentProduct.cert || "Zero-Waste Certified"],
        ["Disposal", "100% Biodegradable & Compostable"],
        ["Impact", `Saves ${currentProduct.saved || 'Plastic waste'}`]
      ],
      specs: [
        ["Product Type", "Eco Sustainable Essential"],
        ["SKU", currentProduct.sku || "SOBA-ECO-01"],
        ["Packaging", "100% Recycled Kraft paper carton"],
        ["Origin", "Responsibly Handcrafted in Sri Lanka"]
      ],
      reviews: [
        ["★★★★★", "Incredible eco quality! Truly feels premium and durable.", "Nimna Perera"],
        ["★★★★★", "Delivered promptly in zero-plastic compostable packaging.", "Kasun Wickramasinghe"]
      ]
    };

    // Document & Breadcrumbs
    document.title = `${currentProduct.name} — Soba Zero-Waste`;
    document.getElementById('breadcrumbName').innerText = currentProduct.name;
    document.getElementById('breadcrumbCategory').innerText = currentProduct.category.toUpperCase();

    // Top Header info
    document.getElementById('detailCategory').innerText = currentProduct.category;
    document.getElementById('detailTitle').innerText = currentProduct.name;
    document.getElementById('detailSubtitle').innerText = details.subtitle;
    document.getElementById('detailCert').innerText = currentProduct.cert;
    document.getElementById('detailSaved').innerText = `🌱 Saves ${currentProduct.saved}`;
    document.getElementById('impactMetric').innerText = currentProduct.saved;

    // Rating & reviews
    document.getElementById('detailStars').innerText = details.rating.startsWith('4.') ? '★★★★★' : '★★★★★';
    document.getElementById('detailRatingScore').innerText = details.ratingScore;
    document.getElementById('detailRatingCount').innerText = `${details.ratingCount} Ratings & Reviews`;
    document.getElementById('tabReviewCount').innerText = details.ratingCount;

    // Prices
    renderPricing();

    // Setup Gallery
    setupGallery(currentProduct);

    // Description & Highlights
    document.getElementById('detailDescription').innerText = details.description;
    document.getElementById('detailHighlights').innerHTML = details.highlights.map(h => `
      <div class="tab-highlight-card">
        <b>${h[0]}</b>
        <span>${h[1]}</span>
      </div>
    `).join('');

    // Specs Table
    document.getElementById('specTable').innerHTML = details.specs.map(s => `
      <tr>
        <td class="spec-name">${s[0]}</td>
        <td class="spec-val">${s[1]}</td>
      </tr>
    `).join('');

    // Reviews List
    document.getElementById('detailReviews').innerHTML = details.reviews.map(r => `
      <div class="review-card">
        <div>
          <div class="stars">${r[0]}</div>
          <p>"${r[1]}"</p>
        </div>
        <div class="reviewer-row">
          <strong>${r[2]}</strong>
          <span class="verified-tag">✓ Verified Buyer</span>
        </div>
      </div>
    `).join('');

    // Related Products
    renderRelatedProducts(currentProduct.id);
  }

  // ========================================================
  // GALLERY LOGIC (Daraz Multiple Pictures + Zoom)
  // ========================================================
  function setupGallery(prod) {
    const thumbsContainer = document.getElementById('galleryThumbs');
    activeImageIdx = 0;

    const images = (prod.images && prod.images.length > 0) ? prod.images : [];

    if (images.length === 0) {
      // Fallback if no images
      thumbsContainer.innerHTML = `
        <div class="thumb-item active" onclick="selectImage(0)">
          <span class="thumb-icon-fallback">${prod.icon}</span>
        </div>
      `;
      showFallbackIcon(prod.icon);
      return;
    }

    // Build thumbnails
    thumbsContainer.innerHTML = images.map((imgUrl, idx) => `
      <div class="thumb-item ${idx === 0 ? 'active' : ''}" onclick="selectImage(${idx})">
        <img src="${imgUrl}" alt="Thumb ${idx + 1}" onerror="this.parentElement.innerHTML='<span class=\\'thumb-icon-fallback\\'>${prod.icon}</span>'">
      </div>
    `).join('');

    displayMainImage(images[0]);
  }

  function selectImage(idx) {
    const images = currentProduct.images || [];
    if (idx < 0 || idx >= images.length) return;
    activeImageIdx = idx;

    // Update active thumb
    const thumbs = document.querySelectorAll('.thumb-item');
    thumbs.forEach((t, i) => {
      t.classList.toggle('active', i === idx);
    });

    displayMainImage(images[idx]);
  }

  function changeImageIndex(delta) {
    const images = currentProduct.images || [];
    if (images.length <= 1) return;
    let nextIdx = activeImageIdx + delta;
    if (nextIdx < 0) nextIdx = images.length - 1;
    if (nextIdx >= images.length) nextIdx = 0;
    selectImage(nextIdx);
  }

  function displayMainImage(url) {
    const imgEl = document.getElementById('mainProductImg');
    const iconEl = document.getElementById('mainProductIcon');

    if (!url) {
      showFallbackIcon(currentProduct.icon);
      return;
    }

    imgEl.style.display = 'block';
    iconEl.style.display = 'none';
    imgEl.style.opacity = '0';

    const tempImg = new Image();
    tempImg.src = url;
    tempImg.onload = () => {
      imgEl.src = url;
      imgEl.style.opacity = '1';
    };
    tempImg.onerror = () => {
      showFallbackIcon(currentProduct.icon);
    };
  }

  function showFallbackIcon(icon) {
    const imgEl = document.getElementById('mainProductImg');
    const iconEl = document.getElementById('mainProductIcon');
    imgEl.style.display = 'none';
    iconEl.style.display = 'block';
    iconEl.innerText = icon;
  }

  // Hover Zoom Effect
  function handleImageZoom(e) {
    const stage = document.getElementById('galleryMainStage');
    const img = document.getElementById('mainProductImg');
    if (img.style.display === 'none') return;

    const rect = stage.getBoundingClientRect();
    const x = ((e.clientX - rect.left) / rect.width) * 100;
    const y = ((e.clientY - rect.top) / rect.height) * 100;

    img.style.transformOrigin = `${x}% ${y}%`;
    img.style.transform = `scale(1.75)`;
  }

  function resetImageZoom() {
    const img = document.getElementById('mainProductImg');
    img.style.transform = `scale(1)`;
  }

  // Lightbox view
  function openLightbox() {
    const images = currentProduct.images || [];
    if (images.length === 0) return;
    const currentUrl = images[activeImageIdx] || images[0];
    document.getElementById('lightboxImg').src = currentUrl;
    document.getElementById('lightboxModal').classList.add('open');
  }

  function closeLightbox() {
    document.getElementById('lightboxModal').classList.remove('open');
  }

  // ========================================================
  // VARIANT & PRICING LOGIC
  // ========================================================
  function selectVariant(btn, multiplier, label, discountRate) {
    document.querySelectorAll('.variant-chip').forEach(c => c.classList.remove('active'));
    btn.classList.add('active');
    currentMultiplier = multiplier;
    currentDiscount = discountRate;
    document.getElementById('selectedVariantLabel').innerText = label;
    renderPricing();
  }

  function renderPricing() {
    const basePrice = currentProduct.price;
    const baseOriginal = currentProduct.originalPrice || Math.round(basePrice * 1.35);

    const effectiveTotal = Math.round(basePrice * currentMultiplier * (1 - currentDiscount));
    const effectiveOriginal = Math.round(baseOriginal * currentMultiplier);

    document.getElementById('detailPrice').innerText = `LKR ${effectiveTotal.toLocaleString()}`;
    document.getElementById('detailOriginalPrice').innerText = `LKR ${effectiveOriginal.toLocaleString()}`;
    
    const pct = Math.round(((effectiveOriginal - effectiveTotal) / effectiveOriginal) * 100);
    document.getElementById('detailDiscountTag').innerText = `-${pct}%`;

    const installment = Math.round(effectiveTotal / 3);
    document.getElementById('installmentPrice').innerText = `LKR ${installment.toLocaleString()}`;
  }

  function changeDetailQty(delta) {
    detailQty += delta;
    if (detailQty < 1) detailQty = 1;
    document.getElementById('detailQtyVal').innerText = detailQty;
  }

  // Tab switching
  function switchTab(tabId, clickedBtn) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));

    const targetPane = document.getElementById(`tab-${tabId}`);
    if (targetPane) targetPane.classList.add('active');

    if (clickedBtn) {
      clickedBtn.classList.add('active');
    } else {
      const btn = Array.from(document.querySelectorAll('.tab-btn')).find(b => b.textContent.toLowerCase().includes(tabId));
      if (btn) btn.classList.add('active');
    }
  }

  // Add To Cart & Buy Now
  function addDetailProductToCart() {
    if (!currentProduct) return;
    
    const unitPrice = Math.round(currentProduct.price * (1 - currentDiscount));
    const effectiveQty = detailQty * currentMultiplier;

    const existing = cart.find(item => item.id === currentProduct.id);
    if (existing) {
      existing.quantity += effectiveQty;
    } else {
      cart.push({ 
        ...currentProduct, 
        price: unitPrice,
        quantity: effectiveQty, 
        selected: false 
      });
    }

    try {
      localStorage.setItem('soba_cart', JSON.stringify(cart));
    } catch(e) {}

    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    const cartCountEl = document.getElementById('cartCount');
    if (cartCountEl) cartCountEl.innerText = totalItems;

    const addBtn = document.getElementById('detailAddBtn');
    addBtn.classList.add('added');
    addBtn.innerHTML = '<span>Added to Cart ✓</span>';
    setTimeout(() => {
      addBtn.classList.remove('added');
      addBtn.innerHTML = '🛒 <span>Add to Cart</span>';
    }, 1400);

    showCartToast(currentProduct.name);
  }

  function showCartToast(name) {
    const toast = document.getElementById('cartToast');
    const toastText = document.getElementById('toastText');
    if (!toast) return;
    toastText.innerText = `${name || 'Item'} added to Cart!`;
    toast.classList.add('show');
    setTimeout(() => {
      toast.classList.remove('show');
    }, 3500);
  }

  function buyNow() {
    if (!currentProduct) return;
    addDetailProductToCart();
    window.location.href = 'checkout.php';
  }

  // Related items
  function renderRelatedProducts(currentId) {
    const relatedContainer = document.getElementById('relatedGrid');
    const related = products.filter(p => p.id !== currentId).slice(0, 4);

    relatedContainer.innerHTML = related.map(p => `
      <a class="card" href="product_details.php?id=${p.id}">
        <div class="card-art">
          <span class="card-cert">${p.cert}</span>
          ${(p.images && p.images[0]) 
            ? `<img src="${p.images[0]}" alt="${p.name}">` 
            : `<span class="emoji-icon">${p.icon}</span>`}
          <span class="poster-price-tag">LKR ${p.price.toLocaleString()}</span>
        </div>
        <div class="card-body">
          <div class="card-meta">${p.category}</div>
          <h3>${p.name}</h3>
          <div class="card-saved">🌱 Saves ${p.saved}</div>
        </div>
      </a>
    `).join('');
  }

  function doHeaderSearch() {
    const q = document.getElementById('productSearch').value.trim();
    if (q) window.location.href = `products.php?search=${encodeURIComponent(q)}`;
    else window.location.href = `products.php`;
  }

  // ========================================================
  // CART DRAWER LOGIC
  // ========================================================
  function loadSavedCart() {
    try {
      const stored = localStorage.getItem('soba_cart');
      if (stored) cart = JSON.parse(stored);
    } catch(e) {
      cart = [];
    }
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    const cartCountEl = document.getElementById('cartCount');
    if (cartCountEl) cartCountEl.innerText = totalItems;
  }

  // Mouse Spotlight Motion disabled

  // Nav indicator
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
    initProductDetails();
    moveNavIndicator(null, false);

    try {
      const stored = localStorage.getItem('soba_user');
      if (stored) {
        const u = JSON.parse(stored);
        if (u.name) {
          const accLabel = document.getElementById('detailsHeaderUserName');
          if (accLabel) accLabel.textContent = u.name.split(' ')[0];
        }
        if (u.role && u.role.toLowerCase() === 'admin') {
          const accLink = document.getElementById('detailsHeaderAccount');
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
