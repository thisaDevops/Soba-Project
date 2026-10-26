<?php
require_once __DIR__ . '/../../../backend/includes/session_handler.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$sessionUser = !empty($_SESSION['u_id']) ? [
    'id' => $_SESSION['u_id'],
    'name' => $_SESSION['name'] ?? '',
    'email' => $_SESSION['email'] ?? '',
    'role' => $_SESSION['role'] ?? 'customer'
] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Shopping Cart — Soba Zero-Waste Marketplace</title>
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
    max-width: 600px; 
    display: flex; 
    height: 44px; 
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
  .search-go { background: var(--neo); border: none; width: 54px; cursor: pointer; font-size: 16px; color: #102117; }

  .bar-right { display: flex; align-items: center; gap: 16px; }
  .cart-badge-link {
    background: rgba(184, 255, 106, 0.15); color: #fff; border: 1px solid var(--neo);
    border-radius: 999px; padding: 9px 18px; font-size: 13.5px; font-weight: 600;
    display: flex; align-items: center; gap: 8px; text-decoration: none; transition: all 0.2s;
  }
  .cart-badge-count {
    background: var(--neo); color: #102117; border-radius: 999px; font-size: 11px;
    min-width: 20px; height: 20px; display: flex; align-items: center; justify-content: center;
    padding: 0 6px; font-weight: 800; font-family: 'IBM Plex Mono', monospace;
  }

  .nav-account-link {
    color: rgba(255, 255, 255, 0.78);
    font-size: 13.5px;
    font-weight: 600;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 99px;
    border: 1px solid rgba(255, 255, 255, 0.12);
    transition: all 0.2s;
  }
  .nav-account-link:hover {
    color: #fff;
    border-color: var(--neo);
    background: rgba(184, 255, 106, 0.1);
  }

  /* Sub Navigation Bar (Referencing Home & Shop) */
  .header-nav {
    background: rgba(6, 23, 17, 0.75);
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(16px);
  }
  .header-nav .bar {
    max-width: 1480px;
    margin: 0 auto;
    padding: 0 28px;
    display: flex;
    align-items: center;
    justify-content: space-between;
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
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    font-size: 13.5px;
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

  .nav-back-home {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: var(--neo);
    font-size: 12.5px;
    font-weight: 700;
    text-decoration: none;
    padding: 5px 12px;
    border-radius: 8px;
    background: rgba(184, 255, 106, 0.08);
    border: 1px solid rgba(184, 255, 106, 0.25);
    transition: all 0.2s;
  }
  .nav-back-home:hover {
    background: var(--neo);
    color: #102117;
  }

  /* Breadcrumb Navigation */
  .breadcrumb-shell {
    max-width: 1440px;
    margin: 18px auto 0;
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

  /* ===== Daraz-Style Checkout Flow Stepper ===== */
  .stepper-shell {
    max-width: 1440px;
    margin: 24px auto 10px;
    padding: 0 28px;
  }
  .daraz-stepper {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 18px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 16px;
    padding: 14px 24px;
    max-width: 680px;
    margin: 0 auto;
  }
  .step-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 600;
    color: rgba(255,255,255,0.45);
  }
  .step-num {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-family: 'IBM Plex Mono', monospace;
    font-weight: 700;
    background: rgba(255,255,255,0.1);
    color: rgba(255,255,255,0.6);
  }
  .step-item.active {
    color: var(--neo);
  }
  .step-item.active .step-num {
    background: var(--neo);
    color: #081d14;
    box-shadow: 0 0 10px rgba(184,255,106,0.4);
  }
  .step-connector {
    width: 36px;
    height: 2px;
    background: rgba(255,255,255,0.12);
  }

  /* ===== Free Shipping Incentive Progress Bar ===== */
  .shipping-goal-card {
    background: linear-gradient(135deg, rgba(184, 255, 106, 0.12) 0%, rgba(12, 45, 33, 0.4) 100%);
    border: 1px solid rgba(184, 255, 106, 0.25);
    border-radius: 16px;
    padding: 14px 22px;
    margin-bottom: 20px;
    display: flex;
    flex-direction: column;
    gap: 8px;
  }
  .shipping-goal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 13px;
    font-weight: 600;
  }
  .shipping-goal-header span b { color: var(--neo); }
  .shipping-goal-bar {
    height: 7px;
    background: rgba(255,255,255,0.1);
    border-radius: 999px;
    overflow: hidden;
  }
  .shipping-goal-fill {
    height: 100%;
    background: linear-gradient(90deg, #5FAE47, var(--neo));
    border-radius: 999px;
    width: 0%;
    transition: width 0.4s ease;
    box-shadow: 0 0 10px var(--neo);
  }

  /* ===== Main Cart Layout ===== */
  .cart-container {
    max-width: 1440px;
    margin: 15px auto 80px;
    padding: 0 28px;
    display: grid;
    grid-template-columns: 1fr 390px;
    gap: 28px;
    align-items: start;
  }

  /* Left Panel */
  .cart-left-pane {
    display: flex;
    flex-direction: column;
    gap: 16px;
  }

  /* Bulk Selection Header (Daraz Style) */
  .bulk-header-card {
    background: rgba(7, 28, 21, 0.7);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 18px;
    padding: 14px 22px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    backdrop-filter: blur(14px);
  }
  .bulk-select-label {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 13.5px;
    font-weight: 600;
    cursor: pointer;
    color: rgba(255,255,255,0.9);
  }
  .bulk-checkbox, .item-checkbox {
    accent-color: var(--neo);
    width: 18px;
    height: 18px;
    cursor: pointer;
  }
  .bulk-actions-group {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .btn-del-selected {
    background: rgba(200, 109, 81, 0.15);
    color: var(--clay-soft);
    border: 1px solid rgba(200, 109, 81, 0.3);
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 12.5px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
  }
  .btn-del-selected:hover:not(:disabled) {
    background: var(--clay);
    color: #fff;
  }
  .btn-del-selected:disabled {
    opacity: 0.35;
    cursor: not-allowed;
    border-color: rgba(255,255,255,0.1);
    color: rgba(255,255,255,0.4);
    background: transparent;
  }

  /* Store Grouping Card (Daraz Store Block) */
  .store-group-card {
    background:
      radial-gradient(circle at 10% 8%, rgba(184,255,106,.06), transparent 30%),
      linear-gradient(145deg, rgba(16, 45, 34, 0.85) 0%, rgba(7, 27, 20, 0.95) 100%);
    border: 1px solid rgba(184, 255, 106, 0.22);
    border-radius: 22px;
    padding: 20px 24px;
    box-shadow: 0 25px 60px rgba(0,0,0,0.45);
    backdrop-filter: blur(18px);
  }
  .store-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 14px;
    border-bottom: 1px solid rgba(255,255,255,0.08);
    margin-bottom: 8px;
  }
  .store-title-badge {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    font-weight: 700;
    color: #f5fff1;
  }
  .store-flagship-tag {
    background: var(--neo);
    color: #081d14;
    font-size: 10px;
    font-weight: 800;
    padding: 2px 8px;
    border-radius: 4px;
    letter-spacing: .08em;
    text-transform: uppercase;
  }
  .store-voucher-btn {
    font-size: 12px;
    color: var(--neo);
    background: rgba(184, 255, 106, 0.1);
    border: 1px solid rgba(184, 255, 106, 0.3);
    padding: 4px 10px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
  }

  /* Daraz-Style Item Card */
  .cart-item-card {
    display: grid;
    grid-template-columns: 24px 90px 1fr 140px;
    gap: 18px;
    align-items: center;
    padding: 18px 0;
    border-bottom: 1px solid rgba(255,255,255,0.07);
    transition: background 0.2s;
  }
  .cart-item-card:last-child {
    border-bottom: none;
    padding-bottom: 4px;
  }

  .item-thumb-box {
    width: 90px;
    height: 90px;
    border-radius: 14px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.12);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 38px;
    overflow: hidden;
    position: relative;
    cursor: pointer;
  }
  .item-thumb-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.25s;
  }
  .item-thumb-box:hover img { transform: scale(1.08); }

  .item-info-col {
    display: flex;
    flex-direction: column;
    gap: 6px;
  }
  .item-title-link {
    color: #f5fff1;
    text-decoration: none;
    font-size: 15px;
    font-weight: 600;
    line-height: 1.35;
    transition: color 0.2s;
  }
  .item-title-link:hover { color: var(--neo); }

  .item-variant-tag {
    font-size: 11.5px;
    color: rgba(255,255,255,0.6);
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    padding: 3px 8px;
    border-radius: 6px;
    width: fit-content;
  }
  .item-eco-pill {
    font-size: 11px;
    color: var(--neo);
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
  }

  /* Price & Counter Column */
  .item-action-col {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 10px;
  }
  .item-price-daraz {
    text-align: right;
  }
  .item-current-price {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 17px;
    font-weight: 700;
    color: var(--neo);
  }
  .item-original-price {
    font-size: 12px;
    color: rgba(255,255,255,0.4);
    text-decoration: line-through;
    font-family: 'IBM Plex Mono', monospace;
  }

  /* Modern Quantity Pill */
  .daraz-qty-pill {
    display: flex;
    align-items: center;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.16);
    border-radius: 8px;
    overflow: hidden;
  }
  .daraz-qty-btn {
    background: transparent;
    border: none;
    color: #fff;
    width: 26px;
    height: 26px;
    font-size: 15px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
  }
  .daraz-qty-btn:hover:not(:disabled) {
    background: var(--neo);
    color: #081d14;
  }
  .daraz-qty-btn:disabled {
    opacity: 0.3;
    cursor: not-allowed;
  }
  .daraz-qty-num {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 13px;
    font-weight: 700;
    min-width: 24px;
    text-align: center;
  }

  .item-tool-btns {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .btn-tool-icon {
    background: none;
    border: none;
    color: rgba(255,255,255,0.4);
    font-size: 15px;
    cursor: pointer;
    transition: color 0.2s, transform 0.2s;
  }
  .btn-tool-icon:hover { color: var(--clay); transform: scale(1.15); }
  .btn-tool-icon.wish:hover { color: #ff6b81; }

  /* Empty Cart State */
  .empty-cart-state {
    text-align: center;
    padding: 60px 20px;
    background: rgba(7, 28, 21, 0.6);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 24px;
  }
  .empty-cart-icon { font-size: 72px; margin-bottom: 14px; display: inline-block; }
  .empty-cart-state h3 { font-size: 24px; color: #f5fff1; margin-bottom: 8px; }
  .empty-cart-state p { color: rgba(255,255,255,0.6); font-size: 14.5px; max-width: 420px; margin: 0 auto 22px; line-height: 1.6; }
  .btn-explore-catalog {
    background: var(--neo);
    color: #081d14;
    text-decoration: none;
    padding: 13px 26px;
    border-radius: 12px;
    font-weight: 800;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: .05em;
    display: inline-block;
    transition: all 0.2s;
  }
  .btn-explore-catalog:hover {
    box-shadow: 0 0 24px rgba(184, 255, 106, 0.45);
    transform: translateY(-2px);
  }

  /* Right Panel: Daraz-Style Order Summary */
  .order-summary-sidebar {
    background:
      radial-gradient(circle at 10% 10%, rgba(184,255,106,.09), transparent 35%),
      linear-gradient(145deg, rgba(16, 45, 34, 0.92) 0%, rgba(7, 27, 20, 0.98) 100%);
    border: 1px solid rgba(184, 255, 106, 0.25);
    border-radius: 24px;
    padding: 26px;
    box-shadow: 0 25px 60px rgba(0,0,0,0.5);
    position: sticky;
    top: 90px;
  }
  .summary-head {
    font-size: 20px;
    color: #f5fff1;
    margin-bottom: 18px;
    padding-bottom: 12px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    display: flex;
    justify-content: space-between;
    align-items: baseline;
  }
  .summary-head small {
    font-size: 12px;
    color: rgba(255,255,255,0.5);
    font-family: 'IBM Plex Mono', monospace;
  }

  .calc-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 11px;
    font-size: 13.5px;
    color: rgba(255,255,255,0.72);
  }
  .calc-row.green { color: var(--neo); font-weight: 600; }
  .calc-val {
    font-family: 'IBM Plex Mono', monospace;
    font-weight: 600;
    color: #fff;
  }

  /* Voucher Input Box */
  .voucher-box {
    margin: 18px 0;
    display: flex;
    gap: 8px;
  }
  .voucher-input {
    flex: 1;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 10px;
    padding: 10px 14px;
    color: #fff;
    font-family: 'Work Sans', sans-serif;
    font-size: 13px;
    outline: none;
  }
  .voucher-input:focus { border-color: var(--neo); }
  .voucher-btn {
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    color: #fff;
    border-radius: 10px;
    padding: 0 16px;
    font-weight: 700;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s;
  }
  .voucher-btn:hover { background: var(--neo); color: #081d14; border-color: var(--neo); }

  /* Eco Points Toggle */
  .eco-points-box {
    background: rgba(184, 255, 106, 0.08);
    border: 1px solid rgba(184, 255, 106, 0.2);
    border-radius: 12px;
    padding: 10px 14px;
    margin-bottom: 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 12.5px;
  }
  .eco-points-box span { color: #e2ffd0; }
  .toggle-switch {
    position: relative; width: 38px; height: 20px;
  }
  .toggle-switch input { opacity: 0; width: 0; height: 0; }
  .slider {
    position: absolute; cursor: pointer; inset: 0; background-color: rgba(255,255,255,0.2);
    border-radius: 20px; transition: .3s;
  }
  .slider:before {
    position: absolute; content: ""; height: 14px; width: 14px; left: 3px; bottom: 3px;
    background-color: white; border-radius: 50%; transition: .3s;
  }
  .toggle-switch input:checked + .slider { background-color: var(--neo); }
  .toggle-switch input:checked + .slider:before { transform: translateX(18px); background-color: #081d14; }

  /* Grand Total */
  .grand-total-divider {
    border-top: 1px solid rgba(255,255,255,0.1);
    padding-top: 16px;
    margin-top: 14px;
  }
  .grand-total-row {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    margin-bottom: 20px;
  }
  .grand-total-label { font-size: 15px; font-weight: 700; color: #fff; }
  .grand-total-price {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 28px;
    font-weight: 700;
    color: var(--neo);
    text-shadow: 0 0 16px rgba(184,255,106,0.3);
  }

  .btn-checkout-daraz {
    width: 100%;
    background: var(--neo);
    color: #081d14;
    border: none;
    border-radius: 14px;
    padding: 16px;
    font-size: 15px;
    font-weight: 800;
    cursor: pointer;
    text-transform: uppercase;
    letter-spacing: .06em;
    transition: all 0.25s;
    font-family: 'Work Sans', sans-serif;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
  }
  .btn-checkout-daraz:hover:not(:disabled) {
    box-shadow: 0 0 30px rgba(184, 255, 106, 0.45);
    transform: translateY(-2px);
  }
  .btn-checkout-daraz:disabled {
    opacity: 0.35;
    cursor: not-allowed;
  }

  /* Payment methods badges */
  .payment-icons-row {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-top: 18px;
    padding-top: 14px;
    border-top: 1px solid rgba(255,255,255,0.08);
    font-size: 11px;
    color: rgba(255,255,255,0.5);
    flex-wrap: wrap;
  }
  .payment-tag {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    padding: 3px 8px;
    border-radius: 4px;
  }

  /* ===== Recommendations Section ===== */
  .recommend-shell {
    max-width: 1440px;
    margin: 30px auto 90px;
    padding: 0 28px;
  }
  .recommend-head {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    margin-bottom: 22px;
  }
  .recommend-head h2 { font-size: 26px; color: #f5fff1; }
  .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 24px; }
  
  .card {
    position: relative;
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.07) 0%, rgba(255, 255, 255, 0.03) 100%);
    border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 20px;
    overflow: hidden; display: flex; flex-direction: column; backdrop-filter: blur(16px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.25);
    transition: transform 0.35s, box-shadow 0.35s;
    text-decoration: none;
    color: inherit;
  }
  .card:hover {
    transform: translateY(-6px);
    box-shadow: 0 30px 60px rgba(0,0,0,0.4), 0 0 30px rgba(184, 255, 106, 0.18);
  }
  .card-art {
    height: 170px; display: flex; align-items: center; justify-content: center; font-size: 60px;
    background: radial-gradient(circle at 50% 50%, rgba(184, 255, 106, 0.12), transparent 70%),
                linear-gradient(160deg, rgba(255,255,255,0.05), rgba(8, 32, 24, 0.4));
    position: relative; overflow: hidden;
  }
  .card-art img { width: 100%; height: 100%; object-fit: cover; }
  .card-cert {
    position: absolute; top: 12px; left: 12px;
    background: rgba(7, 28, 21, 0.75); color: var(--neo);
    border: 1px solid rgba(184, 255, 106, 0.4); font-size: 10px; font-weight: 700; padding: 4px 8px; border-radius: 6px;
  }
  .poster-price-tag {
    position: absolute; bottom: 10px; right: 12px;
    background: var(--neo); color: #081d14;
    padding: 5px 12px; border-radius: 999px;
    font-family: 'IBM Plex Mono', monospace; font-weight: 700; font-size: 13px;
  }
  .card-body { padding: 18px; display: flex; flex-direction: column; gap: 6px; flex: 1; background: rgba(6, 22, 16, 0.3); }
  .card-body h3 { font-size: 16px; font-weight: 700; color: #fff; margin: 0; }
  .card-meta { font-size: 11px; color: rgba(255,255,255,0.45); text-transform: uppercase; }

  /* ===== Footer ===== */
  footer { background: #04110c; border-top: 1px solid rgba(255,255,255,0.08); padding: 60px 28px 24px; font-size: 13px; color: rgba(255,255,255,0.55); margin-top: 90px; }
  .footer-inner { max-width: 1420px; margin: 0 auto; padding-bottom: 40px; display: grid; grid-template-columns: 1.6fr 1fr 1fr 1fr; gap: 36px; }
  .footer-col { display: flex; flex-direction: column; gap: 12px; }
  .footer-col a { color: rgba(255,255,255,0.65); text-decoration: none; font-size: 13.5px; transition: color 0.2s; }
  .footer-col a:hover { color: var(--neo); }
  .footer-title { font-size: 12px; text-transform: uppercase; letter-spacing: 0.12em; color: var(--neo); font-weight: 700; margin-bottom: 6px; }

  @media (max-width: 1024px) {
    .cart-container { grid-template-columns: 1fr; }
    .order-summary-sidebar { position: static; }
  }
  @media (max-width: 768px) {
    header { padding: 0 !important; position: sticky !important; top: 0 !important; z-index: 100 !important; width: 100% !important; }
    .header-top { padding: 10px 14px !important; }
    .header-top .bar { display: flex !important; flex-direction: row !important; flex-wrap: wrap !important; align-items: center !important; justify-content: space-between !important; gap: 10px !important; padding: 0 !important; }
    .logo { order: 1 !important; margin: 0 !important; }
    .logo .mark { font-size: 22px !important; }
    .logo .sub { font-size: 9.5px !important; }
    .bar-right { order: 2 !important; display: flex !important; align-items: center !important; gap: 8px !important; margin: 0 !important; }
    .nav-account-link, .cart-badge-link { padding: 7px 12px !important; font-size: 12px !important; }
    .search-group { order: 3 !important; width: 100% !important; max-width: 100% !important; height: 40px !important; border-radius: 12px !important; margin: 0 !important; }
    .search-group input { font-size: 13px !important; padding: 0 12px !important; }
    .search-go { width: 44px !important; font-size: 14px !important; }

    .header-nav { height: 42px !important; background: rgba(6, 23, 17, 0.9) !important; border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important; }
    .header-nav .bar { display: flex !important; flex-direction: row !important; align-items: center !important; justify-content: flex-start !important; height: 100% !important; padding: 0 12px !important; overflow: hidden !important; }
    .header-nav nav { display: flex !important; flex-direction: row !important; align-items: center !important; gap: 16px !important; height: 100% !important; width: 100% !important; overflow-x: auto !important; -webkit-overflow-scrolling: touch !important; white-space: nowrap !important; scrollbar-width: none !important; padding: 0 4px !important; }
    .header-nav nav::-webkit-scrollbar { display: none !important; }
    .header-nav nav a { font-size: 13px !important; white-space: nowrap !important; flex-shrink: 0 !important; padding-bottom: 2px !important; }
    .nav-back-home { display: none !important; }
    .nav-indicator { display: none !important; }

    .cart-item-card {
      grid-template-columns: 24px 75px 1fr;
      grid-template-rows: auto auto;
      gap: 12px;
    }
    .item-action-col {
      grid-column: 3 / 4;
      flex-direction: row;
      justify-content: space-between;
      align-items: center;
      margin-top: 4px;
    }
    .item-price-daraz { text-align: left; }
    .footer-inner { grid-template-columns: 1fr 1fr; }
  }
  @media (max-width: 580px) {
    .footer-inner { grid-template-columns: 1fr; }
    .cart-container { padding: 0 14px; }
    .stepper-shell { padding: 0 14px; }
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
        <a href="user_dashboard.php" class="nav-account-link" id="cartHeaderAccount">
          👤 <span id="cartHeaderUserName">Account</span>
        </a>
        <a href="cart.php" class="cart-badge-link">
          🛒 <span>Cart</span>
          <span class="cart-badge-count" id="cartCount">0</span>
        </a>
      </div>
    </div>
  </div>

  <div class="header-nav">
    <div class="bar">
      <nav id="navbar">
        <a class="nav-link" href="../../home.php" data-nav="home">Home</a>
        <a class="nav-link" href="products.php" data-nav="shop">Shop Products</a>
        <a class="nav-link active" href="cart.php" data-nav="cart">My Cart</a>
        <a class="nav-link" href="../../home.php#about-section" data-nav="about">About</a>
        <a class="nav-link" href="../../home.php#reviews-section" data-nav="reviews">Reviews</a>
        <a class="nav-link" href="../../home.php#contact-section" data-nav="contact">Contact</a>
        <div class="nav-indicator" id="nav-indicator"></div>
      </nav>
      <a href="../../home.php" class="nav-back-home">
        &larr; Back to Home
      </a>
    </div>
  </div>
</header>

<!-- Breadcrumbs -->
<div class="breadcrumb-shell">
  <div class="breadcrumb">
    <a href="../../home.php">Home</a>
    <span class="separator">/</span>
    <a href="products.php">Shop</a>
    <span class="separator">/</span>
    <span class="current">Shopping Cart</span>
  </div>
</div>

<!-- Daraz Checkout Flow Stepper -->
<div class="stepper-shell">
  <div class="daraz-stepper">
    <div class="step-item active">
      <div class="step-num">1</div>
      <span>Shopping Cart</span>
    </div>
    <div class="step-connector"></div>
    <div class="step-item">
      <div class="step-num">2</div>
      <span>Shipping & Checkout</span>
    </div>
    <div class="step-connector"></div>
    <div class="step-item">
      <div class="step-num">3</div>
      <span>Order Confirmation</span>
    </div>
  </div>
</div>

<!-- Main Cart Interface -->
<main class="cart-container">
  
  <!-- Left Side: Basket Items & Store Groups -->
  <div class="cart-left-pane">
    
    <!-- Free Shipping Progress Tracker -->
    <div class="shipping-goal-card" id="shippingGoalCard">
      <div class="shipping-goal-header">
        <span id="shippingGoalText">🚚 Add <b>LKR 2,500</b> more to enjoy <b>FREE DELIVERY</b></span>
        <span style="color:var(--neo); font-family:'IBM Plex Mono';" id="shippingGoalPct">0%</span>
      </div>
      <div class="shipping-goal-bar">
        <div class="shipping-goal-fill" id="shippingGoalFill"></div>
      </div>
    </div>

    <!-- Bulk Action Header -->
    <div class="bulk-header-card" id="bulkHeaderCard">
      <label class="bulk-select-label">
        <input type="checkbox" id="selectAllCheckbox" class="bulk-checkbox" onchange="toggleSelectAllItems(this.checked)">
        <span>Select All (<span id="selectedCountDisplay">0</span> items)</span>
      </label>

      <div class="bulk-actions-group">
        <button class="btn-del-selected" id="btnDeleteSelected" disabled onclick="deleteSelectedItems()">
          🗑️ Delete Selected
        </button>
        <button class="btn-del-selected" onclick="clearCart()">
          Clear All
        </button>
      </div>
    </div>

    <!-- Store-Grouped Cart Items Container -->
    <div id="cartItemsContainer">
      <!-- Injected via JavaScript -->
    </div>

  </div>

  <!-- Right Side: Daraz-Style Sticky Order Summary -->
  <aside class="order-summary-sidebar">
    <div class="summary-head">
      <span>Order Summary</span>
      <small id="summaryItemCount">0 items</small>
    </div>

    <div class="calc-row">
      <span>Subtotal</span>
      <span class="calc-val" id="summarySubtotal">LKR 0</span>
    </div>

    <div class="calc-row">
      <span>Shipping Fee</span>
      <span class="calc-val" id="summaryShipping">LKR 350</span>
    </div>

    <div class="calc-row green" id="freeDeliveryLine" style="display: none;">
      <span>🎉 Free Delivery Promotion</span>
      <span class="calc-val" style="color:var(--neo);">-LKR 350</span>
    </div>

    <div class="calc-row green" id="voucherDiscountLine" style="display: none;">
      <span>Promo Voucher</span>
      <span class="calc-val" id="summaryVoucherDiscount" style="color:var(--clay-soft);">-LKR 0</span>
    </div>

    <div class="calc-row green" id="ecoPointsLine" style="display: none;">
      <span>🪙 Eco-Points Redeemed</span>
      <span class="calc-val" style="color:var(--neo);">-LKR 150</span>
    </div>

    <!-- Voucher Code Input -->
    <div class="voucher-box">
      <input type="text" id="voucherInput" class="voucher-input" placeholder="Enter Voucher (e.g. ECO10)">
      <button class="voucher-btn" onclick="applyVoucher()">Apply</button>
    </div>

    <!-- Eco Points Toggle -->
    <div class="eco-points-box">
      <span>🪙 Use 150 Eco-Points (Save LKR 150)</span>
      <label class="toggle-switch">
        <input type="checkbox" id="ecoPointsToggle" onchange="toggleEcoPoints(this.checked)">
        <span class="slider"></span>
      </label>
    </div>

    <!-- Grand Total -->
    <div class="grand-total-divider">
      <div class="grand-total-row">
        <span class="grand-total-label">Total Payment</span>
        <span class="grand-total-price" id="summaryGrandTotal">LKR 0</span>
      </div>

      <button class="btn-checkout-daraz" id="btnCheckout" onclick="proceedToCheckout()">
        Proceed to Checkout (<span id="checkoutCountDisplay">0</span>) ⚡
      </button>
    </div>

    <!-- Payment Trust Badges -->
    <div class="payment-icons-row">
      <span class="payment-tag">💳 Visa / Master</span>
      <span class="payment-tag">⚡ Koko / Mintpay</span>
      <span class="payment-tag">💵 Cash on Delivery</span>
    </div>
  </aside>

</main>

<!-- Recommendations Section (Daraz 'You May Also Like') -->
<section class="recommend-shell">
  <div class="recommend-head">
    <h2>You May Also Like</h2>
    <a href="products.php" style="color:var(--neo); text-decoration:none; font-size:13.5px; font-weight:600;">View All →</a>
  </div>
  <div class="grid" id="recommendGrid">
    <!-- Populated via JS -->
  </div>
</section>

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

<!-- Login Required for Checkout Modal Popup -->
<div class="soba-modal-backdrop" id="loginPromptModal" onclick="if(event.target===this) closeLoginPromptModal()">
  <div class="soba-modal-box" style="max-width: 440px; text-align: center; padding: 36px 30px; border-radius: 24px; position: relative; border: 1px solid rgba(184, 255, 61, 0.35); box-shadow: 0 30px 90px rgba(0,0,0,0.8), 0 0 35px rgba(184,255,61,0.15);">
    <button type="button" onclick="closeLoginPromptModal()" style="position: absolute; top: 16px; right: 18px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.15); border-radius: 50%; width: 32px; height: 32px; color: rgba(255,255,255,0.7); font-size: 18px; cursor: pointer; display: flex; align-items: center; justify-content: center; line-height: 1; transition: all 0.2s;" onmouseover="this.style.color='#fff'; this.style.borderColor='var(--neo)';" onmouseout="this.style.color='rgba(255,255,255,0.7)'; this.style.borderColor='rgba(255,255,255,0.15)';">&times;</button>
    
    <div style="width: 64px; height: 64px; border-radius: 50%; background: rgba(184, 255, 61, 0.12); border: 1.5px solid var(--neo); color: var(--neo); display: flex; align-items: center; justify-content: center; font-size: 28px; margin: 0 auto 18px; box-shadow: 0 0 25px rgba(184, 255, 61, 0.25);">
      🔒
    </div>
    
    <h3 style="font-family: 'Fraunces', serif; font-size: 24px; color: #fff; margin-bottom: 10px; font-weight: 600; letter-spacing: -0.01em;">
      Sign In Required to Checkout
    </h3>
    
    <p style="font-size: 13.5px; color: rgba(244, 242, 233, 0.75); line-height: 1.6; margin-bottom: 24px;">
      Please log in to your Soba account to complete your order, earn eco-points, and track plastic-free delivery.
    </p>
    
    <div style="display: flex; flex-direction: column; gap: 12px;">
      <a href="../../login.php?redirect=modules/customer/cart.php&auto_checkout=1" class="btn-checkout-daraz" style="text-decoration: none; text-align: center; justify-content: center; font-weight: 700; border-radius: 12px; padding: 14px 20px; font-size: 15px;">
        Login to My Account →
      </a>
      <a href="register.php?redirect=cart.php&auto_checkout=1" style="display: inline-flex; align-items: center; justify-content: center; padding: 12px 20px; border-radius: 12px; background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.18); color: #fff; text-decoration: none; font-size: 14px; font-weight: 600; transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.14)'" onmouseout="this.style.background='rgba(255,255,255,0.08)'">
        Create New Account
      </a>
      <button type="button" onclick="closeLoginPromptModal()" style="background: none; border: none; color: rgba(244, 242, 233, 0.5); font-size: 13px; cursor: pointer; padding: 6px; margin-top: 4px; transition: color 0.2s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(244, 242, 233, 0.5)'">
        Continue Shopping (Cancel)
      </button>
    </div>
  </div>
</div>

<script>
  // Products Database for Catalog Reference & Recommendations
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
      image: "https://images.unsplash.com/photo-1607613009820-a29f7bb81c04?auto=format&fit=crop&w=800&q=80",
      images: [
        "https://images.unsplash.com/photo-1607613009820-a29f7bb81c04?auto=format&fit=crop&w=800&q=80"
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
      image: "https://images.unsplash.com/photo-1608248597359-0021b369c0d3?auto=format&fit=crop&w=800&q=80",
      images: [
        "https://images.unsplash.com/photo-1608248597359-0021b369c0d3?auto=format&fit=crop&w=800&q=80"
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
      image: "https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=800&q=80",
      images: [
        "https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=800&q=80"
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
      image: "https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?auto=format&fit=crop&w=800&q=80",
      images: [
        "https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?auto=format&fit=crop&w=800&q=80"
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
      image: "https://images.unsplash.com/photo-1602143407151-7111542de6e8?auto=format&fit=crop&w=800&q=80",
      images: [
        "https://images.unsplash.com/photo-1602143407151-7111542de6e8?auto=format&fit=crop&w=800&q=80"
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
      image: "https://images.unsplash.com/photo-1597484661643-2f5fef640dd1?auto=format&fit=crop&w=800&q=80",
      images: [
        "https://images.unsplash.com/photo-1597484661643-2f5fef640dd1?auto=format&fit=crop&w=800&q=80"
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
      image: "https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?auto=format&fit=crop&w=800&q=80",
      images: [
        "https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?auto=format&fit=crop&w=800&q=80"
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
      image: "https://images.unsplash.com/photo-1585751119414-ef2636f8aede?auto=format&fit=crop&w=800&q=80",
      images: [
        "https://images.unsplash.com/photo-1585751119414-ef2636f8aede?auto=format&fit=crop&w=800&q=80"
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
      image: "https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=800&q=80",
      images: [
        "https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=800&q=80"
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
      image: "https://images.unsplash.com/photo-1585336261026-7f573f055375?auto=format&fit=crop&w=800&q=80",
      images: [
        "https://images.unsplash.com/photo-1585336261026-7f573f055375?auto=format&fit=crop&w=800&q=80"
      ]
    }
  ];

  let cart = [];
  let voucherDiscountRate = 0;
  let useEcoPoints = false;
  const FREE_SHIPPING_THRESHOLD = 5000;

  function loadCart() {
    try {
      const stored = localStorage.getItem('soba_cart');
      if (stored) {
        cart = JSON.parse(stored);
        cart.forEach(item => {
          if (item.selected === undefined) item.selected = true;
          // Sync missing image/icon from master products catalog
          const master = products.find(p => p.id === item.id);
          if (master) {
            if (!item.image && master.image) item.image = master.image;
            if (!item.images && master.images) item.images = master.images;
            if (!item.icon && master.icon) item.icon = master.icon;
            if (!item.category && master.category) item.category = master.category;
          }
        });
      }
    } catch(e) {
      cart = [];
    }
    renderCart();
  }

  function saveCart() {
    try {
      localStorage.setItem('soba_cart', JSON.stringify(cart));
    } catch(e) {}
  }

  function renderCart() {
    const container = document.getElementById('cartItemsContainer');
    const bulkCard = document.getElementById('bulkHeaderCard');
    const shippingCard = document.getElementById('shippingGoalCard');
    const cartCountBadge = document.getElementById('cartCount');
    const summaryItemCount = document.getElementById('summaryItemCount');
    const checkoutCountDisplay = document.getElementById('checkoutCountDisplay');

    const totalQuantity = cart.reduce((sum, i) => sum + i.quantity, 0);
    cartCountBadge.innerText = totalQuantity;

    if (cart.length === 0) {
      bulkCard.style.display = 'none';
      shippingCard.style.display = 'none';
      container.innerHTML = `
        <div class="empty-cart-state">
          <span class="empty-cart-icon">🛒🌱</span>
          <h3>Your Shopping Cart is Empty</h3>
          <p>Explore our certified zero-waste catalog and discover hand-crafted items made to protect Sri Lankan nature.</p>
          <a href="products.php" class="btn-explore-catalog">Shop Sustainable Items →</a>
        </div>
      `;
      updateSummary(0, 0);
      return;
    }

    bulkCard.style.display = 'flex';
    shippingCard.style.display = 'flex';

    const allSelected = cart.length > 0 && cart.every(i => i.selected);
    const selectedCount = cart.filter(i => i.selected).length;

    document.getElementById('selectAllCheckbox').checked = allSelected;
    document.getElementById('selectedCountDisplay').innerText = selectedCount;
    document.getElementById('btnDeleteSelected').disabled = (selectedCount === 0);
    summaryItemCount.innerText = `${selectedCount} item${selectedCount === 1 ? '' : 's'}`;
    checkoutCountDisplay.innerText = selectedCount;

    // Build Daraz Store Block
    container.innerHTML = `
      <div class="store-group-card">
        <div class="store-header">
          <label class="store-title-badge" style="cursor:pointer;">
            <input type="checkbox" class="bulk-checkbox" ${allSelected ? 'checked' : ''} onchange="toggleSelectAllItems(this.checked)">
            <span>🌿 Soba Direct Certified Flagship</span>
            <span class="store-flagship-tag">Verified</span>
          </label>
          <button class="store-voucher-btn" onclick="applyVoucher('SOBA10')">Collect LKR 10% Off</button>
        </div>

        <div class="store-items-list">
          ${cart.map(item => {
            const master = products.find(p => p.id === item.id);
            const itemImg = (item.images && item.images[0]) ? item.images[0] : (item.image || (master && master.image) || null);
            const origPrice = item.originalPrice || Math.round(item.price * 1.35);
            const categoryName = (item.category || (master && master.category) || 'Zero-Waste').toUpperCase();

            let isSaved = false;
            try {
              const curW = JSON.parse(localStorage.getItem('soba_wishlist') || '[]');
              isSaved = curW.some(w => Number(w.id) === Number(item.id));
            } catch(e) {}

            return `
              <div class="cart-item-card">
                <input type="checkbox" class="item-checkbox" ${item.selected ? 'checked' : ''} onchange="toggleItem(${item.id}, this.checked)">
                
                <a href="product_details.php?id=${item.id}" class="item-thumb-box" title="View ${item.name} details">
                  ${itemImg 
                    ? `<img src="${itemImg}" alt="${item.name}" onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                       <span style="display:none; font-size:38px;">${item.icon || (master && master.icon) || '🌱'}</span>` 
                    : `<span style="font-size:38px;">${item.icon || (master && master.icon) || '🌱'}</span>`}
                </a>

                <div class="item-info-col">
                  <a href="product_details.php?id=${item.id}" class="item-title-link">${item.name}</a>
                  <div class="item-variant-tag">Category: ${categoryName}</div>
                  <div class="item-eco-pill">🌱 Saves ${item.saved || (master && master.saved) || 'Plastic waste'}</div>
                </div>

                <div class="item-action-col">
                  <div class="item-price-daraz">
                    <div class="item-current-price">LKR ${(item.price * item.quantity).toLocaleString()}</div>
                    <div class="item-original-price">LKR ${(origPrice * item.quantity).toLocaleString()}</div>
                  </div>

                  <div class="daraz-qty-pill">
                    <button class="daraz-qty-btn" ${item.quantity <= 1 ? 'disabled' : ''} onclick="updateQty(${item.id}, -1)">−</button>
                    <span class="daraz-qty-num">${item.quantity}</span>
                    <button class="daraz-qty-btn" onclick="updateQty(${item.id}, 1)">+</button>
                  </div>

                  <div class="item-tool-btns">
                    <button class="btn-tool-icon wish" style="${isSaved ? 'color:#ff4757; background:rgba(255,71,87,0.22); border-color:#ff4757;' : ''}" onclick="saveForLater(${item.id})" title="${isSaved ? 'Saved in Wishlist' : 'Save for later'}">♥</button>
                    <button class="btn-tool-icon" onclick="removeItem(${item.id})" title="Delete">🗑️</button>
                  </div>
                </div>

              </div>
            `;
          }).join('')}
        </div>
      </div>
    `;

    // Calculate Summary
    const selectedItems = cart.filter(i => i.selected);
    const subtotal = selectedItems.reduce((sum, i) => sum + (i.price * i.quantity), 0);
    updateSummary(subtotal, selectedItems.length);
  }

  function updateSummary(subtotal, selectedCount) {
    const subtotalEl = document.getElementById('summarySubtotal');
    const shippingEl = document.getElementById('summaryShipping');
    const freeDeliveryLine = document.getElementById('freeDeliveryLine');
    const voucherDiscountLine = document.getElementById('voucherDiscountLine');
    const summaryVoucherDiscount = document.getElementById('summaryVoucherDiscount');
    const ecoPointsLine = document.getElementById('ecoPointsLine');
    const grandTotalEl = document.getElementById('summaryGrandTotal');
    const btnCheckout = document.getElementById('btnCheckout');

    // Free Shipping Progress calculation
    const shippingGoalText = document.getElementById('shippingGoalText');
    const shippingGoalPct = document.getElementById('shippingGoalPct');
    const shippingGoalFill = document.getElementById('shippingGoalFill');

    subtotalEl.innerText = `LKR ${subtotal.toLocaleString()}`;

    // Shipping calculation
    let shippingFee = subtotal > 0 ? 350 : 0;
    if (subtotal >= FREE_SHIPPING_THRESHOLD) {
      shippingFee = 0;
      freeDeliveryLine.style.display = 'flex';
      shippingEl.innerHTML = `<span style="text-decoration:line-through; opacity:0.5;">LKR 350</span> Free`;
      shippingGoalText.innerHTML = `🎉 Congratulations! You unlocked <b>FREE ISLANDWIDE DELIVERY</b>`;
      shippingGoalPct.innerText = `100%`;
      shippingGoalFill.style.width = `100%`;
    } else {
      freeDeliveryLine.style.display = 'none';
      shippingEl.innerText = `LKR ${shippingFee}`;
      const remaining = FREE_SHIPPING_THRESHOLD - subtotal;
      const pct = Math.min(100, Math.round((subtotal / FREE_SHIPPING_THRESHOLD) * 100));
      shippingGoalText.innerHTML = `🚚 Add <b>LKR ${remaining.toLocaleString()}</b> more to enjoy <b>FREE DELIVERY</b>`;
      shippingGoalPct.innerText = `${pct}%`;
      shippingGoalFill.style.width = `${pct}%`;
    }

    // Voucher Discount
    let discount = Math.round(subtotal * voucherDiscountRate);
    if (discount > 0) {
      voucherDiscountLine.style.display = 'flex';
      summaryVoucherDiscount.innerText = `-LKR ${discount.toLocaleString()}`;
    } else {
      voucherDiscountLine.style.display = 'none';
    }

    // Eco points deduction
    let ecoDeduction = (useEcoPoints && subtotal > 300) ? 150 : 0;
    ecoPointsLine.style.display = ecoDeduction > 0 ? 'flex' : 'none';

    // Grand total
    const total = Math.max(0, subtotal + shippingFee - discount - ecoDeduction);
    grandTotalEl.innerText = `LKR ${total.toLocaleString()}`;

    btnCheckout.disabled = (selectedCount === 0 || subtotal === 0);
  }

  function updateQty(id, delta) {
    const item = cart.find(i => i.id === id);
    if (!item) return;
    if (delta === -1 && item.quantity <= 1) return;
    item.quantity += delta;
    saveCart();
    renderCart();
  }

  function removeItem(id) {
    cart = cart.filter(i => i.id !== id);
    saveCart();
    renderCart();
  }

  function toggleItem(id, checked) {
    const item = cart.find(i => i.id === id);
    if (item) {
      item.selected = checked;
      saveCart();
      renderCart();
    }
  }

  function toggleSelectAllItems(checked) {
    cart.forEach(i => i.selected = checked);
    saveCart();
    renderCart();
  }

  function deleteSelectedItems() {
    cart = cart.filter(i => !i.selected);
    saveCart();
    renderCart();
  }

  async function clearCart() {
    const ok = await sobaConfirm("Are you sure you want to clear all items from your cart?", "Clear Cart", true);
    if (ok) {
      cart = [];
      saveCart();
      renderCart();
      sobaToast("Cart has been cleared");
    }
  }

  function saveForLater(id) {
    const item = cart.find(i => Number(i.id) === Number(id));
    if (!item) return;

    let wishlist = [];
    try {
      const raw = localStorage.getItem('soba_wishlist');
      if (raw) wishlist = JSON.parse(raw);
    } catch(e) {}

    const idx = wishlist.findIndex(w => Number(w.id) === Number(item.id));
    if (idx >= 0) {
      sobaToast(`"${item.name}" is already in your Wishlist! ♥`);
    } else {
      const master = (typeof products !== 'undefined') ? products.find(p => Number(p.id) === Number(item.id)) : null;
      const primaryImg = item.image || (master && master.images && master.images[0]) || (master && master.image) || '';

      wishlist.push({
        id: item.id,
        name: item.name,
        price: Number(item.price) || 0,
        original_price: Number(item.originalPrice || item.original_price || item.price * 1.3),
        icon: item.icon || (master && master.icon) || '🌱',
        image: primaryImg,
        category: item.category || (master && master.category) || 'Zero-Waste',
        cert: item.cert || (master && master.cert) || 'Eco Friendly'
      });

      try {
        localStorage.setItem('soba_wishlist', JSON.stringify(wishlist));
      } catch(e) {}

      sobaToast(`"${item.name}" saved to your Wishlist! ♥`);
      renderCart();
    }
  }

  function applyVoucher(forcedCode) {
    const code = (forcedCode || document.getElementById('voucherInput').value).trim().toUpperCase();
    if (code === 'ECO10' || code === 'SOBA10') {
      voucherDiscountRate = 0.10;
      sobaToast("🎉 10% Flagship store discount applied!");
    } else if (code === 'ZERO20') {
      voucherDiscountRate = 0.20;
      sobaToast("🎉 20% Zero Waste voucher applied!");
    } else {
      sobaToast("Invalid voucher code. Try 'SOBA10' for 10% OFF!", true);
      voucherDiscountRate = 0;
    }
    renderCart();
  }

  function toggleEcoPoints(checked) {
    useEcoPoints = checked;
    renderCart();
  }

  function isUserLoggedIn() {
    try {
      const stored = localStorage.getItem('soba_user');
      if (stored) {
        const u = JSON.parse(stored);
        if (u && (u.id || u.name || u.email)) return u;
      }
    } catch(e) {}
    <?php if (!empty($sessionUser)): ?>
      return <?= json_encode($sessionUser) ?>;
    <?php else: ?>
      return null;
    <?php endif; ?>
  }

  function openLoginPromptModal() {
    const modal = document.getElementById('loginPromptModal');
    if (modal) modal.classList.add('active');
  }

  function closeLoginPromptModal() {
    const modal = document.getElementById('loginPromptModal');
    if (modal) modal.classList.remove('active');
  }

  function proceedToCheckout() {
    const selected = cart.filter(i => i.selected);
    if (selected.length === 0) {
      sobaToast("Please select at least 1 item to proceed!", true);
      return;
    }

    // Save active checkout snapshot
    try {
      const subtotal = selected.reduce((sum, i) => sum + (i.price * i.quantity), 0);
      let shipping = (subtotal >= FREE_SHIPPING_THRESHOLD || subtotal === 0) ? 0 : 350;
      let discount = Math.round(subtotal * voucherDiscountRate);
      let ecoDeduction = (useEcoPoints && subtotal > 300) ? 150 : 0;
      let grandTotal = Math.max(0, subtotal + shipping - discount - ecoDeduction);

      const checkoutState = {
        items: selected,
        subtotal: subtotal,
        shipping: shipping,
        discount: discount,
        ecoDeduction: ecoDeduction,
        voucherRate: voucherDiscountRate,
        grandTotal: grandTotal
      };
      sessionStorage.setItem('soba_checkout_state', JSON.stringify(checkoutState));
    } catch(e) {}

    // Authentication Guard: User MUST be logged in to proceed to checkout
    const user = isUserLoggedIn();
    if (!user) {
      openLoginPromptModal();
      return;
    }

    window.location.href = 'checkout.php';
  }

  function doHeaderSearch() {
    const q = document.getElementById('productSearch').value.trim();
    if (q) window.location.href = `products.php?search=${encodeURIComponent(q)}`;
    else window.location.href = `products.php`;
  }

  function renderRecommendations() {
    const grid = document.getElementById('recommendGrid');
    grid.innerHTML = products.slice(0, 4).map(p => `
      <a class="card" href="product_details.php?id=${p.id}">
        <div class="card-art">
          <span class="card-cert">${p.cert}</span>
          <img src="${p.image}" alt="${p.name}">
          <span class="poster-price-tag">LKR ${p.price.toLocaleString()}</span>
        </div>
        <div class="card-body">
          <div class="card-meta">${p.category}</div>
          <h3>${p.name}</h3>
        </div>
      </a>
    `).join('');
  }

  // Nav indicator animation
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

  // Spotlight mouse track disabled

  window.addEventListener('DOMContentLoaded', async () => {
    try {
      const res = await fetch('/php/web/Soba-Project/backend/api/customer/get_products.php');
      const data = await res.json();
      if (data && data.success && Array.isArray(data.products)) {
        products.splice(0, products.length, ...data.products.map(p => ({
          ...p,
          image: (p.images && p.images[0]) ? p.images[0] : ''
        })));
      }
    } catch(err) {}

    loadCart();
    renderRecommendations();
    moveNavIndicator(null, false);

    // Load logged in user name if available
    try {
      const stored = localStorage.getItem('soba_user');
      if (stored) {
        const u = JSON.parse(stored);
        if (u.name) {
          const accLabel = document.getElementById('cartHeaderUserName');
          if (accLabel) accLabel.textContent = u.name.split(' ')[0];
        }
        if (u.role && u.role.toLowerCase() === 'admin') {
          const accLink = document.getElementById('cartHeaderAccount');
          if (accLink) accLink.href = '../admin/dashboard.php';
        }
      }
    } catch(e) {}

    // Auto-proceed to checkout if user returned from login/register with auto_checkout=1
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('auto_checkout') === '1') {
      try {
        const cleanUrl = window.location.pathname;
        window.history.replaceState({}, document.title, cleanUrl);
      } catch(e) {}

      const user = isUserLoggedIn();
      if (user) {
        sobaToast("Welcome back! Proceeding to checkout…", false);
        setTimeout(() => {
          proceedToCheckout();
        }, 750);
      }
    }
  });
  window.addEventListener('resize', () => moveNavIndicator(null, false));
</script>
<script src="../../assets/js/main.js"></script>
</body>
</html>
