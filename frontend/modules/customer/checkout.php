<?php
require_once __DIR__ . '/../../../backend/includes/session_handler.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// PayHere Credentials - DO NOT MODIFY (Sandbox Credentials & Settings)
$merchant_id = "1238336"; 
$merchant_secret = "Mzc2MTU0NDgzNzY2ODA2NzEzNDE2MTkxNDExMzUxMDg0OTQwOTg0"; // Decoded secret key

// Unique Order ID generated via PHP
$order_id = "SOBA-" . rand(10000, 90000);
$currency = "LKR";

// Dynamic Subtotal & Delivery calculate (Default 2050 + 350 = 2450.00)
$amount = 2450.00; 

// PayHere Hash Generation Function
function generatePayHereHash($merchant_id, $order_id, $amount, $currency, $merchant_secret) {
    return strtoupper(
        md5(
            $merchant_id . $order_id . 
            number_format((float)$amount, 2, '.', '') . $currency . 
            strtoupper(md5($merchant_secret))
        )
    );
}

$hash = generatePayHereHash($merchant_id, $order_id, $amount, $currency, $merchant_secret);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Secure Checkout — Soba Zero-Waste Marketplace</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..700;1,9..144,400..600&family=Work+Sans:wght@400;500;600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- PayHere JavaScript SDK -->
<script type="text/javascript" src="https://www.payhere.lk/lib/payhere.js"></script>
<link rel="stylesheet" href="../../assets/css/style.css">

<style>
  :root {
    --neo: #b8ff6a;
    --neo-glow: rgba(184, 255, 106, 0.35);
    --deep: #071c15;
    --deep-surface: #0b251d;
    --forest: #225e3d;
    --forest-bright: #3ea066;
    --clay: #e07a5f;
    --paper: #f4f6f0;
    --ink: #111a15;
    --glass: rgba(255, 255, 255, 0.05);
    --glass-hover: rgba(255, 255, 255, 0.09);
    --glass-line: rgba(255, 255, 255, 0.12);
    --glass-line-active: rgba(184, 255, 106, 0.45);
    --text-muted: rgba(255, 255, 255, 0.65);
    --daraz-orange: #f85606;
  }

  * { box-sizing: border-box; margin: 0; padding: 0; }
  
  body {
    background-color: var(--deep);
    color: #ffffff;
    font-family: 'Work Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    -webkit-font-smoothing: antialiased;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
  }

  /* Ambient cinematic eco glow */
  body::before {
    content: "";
    position: fixed;
    inset: 0;
    z-index: -1;
    background:
      radial-gradient(circle at 12% 10%, rgba(184,255,106,0.12), transparent 40%),
      radial-gradient(circle at 88% 85%, rgba(62,160,102,0.15), transparent 45%),
      linear-gradient(145deg, #04130e 0%, #082118 55%, #051912 100%);
    pointer-events: none;
  }

  h1, h2, h3, h4 { font-family: 'Fraunces', Georgia, serif; font-weight: 600; }
  .mono { font-family: 'IBM Plex Mono', monospace; }

  /* =========================================================
     TOP NAVBAR & DARAZ-STYLE STEPPER
     ========================================================= */
  header {
    background: rgba(6, 23, 17, 0.88);
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
    border-bottom: 1px solid var(--glass-line);
    position: sticky;
    top: 0;
    z-index: 100;
  }

  .nav-inner {
    max-width: 1240px;
    margin: 0 auto;
    padding: 14px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
  }

  .brand {
    display: flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
  }

  .brand-logo {
    display: flex;
    align-items: baseline;
    gap: 6px;
  }

  .brand-logo .mark {
    font-size: 26px;
    font-weight: 700;
    color: #ffffff;
    letter-spacing: -0.02em;
  }

  .brand-logo .sub {
    font-size: 10px;
    letter-spacing: 0.16em;
    font-weight: 700;
    color: var(--neo);
    background: rgba(184, 255, 106, 0.12);
    border: 1px solid rgba(184, 255, 106, 0.3);
    padding: 2px 7px;
    border-radius: 999px;
  }

  /* Daraz-Style Modern Checkout Stepper */
  .checkout-stepper {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .step-node {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: var(--text-muted);
    font-weight: 500;
    text-decoration: none;
  }

  .step-node .node-idx {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-family: 'IBM Plex Mono', monospace;
    font-weight: 700;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid var(--glass-line);
    color: #fff;
  }

  .step-node.completed .node-idx {
    background: var(--forest);
    border-color: var(--neo);
    color: var(--neo);
  }
  .step-node.completed span {
    color: rgba(255,255,255,0.85);
  }

  .step-node.current {
    color: var(--neo);
    font-weight: 600;
  }
  .step-node.current .node-idx {
    background: var(--neo);
    border-color: var(--neo);
    color: #071c15;
    box-shadow: 0 0 12px var(--neo-glow);
  }

  .step-divider {
    width: 36px;
    height: 1px;
    background: var(--glass-line);
  }

  .header-actions {
    display: flex;
    align-items: center;
    gap: 16px;
  }

  .back-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: var(--text-muted);
    text-decoration: none;
    transition: color 0.2s;
  }
  .back-link:hover { color: var(--neo); }

  .trust-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #a7e0a4;
    background: rgba(62, 160, 102, 0.12);
    border: 1px solid rgba(62, 160, 102, 0.25);
    padding: 5px 12px;
    border-radius: 999px;
  }

  /* =========================================================
     MAIN LAYOUT CONTAINER (DARAZ 2-COLUMN GRID)
     ========================================================= */
  .main-wrapper {
    max-width: 1240px;
    margin: 28px auto 80px;
    padding: 0 24px;
    width: 100%;
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 30px;
    align-items: start;
  }

  @media (max-width: 1024px) {
    .main-wrapper {
      grid-template-columns: 1fr;
    }
    .checkout-stepper { display: none; }
  }

  /* Glass Card Base */
  .daraz-card {
    background: rgba(11, 37, 29, 0.65);
    border: 1px solid var(--glass-line);
    border-radius: 18px;
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    padding: 24px 28px;
    margin-bottom: 22px;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.28);
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
  }
  .daraz-card:hover {
    border-color: rgba(255, 255, 255, 0.2);
  }

  .card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 14px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  }

  .card-title {
    font-size: 19px;
    display: flex;
    align-items: center;
    gap: 10px;
    color: #ffffff;
  }
  .card-title .icon-chip {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: rgba(184, 255, 106, 0.12);
    border: 1px solid rgba(184, 255, 106, 0.3);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    color: var(--neo);
  }

  .card-action-link {
    font-size: 12.5px;
    color: var(--neo);
    text-decoration: none;
    font-weight: 500;
    cursor: pointer;
    background: transparent;
    border: none;
  }
  .card-action-link:hover { text-decoration: underline; }

  /* =========================================================
     ADDRESS / SHIPPING SECTION
     ========================================================= */
  .form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
  }
  .form-group-full { grid-column: span 2; }

  .input-label {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.75);
    margin-bottom: 8px;
  }

  .custom-input {
    width: 100%;
    background: rgba(0, 0, 0, 0.35);
    border: 1px solid var(--glass-line);
    border-radius: 10px;
    padding: 13px 16px;
    color: #ffffff;
    font-family: 'Work Sans', sans-serif;
    font-size: 14px;
    transition: all 0.2s ease;
  }
  .custom-input:focus {
    outline: none;
    border-color: var(--neo);
    background: rgba(0, 0, 0, 0.5);
    box-shadow: 0 0 0 3px rgba(184, 255, 106, 0.15);
  }

  /* Delivery option card */
  .delivery-method-pill {
    margin-top: 18px;
    background: rgba(62, 160, 102, 0.1);
    border: 1px solid rgba(62, 160, 102, 0.28);
    border-radius: 12px;
    padding: 14px 18px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .delivery-method-pill .meta {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .delivery-icon {
    font-size: 22px;
  }
  .delivery-method-pill h5 {
    font-size: 14px;
    color: #ffffff;
    margin-bottom: 3px;
  }
  .delivery-method-pill p {
    font-size: 12px;
    color: var(--text-muted);
  }

  /* =========================================================
     PAYMENT OPTIONS (DARAZ STYLE)
     ========================================================= */
  .payment-selection-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 14px;
  }

  .pay-method-card {
    position: relative;
    cursor: pointer;
  }
  .pay-method-card input[type="radio"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
  }

  .pay-method-body {
    background: rgba(255, 255, 255, 0.03);
    border: 1.5px solid var(--glass-line);
    border-radius: 14px;
    padding: 16px;
    transition: all 0.22s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }

  .pay-method-card:hover .pay-method-body {
    background: rgba(255, 255, 255, 0.06);
    border-color: rgba(255, 255, 255, 0.3);
  }

  .pay-method-card input[type="radio"]:checked + .pay-method-body {
    background: rgba(184, 255, 106, 0.08);
    border-color: var(--neo);
    box-shadow: 0 0 20px rgba(184, 255, 106, 0.2);
  }

  .pay-top-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 10px;
  }

  .pay-badge-logo {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 6px;
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
  }
  .pay-badge-logo.payhere {
    background: #00457C;
    color: #fff;
  }

  .radio-check-circle {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 2px solid var(--glass-line);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
  }
  .pay-method-card input[type="radio"]:checked + .pay-method-body .radio-check-circle {
    border-color: var(--neo);
    background: var(--neo);
  }
  .pay-method-card input[type="radio"]:checked + .pay-method-body .radio-check-circle::after {
    content: "";
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #071c15;
  }

  .pay-method-body h4 {
    font-size: 15px;
    color: #fff;
    margin-bottom: 4px;
  }
  .pay-method-body p {
    font-size: 11.5px;
    color: var(--text-muted);
    line-height: 1.4;
  }

  .gateway-security-notice {
    margin-top: 16px;
    padding: 12px 16px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px dashed rgba(255, 255, 255, 0.15);
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 12px;
    color: rgba(255, 255, 255, 0.7);
  }

  /* =========================================================
     RIGHT SIDEBAR (ORDER SUMMARY & DARAZ STYLED CHECKOUT CTA)
     ========================================================= */
  .order-summary-card {
    position: sticky;
    top: 92px;
  }

  .summary-scroll-items {
    max-height: 260px;
    overflow-y: auto;
    padding-right: 6px;
    margin-bottom: 18px;
  }
  .summary-scroll-items::-webkit-scrollbar { width: 4px; }
  .summary-scroll-items::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.2); border-radius: 4px; }

  .cart-item-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
  }

  .item-thumb-box {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid var(--glass-line);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
  }

  .item-details {
    flex-grow: 1;
    overflow: hidden;
  }
  .item-details h4 {
    font-size: 13.5px;
    color: #ffffff;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 2px;
  }
  .item-details p {
    font-size: 11.5px;
    color: var(--text-muted);
  }

  .item-price-tag {
    font-size: 13px;
    font-weight: 600;
    color: #ffffff;
    text-align: right;
  }

  /* Voucher line */
  .voucher-input-group {
    display: flex;
    gap: 8px;
    margin: 16px 0;
  }
  .voucher-input {
    flex-grow: 1;
    background: rgba(0, 0, 0, 0.3);
    border: 1px solid var(--glass-line);
    border-radius: 8px;
    padding: 10px 12px;
    color: #fff;
    font-size: 12px;
  }
  .voucher-btn {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid var(--glass-line);
    color: #fff;
    padding: 0 14px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
  }
  .voucher-btn:hover {
    background: var(--neo);
    color: #071c15;
  }

  /* Price calculation breakdown */
  .calc-breakdown {
    padding-top: 10px;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
  }
  .calc-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 6px 0;
    font-size: 13px;
    color: var(--text-muted);
  }
  .calc-row.discount {
    color: var(--neo);
  }

  .total-row {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    padding-top: 16px;
    margin-top: 10px;
    border-top: 2px solid rgba(184, 255, 106, 0.4);
  }
  .total-row .label {
    font-size: 16px;
    font-weight: 700;
    color: #fff;
  }
  .total-row .final-val {
    font-size: 24px;
    font-weight: 700;
    color: var(--neo);
  }

  /* Action Buttons */
  .btn-place-order {
    width: 100%;
    margin-top: 20px;
    padding: 16px;
    background: linear-gradient(135deg, var(--neo) 0%, #8ae434 100%);
    color: #071c15;
    font-size: 15px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    box-shadow: 0 8px 24px rgba(184, 255, 106, 0.35);
    transition: all 0.22s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
  }
  .btn-place-order:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 32px rgba(184, 255, 106, 0.5);
    background: #c3ff7f;
  }

  .terms-note {
    font-size: 11.5px;
    color: rgba(255, 255, 255, 0.5);
    text-align: center;
    margin-top: 14px;
    line-height: 1.5;
  }

  /* =========================================================
     LIVE TRACKING & GPS TIMELINE (AFTER PAYMENT / COD)
     ========================================================= */
  #trackingView {
    display: none;
    animation: fadeIn 0.4s ease;
  }
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
  }

  .tracking-hero {
    background: rgba(184, 255, 106, 0.06);
    border: 1px solid rgba(184, 255, 106, 0.22);
    border-radius: 14px;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
  }
  .tracking-hero-left h3 {
    color: var(--neo);
    font-size: 18px;
    margin-bottom: 4px;
  }
  .tracking-hero-left p {
    font-size: 13px;
    color: var(--text-muted);
  }

  .daraz-timeline {
    position: relative;
    padding: 20px 0;
    display: flex;
    justify-content: space-between;
    margin: 20px 0 30px;
  }
  .daraz-timeline::before {
    content: "";
    position: absolute;
    top: 38px;
    left: 10%;
    right: 10%;
    height: 3px;
    background: rgba(255, 255, 255, 0.12);
    z-index: 1;
  }
  .timeline-progress {
    position: absolute;
    top: 38px;
    left: 10%;
    height: 3px;
    background: var(--neo);
    z-index: 1;
    width: 33%;
    transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .timeline-node {
    position: relative;
    z-index: 2;
    text-align: center;
    width: 25%;
  }
  .timeline-circle {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #0b251d;
    border: 2px solid var(--glass-line);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
    font-size: 16px;
    transition: all 0.3s;
  }

  .timeline-node.done .timeline-circle {
    background: var(--forest);
    border-color: var(--neo);
    color: var(--neo);
  }
  .timeline-node.active .timeline-circle {
    background: var(--neo);
    border-color: var(--neo);
    color: #071c15;
    box-shadow: 0 0 16px var(--neo-glow);
  }

  .timeline-node h6 {
    font-size: 12.5px;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.7);
    margin-bottom: 2px;
  }
  .timeline-node.active h6 { color: var(--neo); }
  .timeline-node span {
    font-size: 11px;
    color: var(--text-muted);
  }

  .live-status-card {
    background: rgba(0, 0, 0, 0.35);
    border: 1px solid var(--glass-line);
    border-radius: 14px;
    padding: 18px 22px;
  }
  .live-status-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
  }
  .gps-beacon {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 11px;
    background: rgba(184, 255, 106, 0.15);
    border: 1px solid rgba(184, 255, 106, 0.3);
    color: var(--neo);
    padding: 4px 10px;
    border-radius: 999px;
  }
  .pulse-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: var(--neo);
    animation: blink 1.2s infinite;
  }
  @keyframes blink {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.3; transform: scale(0.8); }
  }

</style>
</head>
<body>

<!-- TOP NAVIGATION -->
<header>
  <div class="nav-inner">
    <!-- Brand -->
    <a href="products_2.html" class="brand">
      <div class="brand-logo">
        <span class="mark">Soba</span>
        <span class="sub">MARKET</span>
      </div>
    </a>

    <!-- Daraz-Style Stepper: Cart -> Checkout -> Success -->
    <div class="checkout-stepper">
      <a href="cart.php" class="step-node completed">
        <div class="node-idx">✓</div>
        <span>1. Cart</span>
      </a>
      <div class="step-divider"></div>
      <div class="step-node current">
        <div class="node-idx">2</div>
        <span>2. Delivery & Payment</span>
      </div>
      <div class="step-divider"></div>
      <div class="step-node">
        <div class="node-idx">3</div>
        <span>3. Confirmation</span>
      </div>
    </div>

    <!-- Header Right Badges -->
    <div class="header-actions">
      <a href="cart.php" class="back-link">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
        Back to Cart
      </a>
      <div class="trust-badge">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
        PayHere 256-Bit SSL
      </div>
    </div>
  </div>
</header>

<main class="main-wrapper">
  
  <!-- LEFT COLUMN: SHIPPING ADDRESS & PAYMENT OPTIONS -->
  <div>
    
    <!-- CHECKOUT FORM CARD -->
    <div class="daraz-card" id="checkoutFormCard">
      <form id="checkoutForm" onsubmit="handlePlaceOrder(event)">
        
        <!-- Section 1: Delivery Address -->
        <div class="card-header">
          <div class="card-title">
            <span class="icon-chip">📍</span>
            Delivery Information
          </div>
          <span style="font-size: 12px; color: var(--text-muted);">Island-wide Courier</span>
        </div>

        <div class="form-grid">
          <div class="form-group">
            <label class="input-label" for="fullName">Recipient Full Name *</label>
            <input type="text" id="fullName" class="custom-input" placeholder="e.g. Kasun Kalhara" required>
          </div>

          <div class="form-group">
            <label class="input-label" for="phoneNumber">Mobile Number *</label>
            <input type="tel" id="phoneNumber" class="custom-input" placeholder="07X XXXXXXX" required>
          </div>

          <div class="form-group">
            <label class="input-label" for="city">City / Region *</label>
            <input type="text" id="city" class="custom-input" placeholder="e.g. Galle / Colombo 07" required>
          </div>

          <div class="form-group">
            <label class="input-label" for="postalCode">Postal Code (Optional)</label>
            <input type="text" id="postalCode" class="custom-input" placeholder="e.g. 80000">
          </div>

          <div class="form-group-full">
            <label class="input-label" for="address">Street Address / Landmark *</label>
            <input type="text" id="address" class="custom-input" placeholder="House / Apt No, Street Name, Landmark" required>
          </div>
        </div>

        <!-- Carbon-Neutral Courier Info -->
        <div class="delivery-method-pill">
          <div class="meta">
            <span class="delivery-icon">🌱</span>
            <div>
              <h5>Eco-Express Delivery (Zero Plastic Packing)</h5>
              <p>Estimated dispatch: Within 24-48 hrs • Real-time SMS notifications</p>
            </div>
          </div>
          <span class="mono" style="font-weight: 700; color: var(--neo); font-size: 14px;">LKR 350.00</span>
        </div>

        <!-- Section 2: Payment Method Selection -->
        <div class="card-header" style="margin-top: 32px;">
          <div class="card-title">
            <span class="icon-chip">💳</span>
            Select Payment Method
          </div>
          <span style="font-size: 12px; color: var(--neo);">Guaranteed Safe</span>
        </div>

        <div class="payment-selection-grid">
          <!-- Option 1: Card / PayHere -->
          <label class="pay-method-card">
            <input type="radio" name="paymentMethod" value="card" checked>
            <div class="pay-method-body">
              <div>
                <div class="pay-top-row">
                  <span class="pay-badge-logo payhere">PayHere Gateway</span>
                  <div class="radio-check-circle"></div>
                </div>
                <h4>Credit / Debit Card</h4>
                <p>Pay securely via Visa, MasterCard, AMEX & Genie via PayHere Sri Lanka.</p>
              </div>
            </div>
          </label>

          <!-- Option 2: Cash on Delivery -->
          <label class="pay-method-card">
            <input type="radio" name="paymentMethod" value="cod">
            <div class="pay-method-body">
              <div>
                <div class="pay-top-row">
                  <span class="pay-badge-logo">Cash on Delivery</span>
                  <div class="radio-check-circle"></div>
                </div>
                <h4>Cash on Delivery (COD)</h4>
                <p>Pay in cash upon package arrival at your doorstep.</p>
              </div>
            </div>
          </label>
        </div>

        <!-- Gateway Security Info -->
        <div class="gateway-security-notice">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
          <div>
            Your transaction is encrypted by PayHere Sandbox gateway. No card credentials are stored on Soba servers.
          </div>
        </div>



      </form>
    </div>

    <!-- LIVE TRACKING VIEW (Revealed after successful payment / COD) -->
    <div class="daraz-card" id="trackingView">
      <div class="tracking-hero">
        <div class="tracking-hero-left">
          <h3>🎉 Thank You! Your Order has been Placed.</h3>
          <p>We've dispatched order details to your phone and email.</p>
        </div>
        <div style="text-align: right;">
          <span style="font-size: 11px; color: var(--text-muted); text-transform: uppercase;">Order Number</span>
          <div class="mono" id="orderIdTag" style="font-size: 18px; font-weight: 700; color: var(--neo);">#<?php echo $order_id; ?></div>
        </div>
      </div>

      <!-- Daraz-Style Delivery Stepper -->
      <div class="daraz-timeline">
        <div class="timeline-progress" id="progressFill"></div>
        <div class="timeline-node done">
          <div class="timeline-circle">✓</div>
          <h6>Placed</h6>
          <span>Just now</span>
        </div>
        <div class="timeline-node active" id="step2">
          <div class="timeline-circle">📦</div>
          <h6>Processing</h6>
          <span>Warehouse</span>
        </div>
        <div class="timeline-node" id="step3">
          <div class="timeline-circle">🚚</div>
          <h6>In Transit</h6>
          <span>Courier dispatch</span>
        </div>
        <div class="timeline-node" id="step4">
          <div class="timeline-circle">🏠</div>
          <h6>Delivered</h6>
          <span>Destination</span>
        </div>
      </div>

      <!-- Real-Time Courier GPS Status Box -->
      <div class="live-status-card live-status-box">
        <div class="live-status-card-header">
          <strong style="color: var(--neo); font-size: 15px;">Live Dispatch Hub Update</strong>
          <span class="gps-beacon mono">
            <span class="pulse-dot"></span> GPS Synced
          </span>
        </div>
        <p style="font-size: 13.5px; color: rgba(255,255,255,0.85); line-height: 1.6; margin: 0;">
          📍 Current Hub: <strong>Soba Zero-Waste Warehouse (Galle Central)</strong><br>
          🌱 Packaging: 100% Biodegradable Kraft Wrapping & Plastic-free Seals<br>
          ⚡ Carrier: Green Express Courier Fleet
        </p>
      </div>

      <div style="margin-top: 24px; display: flex; gap: 14px;">
        <a href="products_2.html" class="btn-place-order" style="text-decoration: none; margin: 0; background: rgba(255,255,255,0.1); color: #fff; box-shadow: none;">
          Continue Shopping
        </a>
      </div>
    </div>

  </div>

  <!-- RIGHT COLUMN: ORDER SUMMARY SIDEBAR -->
  <div>
    <div class="daraz-card order-summary-card">
      <div class="card-header">
        <div class="card-title" style="font-size: 18px;">
          Order Summary
        </div>
        <a href="cart.php" class="card-action-link">Edit Items</a>
      </div>

      <!-- Cart Item Stream -->
      <div class="summary-scroll-items" id="summaryItemsContainer">
        <!-- Rendered dynamically via JavaScript -->
      </div>

      <!-- Applied Benefits Breakdown (Transferred from Cart) -->
      <div class="calc-breakdown">
        <div class="calc-row">
          <span>Items Subtotal (<span id="summaryItemCountText">0 items</span>)</span>
          <span class="mono" id="subtotalVal">LKR 0.00</span>
        </div>
        <div class="calc-row">
          <span>Express Delivery</span>
          <span class="mono" id="deliveryVal">LKR 350.00</span>
        </div>
        <div class="calc-row discount" id="discountRow" style="display: none;">
          <span>Voucher Savings Applied</span>
          <span class="mono" id="discountVal">- LKR 0.00</span>
        </div>
        <div class="calc-row discount" id="ecoPointsRow" style="display: none;">
          <span>🌱 Eco-Credits Redeemed</span>
          <span class="mono" id="ecoPointsVal">- LKR 150.00</span>
        </div>

        <div class="total-row">
          <span class="label">Total Amount</span>
          <div style="text-align: right;">
            <div class="mono final-val" id="totalVal">LKR 2,450.00</div>
            <span style="font-size: 11px; color: var(--text-muted);">VAT included where applicable</span>
          </div>
        </div>
      </div>

      <!-- Floating Order Confirmation Trigger -->
      <button type="button" class="btn-place-order" onclick="document.getElementById('checkoutForm').requestSubmit()">
        <span>Place Order Now</span>
        <span class="mono" style="font-size: 13px; opacity: 0.85;">➔</span>
      </button>

      <p class="terms-note">
        By placing your order, you agree to Soba's 
        <a href="#" style="color: rgba(255,255,255,0.7); text-decoration: underline;">Terms of Service</a> & 
        <a href="#" style="color: rgba(255,255,255,0.7); text-decoration: underline;">Zero-Waste Policy</a>.
      </p>
    </div>
  </div>

</main>

<script>
  // PHP Backend එකෙන් secure කරපු Values Javascript එකට ගනිමු
  const merchantId = "<?php echo $merchant_id; ?>";
  const orderId = "<?php echo $order_id; ?>";
  const amountFormatted = "<?php echo number_format((float)$amount, 2, '.', ''); ?>";
  const currency = "<?php echo $currency; ?>";
  const payhereHash = "<?php echo $hash; ?>";

  let activeCheckoutItems = [];
  let currentCheckoutTotal = 0;

  const fallbackItems = [
    { id: 1, name: "Organic Bamboo Toothbrush", price: 450, quantity: 2, icon: "🪥" },
    { id: 2, name: "Solid Shampoo Bar (Citrus)", price: 1200, quantity: 1, icon: "🧼" }
  ];

  function loadOrderSummary() {
    // Ensure user is authenticated to access checkout
    try {
      const savedUser = localStorage.getItem('soba_user');
      <?php if (empty($_SESSION['u_id'])): ?>
      if (!savedUser) {
        window.location.replace('cart.php');
        return;
      }
      <?php endif; ?>
    } catch(e) {}

    let checkoutState = null;
    let selectedCartItems = [];

    // 1. Try reading checkout state prepared by cart.php
    try {
      const stateStr = sessionStorage.getItem('soba_checkout_state');
      if (stateStr) checkoutState = JSON.parse(stateStr);
    } catch(e) {}

    // 2. Read full cart from localStorage
    try {
      const cartStr = localStorage.getItem('soba_cart');
      if (cartStr) {
        const fullCart = JSON.parse(cartStr);
        // Only take items that were selected in the cart
        selectedCartItems = fullCart.filter(item => item.selected !== false);
        if (selectedCartItems.length === 0 && fullCart.length > 0) {
          selectedCartItems = fullCart; // fallback if none explicitly selected
        }
      }
    } catch(e) {}

    // Determine the items to display
    if (checkoutState && checkoutState.items && checkoutState.items.length > 0) {
      activeCheckoutItems = checkoutState.items;
    } else if (selectedCartItems.length > 0) {
      activeCheckoutItems = selectedCartItems;
    } else {
      activeCheckoutItems = fallbackItems;
    }

    // Render items list
    const container = document.getElementById('summaryItemsContainer');
    let calculatedSubtotal = 0;

    container.innerHTML = activeCheckoutItems.map(item => {
      const itemTotal = item.price * item.quantity;
      calculatedSubtotal += itemTotal;
      const imgDisplay = item.image 
        ? `<img src="${item.image}" alt="${item.name}" style="width:100%;height:100%;object-fit:cover;border-radius:8px;">`
        : (item.icon || '🌱');

      return `
        <div class="cart-item-row">
          <div class="item-thumb-box">${imgDisplay}</div>
          <div class="item-details">
            <h4>${item.name}</h4>
            <p>Qty: ${item.quantity} × LKR ${item.price.toLocaleString()}</p>
          </div>
          <div class="item-price-tag mono">
            LKR ${itemTotal.toLocaleString()}
          </div>
        </div>
      `;
    }).join('');

    // Calculate or retrieve financial figures
    let subtotal = calculatedSubtotal;
    let shipping = (subtotal >= 5000 || subtotal === 0) ? 0 : 350;
    let discount = 0;
    let ecoDeduction = 0;

    if (checkoutState) {
      if (checkoutState.shipping !== undefined) shipping = checkoutState.shipping;
      if (checkoutState.discount !== undefined) discount = checkoutState.discount;
      if (checkoutState.ecoDeduction !== undefined) ecoDeduction = checkoutState.ecoDeduction;
    }

    const grandTotal = Math.max(0, subtotal + shipping - discount - ecoDeduction);
    currentCheckoutTotal = grandTotal;

    // Update UI Breakdown
    const totalCount = activeCheckoutItems.reduce((acc, i) => acc + i.quantity, 0);
    const countEl = document.getElementById('summaryItemCountText');
    if (countEl) countEl.innerText = `${totalCount} item${totalCount === 1 ? '' : 's'}`;

    document.getElementById('subtotalVal').innerText = `LKR ${subtotal.toLocaleString()}.00`;
    
    const deliveryEl = document.getElementById('deliveryVal');
    if (deliveryEl) {
      deliveryEl.innerHTML = shipping === 0 
        ? `<span style="text-decoration:line-through;opacity:0.5;">LKR 350</span> Free` 
        : `LKR ${shipping.toLocaleString()}.00`;
    }

    const discountRow = document.getElementById('discountRow');
    const discountVal = document.getElementById('discountVal');
    if (discount > 0) {
      discountRow.style.display = 'flex';
      discountVal.innerText = `- LKR ${discount.toLocaleString()}.00`;
    } else {
      discountRow.style.display = 'none';
    }

    const ecoPointsRow = document.getElementById('ecoPointsRow');
    if (ecoDeduction > 0) {
      ecoPointsRow.style.display = 'flex';
    } else {
      ecoPointsRow.style.display = 'none';
    }

    document.getElementById('totalVal').innerText = `LKR ${grandTotal.toLocaleString()}.00`;

    // Auto-fill logged-in customer info from Dashboard & Database
    try {
      const savedUser = localStorage.getItem('soba_user');
      if (savedUser) {
        const u = JSON.parse(savedUser);

        const fillCustomerInputs = (obj) => {
          if (!obj) return;
          const fn = document.getElementById('fullName');
          if (fn && obj.name && !fn.value) fn.value = obj.name;

          const ph = document.getElementById('phoneNumber');
          if (ph && obj.phone && !ph.value) ph.value = obj.phone;

          const addr = document.getElementById('address');
          if (addr && obj.address && !addr.value) addr.value = obj.address;

          const ct = document.getElementById('city');
          if (ct && obj.city && !ct.value) ct.value = obj.city;

          const pc = document.getElementById('postalCode');
          if (pc && (obj.postal || obj.postalCode) && !pc.value) {
            pc.value = obj.postal || obj.postalCode;
          }

          if (obj.email) {
            window.customerEmail = obj.email;
          }
        };

        // 1. Fill immediately from localStorage
        fillCustomerInputs(u);

        // 2. Also fetch latest DB profile if phone/address missing in localStorage
        if (u.email && (!u.phone || !u.address || !u.city)) {
          fetch(`/php/web/Soba-Project/backend/api/customer/user_profile.php?email=${encodeURIComponent(u.email)}`)
            .then(res => res.json())
            .then(data => {
              if (data && data.success && data.user) {
                fillCustomerInputs(data.user);
                // Cache back into localStorage
                try {
                  localStorage.setItem('soba_user', JSON.stringify({ ...u, ...data.user }));
                } catch(e) {}
              }
            })
            .catch(() => {});
        }
      }
    } catch(e) {}
  }

  // PayHere Callbacks
  payhere.onCompleted = function onCompleted(completedOrderId) {
    console.log("Payment completed. OrderID:" + completedOrderId);
    showTrackingView(completedOrderId);
  };

  payhere.onDismissed = function onDismissed() {
    console.log("Payment popup closed by user.");
    sobaToast("Payment process was cancelled.", true);
  };

  payhere.onError = function onError(error) {
    console.log("Error:" + error);
    sobaToast("PayHere Error: " + error, true);
  };

  function handlePlaceOrder(e) {
    e.preventDefault();

    const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked').value;
    const name = document.getElementById('fullName').value.trim();
    const phone = document.getElementById('phoneNumber').value.trim();
    const city = document.getElementById('city').value.trim();
    const address = document.getElementById('address').value.trim();

    const nameParts = name.split(" ");
    const firstName = nameParts[0] || "Customer";
    const lastName = nameParts.slice(1).join(" ") || "User";

    if (paymentMethod === 'card') {
      const payableAmount = (currentCheckoutTotal > 0 ? currentCheckoutTotal : parseFloat(amountFormatted)).toFixed(2);
      const itemsDescription = activeCheckoutItems.length > 0
        ? activeCheckoutItems.map(i => i.name).slice(0, 3).join(', ')
        : "Soba Market Order";

      // Fetch precise server-generated hash for the payable amount
      const formData = new FormData();
      formData.append('order_id', orderId);
      formData.append('amount', payableAmount);
      formData.append('currency', currency);

      fetch('get_payhere_hash.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        const finalHash = (data && data.status === 'success') ? data.hash : payhereHash;
        const finalAmount = (data && data.status === 'success') ? data.amount : amountFormatted;

        var payment = {
          "sandbox": true,
          "merchant_id": merchantId,
          "return_url": "http://localhost/return.php",
          "cancel_url": "http://localhost/cancel.php",
          "notify_url": "http://localhost/notify.php",
          "order_id": orderId,
          "items": itemsDescription,
          "amount": finalAmount,
          "currency": currency,
          "hash": finalHash,
          "first_name": firstName,
          "last_name": lastName,
          "email": window.customerEmail || "customer@example.com",
          "phone": phone,
          "address": address,
          "city": city,
          "country": "Sri Lanka"
        };

        // Open PayHere Popup with 100% matched hash & amount
        payhere.startPayment(payment);
      })
      .catch(err => {
        // Fallback with default backend generated hash & amount
        var payment = {
          "sandbox": true,
          "merchant_id": merchantId,
          "return_url": "http://localhost/return.php",
          "cancel_url": "http://localhost/cancel.php",
          "notify_url": "http://localhost/notify.php",
          "order_id": orderId,
          "items": itemsDescription,
          "amount": amountFormatted,
          "currency": currency,
          "hash": payhereHash,
          "first_name": firstName,
          "last_name": lastName,
          "email": window.customerEmail || "customer@example.com",
          "phone": phone,
          "address": address,
          "city": city,
          "country": "Sri Lanka"
        };
        payhere.startPayment(payment);
      });

    } else {
      // Cash on Delivery - Save order to database
      try {
        fetch('/php/web/Soba-Project/backend/api/customer/place_order.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            order_number: orderId,
            customer_name: firstName + ' ' + lastName,
            customer_phone: phone,
            customer_email: window.customerEmail || 'customer@example.com',
            shipping_address: address + ', ' + city,
            payment_method: 'Cash on Delivery',
            subtotal: parseFloat(orderSubtotal),
            shipping_fee: parseFloat(shippingCost),
            discount_amount: parseFloat(discountVal),
            total: parseFloat(finalPayableTotal),
            items: orderItems
          })
        }).catch(e => console.warn(e));
      } catch(e) {}

      showTrackingView(orderId);
    }
  }

  function showTrackingView(placedOrderId) {
    document.getElementById('orderIdTag').innerText = "#" + placedOrderId;
    document.getElementById('checkoutFormCard').style.display = 'none';
    document.getElementById('trackingView').style.display = 'block';

    // Scroll smoothly to top of tracking view
    window.scrollTo({ top: 0, behavior: 'smooth' });

    // Clean up cart and active checkout session
    try { 
      localStorage.removeItem('soba_cart'); 
      sessionStorage.removeItem('soba_checkout_state');
    } catch(err) {}

    setTimeout(() => {
      document.getElementById('progressFill').style.width = '66%';
      document.getElementById('step2').className = 'timeline-node done';
      document.getElementById('step3').className = 'timeline-node active';
      
      const liveBox = document.querySelector('.live-status-box');
      liveBox.innerHTML = `
        <div class="live-status-card-header">
          <strong style="color: var(--neo); font-size: 15px;">Status: In Transit with Courier</strong>
          <span class="gps-beacon mono">
            <span class="pulse-dot"></span> Real-time GPS Tracking
          </span>
        </div>
        <p style="font-size: 13.5px; color: rgba(255,255,255,0.85); line-height: 1.6; margin: 0;">
          📍 Current Location: <strong>Southern Expressway (En route to destination)</strong><br>
          🚚 Vehicle: Zero-Emission Electric Courier Van #EB-4402<br>
          ⏱️ Estimated Arrival: Tomorrow, by 2:00 PM
        </p>
      `;
    }, 4500);
  }

  window.addEventListener('load', loadOrderSummary);
</script>
<script src="../../assets/js/main.js"></script>
</body>
</html>