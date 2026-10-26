<?php
// frontend/modules/customer/register.php
require_once __DIR__ . '/../../../backend/includes/session_handler.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$sessionUser = isset($_SESSION['u_id'])
    ? ['name' => $_SESSION['name'] ?? '', 'email' => $_SESSION['email'] ?? '', 'role' => $_SESSION['role'] ?? 'customer']
    : null;

$redirectParam = isset($_GET['redirect']) ? trim($_GET['redirect']) : '';
$autoCheckoutParam = isset($_GET['auto_checkout']) ? trim($_GET['auto_checkout']) : '';

// Build login URL preserving redirect
$loginUrl = "../../login.php";
$loginParams = [];
if (!empty($redirectParam)) {
    $r = (strpos($redirectParam, 'modules/customer/') === false && $redirectParam === 'cart.php')
        ? 'modules/customer/' . $redirectParam 
        : $redirectParam;
    $loginParams[] = 'redirect=' . urlencode($r);
}
if (!empty($autoCheckoutParam)) {
    $loginParams[] = 'auto_checkout=1';
}
if (!empty($loginParams)) {
    $loginUrl .= '?' . implode('&', $loginParams);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register — Soba</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..700;1,9..144,400..600&family=Work+Sans:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../../assets/css/style.css">
<script src="https://accounts.google.com/gsi/client" async defer></script>
<style>
  :root{
    --neo:#B8FF3D;
    --forest:#3B7A34;
    --deep:#0b2b20;
    --deep2:#123d2c;
    --ink:#0c1f14;
    --paper:#F9F8F3;
    --logo-white:#F4F8F1;
  }
  *{box-sizing:border-box;}
  html{scroll-behavior:smooth;}
  body{
    margin:0; min-height:100vh; font-family:'Work Sans', sans-serif;
    color:#F4F2E9; background:var(--deep);
    -webkit-font-smoothing:antialiased;
    display:flex; flex-direction:column;
  }
  h1,h2,h3{font-family:'Fraunces', serif; font-weight:500; letter-spacing:-0.01em; margin:0;}

  /* Ambient cinematic background */
  .bg-scene{
    position:fixed; inset:0; z-index:0; overflow:hidden;
    background:
      radial-gradient(1200px 700px at 80% -10%, rgba(184,255,61,.12), transparent 55%),
      radial-gradient(900px 600px at 10% 90%, rgba(184,255,61,.09), transparent 55%),
      linear-gradient(180deg, #071a12 0%, #0b2b20 55%, #081e16 100%);
  }
  .bg-noise{
    position:fixed; inset:0; z-index:0; pointer-events:none; opacity:.05; mix-blend-mode:overlay;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='140' height='140'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
  }
  .bg-glow{
    position:fixed; z-index:0; pointer-events:none; border-radius:50%;
    filter:blur(90px); opacity:.35;
  }
  .bg-glow.g1{width:420px;height:420px; background:var(--neo); top:-140px; right:-140px;}
  .bg-glow.g2{width:380px;height:380px; background:#3B7A34; bottom:-120px; left:-100px;}

  /* Top nav */
  header{position:relative; z-index:10; padding:22px 5vw;}
  .bar{
    max-width:1300px; margin:0 auto;
    display:flex; align-items:center; justify-content:space-between;
  }
  .logo{
    display:inline-flex; align-items:baseline; gap:12px; cursor:pointer; text-decoration:none;
  }
  .logo .mark{
    font-family:'Fraunces', serif; font-weight:700; font-size:32px; color:var(--logo-white);
    letter-spacing:-0.02em; line-height:1;
  }
  .logo .sub{
    font-size:12px; letter-spacing:0.18em; text-transform:uppercase; color:var(--neo); font-weight:700;
  }

  nav{display:flex; gap:30px; align-items:center;}
  nav a{
    color:rgba(244,242,233,.78); text-decoration:none; font-size:14px; font-weight:500;
    border-bottom:2px solid transparent; padding-bottom:3px; transition:all .2s;
  }
  nav a:hover{color:#fff; border-color:var(--neo);}

  .nav-toggle{display:none; background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.16); border-radius:99px; width:38px; height:38px; color:#fff; font-size:16px; cursor:pointer;}

  /* Main container */
  main{
    position:relative; z-index:5; flex:1;
    display:flex; align-items:center; justify-content:center;
    padding:40px 20px 70px;
  }
  .auth-card{
    position:relative; width:420px; max-width:100%;
    background:linear-gradient(160deg, rgba(18,61,44,.62), rgba(8,26,19,.78));
    border:1px solid rgba(184,255,61,.18);
    border-radius:24px; padding:42px 34px 32px;
    box-shadow:0 30px 90px rgba(0,0,0,.5), inset 0 1px 0 rgba(255,255,255,.06);
    backdrop-filter:blur(22px); -webkit-backdrop-filter:blur(22px);
  }
  .auth-head{text-align:center; margin-bottom:28px;}
  .auth-head h1{font-size:32px; color:#fff;}
  .auth-head p{font-size:13.5px; color:rgba(244,242,233,.6); margin-top:8px; line-height:1.55;}

  #google-signin-btn{display:flex; justify-content:center;}
  .google-btn{
    width:100%; display:flex; align-items:center; justify-content:center; gap:12px;
    background:rgba(255,255,255,.96);
    border:1.5px solid rgba(255,255,255,.3);
    border-radius:99px;
    padding:13px 20px; font-size:14.5px; font-weight:600; color:#1a1a1a; cursor:pointer;
    font-family:'Work Sans', sans-serif;
    box-shadow:0 4px 18px rgba(0,0,0,.22), 0 0 0 0 rgba(184,255,61,0);
    transition:transform .18s ease, box-shadow .18s ease, background .18s ease;
    letter-spacing:0.01em;
  }
  .google-btn:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 28px rgba(0,0,0,.32), 0 0 0 3px rgba(184,255,61,.22);
    background:#ffffff;
  }
  .google-btn svg{ flex-shrink:0; }

  .divider{
    display:flex; align-items:center; gap:12px; margin:22px 0;
    font-size:11px; color:rgba(244,242,233,.42); text-transform:uppercase; letter-spacing:0.1em;
  }
  .divider::before, .divider::after{content:""; flex:1; height:1px; background:rgba(255,255,255,.14);}

  .field-label{font-size:12px; font-weight:600; color:rgba(244,242,233,.75); margin-bottom:7px; display:block;}
  .field-wrap{position:relative; margin-bottom:18px;}
  .field-input{
    width:100%; border:none; border-bottom:1.5px solid rgba(255,255,255,.24);
    padding:10px 30px 10px 2px; background:transparent; color:#fff;
    font-size:14.5px; font-family:'Work Sans', sans-serif; transition:border-color .2s ease;
  }
  .field-input::placeholder{color:rgba(244,242,233,.4);}
  .field-input:focus{outline:none; border-color:var(--neo);}
  .field-icon{position:absolute; right:2px; top:38px; font-size:15px; opacity:.6; pointer-events:none;}

  /* Password strength bar */
  .strength-bar{height:3px; border-radius:99px; margin-top:8px; background:rgba(255,255,255,.1); overflow:hidden;}
  .strength-fill{height:100%; border-radius:99px; width:0%; transition:width .3s ease, background .3s ease;}

  /* Terms checkbox */
  .terms-row{display:flex; align-items:flex-start; gap:10px; margin:4px 0 22px; font-size:12.5px; color:rgba(244,242,233,.65);}
  .terms-row input{accent-color:var(--neo); width:15px; height:15px; flex-shrink:0; margin-top:2px; cursor:pointer;}
  .terms-row a{color:var(--neo); text-decoration:none;}
  .terms-row a:hover{text-decoration:underline;}

  .submit-btn{
    width:100%; background:linear-gradient(135deg, var(--neo), #8fe04f); color:var(--ink); border:none; border-radius:11px;
    padding:14px; font-size:15px; font-weight:700; cursor:pointer; font-family:'Work Sans', sans-serif;
    box-shadow:0 16px 34px rgba(184,255,61,.24); transition:transform .15s ease, box-shadow .15s ease;
    margin-top:8px;
  }
  .submit-btn:hover{transform:translateY(-1px); box-shadow:0 20px 40px rgba(184,255,61,.34);}
  .submit-btn:disabled{opacity:.55; cursor:not-allowed; transform:none;}

  .auth-foot{text-align:center; margin-top:20px; font-size:13px; color:rgba(244,242,233,.55);}
  .auth-link{color:var(--neo); text-decoration:none; font-weight:600;}
  .auth-link:hover{text-decoration:underline;}
  .fine-print{text-align:center; margin-top:14px; font-size:11.5px; color:rgba(244,242,233,.4); line-height:1.5;}

  /* Eco perks strip */
  .perks{
    display:flex; gap:12px; margin-bottom:26px; justify-content:center;
  }
  .perk{
    display:flex; flex-direction:column; align-items:center; gap:5px;
    font-size:11px; color:rgba(244,242,233,.65); text-align:center;
    background:rgba(184,255,61,.06); border:1px solid rgba(184,255,61,.12);
    border-radius:12px; padding:10px 12px; flex:1;
  }
  .perk-ico{font-size:18px;}

  .toast{
    position:fixed; top:24px; left:50%; transform:translateX(-50%) translateY(-20px);
    background:rgba(184,255,61,.14); border:1px solid rgba(184,255,61,.4); color:#e4ffc7;
    padding:12px 22px; border-radius:99px; font-size:13.5px; z-index:50;
    opacity:0; pointer-events:none; transition:opacity .3s ease, transform .3s ease;
    white-space:nowrap;
  }
  .toast.show{opacity:1; transform:translateX(-50%) translateY(0);}

  @media (max-width:820px){
    nav{display:none; position:absolute; top:100%; left:5vw; right:5vw; margin-top:12px;
      background:rgba(8,26,19,.96); border:1px solid rgba(184,255,61,.16); border-radius:16px;
      flex-direction:column; align-items:flex-start; gap:0; padding:8px; backdrop-filter:blur(20px);}
    nav.open{display:flex;}
    nav a{width:100%; padding:12px 10px; border-bottom:1px solid rgba(255,255,255,.06);}
    .nav-toggle{display:flex; align-items:center; justify-content:center;}
    header .bar{position:relative;}
    .perks{gap:8px;}
    .perk{padding:8px;}
  }
  @media (max-width:480px){
    .auth-card{padding:32px 22px 26px; border-radius:18px;}
    .auth-head h1{font-size:27px;}
  }

  /* Google Modal */
  .google-modal-overlay{
    position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.68);
    backdrop-filter:blur(6px); display:flex; align-items:center; justify-content:center; padding:18px;
  }
  .google-modal-card{
    background:#ffffff; color:#1f1f1f; width:440px; max-width:100%; border-radius:20px;
    box-shadow:0 24px 60px rgba(0,0,0,0.45); padding:26px 24px; position:relative;
    animation:fadeUp 0.25s ease;
  }
  .google-modal-head{
    display:flex; align-items:flex-start; gap:14px; border-bottom:1px solid #e5e7eb; padding-bottom:16px; position:relative;
  }
  .google-modal-head h3{font-family:'Work Sans',sans-serif; font-size:17px; font-weight:700; color:#111; margin:0 0 2px;}
  .google-modal-head p{font-size:12.5px; color:#5f6368; margin:0;}
  .google-modal-close{
    position:absolute; right:-4px; top:-6px; background:none; border:none; font-size:26px; color:#5f6368; cursor:pointer; line-height:1;
  }
  .google-modal-close:hover{color:#111;}
  .google-acc-list{padding-top:16px;}
  .google-acc-item{
    display:flex; align-items:center; gap:14px; padding:12px 14px; border-radius:12px; border:1px solid #e5e7eb;
    cursor:pointer; transition:all 0.15s ease; background:#f9fafb; margin-bottom:14px;
  }
  .google-acc-item:hover{background:#f3f4f6; border-color:#d1d5db; transform:translateY(-1px); box-shadow:0 4px 12px rgba(0,0,0,0.06);}
  .google-acc-avatar{
    width:38px; height:38px; border-radius:50%; background:#10b981; color:#fff; font-weight:700;
    display:flex; align-items:center; justify-content:center; font-size:16px;
  }
  .google-acc-info{flex:1; min-width:0;}
  .google-acc-info .acc-name{font-size:13.5px; font-weight:600; color:#1f2937;}
  .google-acc-info .acc-email{font-size:12px; color:#6b7280; overflow:hidden; text-overflow:ellipsis;}
  .acc-badge{font-size:10px; background:#e0f2fe; color:#0369a1; padding:3px 8px; border-radius:99px; font-weight:600;}
  .google-acc-divider{
    display:flex; align-items:center; gap:10px; margin:16px 0 14px; font-size:11px; color:#9ca3af; text-transform:uppercase; letter-spacing:0.05em;
  }
  .google-acc-divider::before, .google-acc-divider::after{content:""; flex:1; height:1px; background:#e5e7eb;}
  .google-custom-form{display:flex; flex-direction:column; gap:10px;}
  .g-input{
    width:100%; border:1px solid #d1d5db; border-radius:9px; padding:10px 12px; font-size:13px; color:#111;
    font-family:'Work Sans',sans-serif; background:#fff;
  }
  .g-input:focus{outline:none; border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,0.15);}
  .g-submit-btn{
    background:#1a73e8; color:#fff; border:none; border-radius:9px; padding:11px; font-size:13px; font-weight:600;
    cursor:pointer; font-family:'Work Sans',sans-serif; transition:background 0.15s;
  }
  .g-submit-btn:hover{background:#1557b0;}
</style>
</head>
<body>

<div class="bg-scene"></div>
<div class="bg-noise"></div>
<div class="bg-glow g1"></div>
<div class="bg-glow g2"></div>

<div class="toast" id="toast"></div>

<header>
  <div class="bar">
    <a class="logo" href="../../index.php">
      <span class="mark">Soba</span>
      <span class="sub">sobadahama · nature</span>
    </a>
    <nav id="mainNav">
      <a href="../../index.php#home-top">Home</a>
      <a href="../../index.php#shop">Shop</a>
      <a href="../../index.php#about-section">About</a>
      <a href="../../index.php#contact-section">Contact</a>
    </nav>
    <button class="nav-toggle" id="navToggle" onclick="document.getElementById('mainNav').classList.toggle('open')" aria-label="Menu">☰</button>
  </div>
</header>

<main>
  <div class="auth-card">
    <div class="auth-head">
      <h1>Create account</h1>
      <p>Join Soba to track every gram of plastic you help avoid.</p>
    </div>

    <!-- Eco perks -->
    <div class="perks">
      <div class="perk"><span class="perk-ico">🌱</span>Eco tracker</div>
      <div class="perk"><span class="perk-ico">📦</span>Order history</div>
      <div class="perk"><span class="perk-ico">🏅</span>Eco badges</div>
    </div>

    <div id="google-signin-btn"></div>
    <button class="google-btn" id="google-fallback-btn" onclick="signInWithGoogle()" style="display:flex;">
      <svg width="20" height="20" viewBox="0 0 24 24">
        <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.66v3.05h3.9c2.28-2.1 3.64-5.18 3.64-9.15z"/>
        <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.9-3.05c-1.08.72-2.45 1.16-4.03 1.16-3.1 0-5.72-2.1-6.66-4.92H1.3v3.15C3.29 21.36 7.37 24 12 24z"/>
        <path fill="#FBBC05" d="M5.34 14.28c-.24-.72-.38-1.49-.38-2.28s.14-1.56.38-2.28V6.57H1.3C.47 8.23 0 10.06 0 12s.47 3.77 1.3 5.43l4.04-3.15z"/>
        <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.37 0 3.29 2.64 1.3 6.57l4.04 3.15c.94-2.82 3.56-4.97 6.66-4.97z"/>
      </svg>
      Continue with Google
    </button>

    <div class="divider">or with email</div>

    <form id="authForm" onsubmit="return handleSubmit(event)">
      <div class="field-wrap">
        <label class="field-label">Full name</label>
        <input class="field-input" id="auth-name" type="text" placeholder="Your name" required>
        <span class="field-icon">👤</span>
      </div>
      <div class="field-wrap">
        <label class="field-label">Email</label>
        <input class="field-input" id="auth-email" type="email" placeholder="you@example.com" required>
        <span class="field-icon">✉️</span>
      </div>
      <div class="field-wrap">
        <label class="field-label">Password</label>
        <input class="field-input" id="auth-password" type="password" placeholder="Min 6 characters" required oninput="updateStrength(this.value)">
        <span class="field-icon">🔒</span>
        <div class="strength-bar"><div class="strength-fill" id="strength-fill"></div></div>
      </div>

      <div class="terms-row">
        <input type="checkbox" id="auth-terms" required>
        <label for="auth-terms">I agree to the <a href="#">Terms</a> and <a href="#">Privacy Policy</a> of this prototype.</label>
      </div>

      <button type="submit" class="submit-btn" id="submit-btn">Create account</button>
    </form>

    <div class="auth-foot">
      Already have an account? <a href="<?= htmlspecialchars($loginUrl) ?>" class="auth-link">Login</a>
    </div>
    <div class="fine-print">This is a course prototype — no real account or password is stored anywhere.</div>
  </div>

  <!-- Google Account Selector Modal -->
  <div id="google-modal" class="google-modal-overlay" style="display:none;" onclick="if(event.target===this)closeGoogleModal()">
    <div class="google-modal-card">
      <div class="google-modal-head">
        <svg width="28" height="28" viewBox="0 0 24 24">
          <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.66v3.05h3.9c2.28-2.1 3.64-5.18 3.64-9.15z"/>
          <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.9-3.05c-1.08.72-2.45 1.16-4.03 1.16-3.1 0-5.72-2.1-6.66-4.92H1.3v3.15C3.29 21.36 7.37 24 12 24z"/>
          <path fill="#FBBC05" d="M5.34 14.28c-.24-.72-.38-1.49-.38-2.28s.14-1.56.38-2.28V6.57H1.3C.47 8.23 0 10.06 0 12s.47 3.77 1.3 5.43l4.04-3.15z"/>
          <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.37 0 3.29 2.64 1.3 6.57l4.04 3.15c.94-2.82 3.56-4.97 6.66-4.97z"/>
        </svg>
        <div>
          <h3>Continue with Google</h3>
          <p>Choose an account to continue to <b>Soba Market</b></p>
        </div>
        <button class="google-modal-close" onclick="closeGoogleModal()" type="button" aria-label="Close">&times;</button>
      </div>
      <div class="google-acc-list">
        <div class="google-acc-item" onclick="submitGoogleAuth('thisaruranathunga06@gmail.com', 'Thisaru Ranathunga')">
          <div class="google-acc-avatar">T</div>
          <div class="google-acc-info">
            <div class="acc-name">Thisaru Ranathunga</div>
            <div class="acc-email">thisaruranathunga06@gmail.com</div>
          </div>
          <span class="acc-badge">Account</span>
        </div>
        <div class="google-acc-divider"><span>or register with another Google account</span></div>
        <form onsubmit="return handleCustomGoogleSubmit(event)" class="google-custom-form">
          <input type="email" id="g-custom-email" placeholder="Google email (e.g. name@gmail.com)" required class="g-input">
          <input type="text" id="g-custom-name" placeholder="Full name (optional)" class="g-input">
          <button type="submit" class="g-submit-btn">Continue with this Google account</button>
        </form>
      </div>
    </div>
  </div>
</main>

<script>
  function showToast(msg){
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.classList.add('show');
    clearTimeout(window._toastTimer);
    window._toastTimer = setTimeout(() => t.classList.remove('show'), 2600);
  }

  // register_auth.php එකට යන path එක (frontend/modules/customer/register.php සිට)
  const API_REGISTER = '../../../backend/api/register_auth.php';
  // Register වුණාට පස්සේ යන්නේ login page එකට
  const LOGIN_URL = '../../login.php';
  // දැනටමත් login වෙලා ඉන්න කෙනෙක් යන්න ඕනේ page (frontend/home.php)
  const HOME_URL = '../../home.php';
  const ADMIN_URL = '../admin/dashboard.php';

  const urlParams = new URLSearchParams(window.location.search);
  let REDIRECT_URL = urlParams.get('redirect') || <?= json_encode($redirectParam) ?> || '';
  const AUTO_CHECKOUT = urlParams.get('auto_checkout') || <?= json_encode($autoCheckoutParam) ?> || '';

  function destinationFor(role){
    if (role === 'admin') return ADMIN_URL;
    if (REDIRECT_URL) {
      if (!REDIRECT_URL.startsWith('http://') && !REDIRECT_URL.startsWith('https://') && !REDIRECT_URL.startsWith('//')) {
        let dest = REDIRECT_URL;
        if (AUTO_CHECKOUT === '1' && !dest.includes('auto_checkout=')) {
          dest += (dest.includes('?') ? '&' : '?') + 'auto_checkout=1';
        }
        return dest;
      }
    }
    return HOME_URL;
  }

  function updateStrength(val){
    const fill = document.getElementById('strength-fill');
    const len = val.length;
    let width = 0, color = '#ef4444';
    if(len >= 6){ width = 33; color = '#f97316'; }
    if(len >= 8 && /[A-Z]/.test(val)){ width = 66; color = '#eab308'; }
    if(len >= 10 && /[A-Z]/.test(val) && /[0-9!@#$%^&*]/.test(val)){ width = 100; color = 'var(--neo)'; }
    fill.style.width = width + '%';
    fill.style.background = color;
  }

  function completeRegister(name){
    showToast(`Account created${name ? ', ' + name : ''}! Please login…`);
    const loginTarget = <?= json_encode($loginUrl) ?>;
    setTimeout(() => { window.location.href = loginTarget; }, 1300);
  }

  async function handleSubmit(e){
    e.preventDefault();
    const name = document.getElementById('auth-name').value.trim();
    const email = document.getElementById('auth-email').value.trim();
    const password = document.getElementById('auth-password').value;
    const terms = document.getElementById('auth-terms').checked;

    if(!name || !email || !password){ showToast('Please fill in all required fields.'); return false; }
    if(password.length < 6){ showToast('Password must be at least 6 characters.'); return false; }
    if(!terms){ showToast('Please agree to the terms to continue.'); return false; }

    const btn = document.getElementById('submit-btn');
    btn.disabled = true;

    try{
      const fd = new FormData();
      fd.append('name', name);
      fd.append('email', email);
      fd.append('password', password);

      const res = await fetch(API_REGISTER, { method:'POST', body: fd, credentials:'same-origin' });
      const text = await res.text();
      let data;
      try{
        data = JSON.parse(text);
      }catch(parseErr){
        // Server එකෙන් JSON නෙවෙයි වෙන දෙයක් ආවා (PHP error / 404 page)
        console.error('Response from ' + API_REGISTER + ' (HTTP ' + res.status + '):', text);
        showToast('Server error (HTTP ' + res.status + '). Press F12 → Console.');
        btn.disabled = false;
        return false;
      }

      if(data.success){
        completeRegister(data.user.name);
      }else{
        showToast(data.message || 'Registration failed.');
        btn.disabled = false;
      }
    }catch(err){
      showToast('Server error. Is Apache/MySQL running?');
      btn.disabled = false;
    }
    return false;
  }

  const API_GOOGLE = '../../../backend/api/google_auth.php';

  function signInWithGoogle(){
    const modal = document.getElementById('google-modal');
    if (modal) modal.style.display = 'flex';
  }

  function closeGoogleModal(){
    const modal = document.getElementById('google-modal');
    if (modal) modal.style.display = 'none';
  }

  async function submitGoogleAuth(email, name, credential = null){
    showToast('Connecting with Google…');
    closeGoogleModal();
    try {
      const payload = credential ? { credential } : { email, name };
      const res = await fetch(API_GOOGLE, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
        credentials: 'same-origin'
      });
      const data = await res.json();
      if (data.success && data.user) {
        try {
          localStorage.setItem('soba_user', JSON.stringify({
            id: data.user.id,
            name: data.user.name,
            email: data.user.email,
            role: data.user.role
          }));
        } catch(e) {}
        showToast(`Welcome ${data.user.name} — redirecting…`);
        setTimeout(() => {
          window.location.href = destinationFor(data.user.role);
        }, 700);
      } else {
        showToast(data.message || 'Google registration failed.');
      }
    } catch(err) {
      showToast('Network error during Google sign-up.');
    }
  }

  function handleCustomGoogleSubmit(e){
    e.preventDefault();
    const email = document.getElementById('g-custom-email').value.trim();
    const name = document.getElementById('g-custom-name').value.trim();
    if (!email) return false;
    submitGoogleAuth(email, name);
    return false;
  }

  function handleGoogleCredential(response){
    if (response && response.credential) {
      submitGoogleAuth(null, null, response.credential);
    }
  }

  function initGoogleSignIn(){
    const GOOGLE_CLIENT_ID = "YOUR_GOOGLE_CLIENT_ID.apps.googleusercontent.com";
    const target = document.getElementById('google-signin-btn');
    const fallback = document.getElementById('google-fallback-btn');
    if(GOOGLE_CLIENT_ID.startsWith('YOUR_') || !window.google || !window.google.accounts){
      fallback.style.display = 'flex';
      return;
    }
    try{
      google.accounts.id.initialize({ client_id: GOOGLE_CLIENT_ID, callback: handleGoogleCredential });
      google.accounts.id.renderButton(target, { theme:'filled_white', size:'large', width:352, shape:'pill', text:'signup_with' });
    }catch(e){
      fallback.style.display = 'flex';
    }
  }
  window.addEventListener('load', () => setTimeout(initGoogleSignIn, 300));

  
  const SESSION_USER = <?= json_encode($sessionUser, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  try{
    if(SESSION_USER){
      localStorage.setItem('soba_user', JSON.stringify(SESSION_USER));
      window.location.replace(destinationFor(SESSION_USER.role));
    }else{
      localStorage.removeItem('soba_user');
    }
  }catch(e){}
</script>
<script src="../../assets/js/main.js"></script>
</body>
</html>