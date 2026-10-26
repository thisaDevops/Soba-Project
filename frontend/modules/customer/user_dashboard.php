<?php
require_once __DIR__ . '/../../../backend/includes/session_handler.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'admin') {
    header('Location: ../admin/dashboard.php');
    exit;
}

// Redirect guest/unauthenticated users to login
if (empty($_SESSION['u_id'])) {
    header('Location: ../../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<script>
  // Immediate client-side check to redirect admin or unauthenticated users
  try {
    const stored = localStorage.getItem('soba_user');
    if (!stored) {
      window.location.replace('../../login.php');
    } else {
      const u = JSON.parse(stored);
      if (u && u.role && u.role.toLowerCase() === 'admin') {
        window.location.replace('../admin/dashboard.php');
      }
    }
  } catch(e) {}
</script>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Dashboard & Account — Soba Zero-Waste</title>
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
    --deep-card: rgba(14, 48, 35, 0.75);
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
  .bg-glow {
    position: fixed; z-index: 0; pointer-events: none; border-radius: 50%;
    filter: blur(100px); opacity: .3;
  }
  .bg-glow.g1 { width: 440px; height: 440px; background: var(--neo); top: -140px; left: -140px; }
  .bg-glow.g2 { width: 400px; height: 400px; background: #3B7A34; bottom: -120px; right: -100px; }

  /* Top Bar */
  .topbar {
    position: relative; z-index: 20;
    background: rgba(184, 255, 61, 0.08);
    border-bottom: 1px solid rgba(184, 255, 61, 0.14);
    backdrop-filter: blur(14px);
    height: 32px; display: flex; align-items: center; overflow: hidden;
  }
  .marquee-track {
    display: flex; gap: 60px; white-space: nowrap; animation: marquee 38s linear infinite;
    font-size: 12px; color: #dfffd0; padding-left: 100%; font-weight: 500;
  }
  @keyframes marquee { from { transform: translateX(0); } to { transform: translateX(-100%); } }

  /* Header Nav */
  header {
    position: sticky; top: 0; z-index: 30;
    background: rgba(7, 28, 20, 0.88);
    backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
    border-bottom: 1px solid var(--line);
    padding: 14px 5vw;
  }
  .bar {
    max-width: 1440px; margin: 0 auto;
    display: flex; align-items: center; justify-content: space-between; gap: 20px;
  }
  .logo { display: inline-flex; align-items: baseline; gap: 10px; cursor: pointer; text-decoration: none; }
  .logo .mark { font-family: 'Fraunces', serif; font-weight: 700; font-size: 28px; color: #F4F8F1; letter-spacing: -0.02em; line-height: 1; }
  .logo .sub { font-size: 11px; letter-spacing: 0.16em; text-transform: uppercase; color: var(--neo); font-weight: 700; }

  nav { display: flex; gap: 24px; align-items: center; }
  nav a {
    color: rgba(244,242,233,.78); text-decoration: none; font-size: 13.5px; font-weight: 500;
    padding-bottom: 3px; border-bottom: 2px solid transparent; transition: all .2s;
  }
  nav a:hover, nav a.active { color: #fff; border-color: var(--neo); }

  .header-actions { display: flex; align-items: center; gap: 14px; }
  
  .cart-badge-btn {
    display: inline-flex; align-items: center; gap: 7px;
    background: rgba(255, 255, 255, 0.08); border: 1px solid var(--line);
    color: #fff; padding: 7px 14px; border-radius: 99px; font-size: 13px; font-weight: 600;
    text-decoration: none; transition: all 0.2s ease;
  }
  .cart-badge-btn:hover { border-color: var(--neo); background: rgba(184, 255, 61, 0.12); }
  .cart-num-badge {
    background: var(--neo); color: var(--ink); border-radius: 999px;
    font-size: 11px; font-weight: 800; min-width: 19px; height: 19px;
    display: flex; align-items: center; justify-content: center; padding: 0 5px;
  }

  .user-badge {
    display: flex; align-items: center; gap: 9px;
    background: rgba(255,255,255,0.08); border: 1px solid var(--line);
    padding: 5px 14px 5px 6px; border-radius: 99px;
  }
  .user-avatar {
    width: 28px; height: 28px; border-radius: 50%; background: var(--neo); color: var(--ink);
    font-weight: 700; font-size: 12px; display: flex; align-items: center; justify-content: center;
  }
  .user-badge-name { font-size: 13px; font-weight: 600; color: #fff; }

  .logout-btn-nav {
    display: inline-flex; align-items: center; gap: 6px;
    background: rgba(200, 109, 81, 0.15); border: 1px solid rgba(200, 109, 81, 0.35);
    color: #FFA07A; padding: 7px 14px; border-radius: 99px; font-size: 12.5px;
    font-weight: 600; cursor: pointer; transition: all 0.2s ease;
  }
  .logout-btn-nav:hover { background: rgba(200, 109, 81, 0.3); color: #FFF; border-color: rgba(200, 109, 81, 0.8); }

  /* Main Shell */
  main {
    position: relative; z-index: 10; flex: 1;
    max-width: 1440px; width: 100%; margin: 0 auto;
    padding: 30px 5vw 80px;
  }

  /* Breadcrumb & Welcome Bar */
  .daraz-welcome-bar {
    background: linear-gradient(135deg, rgba(18, 61, 44, 0.7) 0%, rgba(8, 30, 21, 0.85) 100%);
    border: 1px solid var(--glass-border);
    border-radius: 20px;
    padding: 22px 28px;
    margin-bottom: 28px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.3);
    backdrop-filter: blur(16px);
  }
  .welcome-left h1 { font-size: 24px; color: #fff; display: flex; align-items: center; gap: 10px; }
  .welcome-left p { margin: 4px 0 0; font-size: 13.5px; color: rgba(244,242,233,0.65); }
  .welcome-badge {
    background: rgba(184,255,61,0.15); border: 1px solid var(--neo);
    color: var(--neo); font-size: 11px; padding: 3px 9px; border-radius: 99px;
    font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;
  }

  .quick-stat-pills {
    display: flex; gap: 14px; align-items: center; flex-wrap: wrap;
  }
  .stat-pill {
    background: rgba(255,255,255,0.05); border: 1px solid var(--line);
    border-radius: 14px; padding: 10px 18px; display: flex; align-items: center; gap: 12px;
  }
  .stat-pill .icon { color: var(--neo); width: 20px; height: 20px; }
  .stat-pill-info .num { font-size: 16px; font-weight: 700; color: #fff; }
  .stat-pill-info .lbl { font-size: 11px; color: rgba(244,242,233,0.55); text-transform: uppercase; letter-spacing: 0.05em; }

  /* Dashboard 2-Column Grid (Daraz Standard) */
  .dashboard-grid {
    display: grid;
    grid-template-columns: 290px 1fr;
    gap: 28px;
    align-items: start;
  }

  /* Left Sidebar: Daraz-Style Account Navigator */
  .daraz-sidebar {
    display: flex; flex-direction: column; gap: 20px;
  }

  .profile-summary-card {
    background: linear-gradient(160deg, rgba(18,61,44,.7), rgba(8,26,19,.85));
    border: 1px solid var(--glass-border);
    border-radius: 20px; padding: 26px 20px;
    box-shadow: 0 20px 60px rgba(0,0,0,.4);
    backdrop-filter: blur(20px);
    display: flex; flex-direction: column; align-items: center; text-align: center;
  }

  .avatar-large {
    width: 80px; height: 80px; border-radius: 50%;
    background: linear-gradient(135deg, var(--neo), #5FAE47);
    color: var(--ink); font-size: 32px; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 10px 25px rgba(184,255,61,.28);
    margin-bottom: 14px; border: 3px solid rgba(255,255,255,0.25);
  }

  .profile-summary-card h2 { font-size: 20px; color: #fff; margin-bottom: 4px; }
  .profile-summary-card .role { font-size: 11.5px; color: var(--neo); text-transform: uppercase; letter-spacing: 0.09em; font-weight: 700; }

  .verification-badge {
    margin-top: 12px; display: inline-flex; align-items: center; gap: 6px;
    background: rgba(184,255,61,0.12); border: 1px solid rgba(184,255,61,0.3);
    color: var(--neo); padding: 5px 12px; border-radius: 99px; font-size: 11.5px; font-weight: 600;
    cursor: pointer; transition: all 0.2s;
  }
  .verification-badge:hover { background: rgba(184,255,61,0.2); }
  .verification-badge.unverified {
    background: rgba(200,109,81,0.15); border-color: rgba(200,109,81,0.4); color: #FFA07A;
  }

  .eco-metrics {
    width: 100%; margin-top: 20px; padding-top: 18px; border-top: 1px solid var(--line);
    display: grid; grid-template-columns: 1fr 1fr; gap: 10px;
  }
  .metric-box {
    background: rgba(255,255,255,0.04); border: 1px solid var(--line);
    border-radius: 12px; padding: 12px 8px; text-align: center;
  }
  .metric-box .icon { color: var(--neo); margin-bottom: 4px; }
  .metric-box .num { font-size: 17px; font-weight: 700; color: var(--neo); }
  .metric-box .lbl { font-size: 10.5px; opacity: 0.65; margin-top: 2px; }

  /* Daraz Navigation Menu Panel */
  .daraz-nav-panel {
    background: linear-gradient(160deg, rgba(18,61,44,.6), rgba(8,26,19,.75));
    border: 1px solid var(--glass-border);
    border-radius: 20px; padding: 16px 12px;
    box-shadow: 0 20px 50px rgba(0,0,0,.35);
    backdrop-filter: blur(16px);
  }

  .nav-group-title {
    font-size: 11px; text-transform: uppercase; letter-spacing: 0.12em;
    color: rgba(244,242,233,0.45); font-weight: 700; padding: 10px 14px 6px;
  }

  .nav-menu-item {
    display: flex; align-items: center; justify-content: space-between;
    padding: 10px 14px; border-radius: 12px; color: rgba(244,242,233,0.8);
    font-size: 13.5px; font-weight: 500; cursor: pointer; text-decoration: none;
    transition: all 0.18s ease; margin-bottom: 3px;
  }
  .nav-menu-item:hover {
    background: rgba(255,255,255,0.06); color: #fff;
  }
  .nav-menu-item.active {
    background: rgba(184, 255, 61, 0.15); color: var(--neo); font-weight: 700;
    border: 1px solid rgba(184, 255, 61, 0.3);
  }
  .nav-menu-item-left {
    display: flex; align-items: center; gap: 10px;
  }
  .nav-menu-item .badge-count {
    background: rgba(255,255,255,0.1); color: #fff; border-radius: 99px;
    font-size: 11px; font-weight: 700; padding: 2px 7px;
  }
  .nav-menu-item.active .badge-count {
    background: var(--neo); color: var(--ink);
  }

  .logout-btn-card {
    width: 100%; margin-top: 10px;
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    background: rgba(200, 109, 81, 0.12); border: 1px solid rgba(200, 109, 81, 0.3);
    color: #FFA07A; padding: 12px; border-radius: 12px; font-size: 13px; font-weight: 600;
    cursor: pointer; font-family: 'Work Sans', sans-serif; transition: all 0.2s ease;
  }
  .logout-btn-card:hover { background: rgba(200, 109, 81, 0.25); color: #FFF; }

  /* Right Side: Main Content Panels */
  .daraz-main-content {
    display: flex; flex-direction: column; gap: 24px;
  }

  /* Daraz Orders Stepper / Status Hub */
  .daraz-orders-hub {
    background: linear-gradient(160deg, rgba(18,61,44,.65), rgba(8,26,19,.8));
    border: 1px solid var(--glass-border);
    border-radius: 20px; padding: 22px 26px;
    box-shadow: 0 20px 60px rgba(0,0,0,.35);
    backdrop-filter: blur(20px);
  }

  .hub-header {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 18px; padding-bottom: 14px; border-bottom: 1px solid var(--line);
  }
  .hub-header h2 { font-size: 19px; color: #fff; }
  .hub-header a { font-size: 12.5px; color: var(--neo); text-decoration: none; font-weight: 600; }
  .hub-header a:hover { text-decoration: underline; }

  .daraz-order-tabs {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px;
  }
  .order-tab-tile {
    background: rgba(255,255,255,0.04); border: 1px solid var(--line);
    border-radius: 14px; padding: 16px 12px; text-align: center;
    cursor: pointer; transition: all 0.2s; position: relative;
  }
  .order-tab-tile:hover {
    background: rgba(255,255,255,0.08); border-color: rgba(184,255,61,0.4);
    transform: translateY(-2px);
  }
  .order-tab-tile .tile-icon {
    font-size: 24px; margin-bottom: 6px; display: block;
  }
  .order-tab-tile .tile-title {
    font-size: 13px; font-weight: 600; color: #fff;
  }
  .order-tab-tile .tile-badge {
    position: absolute; top: 8px; right: 12px;
    background: var(--neo); color: var(--ink); border-radius: 99px;
    font-size: 10.5px; font-weight: 800; min-width: 18px; height: 18px;
    display: inline-flex; align-items: center; justify-content: center; padding: 0 4px;
  }

  /* Live Order Tracker Banner (Daraz Package Tracker) */
  .package-tracker-card {
    margin-top: 18px;
    background: rgba(255,255,255,0.03); border: 1px dashed rgba(184,255,61,0.3);
    border-radius: 14px; padding: 16px 20px;
    display: flex; align-items: center; justify-content: space-between; gap: 18px;
  }
  .tracker-product {
    display: flex; align-items: center; gap: 14px;
  }
  .tracker-thumb {
    width: 48px; height: 48px; border-radius: 10px; background: rgba(184,255,61,0.15);
    display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;
  }
  .tracker-info h4 { font-size: 14px; font-weight: 600; color: #fff; margin-bottom: 3px; }
  .tracker-info p { margin: 0; font-size: 12px; color: rgba(244,242,233,0.6); }
  .status-badge-transit {
    display: inline-flex; align-items: center; gap: 6px;
    background: rgba(184,255,61,0.15); color: var(--neo); font-size: 11.5px;
    font-weight: 700; padding: 4px 10px; border-radius: 99px;
  }
  .pulse-dot {
    width: 7px; height: 7px; border-radius: 50%; background: var(--neo);
    box-shadow: 0 0 8px var(--neo); animation: pulse 1.5s infinite;
  }
  @keyframes pulse { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.4; transform: scale(0.8); } }

  /* Right: User Details Dashboard Form Card */
  .dashboard-main-card {
    background: linear-gradient(160deg, rgba(18,61,44,.65), rgba(8,26,19,.8));
    border: 1px solid var(--glass-border);
    border-radius: 20px; padding: 32px;
    box-shadow: 0 25px 70px rgba(0,0,0,.45);
    backdrop-filter: blur(20px);
  }

  .card-header-title {
    display: flex; justify-content: space-between; align-items: flex-start;
    margin-bottom: 24px; border-bottom: 1px solid var(--line); padding-bottom: 16px;
  }
  .card-header-title h1 { font-size: 24px; color: #fff; }
  .card-header-title p { font-size: 13.5px; color: rgba(244,242,233,.6); margin-top: 4px; }

  /* Form Fields Styling */
  .form-grid {
    display: grid; grid-template-columns: 1fr 1fr; gap: 18px 24px;
  }
  .form-grid.full { grid-template-columns: 1fr; }

  .field-group { display: flex; flex-direction: column; }
  .field-label {
    display: flex; align-items: center; gap: 6px;
    font-size: 12px; font-weight: 600; color: rgba(244,242,233,.85); margin-bottom: 7px;
  }
  .field-label .icon { color: var(--neo); }

  .input-wrapper { position: relative; display: flex; align-items: center; }
  .input-wrapper .field-icon {
    position: absolute; left: 14px; color: rgba(244,242,233,.45); pointer-events: none;
  }
  .field-input {
    width: 100%; border: 1px solid var(--line); border-radius: 12px;
    padding: 12px 14px 12px 42px; background: rgba(255,255,255,0.05); color: #fff;
    font-size: 13.5px; font-family: 'Work Sans', sans-serif;
    transition: all 0.2s ease;
  }
  .field-input:focus {
    outline: none; border-color: var(--neo); background: rgba(255,255,255,0.08);
    box-shadow: 0 0 16px rgba(184,255,61,0.18);
  }
  .field-input:focus ~ .field-icon { color: var(--neo); }
  .field-input::placeholder { color: rgba(244,242,233,.3); }

  .pw-toggle {
    position: absolute; right: 12px; background: none; border: none; cursor: pointer;
    color: rgba(244,242,233,.5); display: flex; align-items: center; padding: 4px;
  }
  .pw-toggle:hover { color: var(--neo); }

  /* Section Dividers */
  .section-divider {
    grid-column: 1 / -1; margin: 12px 0 4px; font-size: 12.5px; font-weight: 700;
    color: var(--neo); text-transform: uppercase; letter-spacing: 0.08em;
    display: flex; align-items: center; gap: 10px;
  }
  .section-divider .icon { color: var(--neo); }
  .section-divider::after { content: ""; flex: 1; height: 1px; background: var(--line); }

  /* Address Preview Card (Daraz Default Shipping Card) */
  .address-preview-box {
    grid-column: 1 / -1;
    background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1);
    border-radius: 14px; padding: 14px 18px; margin-top: 4px;
    display: flex; justify-content: space-between; align-items: center;
  }
  .addr-badge {
    background: rgba(184,255,61,0.15); color: var(--neo); font-size: 10.5px;
    font-weight: 700; padding: 3px 8px; border-radius: 6px; text-transform: uppercase;
  }
  .addr-text {
    font-size: 12.5px; color: rgba(244,242,233,0.7); margin-top: 4px;
  }

  /* Submit Button & Verification Trigger */
  .action-row {
    margin-top: 28px; display: flex; justify-content: space-between; align-items: center; gap: 16px;
    padding-top: 20px; border-top: 1px solid var(--line);
  }

  .submit-btn {
    display: inline-flex; align-items: center; gap: 9px;
    background: linear-gradient(135deg, var(--neo), #8fe04f); color: var(--ink);
    border: none; border-radius: 12px; padding: 13px 26px; font-size: 14px; font-weight: 700;
    cursor: pointer; font-family: 'Work Sans', sans-serif;
    box-shadow: 0 14px 30px rgba(184,255,61,.22); transition: all .15s ease;
  }
  .submit-btn:hover { transform: translateY(-1px); box-shadow: 0 18px 36px rgba(184,255,61,.32); }

  .verify-btn {
    display: inline-flex; align-items: center; gap: 8px;
    background: transparent; color: var(--neo); border: 1px solid var(--neo);
    border-radius: 12px; padding: 12px 20px; font-size: 13.5px; font-weight: 600;
    cursor: pointer; font-family: 'Work Sans', sans-serif; transition: all .15s ease;
  }
  .verify-btn:hover { background: rgba(184,255,61,0.12); }

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

  /* Tab View Visibility */
  .tab-content { display: none; }
  .tab-content.active { display: block; }

  /* Daraz Order History List */
  .order-history-card {
    background: rgba(255,255,255,0.03); border: 1px solid var(--line);
    border-radius: 16px; padding: 18px 20px; margin-bottom: 16px;
  }
  .order-history-header {
    display: flex; justify-content: space-between; align-items: center;
    border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 12px; margin-bottom: 14px;
    font-size: 13px; color: rgba(244,242,233,0.7);
  }
  .order-history-item {
    display: flex; align-items: center; justify-content: space-between; gap: 14px;
  }
  .order-item-left {
    display: flex; align-items: center; gap: 14px;
  }
  .order-item-thumb {
    width: 52px; height: 52px; border-radius: 10px; background: rgba(184,255,61,0.1);
    display: flex; align-items: center; justify-content: center; font-size: 24px;
  }
  .order-history-actions {
    display: flex; gap: 10px; align-items: center;
  }
  .btn-sm-action {
    background: rgba(255,255,255,0.06); border: 1px solid var(--line);
    color: #fff; padding: 7px 14px; border-radius: 8px; font-size: 12px; font-weight: 600;
    text-decoration: none; cursor: pointer; transition: all 0.2s;
  }
  .btn-sm-action:hover { border-color: var(--neo); color: var(--neo); }

  /* Responsive Queries */
  @media (max-width: 1024px) {
    .dashboard-grid { grid-template-columns: 1fr; }
    .daraz-welcome-bar { flex-direction: column; align-items: flex-start; }
    .quick-stat-pills { width: 100%; justify-content: space-between; }
  }
  @media (max-width: 640px) {
    .daraz-order-tabs { grid-template-columns: 1fr 1fr; }
    .form-grid { grid-template-columns: 1fr; }
    .action-row { flex-direction: column-reverse; align-items: stretch; }
    .submit-btn, .verify-btn { justify-content: center; width: 100%; }
    .quick-stat-pills { flex-direction: column; width: 100%; }
    .stat-pill { width: 100%; }
  }
</style>
</head>
<body>

<div class="bg-scene"></div>
<div class="bg-noise"></div>
<div class="bg-glow g1"></div>
<div class="bg-glow g2"></div>

<!-- Toast notification -->
<div class="toast" id="toast">
  <svg class="icon" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
  <span id="toastMsg">Profile details saved successfully</span>
</div>

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
<header>
  <div class="bar">
    <a class="logo" href="../../home.php">
      <span class="mark">Soba</span>
      <span class="sub">MARKET</span>
    </a>
    <nav>
      <a href="../../home.php">Home</a>
      <a href="products.php">Shop Products</a>
      <a href="cart.php">My Cart</a>
      <a href="user_dashboard.php" class="active">Account</a>
    </nav>
    <div class="header-actions">
      <a href="cart.php" class="cart-badge-btn" title="View Cart">
        🛒 <span>Cart</span>
        <span class="cart-num-badge" id="cartCount">0</span>
      </a>
      <div class="user-badge">
        <div class="user-avatar" id="headerAvatar">N</div>
        <span class="user-badge-name" id="headerUserName">Nimal</span>
      </div>
      <button type="button" class="logout-btn-nav" onclick="handleLogout()" title="Sign out">
        <svg class="icon icon-sm" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        <span>Logout</span>
      </button>
    </div>
  </div>
</header>

<main>
  <!-- Daraz Quick Summary Banner -->
  <div class="daraz-welcome-bar">
    <div class="welcome-left">
      <h1>
        <span>Hello, <span id="welcomeName">Thisaru</span>!</span>
        <span class="welcome-badge" id="welcomeTierBadge">Seedling Saver · Eco Club</span>
      </h1>
      <p>Manage your orders, profile details, delivery addresses, and eco impact reward points.</p>
    </div>
    <div class="quick-stat-pills">
      <div class="stat-pill">
        <svg class="icon" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        <div class="stat-pill-info">
          <div class="num mono" id="topEcoPoints">1,500</div>
          <div class="lbl">Eco Points</div>
        </div>
      </div>
      <div class="stat-pill">
        <svg class="icon" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
        <div class="stat-pill-info">
          <div class="num mono" id="topTotalOrders">03</div>
          <div class="lbl">Total Orders</div>
        </div>
      </div>
      <div class="stat-pill">
        <svg class="icon" viewBox="0 0 24 24"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 7 18 7c1 8-6 11-7 13z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 11 14 11 14"/></svg>
        <div class="stat-pill-info">
          <div class="num mono" id="topPlasticSaved">0.30 kg</div>
          <div class="lbl">Plastic Saved</div>
        </div>
      </div>
    </div>
  </div>

  <div class="dashboard-grid">

    <!-- Left: Daraz Account Sidebar -->
    <aside class="daraz-sidebar">
      
      <!-- Profile Card -->
      <div class="profile-summary-card">
        <div class="avatar-large" id="profileAvatar">T</div>
        <h2 id="summaryName">Thisaru Ranathunga</h2>
        <span class="role" id="summaryRole">Eco Warrior Member</span>

        <div class="verification-badge" id="vBadge" onclick="verifyAccount()" title="Click to verify account">
          <svg class="icon icon-sm" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          <span>Account Verified</span>
        </div>

        <div class="eco-metrics">
          <div class="metric-box">
            <svg class="icon icon-lg" viewBox="0 0 24 24" style="margin:0 auto 4px;"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 7 18 7c1 8-6 11-7 13z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 11 14 11 14"/></svg>
            <div class="num mono" id="sidePlasticSaved">0.30 kg</div>
            <div class="lbl">Plastic Saved</div>
          </div>
          <div class="metric-box">
            <svg class="icon icon-lg" viewBox="0 0 24 24" style="margin:0 auto 4px;"><path d="M16.5 9.4 7.55 4.24"/><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.29 7 12 12 20.71 7"/><line x1="12" y1="22" x2="12" y2="12"/></svg>
            <div class="num mono" id="sideTotalOrders">03</div>
            <div class="lbl">Eco Orders</div>
          </div>
        </div>
      </div>

      <!-- Daraz-Style Navigation Menu -->
      <div class="daraz-nav-panel">
        <div class="nav-group-title">Manage My Account</div>
        <div class="nav-menu-item active" onclick="switchTab('profile', this)">
          <div class="nav-menu-item-left">
            <svg class="icon" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <span>My Profile & Settings</span>
          </div>
        </div>
        <div class="nav-menu-item" onclick="switchTab('address', this)">
          <div class="nav-menu-item-left">
            <svg class="icon" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <span>Address Book</span>
          </div>
          <span class="badge-count">1</span>
        </div>
        <div class="nav-menu-item" onclick="switchTab('vouchers', this)">
          <div class="nav-menu-item-left">
            <svg class="icon" viewBox="0 0 24 24"><line x1="4" y1="9" x2="20" y2="9"/><line x1="4" y1="15" x2="20" y2="15"/><line x1="10" y1="3" x2="8" y2="21"/><line x1="16" y1="3" x2="14" y2="21"/></svg>
            <span>Eco Vouchers & Points</span>
          </div>
          <span class="badge-count">2</span>
        </div>

        <div class="nav-group-title" style="margin-top: 10px;">My Orders</div>
        <div class="nav-menu-item" onclick="switchTab('orders', this)">
          <div class="nav-menu-item-left">
            <svg class="icon" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-2z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            <span>All My Orders</span>
          </div>
          <span class="badge-count">4</span>
        </div>
        <div class="nav-menu-item" onclick="switchTab('wishlist', this)">
          <div class="nav-menu-item-left">
            <svg class="icon" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            <span>Saved Wishlist</span>
          </div>
          <span class="badge-count" id="dashWishlistCount">0</span>
        </div>
      </div>

    </aside>

    <!-- Right: Main Content Area -->
    <div class="daraz-main-content">

      <!-- Daraz-Style Orders Quick Tracker Hub -->
      <div class="daraz-orders-hub">
        <div class="hub-header">
          <h2>Recent Orders Tracker</h2>
          <a href="javascript:void(0)" onclick="switchTab('orders')">View All (4) &rarr;</a>
        </div>

        <div class="daraz-order-tabs">
          <div class="order-tab-tile" onclick="switchTab('orders')">
            <span class="tile-icon">💳</span>
            <span class="tile-title">To Pay</span>
            <span class="tile-badge">1</span>
          </div>
          <div class="order-tab-tile" onclick="switchTab('orders')">
            <span class="tile-icon">📦</span>
            <span class="tile-title">To Ship</span>
            <span class="tile-badge">2</span>
          </div>
          <div class="order-tab-tile" onclick="switchTab('orders')">
            <span class="tile-icon">🚚</span>
            <span class="tile-title">To Receive</span>
            <span class="tile-badge">1</span>
          </div>
          <div class="order-tab-tile" onclick="switchTab('orders')">
            <span class="tile-icon">⭐</span>
            <span class="tile-title">To Review</span>
          </div>
        </div>

        <!-- Package In-Transit Card (Daraz Real-time status) -->
        <div class="package-tracker-card">
          <div class="tracker-product">
            <div class="tracker-thumb">🌱</div>
            <div class="tracker-info">
              <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 2px;">
                <span class="status-badge-transit"><span class="pulse-dot"></span> In Transit</span>
                <span style="font-size: 11px; color: rgba(244,242,233,0.5); font-family: 'IBM Plex Mono', monospace;">#SOBA-991204</span>
              </div>
              <h4>Organic Bamboo Cutlery Set (Eco Pack of 2)</h4>
              <p>Estimated delivery: Today, 3:30 PM — Carbon Neutral Express</p>
            </div>
          </div>
          <a href="javascript:void(0)" onclick="showToast('Tracking package #SOBA-991204: Courier is out for delivery in Colombo 04')" class="btn-sm-action" style="border-color: var(--neo); color: var(--neo);">
            Track Package
          </a>
        </div>
      </div>

      <!-- Tab 1: Profile & Address Form (Default Tab) -->
      <div id="tab-profile" class="tab-content active">
        <div class="dashboard-main-card">
          <div class="card-header-title">
            <div>
              <h1>User Profile & Settings</h1>
              <p>Update your personal information, login credentials, and shipping addresses.</p>
            </div>
          </div>

          <form id="dashboardForm" onsubmit="return handleSave(event)">
            <div class="form-grid">

              <div class="section-divider">
                <svg class="icon" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Account Identity & Security
              </div>

              <div class="field-group">
                <label class="field-label" for="dashName">Username / Full Name</label>
                <div class="input-wrapper">
                  <svg class="icon field-icon" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                  <input type="text" class="field-input" id="dashName" value="Nimal Perera" required oninput="updateLiveSummary()">
                </div>
              </div>

              <div class="field-group">
                <label class="field-label" for="dashEmail">Email Address</label>
                <div class="input-wrapper">
                  <svg class="icon field-icon" viewBox="0 0 24 24"><path d="M22 6c0-1.1-.9-2-2-2H4a2 2 0 0 0-2 2v12c0 1.1.9 2 2 2h16a2 2 0 0 0 2-2V6z"/><polyline points="22 6 12 13 2 6"/></svg>
                  <input type="email" class="field-input" id="dashEmail" value="nimal.perera@gmail.com" required>
                </div>
              </div>

              <div class="field-group">
                <label class="field-label" for="dashPassword">Password</label>
                <div class="input-wrapper">
                  <svg class="icon field-icon" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                  <input type="password" class="field-input" id="dashPassword" value="password123" required style="padding-right:40px;">
                  <button type="button" class="pw-toggle" id="pwToggle" aria-label="Show password" onclick="togglePassword()">
                    <svg class="icon" viewBox="0 0 24 24" id="pwIcon"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                  </button>
                </div>
              </div>

              <div class="field-group">
                <label class="field-label" for="dashPhone">Phone Number</label>
                <div class="input-wrapper">
                  <svg class="icon field-icon" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                  <input type="text" class="field-input" id="dashPhone" value="+94 77 123 4567" placeholder="+94 7X XXX XXXX" required>
                </div>
              </div>

              <div class="section-divider">
                <svg class="icon" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                Shipping & Delivery Details (Default Address)
              </div>

              <div class="field-group" style="grid-column: 1 / -1;">
                <label class="field-label" for="dashAddress">Street Address</label>
                <div class="input-wrapper">
                  <svg class="icon field-icon" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                  <input type="text" class="field-input" id="dashAddress" value="No. 45, Galle Road, Bambalapitiya" placeholder="House No, Street Name" required oninput="updateLiveSummary()">
                </div>
              </div>

              <div class="field-group">
                <label class="field-label" for="dashCity">City / Region</label>
                <div class="input-wrapper">
                  <svg class="icon field-icon" viewBox="0 0 24 24"><path d="M6 22V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v18"/><path d="M18 22V9a1 1 0 0 0-1-1h-3"/><path d="M2 22h20"/></svg>
                  <input type="text" class="field-input" id="dashCity" value="Colombo" required oninput="updateLiveSummary()">
                </div>
              </div>

              <div class="field-group">
                <label class="field-label" for="dashPostal">Postal Code</label>
                <div class="input-wrapper">
                  <svg class="icon field-icon" viewBox="0 0 24 24"><line x1="4" y1="9" x2="20" y2="9"/><line x1="4" y1="15" x2="20" y2="15"/><line x1="10" y1="3" x2="8" y2="21"/><line x1="16" y1="3" x2="14" y2="21"/></svg>
                  <input type="text" class="field-input" id="dashPostal" value="00400" oninput="updateLiveSummary()">
                </div>
              </div>

              <!-- Daraz Address Preview Strip -->
              <div class="address-preview-box">
                <div>
                  <div style="display: flex; align-items: center; gap: 8px;">
                    <span class="addr-badge">Default Delivery Address</span>
                    <span style="font-size: 11px; color: var(--neo);">Verified for Carbon-Neutral Express</span>
                  </div>
                  <div class="addr-text" id="liveAddrPreview">No. 45, Galle Road, Bambalapitiya, Colombo 00400</div>
                </div>
                <button type="button" class="btn-sm-action" onclick="document.getElementById('dashAddress').focus()">Edit</button>
              </div>

            </div>

            <div class="action-row">
              <button type="button" class="verify-btn" onclick="verifyAccount()">
                <svg class="icon" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
                Verify Account
              </button>
              <button type="submit" class="submit-btn">
                <svg class="icon" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Save Dashboard Info
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Tab 2: All Orders History -->
      <div id="tab-orders" class="tab-content">
        <div class="dashboard-main-card">
          <div class="card-header-title">
            <div>
              <h1>Order History & Invoices</h1>
              <p>Track live packages, review purchased zero-waste goods, and download carbon offset receipts.</p>
            </div>
          </div>

          <div id="userOrdersContainer">

          <!-- Order 1 -->
          <div class="order-history-card">
            <div class="order-history-header">
              <div>
                <strong style="color: #fff;">Order #SOBA-991204</strong>
                <span style="margin: 0 8px;">·</span> Placed on Oct 10, 2026
              </div>
              <div>
                <span class="status-badge-transit"><span class="pulse-dot"></span> In Transit</span>
              </div>
            </div>
            <div class="order-history-item">
              <div class="order-item-left">
                <div class="order-item-thumb">🎋</div>
                <div>
                  <h4 style="color:#fff; font-size:14px; margin-bottom: 2px;">Organic Bamboo Cutlery Set (Pack of 2)</h4>
                  <div style="font-size:12px; color:rgba(244,242,233,0.6);">Qty: 2 · Flagship Store: EcoLiving Colombo</div>
                  <div class="mono" style="color:var(--neo); font-weight:700; font-size:13px; margin-top:2px;">Rs. 1,450</div>
                </div>
              </div>
              <div class="order-history-actions">
                <button type="button" class="btn-sm-action" onclick="showToast('Tracking package #SOBA-991204: Out for delivery today.')">Track</button>
                <a href="products.php" class="btn-sm-action">Buy Again</a>
              </div>
            </div>
          </div>

          <!-- Order 2 -->
          <div class="order-history-card">
            <div class="order-history-header">
              <div>
                <strong style="color: #fff;">Order #SOBA-849102</strong>
                <span style="margin: 0 8px;">·</span> Delivered on Oct 03, 2026
              </div>
              <div>
                <span style="background:rgba(255,255,255,0.08); color:#dfffd0; font-size:11.5px; font-weight:600; padding:4px 10px; border-radius:99px;">✓ Delivered</span>
              </div>
            </div>
            <div class="order-history-item">
              <div class="order-item-left">
                <div class="order-item-thumb">🧼</div>
                <div>
                  <h4 style="color:#fff; font-size:14px; margin-bottom: 2px;">Cold-Pressed Neem & Turmeric Herbal Body Soap</h4>
                  <div style="font-size:12px; color:rgba(244,242,233,0.6);">Qty: 3 · Flagship Store: Soba Essentials</div>
                  <div class="mono" style="color:var(--neo); font-weight:700; font-size:13px; margin-top:2px;">Rs. 2,550</div>
                </div>
              </div>
              <div class="order-history-actions">
                <button type="button" class="btn-sm-action" onclick="showToast('Thank you for rating! +50 Eco points awarded.')">Review (+50 pts)</button>
                <a href="products.php" class="btn-sm-action">Buy Again</a>
              </div>
            </div>
          </div>
          </div>
        </div>
      </div>

      <!-- Tab 3: Wishlist -->
      <div id="tab-wishlist" class="tab-content">
        <div class="dashboard-main-card">
          <div class="card-header-title">
            <div>
              <h1>My Saved Wishlist</h1>
              <p>Products you have bookmarked for future zero-waste lifestyle purchases.</p>
            </div>
          </div>

          <div id="wishlistGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 18px;">
            <!-- Populated dynamically via renderUserWishlist() -->
          </div>
        </div>
      </div>

      <!-- Tab 4: Vouchers & Points -->
      <div id="tab-vouchers" class="tab-content">
        <div class="dashboard-main-card">
          <div class="card-header-title">
            <div>
              <h1>Eco Vouchers & Loyalty Points</h1>
              <p>Redeem your plastic-free shopping rewards on checkout.</p>
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div style="background: linear-gradient(135deg, rgba(184,255,61,0.12), rgba(18,61,44,0.4)); border: 1px dashed var(--neo); border-radius: 14px; padding: 18px;">
              <div style="font-size: 11px; text-transform: uppercase; color: var(--neo); font-weight: 700;">Active Voucher</div>
              <h3 style="font-size: 20px; color:#fff; margin: 4px 0 6px;">ECO15 - 15% OFF</h3>
              <p style="font-size: 12px; color: rgba(244,242,233,0.7); margin: 0 0 12px;">Valid on all zero-waste products over Rs. 2,000</p>
              <button type="button" class="btn-sm-action" onclick="navigator.clipboard ? navigator.clipboard.writeText('ECO15') : null; showToast('Voucher code ECO15 copied to clipboard!')">Copy Code</button>
            </div>

            <div style="background: linear-gradient(135deg, rgba(184,255,61,0.12), rgba(18,61,44,0.4)); border: 1px dashed var(--neo); border-radius: 14px; padding: 18px;">
              <div style="font-size: 11px; text-transform: uppercase; color: var(--neo); font-weight: 700;">Free Shipping Voucher</div>
              <h3 style="font-size: 20px; color:#fff; margin: 4px 0 6px;">FREESHIP</h3>
              <p style="font-size: 12px; color: rgba(244,242,233,0.7); margin: 0 0 12px;">Free island-wide carbon neutral courier delivery</p>
              <button type="button" class="btn-sm-action" onclick="navigator.clipboard ? navigator.clipboard.writeText('FREESHIP') : null; showToast('Voucher code FREESHIP copied to clipboard!')">Copy Code</button>
            </div>
          </div>
        </div>
      </div>

    </div>

  </div>
</main>

<script>
  async function loadUserProfileFromDB() {
    let email = null;
    try {
      const stored = localStorage.getItem('soba_user');
      if (stored) {
        const u = JSON.parse(stored);
        if (u.email) email = u.email;
      }
    } catch(e) {}

    const url = email 
      ? `/php/web/Soba-Project/backend/api/customer/user_profile.php?email=${encodeURIComponent(email)}`
      : `/php/web/Soba-Project/backend/api/customer/user_profile.php`;

    try {
      const res = await fetch(url);
      if (res.status === 401) {
        localStorage.removeItem('soba_user');
        window.location.replace('../../login.php');
        return;
      }
      const data = await res.json();
      if (!data || !data.success || !data.user) {
        localStorage.removeItem('soba_user');
        window.location.replace('../../login.php');
        return;
      }
      if (data && data.success && data.user) {
        const u = data.user;
        if (u.role && u.role.toLowerCase() === 'admin') {
          window.location.replace('../admin/dashboard.php');
          return;
        }
        document.getElementById('dashName').value = u.name;
        document.getElementById('summaryName').textContent = u.name;
        const firstName = u.name.split(' ')[0];
        document.getElementById('headerUserName').textContent = firstName;
        if (document.getElementById('welcomeName')) {
          document.getElementById('welcomeName').textContent = firstName;
        }
        const initial = u.name.charAt(0).toUpperCase();
        document.getElementById('headerAvatar').textContent = initial;
        document.getElementById('profileAvatar').textContent = initial;

        if (document.getElementById('welcomeTierBadge')) {
          document.getElementById('welcomeTierBadge').textContent = `${u.tier_icon || '🌱'} ${u.tier} · Eco Club`;
        }
        if (document.getElementById('topEcoPoints')) {
          document.getElementById('topEcoPoints').textContent = Number(u.eco_points).toLocaleString();
        }
        if (document.getElementById('topTotalOrders')) {
          document.getElementById('topTotalOrders').textContent = String(data.orders ? data.orders.length : 0).padStart(2, '0');
        }
        if (document.getElementById('sideTotalOrders')) {
          document.getElementById('sideTotalOrders').textContent = String(data.orders ? data.orders.length : 0).padStart(2, '0');
        }
        if (document.getElementById('topPlasticSaved')) {
          document.getElementById('topPlasticSaved').textContent = (u.lifetime_grams_saved / 1000).toFixed(2) + ' kg';
        }
        if (document.getElementById('sidePlasticSaved')) {
          document.getElementById('sidePlasticSaved').textContent = (u.lifetime_grams_saved / 1000).toFixed(2) + ' kg';
        }

        if (u.email) document.getElementById('dashEmail').value = u.email;
        if (u.phone) document.getElementById('dashPhone').value = u.phone;
        if (u.address) document.getElementById('dashAddress').value = u.address;
        if (u.city) document.getElementById('dashCity').value = u.city;
        if (u.postal) document.getElementById('dashPostal').value = u.postal;

        updateLiveSummary();

        // Sync full profile into localStorage so checkout.php can auto-fill
        try {
          const existing = JSON.parse(localStorage.getItem('soba_user') || '{}');
          localStorage.setItem('soba_user', JSON.stringify({
            ...existing,
            id: u.id,
            name: u.name,
            email: u.email,
            phone: u.phone || document.getElementById('dashPhone').value,
            address: u.address || document.getElementById('dashAddress').value,
            city: u.city || document.getElementById('dashCity').value,
            postal: u.postal || document.getElementById('dashPostal').value,
            role: existing.role || 'customer'
          }));
        } catch(e) {}

        // Render dynamic orders from DB
        if (Array.isArray(data.orders) && data.orders.length > 0) {
          const container = document.getElementById('userOrdersContainer');
          if (container) {
            container.innerHTML = '';
            data.orders.forEach(ord => {
              let badgeHtml = '<span class="status-badge-transit"><span class="pulse-dot"></span> In Transit</span>';
              const s = (ord.status || '').toLowerCase();
              if (s.includes('delivered')) {
                badgeHtml = '<span style="background:rgba(255,255,255,0.08); color:#dfffd0; font-size:11.5px; font-weight:600; padding:4px 10px; border-radius:99px;">✓ Delivered</span>';
              } else if (s.includes('pending') || s.includes('pack')) {
                badgeHtml = '<span style="background:rgba(244,160,122,0.15); color:#FFA07A; font-size:11.5px; font-weight:600; padding:4px 10px; border-radius:99px;">⏳ Pending Packing</span>';
              }

              const itemsHtml = (ord.items && ord.items.length > 0) ? ord.items.map(it => `
                <div class="order-history-item">
                  <div class="order-item-left">
                    <div class="order-item-thumb">${it.icon || '📦'}</div>
                    <div>
                      <h4 style="color:#fff; font-size:14px; margin-bottom: 2px;">${it.name}</h4>
                      <div style="font-size:12px; color:rgba(244,242,233,0.6);">Qty: ${it.quantity} · ${it.cert_badge || 'Zero-Waste Standard'}</div>
                      <div class="mono" style="color:var(--neo); font-weight:700; font-size:13px; margin-top:2px;">Rs. ${Number(it.unit_price * it.quantity).toLocaleString()}</div>
                    </div>
                  </div>
                  <div class="order-history-actions">
                    <button type="button" class="btn-sm-action" onclick="showToast('Tracking #${ord.order_number}: Live via Soba Green Express')">Track</button>
                    <a href="products.php" class="btn-sm-action">Buy Again</a>
                  </div>
                </div>
              `).join('') : `
                <div class="order-history-item">
                  <div class="order-item-left">
                    <div class="order-item-thumb">🌱</div>
                    <div>
                      <h4 style="color:#fff; font-size:14px; margin-bottom: 2px;">Zero-Waste Marketplace Order</h4>
                      <div class="mono" style="color:var(--neo); font-weight:700; font-size:13px; margin-top:2px;">Rs. ${Number(ord.total).toLocaleString()}</div>
                    </div>
                  </div>
                  <div class="order-history-actions">
                    <button type="button" class="btn-sm-action" onclick="showToast('Tracking #${ord.order_number}')">Track</button>
                  </div>
                </div>
              `;

              const dateStr = ord.created_at ? ord.created_at.split(' ')[0] : 'Recent';

              const card = document.createElement('div');
              card.className = 'order-history-card';
              card.innerHTML = `
                <div class="order-history-header">
                  <div>
                    <strong style="color: #fff;">Order #${ord.order_number}</strong>
                    <span style="margin: 0 8px;">·</span> Placed on ${dateStr}
                    <span style="margin: 0 8px;">·</span> Total: <strong style="color:var(--neo);">Rs. ${Number(ord.total).toLocaleString()}</strong>
                  </div>
                  <div>
                    ${badgeHtml}
                  </div>
                </div>
                ${itemsHtml}
              `;
              container.appendChild(card);
            });
          }
        }
      }
    } catch(err) {
      console.warn("Could not fetch user profile from DB", err);
    }
  }

  // On Page Load: Load user data, cart count and saved wishlist
  window.addEventListener('DOMContentLoaded', () => {
    loadUserProfileFromDB();
    renderUserWishlist();

    try {
      // Read cart count from localStorage
      const cart = localStorage.getItem('soba_cart');
      if (cart) {
        const cartItems = JSON.parse(cart);
        const count = Array.isArray(cartItems) ? cartItems.reduce((acc, it) => acc + (it.quantity || it.qty || 1), 0) : 0;
        document.getElementById('cartCount').textContent = count;
      }
    } catch(e) {}
  });

  window.addEventListener('storage', (e) => {
    if (e.key === 'soba_wishlist') {
      renderUserWishlist();
    }
    if (e.key === 'soba_cart') {
      try {
        const cartItems = JSON.parse(e.newValue || '[]');
        const count = Array.isArray(cartItems) ? cartItems.reduce((acc, it) => acc + (it.quantity || it.qty || 1), 0) : 0;
        document.getElementById('cartCount').textContent = count;
      } catch(err) {}
    }
  });

  function updateLiveSummary() {
    const addr = document.getElementById('dashAddress').value || '';
    const city = document.getElementById('dashCity').value || '';
    const postal = document.getElementById('dashPostal').value || '';
    const preview = document.getElementById('liveAddrPreview');
    if (preview) {
      preview.textContent = `${addr}${city ? ', ' + city : ''}${postal ? ' ' + postal : ''}` || 'No address specified yet';
    }
  }

  function switchTab(tabKey, clickedNav) {
    // Switch active nav item styling
    document.querySelectorAll('.nav-menu-item').forEach(el => el.classList.remove('active'));
    if (clickedNav) {
      clickedNav.classList.add('active');
    }

    // Switch visible tab panel
    document.querySelectorAll('.tab-content').forEach(panel => panel.classList.remove('active'));
    
    if (tabKey === 'profile' || tabKey === 'address') {
      const target = document.getElementById('tab-profile');
      if (target) target.classList.add('active');
      if (tabKey === 'address') {
        setTimeout(() => {
          document.getElementById('dashAddress').focus();
        }, 100);
      }
    } else if (tabKey === 'orders') {
      const target = document.getElementById('tab-orders');
      if (target) target.classList.add('active');
    } else if (tabKey === 'wishlist') {
      const target = document.getElementById('tab-wishlist');
      if (target) target.classList.add('active');
      renderUserWishlist();
    } else if (tabKey === 'vouchers') {
      const target = document.getElementById('tab-vouchers');
      if (target) target.classList.add('active');
    }
  }

  function showToast(msg) {
    const t = document.getElementById('toast');
    document.getElementById('toastMsg').textContent = msg;
    t.classList.add('show');
    clearTimeout(window._toastTimer);
    window._toastTimer = setTimeout(() => t.classList.remove('show'), 2800);
  }

  function handleSave(e) {
    e.preventDefault();
    const name = document.getElementById('dashName').value.trim();
    const email = document.getElementById('dashEmail').value.trim();
    const phone = document.getElementById('dashPhone').value.trim();
    const address = document.getElementById('dashAddress').value.trim();
    const city = document.getElementById('dashCity').value.trim();
    const postal = document.getElementById('dashPostal').value.trim();

    // Update dynamic UI components
    document.getElementById('summaryName').textContent = name;
    const firstName = name.split(' ')[0];
    document.getElementById('headerUserName').textContent = firstName;
    if (document.getElementById('welcomeName')) {
      document.getElementById('welcomeName').textContent = firstName;
    }
    const initial = name.charAt(0).toUpperCase();
    document.getElementById('headerAvatar').textContent = initial;
    document.getElementById('profileAvatar').textContent = initial;

    updateLiveSummary();

    // Persist profile
    try {
      const existing = JSON.parse(localStorage.getItem('soba_user') || '{}');
      localStorage.setItem('soba_user', JSON.stringify({ ...existing, name, email, phone, address, city, postal }));
    } catch(err) {}

    showToast('Dashboard details saved successfully');
    return false;
  }

  // ========================================================
  // USER WISHLIST DYNAMIC LOGIC
  // ========================================================
  function getStoredWishlist() {
    try {
      const raw = localStorage.getItem('soba_wishlist');
      if (raw) return JSON.parse(raw);
    } catch(e) {}
    // Initial sample products matching mockup if none exist yet
    return [
      { id: 101, name: "Handcrafted Coconut Bowl", price: 890, icon: "🥥", image: "" },
      { id: 102, name: "Charcoal Bamboo Toothbrush (4-Pack)", price: 1200, icon: "🪥", image: "" }
    ];
  }

  function renderUserWishlist() {
    const list = getStoredWishlist();
    const badge = document.getElementById('dashWishlistCount');
    if (badge) badge.textContent = list.length;

    const grid = document.getElementById('wishlistGrid');
    if (!grid) return;

    if (list.length === 0) {
      grid.innerHTML = `
        <div style="grid-column: 1 / -1; text-align: center; padding: 48px 20px; background: rgba(255,255,255,0.02); border: 1px dashed var(--line); border-radius: 16px;">
          <div style="font-size: 40px; margin-bottom: 12px;">💚</div>
          <h3 style="font-size: 18px; color: #fff; margin-bottom: 6px;">Your Wishlist is Empty</h3>
          <p style="font-size: 13.5px; color: rgba(244,242,233,0.6); margin-bottom: 18px;">Bookmark sustainable items from the product details page for future zero-waste purchases.</p>
          <a href="products.php" class="btn-sm-action" style="display: inline-block; border-color: var(--neo); color: var(--neo); padding: 8px 18px;">Browse Products</a>
        </div>
      `;
      return;
    }

    grid.innerHTML = list.map((item, idx) => {
      const imgOrIcon = item.image 
        ? `<img src="${item.image}" alt="${item.name}" style="width:64px;height:64px;object-fit:cover;border-radius:12px;margin:0 auto 10px;display:block;" onerror="this.style.display='none';this.nextElementSibling.style.display='block';"><span style="display:none;font-size:38px;margin-bottom:8px;">${item.icon || '🌱'}</span>`
        : `<div style="font-size: 38px; margin-bottom: 8px;">${item.icon || '🌱'}</div>`;

      const viewUrl = item.id > 100 ? 'products.php' : `product_details.php?id=${item.id}`;

      return `
        <div style="background: rgba(255,255,255,0.03); border:1px solid var(--line); border-radius: 14px; padding: 18px 16px; text-align: center; position: relative; display: flex; flex-direction: column; justify-content: space-between;">
          <button type="button" onclick="removeFromWishlist(${idx})" title="Remove from wishlist" style="position: absolute; top: 10px; right: 10px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.14); border-radius: 50%; width: 26px; height: 26px; color: rgba(255,255,255,0.6); font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center; line-height: 1; transition: all 0.2s;" onmouseover="this.style.color='#ff6b81'; this.style.borderColor='#ff6b81';" onmouseout="this.style.color='rgba(255,255,255,0.6)'; this.style.borderColor='rgba(255,255,255,0.14)';">&times;</button>
          
          <div>
            ${imgOrIcon}
            <a href="${viewUrl}" style="color:#fff; font-size:14px; font-weight: 600; margin-bottom:4px; display:block; text-decoration:none; line-height: 1.3;" onmouseover="this.style.color='var(--neo)'" onmouseout="this.style.color='#fff'">${item.name}</a>
            <div class="mono" style="color:var(--neo); font-weight:700; margin-top: 6px; margin-bottom: 14px; font-size: 15px;">Rs. ${Number(item.price).toLocaleString()}</div>
          </div>

          <div>
            <button type="button" class="btn-sm-action" style="width: 100%; text-align:center; border-color:var(--neo); color:var(--neo); padding: 8px 12px; font-weight: 600; cursor:pointer;" onclick="moveWishlistItemToCart(${idx})">Move to Cart</button>
          </div>
        </div>
      `;
    }).join('');
  }

  function moveWishlistItemToCart(index) {
    const list = getStoredWishlist();
    if (index < 0 || index >= list.length) return;
    const item = list[index];

    try {
      let cart = [];
      const rawCart = localStorage.getItem('soba_cart');
      if (rawCart) cart = JSON.parse(rawCart);

      const existing = cart.find(c => Number(c.id) === Number(item.id));
      if (existing) {
        existing.quantity = (existing.quantity || 1) + 1;
        existing.selected = true;
      } else {
        cart.push({
          id: item.id,
          name: item.name,
          price: Number(item.price) || 0,
          quantity: 1,
          selected: true,
          icon: item.icon || '🌱',
          image: item.image || ''
        });
      }

      localStorage.setItem('soba_cart', JSON.stringify(cart));

      // Update header cart count
      const totalCount = cart.reduce((acc, it) => acc + (it.quantity || 1), 0);
      const cartCountEl = document.getElementById('cartCount');
      if (cartCountEl) cartCountEl.textContent = totalCount;

      // Remove from wishlist
      list.splice(index, 1);
      localStorage.setItem('soba_wishlist', JSON.stringify(list));
      renderUserWishlist();

      showToast(`"${item.name}" moved to your Cart! 🛒`);
    } catch(err) {
      console.error(err);
      showToast('Error moving item to cart');
    }
  }

  function removeFromWishlist(index) {
    const list = getStoredWishlist();
    if (index < 0 || index >= list.length) return;
    const removed = list.splice(index, 1)[0];
    localStorage.setItem('soba_wishlist', JSON.stringify(list));
    renderUserWishlist();
    showToast(`"${removed.name}" removed from Wishlist.`);
  }

  function verifyAccount() {
    const badge = document.getElementById('vBadge');
    badge.classList.remove('unverified');
    badge.innerHTML = '<svg class="icon icon-sm" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg><span>Account Verified</span>';
    showToast('Verification code sent — account verified successfully');
  }

  function togglePassword() {
    const pw = document.getElementById('dashPassword');
    const icon = document.getElementById('pwIcon');
    const btn = document.getElementById('pwToggle');
    if (pw.type === 'password') {
      pw.type = 'text';
      btn.setAttribute('aria-label', 'Hide password');
      icon.innerHTML = '<path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a21.62 21.62 0 0 1 5.06-6.06M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a21.6 21.6 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
    } else {
      pw.type = 'password';
      btn.setAttribute('aria-label', 'Show password');
      icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
    }
  }

  // Log Out Functionality
  function handleLogout() {
    localStorage.removeItem('soba_user');
    sessionStorage.removeItem('soba_user');
    window.location.href = '/php/web/Soba-Project/backend/api/logout.php';
  }
</script>
<script src="../../assets/js/main.js"></script>
</body>
</html>