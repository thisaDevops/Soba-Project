<?php
require_once __DIR__ . '/../backend/includes/session_handler.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }
// Preserve the authenticated session so the account name is shown after sign-in.
$sessionUser = null;
if (!empty($_SESSION['u_id'])) {
    $sessionUser = [
        'id' => $_SESSION['u_id'],
        'name' => $_SESSION['name'] ?? $_SESSION['username'] ?? 'Account',
        'email' => $_SESSION['email'] ?? '',
        'role' => $_SESSION['role'] ?? 'customer'
    ];
}

// Fetch live products directly from database with images
$dbProducts = [];
try {
    require_once __DIR__ . '/../backend/config/db_connection.php';
    if (isset($conn) && $conn instanceof mysqli) {
        $pSql = "SELECT p.p_id as id, p.name, p.sku, p.material, p.price, p.original_price as originalPrice, 
                        p.stock_qty, p.plastic_saved_g as saved, p.cert_badge as cert, p.icon, 
                        p.description as `desc`, c.name as cat, c.slug as category 
                 FROM products p 
                 LEFT JOIN categories c ON p.cat_id = c.cat_id 
                 WHERE p.is_active = 1 
                 ORDER BY p.p_id ASC";
        $pRes = $conn->query($pSql);
        if ($pRes) {
            while ($row = $pRes->fetch_assoc()) {
                $row['id'] = (int)$row['id'];
                $row['price'] = (float)$row['price'];
                $row['saved'] = (int)$row['saved'];
                $row['stock_qty'] = (int)$row['stock_qty'];
                
                // Fetch associated images from product_images table
                $imgStmt = $conn->prepare("SELECT url FROM product_images WHERE p_id = ? ORDER BY sort_order ASC");
                $imgStmt->bind_param("i", $row['id']);
                $imgStmt->execute();
                $imgRes = $imgStmt->get_result();
                $imgs = [];
                while ($im = $imgRes->fetch_assoc()) {
                    if (!empty($im['url'])) { $imgs[] = $im['url']; }
                }
                $row['images'] = $imgs;
                $dbProducts[] = $row;
            }
        }
    }
} catch (Throwable $t) {
    // Graceful fallback to static list if database is unreachable
    $dbProducts = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Soba — Zero-Waste Marketplace</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..700;1,9..144,400..600&family=Work+Sans:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
<style>
  :root{
    --forest: #3B7A34;      /* Primary — bright, lighter green, logo, header, primary buttons */
    --forest-dim: #2C5D27;
    --forest-bright: #5FAE47;
    --sand: #FFFFFF;        /* Card / container background */
    --sand-deep: #F1F0E9;   /* subtle tint for card art panels */
    --clay: #C86D51;        /* Accent — savings badge, sale tags, cart notification */
    --clay-soft: #E9B7A4;
    --lime: #A8E070;        /* green accent for pops/highlights */
    --sage: #768A6E;        /* Secondary — hover states, filter tags, sub-headers */
    --ink: #2B2D2F;         /* Primary text */
    --paper: #F9F8F3;       /* Main page background */
    --line: #E2DFD7;        /* Border & divider */
  }
  *{box-sizing:border-box;}
  html{scroll-behavior:smooth;}
  body{
    margin:0;
    background:var(--paper);
    color:var(--ink);
    font-family:'Work Sans', sans-serif;
    -webkit-font-smoothing:antialiased;
  }

  /* ===== Grain texture (subtle print-like polish layer) ===== */
  #grain{
    position:fixed; inset:0; z-index:9998; pointer-events:none; opacity:0.05; mix-blend-mode:multiply;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='140' height='140'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
  }

  /* ===== Ambient spotlight disabled ===== */
  #spotlight{ display:none !important; }
  h1,h2,h3,.display{
    font-family:'Fraunces', serif;
    font-weight:500;
    letter-spacing:-0.01em;
    margin:0;
  }
  .mono{font-family:'IBM Plex Mono', monospace;}

  /* subtle woven texture */
  .texture{
    background-image:
      repeating-linear-gradient(45deg, rgba(34,62,48,0.035) 0px, rgba(34,62,48,0.035) 1px, transparent 1px, transparent 10px);
  }

  /* ===== Top bar ===== */
  header{
    position:sticky; top:0; z-index:50;
    background:var(--paper);
    border-bottom:1px solid var(--line);
  }
  .bar{
    max-width:1400px; margin:0 auto;
    display:flex; align-items:center; justify-content:space-between;
    padding:18px 28px;
  }
  .logo{
    display:flex; align-items:baseline; gap:8px; cursor:pointer;
  }
  .logo .mark{
    font-family:'Fraunces', serif; font-weight:600; font-size:24px; color:var(--forest);
  }
  .logo .sub{
    font-size:11px; letter-spacing:0.14em; text-transform:uppercase; color:var(--clay); font-weight:500;
  }
  nav{display:flex; gap:28px;}
  nav a{
    color:var(--ink); text-decoration:none; font-size:14px; font-weight:500; opacity:0.75;
    border-bottom:2px solid transparent; padding-bottom:2px; cursor:pointer; transition: all 0.2s;
  }
  nav a:hover, nav a.active{opacity:1; border-color:var(--clay); color:var(--forest);}
  .bar-right{display:flex; align-items:center; gap:18px;}
  .badge-pill{
    display:flex; align-items:center; gap:8px;
    background:var(--forest); color:var(--sand);
    border-radius:999px; padding:6px 14px 6px 8px;
    font-size:12px;
  }
  .badge-pill .leaf{font-size:14px;}
  .cart-btn{
    position:relative;
    background:var(--ink); color:var(--paper);
    border:none; border-radius:999px;
    padding:10px 18px; font-size:14px; font-weight:500;
    cursor:pointer; display:flex; align-items:center; gap:8px;
    font-family:'Work Sans', sans-serif;
  }
  .cart-count{
    background:var(--clay); color:white; border-radius:999px;
    font-size:11px; min-width:18px; height:18px; display:flex; align-items:center; justify-content:center;
    padding:0 5px; font-family:'IBM Plex Mono', monospace;
  }

  /* ===== Hero: the signature jar ===== */
  .hero{
    max-width:1400px; margin:0 auto;
    padding:56px 28px 64px;
    display:grid; grid-template-columns:1.15fr 0.85fr; gap:56px; align-items:center;
  }
  .hero-eyebrow{
    font-size:12px; letter-spacing:0.16em; text-transform:uppercase; color:var(--forest);
    font-weight:600; margin-bottom:14px; display:block;
  }
  .hero h1{
    font-size:64px; line-height:0.98; color:var(--forest); font-weight:400; letter-spacing:-0.02em;
  }
  .hero h1 em{font-style:italic; color:var(--clay); font-weight:500; position:relative;}
  .hero h1 em::after{
    content:""; position:absolute; left:-2%; right:-2%; bottom:0.02em; height:0.16em;
    background:var(--lime); z-index:-1; opacity:0.55; transform:skewX(-8deg);
  }
  /* rotating stamp badge — editorial flourish */
  .hero-stamp{
    position:absolute; width:118px; height:118px; top:22px; right:6%;
    animation:spinStamp 16s linear infinite; z-index:4;
  }
  @keyframes spinStamp{ to{transform:rotate(360deg);} }
  .hero-stamp svg{width:100%; height:100%;}
  .hero-stamp .core{
    position:absolute; inset:0; display:flex; align-items:center; justify-content:center;
    font-size:22px; animation:spinStamp 16s linear infinite reverse;
  }
  @media (max-width:820px){ .hero-stamp{display:none;} }
  .hero p.lede{
    margin-top:20px; font-size:16.5px; line-height:1.6; color:rgba(43,45,47,0.75); max-width:460px;
  }
  .hero-ctas{margin-top:28px; display:flex; gap:14px;}
  .btn-primary{
    background:var(--clay); color:white; border:none; border-radius:8px;
    padding:13px 24px; font-size:14.5px; font-weight:600; cursor:pointer;
    font-family:'Work Sans', sans-serif;
  }
  .btn-ghost{
    background:transparent; color:var(--forest); border:1px solid var(--line); border-radius:8px;
    padding:13px 24px; font-size:14.5px; font-weight:600; cursor:pointer;
    font-family:'Work Sans', sans-serif;
  }

  /* the jar visual — signature element */
  .jar-wrap{display:flex; flex-direction:column; align-items:center; position:relative; z-index:3;}
  .jar-frame{
    position:relative; width:190px; height:260px;
  }
  .jar-svg{width:100%; height:100%; display:block;}
  .jar-liquid{
    transition:height 0.6s cubic-bezier(.4,1.4,.4,1), y 0.6s cubic-bezier(.4,1.4,.4,1);
  }
  .jar-readout{
    margin-top:18px; text-align:center;
  }
  .jar-readout .num{
    font-size:34px; color:var(--forest); font-weight:500;
  }
  .jar-readout .unit{font-size:16px; opacity:0.6;}
  .jar-readout .label{
    font-size:11.5px; letter-spacing:0.1em; text-transform:uppercase; color:var(--clay);
    margin-top:4px; font-weight:600;
  }

  /* ===== Filters ===== */
  .shop-shell{max-width:1400px; margin:0 auto; padding:20px 28px 100px;}
  .filter-row{
    display:flex; gap:10px; flex-wrap:wrap; margin-bottom:30px;
    border-top:1px solid var(--line); border-bottom:1px solid var(--line);
    padding:16px 0;
  }
  .chip{
    border:1px solid var(--line); background:var(--paper); color:var(--ink);
    border-radius:999px; padding:8px 16px; font-size:13px; cursor:pointer;
    font-family:'Work Sans', sans-serif; font-weight:500;
    transition:all 0.15s;
  }
  .chip.active{background:var(--forest); border-color:var(--forest); color:var(--sand);}
  .chip:hover:not(.active){border-color:var(--forest);}

  .filter-group{display:flex; align-items:center; gap:10px; flex-wrap:wrap;}
  .filter-group-label{font-size:11px; text-transform:uppercase; letter-spacing:0.06em; color:rgba(43,45,47,0.5); font-weight:600; margin-right:2px;}
  .filter-group-chips{display:flex; gap:8px; flex-wrap:wrap;}
  .select-filter{
    border:1px solid var(--line); background:#fff; color:var(--ink); border-radius:8px;
    padding:8px 10px; font-size:12.5px; font-family:'Work Sans', sans-serif; cursor:pointer;
  }
  #filters-extra{display:flex; gap:28px; flex-wrap:wrap; margin-bottom:26px;}

  .section-head{
    display:flex; justify-content:space-between; align-items:baseline; margin-bottom:22px;
  }
  .section-head h2{font-size:26px; color:var(--forest); font-weight:400;}
  .section-head .count{font-size:13px; color:rgba(43,45,47,0.5);}

  /* ===== Product grid ===== */
  .grid{
    display:grid; grid-template-columns:repeat(auto-fill, minmax(240px,1fr)); gap:22px;
  }
  .card{
    background:var(--sand); border:1px solid var(--line); border-radius:14px;
    overflow:hidden; display:flex; flex-direction:column;
    transition:transform 0.18s ease, box-shadow 0.18s ease;
  }
  .card:hover{transform:translateY(-3px); box-shadow:0 12px 28px rgba(34,62,48,0.12);}
  .card-art{
    height:150px; display:flex; align-items:center; justify-content:center;
    font-size:52px; background:var(--sand-deep); position:relative; overflow:hidden;
  }
  .card-art img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease; }
  .card:hover .card-art img { transform: scale(1.06); }
  .card-cert{
    position:absolute; top:10px; left:10px;
    background:var(--forest); color:var(--sand); font-size:10px; font-weight:600;
    padding:4px 8px; border-radius:6px; letter-spacing:0.03em;
  }
  .card-body{padding:16px 16px 18px; display:flex; flex-direction:column; gap:8px; flex:1;}
  .card-body h3{font-size:16px; font-weight:600; font-family:'Work Sans',sans-serif; color:var(--ink);}
  .card-meta{font-size:12px; color:rgba(43,45,47,0.55);}
  .card-saved{
    display:flex; align-items:center; gap:6px; font-size:12px; color:var(--forest); font-weight:600;
  }
  .card-saved .mono{font-size:12px;}
  .card-foot{display:flex; align-items:center; justify-content:space-between; margin-top:auto; padding-top:8px;}
  .price{font-family:'IBM Plex Mono', monospace; font-size:15px; color:var(--ink);}
  .add-btn{
    background:var(--ink); color:var(--paper); border:none; border-radius:7px;
    padding:8px 14px; font-size:12.5px; font-weight:600; cursor:pointer;
    font-family:'Work Sans', sans-serif;
  }
  .add-btn:hover{background:var(--clay);}
  .add-btn.added{background:var(--sage);}

  /* ===== Cart drawer ===== */
  .overlay{
    position:fixed; inset:0; background:rgba(34,62,48,0.35);
    opacity:0; pointer-events:none; transition:opacity 0.25s; z-index:80;
  }
  .overlay.open{opacity:1; pointer-events:auto;}
  .drawer{
    position:fixed; top:0; right:0; height:100%; width:400px; max-width:92vw;
    background:var(--paper); z-index:90;
    transform:translateX(100%); transition:transform 0.3s cubic-bezier(.4,0,.2,1);
    display:flex; flex-direction:column; box-shadow:-10px 0 40px rgba(0,0,0,0.15);
  }
  .drawer.open{transform:translateX(0);}
  .drawer-head{
    padding:22px 24px; border-bottom:1px solid var(--line);
    display:flex; justify-content:space-between; align-items:center;
  }
  .drawer-head h2{font-size:20px; color:var(--forest);}
  .drawer-close{
    background:none; border:none; font-size:20px; cursor:pointer; color:var(--ink); opacity:0.6;
  }
  .drawer-body{flex:1; overflow-y:auto; padding:18px 24px;}
  .drawer-empty{
    text-align:center; padding:60px 20px; color:rgba(43,45,47,0.5); font-size:14px;
  }
  .cart-item{
    display:flex; gap:12px; padding:14px 0; border-bottom:1px solid var(--line);
  }
  .cart-item .art{
    width:52px; height:52px; background:var(--sand-deep); border-radius:8px;
    display:flex; align-items:center; justify-content:center; font-size:24px; flex-shrink:0;
  }
  .cart-item .info{flex:1;}
  .cart-item .info h4{font-size:13.5px; font-weight:600; margin:0 0 4px;}
  .cart-item .info .sub{font-size:11.5px; color:rgba(43,45,47,0.5);}
  .qty-row{display:flex; align-items:center; gap:10px; margin-top:6px;}
  .qty-btn{
    width:22px; height:22px; border:1px solid var(--line); background:white; border-radius:5px;
    cursor:pointer; font-size:13px; line-height:1; display:flex; align-items:center; justify-content:center;
  }
  .qty-num{font-family:'IBM Plex Mono', monospace; font-size:13px; min-width:14px; text-align:center;}
  .remove-x{
    background:none; border:none; color:var(--clay); font-size:11.5px; cursor:pointer;
    margin-left:auto; font-family:'Work Sans', sans-serif; text-decoration:underline;
  }

  .eco-strip{
    margin:16px 0; padding:14px; background:var(--forest); border-radius:10px; color:var(--sand);
  }
  .eco-strip .row{display:flex; justify-content:space-between; align-items:center; font-size:13px;}
  .eco-strip .row + .row{margin-top:6px;}
  .eco-strip .val{font-family:'IBM Plex Mono', monospace; font-weight:500;}

  .drawer-foot{border-top:1px solid var(--line); padding:20px 24px;}
  .total-row{display:flex; justify-content:space-between; font-size:15px; margin-bottom:14px;}
  .total-row .val{font-family:'IBM Plex Mono', monospace; font-weight:600;}
  .checkout-btn{
    width:100%; background:var(--clay); color:white; border:none; border-radius:9px;
    padding:14px; font-size:14.5px; font-weight:600; cursor:pointer;
    font-family:'Work Sans', sans-serif;
  }
  .plant-row{
    display:flex; align-items:center; gap:8px; font-size:12.5px; margin-top:12px; color:rgba(43,45,47,0.7);
  }

  footer{
    border-top:1px solid var(--line); padding:56px 28px 0;
    font-size:12px; color:rgba(43,45,47,0.55); background:var(--sand-deep);
  }
  .footer-inner{
    max-width:1400px; margin:0 auto; padding-bottom:36px;
    display:grid; grid-template-columns:1.6fr 1fr 1fr 1fr; gap:32px;
  }
  .footer-col{display:flex; flex-direction:column; gap:10px;}
  .footer-col a, .footer-col span{
    color:var(--ink); text-decoration:none; font-size:13px; opacity:0.72; cursor:pointer;
    transition:opacity 0.2s ease, transform 0.2s ease;
  }
  .footer-col a:hover{opacity:1; color:var(--forest); transform:translateX(3px);}
  .footer-title{
    font-size:11.5px; text-transform:uppercase; letter-spacing:0.08em; color:var(--forest);
    font-weight:600; margin-bottom:2px;
  }
  .footer-brand p{font-size:13px; line-height:1.6; color:rgba(43,45,47,0.65); margin:14px 0 0; max-width:280px;}
  .footer-bottom{
    border-top:1px solid var(--line); padding:18px 0; text-align:center; max-width:1400px; margin:0 auto;
  }
  @media (max-width:820px){ .footer-inner{grid-template-columns:1fr 1fr;} }
  @media (max-width:480px){ .footer-inner{grid-template-columns:1fr; gap:26px;} footer{padding:44px 20px 0;} }

  @media (max-width:820px){
    .hero{grid-template-columns:1fr; padding-top:32px;}
    .hero h1{font-size:38px;}
    nav{flex-wrap: wrap; gap: 14px;}
  }

  :focus-visible{outline:2px solid var(--clay); outline-offset:2px;}
  @media (prefers-reduced-motion: reduce){
    *{transition:none !important; animation:none !important;}
  }

  /* ===== Page-load / route-transition animation system ===== */
  @keyframes fadeUp{
    from{opacity:0; transform:translateY(22px);}
    to{opacity:1; transform:translateY(0);}
  }
  @keyframes fadeIn{
    from{opacity:0;} to{opacity:1;}
  }
  @keyframes popIn{
    0%{opacity:0; transform:scale(0.92) translateY(10px);}
    60%{opacity:1; transform:scale(1.015) translateY(-1px);}
    100%{opacity:1; transform:scale(1) translateY(0);}
  }
  @keyframes floatSlow{
    0%,100%{transform:translateY(0) rotate(0deg);}
    50%{transform:translateY(-5px) rotate(0.6deg);}
  }
  @keyframes shimmer{
    0%{background-position:-300px 0;}
    100%{background-position:300px 0;}
  }
  @keyframes barPulse{
    0%{transform:scaleX(0); transform-origin:left;}
    55%{transform:scaleX(1); transform-origin:left;}
    55.001%{transform-origin:right;}
    100%{transform:scaleX(0); transform-origin:right;}
  }

  /* top route-progress bar — mimics an app "new page loading" feel */
  #route-bar{
    position:fixed; top:0; left:0; height:3px; width:100%;
    background:linear-gradient(90deg, var(--clay), var(--forest));
    transform:scaleX(0); transform-origin:left;
    z-index:200; opacity:0; pointer-events:none;
    transition:opacity 0.2s;
  }
  #route-bar.loading{opacity:1; animation:barPulse 0.55s ease forwards;}

  /* hero: gentle staggered entrance on first load */
  .hero-eyebrow{opacity:0; animation:fadeUp 0.7s ease 0.05s forwards;}
  .hero h1{opacity:0; animation:fadeUp 0.8s ease 0.15s forwards;}
  .hero p.lede{opacity:0; animation:fadeUp 0.8s ease 0.28s forwards;}
  .hero-ctas{opacity:0; animation:fadeUp 0.8s ease 0.4s forwards;}
  .jar-wrap{opacity:0; animation:fadeUp 0.9s ease 0.35s forwards;}
  .jar-frame{animation:floatSlow 6s ease-in-out infinite;}

  .logo .mark{display:inline-block; transition:transform 0.35s cubic-bezier(.4,1.6,.4,1);}
  .logo:hover .mark{transform:rotate(-4deg) scale(1.05);}

  nav a{position:relative;}
  nav a::after{
    content:""; position:absolute; left:0; bottom:-3px; height:2px; width:100%;
    background:var(--clay); transform:scaleX(0); transform-origin:right;
    transition:transform 0.28s cubic-bezier(.4,0,.2,1);
  }
  nav a:hover::after, nav a.active::after{transform:scaleX(1); transform-origin:left;}

  .btn-primary, .btn-ghost, .add-btn, .checkout-btn, .pay-submit, .email-submit{
    transition:transform 0.18s cubic-bezier(.4,1.6,.4,1), box-shadow 0.18s ease, background 0.18s ease;
  }
  .btn-primary:hover, .checkout-btn:hover, .pay-submit:hover, .email-submit:hover{
    transform:translateY(-2px); box-shadow:0 10px 22px rgba(200,109,81,0.28);
  }
  .btn-ghost:hover{transform:translateY(-2px);}
  .btn-primary:active, .btn-ghost:active, .add-btn:active, .checkout-btn:active{transform:translateY(0) scale(0.97);}

  .add-btn{position:relative; overflow:hidden;}
  .add-btn.added{animation:popIn 0.35s ease;}

  .chip{transition:transform 0.15s ease, background 0.2s ease, border-color 0.2s ease, color 0.2s ease;}
  .chip:active{transform:scale(0.95);}
  .chip.active{animation:popIn 0.3s ease;}

  /* card entrance + hover polish */
  .card{
    opacity:0; transform:translateY(18px) scale(0.98);
    animation:fadeUp 0.55s cubic-bezier(.2,.7,.3,1) forwards;
    animation-delay:calc(var(--i, 0) * 55ms);
  }
  .card:hover{transform:translateY(-6px) scale(1.012); box-shadow:0 18px 34px rgba(34,62,48,0.16);}
  .card-art{overflow:hidden;}
  .card-art span{transition:transform 0.3s cubic-bezier(.4,1.6,.4,1);}
  .card:hover .card-art > :not(.card-cert){transform:scale(1.14) rotate(-4deg);}

  /* scroll-reveal for page sections beyond the fold */
  .reveal{opacity:0; transform:translateY(26px); transition:opacity 0.7s ease, transform 0.7s cubic-bezier(.2,.7,.3,1);}
  .reveal.in-view{opacity:1; transform:translateY(0);}
  .reveal-stagger > *{opacity:0; transform:translateY(22px); transition:opacity 0.6s ease, transform 0.6s cubic-bezier(.2,.7,.3,1);}
  .reveal-stagger.in-view > *{opacity:1; transform:translateY(0);}
  .reveal-stagger.in-view > *:nth-child(1){transition-delay:0.03s;}
  .reveal-stagger.in-view > *:nth-child(2){transition-delay:0.11s;}
  .reveal-stagger.in-view > *:nth-child(3){transition-delay:0.19s;}
  .reveal-stagger.in-view > *:nth-child(4){transition-delay:0.27s;}

  /* section head underline flourish */
  .section-head h2{position:relative; display:inline-block;}

  /* category page-transition: shop shell fades/slides like a route change */
  #shop.route-out{animation:catOut 0.22s ease forwards;}
  #shop.route-in{animation:catIn 0.42s cubic-bezier(.2,.7,.3,1) forwards;}
  @keyframes catOut{ to{opacity:0; transform:translateY(10px) scale(0.995);} }
  @keyframes catIn{ from{opacity:0; transform:translateY(16px) scale(0.99);} to{opacity:1; transform:translateY(0) scale(1);} }

  /* ===== New scroll sections ===== */
  .story-section{max-width:1400px; margin:0 auto; padding:70px 28px;}
  .story-eyebrow{
    font-size:12px; letter-spacing:0.16em; text-transform:uppercase; color:var(--clay);
    font-weight:600; margin-bottom:10px; display:block;
  }
  .story-head h2{font-size:30px; color:var(--forest); font-weight:400; max-width:640px; line-height:1.2;}

  .steps-grid{display:grid; grid-template-columns:repeat(3,1fr); gap:22px; margin-top:38px;}
  .step-card{
    background:var(--sand); border:1px solid var(--line); border-radius:16px; padding:26px 22px;
    transition:transform 0.25s cubic-bezier(.4,1.6,.4,1), box-shadow 0.25s ease;
  }
  .step-card:hover{transform:translateY(-6px); box-shadow:0 16px 30px rgba(34,62,48,0.12);}
  .step-num{
    width:36px; height:36px; border-radius:50%; background:var(--forest); color:var(--sand);
    display:flex; align-items:center; justify-content:center; font-family:'IBM Plex Mono',monospace;
    font-size:14px; margin-bottom:16px;
  }
  .step-card h3{font-size:16.5px; font-weight:600; font-family:'Work Sans',sans-serif; color:var(--ink); margin-bottom:8px;}
  .step-card p{font-size:13.5px; line-height:1.6; color:rgba(43,45,47,0.7); margin:0;}

  .impact-strip{
    background:var(--forest); border-radius:20px; padding:44px 40px; margin-top:0;
    display:grid; grid-template-columns:repeat(4,1fr); gap:20px; color:var(--sand);
  }
  .impact-stat .num{font-family:'IBM Plex Mono',monospace; font-size:32px; font-weight:600;}
  .impact-stat .lbl{font-size:12.5px; opacity:0.75; margin-top:4px;}

  .quote-grid{display:grid; grid-template-columns:repeat(3,1fr); gap:22px; margin-top:38px;}
  .quote-card{
    background:var(--sand-deep); border:1px solid var(--line); border-radius:16px; padding:24px;
    transition:transform 0.25s cubic-bezier(.4,1.6,.4,1);
  }
  .quote-card:hover{transform:translateY(-5px);}
  .quote-card .stars{color:var(--clay); font-size:13px; letter-spacing:1px; margin-bottom:10px;}
  .quote-card p{font-size:13.5px; line-height:1.6; color:var(--ink); font-style:italic; margin:0 0 12px;}
  .quote-card .who{font-size:12px; color:rgba(43,45,47,0.55); font-weight:600;}

  @media (max-width:820px){
    .steps-grid, .quote-grid{grid-template-columns:1fr;}
    .impact-strip{grid-template-columns:repeat(2,1fr);}
  }
  @media (max-width:420px){
    .impact-strip{grid-template-columns:1fr 1fr; padding:32px 22px; gap:26px 16px;}
    .impact-stat .num{font-size:26px;}
  }

  /* ===== Full-bleed alternating bands: sections stretch edge-to-edge, content stays centered ===== */
  .story-section{position:relative; isolation:isolate; overflow:hidden;}
  .story-section.band-sand::before{
    content:""; position:absolute; top:0; bottom:0; left:50%; width:100vw;
    transform:translateX(-50%); z-index:-2; background:var(--sand-deep);
  }
  .story-section.band-forest::before{
    content:""; position:absolute; top:0; bottom:0; left:50%; width:100vw;
    transform:translateX(-50%); z-index:-2;
    background:linear-gradient(120deg, var(--forest) 0%, var(--forest-dim) 100%);
  }
  .story-section.band-forest, .story-section.band-forest h2, .story-section.band-forest .story-eyebrow{color:var(--sand);}
  .story-section.band-forest .story-head h2{color:var(--sand);}
  .story-section.band-forest .step-card p{color:rgba(249,248,243,0.75);}

  /* drifting soft blobs behind banded sections, for full-width visual richness */
  .blob{position:absolute; border-radius:50%; filter:blur(50px); z-index:-1; pointer-events:none; opacity:0.55;}
  @keyframes blobDrift{
    0%,100%{transform:translate(0,0) scale(1);}
    33%{transform:translate(30px,-24px) scale(1.08);}
    66%{transform:translate(-22px,18px) scale(0.95);}
  }
  .blob-a{width:340px; height:340px; background:radial-gradient(circle, rgba(200,109,81,0.28), transparent 70%); top:-60px; left:-40px; animation:blobDrift 16s ease-in-out infinite;}
  .blob-b{width:280px; height:280px; background:radial-gradient(circle, rgba(118,138,110,0.35), transparent 70%); bottom:-70px; right:-30px; animation:blobDrift 20s ease-in-out infinite reverse;}
  .blob-c{width:220px; height:220px; background:radial-gradient(circle, rgba(255,255,255,0.18), transparent 70%); top:20%; right:10%; animation:blobDrift 14s ease-in-out infinite;}

  /* ===== Zigzag layout: steps alternate left/right down the page ===== */
  .zigzag{display:flex; flex-direction:column; gap:64px; margin-top:44px;}
  .zigzag-row{
    display:flex; align-items:center; gap:56px;
  }
  .zigzag-row.reverse{flex-direction:row-reverse;}
  .zigzag-media, .zigzag-text{flex:1; min-width:0;}
  .zigzag-media{
    display:flex; align-items:center; justify-content:center; position:relative;
  }
  .zigzag-num{
    font-family:'Fraunces', serif; font-size:min(20vw, 168px); line-height:1; font-weight:600;
    color:var(--clay); opacity:0.16; user-select:none;
  }
  .zigzag-row.reverse .zigzag-num{color:var(--sand); opacity:0.22;}
  .zigzag-icon-badge{
    position:absolute; width:84px; height:84px; border-radius:24px; background:var(--forest);
    color:var(--sand); display:flex; align-items:center; justify-content:center; font-size:36px;
    box-shadow:0 18px 34px rgba(34,62,48,0.25); transform:rotate(-6deg);
    transition:transform 0.4s cubic-bezier(.4,1.6,.4,1);
  }
  .zigzag-row.reverse .zigzag-icon-badge{background:var(--clay); box-shadow:0 18px 34px rgba(200,109,81,0.32);}
  .zigzag-row:hover .zigzag-icon-badge{transform:rotate(0deg) scale(1.08);}
  .zigzag-text h3{font-size:22px; font-weight:600; font-family:'Work Sans',sans-serif; margin-bottom:10px;}
  .zigzag-text p{font-size:14.5px; line-height:1.7; margin:0; max-width:440px;}
  .band-forest .zigzag-text p{color:rgba(249,248,243,0.78);}
  .zigzag-tag{
    display:inline-flex; font-size:11px; letter-spacing:0.1em; text-transform:uppercase; font-weight:600;
    color:var(--clay); margin-bottom:10px;
  }
  .band-forest .zigzag-tag{color:var(--clay-soft);}
  @media (max-width:820px){
    .zigzag-row, .zigzag-row.reverse{flex-direction:column; gap:26px;}
    .zigzag-num{font-size:96px;}
  }

  /* ===== Testimonials: staggered offset for a livelier, less grid-y feel ===== */
  .quote-grid > .quote-card:nth-child(1){transform:translateY(0);}
  .quote-grid > .quote-card:nth-child(2){transform:translateY(-22px);}
  .quote-grid > .quote-card:nth-child(3){transform:translateY(14px);}
  .quote-grid.reveal-stagger.in-view > .quote-card:nth-child(1):hover{transform:translateY(-6px);}
  .quote-grid.reveal-stagger.in-view > .quote-card:nth-child(2):hover{transform:translateY(-28px);}
  .quote-grid.reveal-stagger.in-view > .quote-card:nth-child(3):hover{transform:translateY(8px);}
  @media (max-width:820px){
    .quote-grid > .quote-card{transform:none !important;}
  }
  .quote-card{position:relative;}
  .quote-card::before{
    content:"“"; position:absolute; top:6px; right:16px; font-family:'Fraunces',serif;
    font-size:54px; color:var(--clay-soft); opacity:0.6; line-height:1;
  }

  /* ===== Count-up numbers on scroll ===== */
  .impact-stat .num{transition:transform 0.3s ease;}
  .impact-strip:hover .impact-stat .num{transform:scale(1.03);}

  /* ===== Closing CTA band: bold, editorial, lime accent ===== */
  .cta-band{
    background:linear-gradient(135deg, var(--forest) 0%, var(--forest-dim) 100%); color:var(--paper); overflow:hidden; position:relative;
  }
  .cta-inner{
    max-width:1400px; margin:0 auto; padding:100px 28px 70px; text-align:center;
  }
  .cta-eyebrow{
    font-size:12px; letter-spacing:0.18em; text-transform:uppercase; color:var(--lime); font-weight:600;
  }
  .cta-band h2{
    font-size:min(9vw, 76px); line-height:0.98; color:var(--paper); font-weight:400; margin:16px 0 34px;
    letter-spacing:-0.02em;
  }
  .btn-cta{
    background:var(--lime); color:var(--ink); border:none; border-radius:999px;
    padding:16px 34px; font-size:15px; font-weight:700; cursor:pointer;
    font-family:'Work Sans', sans-serif;
    transition:transform 0.25s cubic-bezier(.4,1.6,.4,1), box-shadow 0.25s ease;
  }
  .btn-cta:hover{transform:translateY(-3px) scale(1.03); box-shadow:0 16px 32px rgba(200,255,91,0.28);}
  .cta-marquee{
    border-top:1px solid rgba(249,248,243,0.14); overflow:hidden; padding:22px 0;
  }
  .cta-marquee-track{
    display:flex; gap:26px; white-space:nowrap; animation:marquee 22s linear infinite;
    font-family:'Fraunces', serif; font-size:26px; font-weight:500; color:rgba(249,248,243,0.35);
  }
  .cta-marquee-track span:nth-child(odd){color:var(--lime); opacity:0.5;}

  /* ===== Magnetic tilt for feature tiles + step cards ===== */
  .feature-tile, .step-card, .zigzag-icon-badge{will-change:transform;}
  .feature-tile{transform-style:preserve-3d;}

  /* ===== Top announcement marquee (FreshCart-inspired) ===== */
  .topbar{
    background:var(--forest); color:var(--sand); overflow:hidden;
    height:34px; display:flex; align-items:center; position:relative;
  }
  .marquee-track{
    display:flex; gap:60px; white-space:nowrap; align-items:center;
    animation:marquee 40s linear infinite;
    font-size:12.5px; letter-spacing:0.02em; padding-left:100%;
  }
  .marquee-track span{display:flex; align-items:center; gap:8px; opacity:0.92;}
  @keyframes marquee{
    from{transform:translateX(0);}
    to{transform:translateX(-100%);}
  }

  /* ===== Header: search + wishlist (FreshCart-inspired) ===== */
  header{transition:padding 0.25s ease, box-shadow 0.25s ease;}
  header.shrink{box-shadow:0 6px 20px rgba(34,62,48,0.18);}
  .bar{gap:20px;}

  /* ===== Header redesign (Modern Shopify / Clean Look) ===== */
  .header-top{
    background:rgba(59,122,52,0.92); color:var(--paper);
    backdrop-filter:blur(14px); -webkit-backdrop-filter:blur(14px);
  }
  .header-top .bar{
    max-width:1400px; margin:0 auto; padding:14px 28px 14px 16px;
    display:flex; align-items:center; gap:22px;
  }
  header.shrink .header-top .bar{padding:9px 28px 9px 16px;}
  .header-top .logo .mark{color:var(--sand);}
  .header-top .logo .sub{color:var(--clay-soft);}
  .deliver-to{
    display:flex; align-items:flex-start; gap:6px; cursor:pointer;
    font-size:12px; line-height:1.35; color:rgba(249,248,243,0.85);
    border:1px solid transparent; border-radius:6px; padding:4px 6px;
    transition:border-color 0.2s ease;
  }
  .deliver-to:hover{border-color:rgba(249,248,243,0.35);}
  .deliver-to .pin{font-size:15px; margin-top:1px;}
  .deliver-to b{display:block; font-size:13px; color:var(--sand); font-weight:600;}
  .search-group{
    flex:1; max-width:640px; display:flex; height:40px; border-radius:8px; overflow:hidden;
    box-shadow:0 1px 0 rgba(0,0,0,0.15);
  }
  .search-group input{
    flex:1; border:none; padding:0 16px; font-size:14px; color:var(--ink);
    font-family:'Work Sans', sans-serif; background:#fff;
  }
  .search-group input:focus{outline:none;}
  .search-go{
    background:var(--clay); border:none; width:48px; cursor:pointer; font-size:16px; color:var(--ink);
    transition:background 0.2s ease;
  }
  .search-go:hover{background:#B85C41;}
  .header-top .bar-right{display:flex; align-items:center; gap:20px;}
  .header-top .badge-pill{background:rgba(249,248,243,0.14); color:var(--sand);}
  .header-top .account-btn{
    border:1px solid transparent; background:transparent; color:var(--paper);
    display:flex; flex-direction:column; align-items:flex-start; gap:0; padding:4px 8px; border-radius:6px;
  }
  .header-top .account-btn:hover{border-color:rgba(249,248,243,0.35);}
  .header-top .account-btn .hello{font-size:11px; opacity:0.8;}
  .header-top .account-btn #account-label{font-size:13px; font-weight:600; color:var(--sand);}
  .header-top .account-btn .avatar{display:none;}
  .header-top .account-btn .avatar.signed-in{
    display:flex; width:22px; height:22px; font-size:10px; margin-right:6px;
  }
  .header-top .cart-btn{
    background:transparent; border:1px solid rgba(249,248,243,0.3); color:var(--sand);
  }
  .header-top .cart-btn:hover{border-color:var(--clay);}
  .header-nav{
    background:rgba(44,93,39,0.94); color:var(--paper);
    border-top:1px solid rgba(200,255,91,0.18);
  }

  /* Keep the EXACT navbar styling and add responsive sliding indicator */
  .header-nav nav{
    position:relative;
    display:flex;
    gap:28px;
    align-items:center;
  }
  .header-nav nav a{
    color:rgba(249,248,243,0.9);
    position:relative;
    padding-bottom:4px;
  }
  .header-nav nav a:hover, .header-nav nav a.active{
    color:var(--sand);
    opacity:1;
  }

  /* The responsive sliding underline line (exact same lime color and glow) */
  .nav-indicator{
    position:absolute;
    bottom:0px;
    left:0;
    height:2px;
    background:var(--neo, #b8ff6a);
    box-shadow:0 0 14px rgba(184,255,106,.5);
    border-radius:2px;
    pointer-events:none;
    transition:transform 0.28s cubic-bezier(.4,0,.2,1), width 0.28s cubic-bezier(.4,0,.2,1), opacity 0.2s ease;
    z-index:10;
    width:0;
    transform:translateX(0);
    opacity:0;
  }
  .header-nav nav a::after{
    display:none !important; /* Hide old static duplicate */
  }

  .nav-all-btn:hover{color:var(--lime);}
  .header-nav .bar{
    max-width:1400px; margin:0 auto; padding:9px 28px;
    display:flex; align-items:center; gap:26px;
  }
  .nav-all-btn{
    display:flex; align-items:center; gap:6px; background:transparent; border:none;
    color:var(--sand); font-size:13px; font-weight:600; cursor:pointer;
    font-family:'Work Sans', sans-serif;
  }
  .icon-btn{
    position:relative; background:transparent; border:1px solid var(--line); border-radius:999px;
    width:38px; height:38px; display:flex; align-items:center; justify-content:center;
    cursor:pointer; font-size:15px; transition:transform 0.18s cubic-bezier(.4,1.6,.4,1), border-color 0.2s;
  }
  .icon-btn:hover{border-color:var(--clay); transform:translateY(-2px);}
  .icon-btn.bump{animation:iconBump 0.4s ease;}
  @keyframes iconBump{
    0%{transform:scale(1);} 35%{transform:scale(1.3) rotate(-8deg);} 100%{transform:scale(1);}
  }
  .icon-badge{
    position:absolute; top:-5px; right:-5px; background:var(--clay); color:#fff;
    font-size:9.5px; font-family:'IBM Plex Mono',monospace; min-width:16px; height:16px; border-radius:999px;
    display:flex; align-items:center; justify-content:center; padding:0 3px;
    transform:scale(0); transition:transform 0.25s cubic-bezier(.4,1.6,.4,1);
  }
  .icon-badge.show{transform:scale(1);}
  .cart-btn{position:relative;}
  .cart-btn.bump .cart-count{animation:iconBump 0.4s ease;}

  .card-wish{
    position:absolute; top:10px; right:10px; background:rgba(255,255,255,0.85); border:none;
    width:30px; height:30px; border-radius:50%; display:flex; align-items:center; justify-content:center;
    cursor:pointer; font-size:14px; transition:transform 0.2s cubic-bezier(.4,1.6,.4,1), background 0.2s;
    z-index:2;
  }
  .card-wish:hover{transform:scale(1.15);}
  .card-wish.active{background:var(--clay); }
  .card-wish.pulse{animation:iconBump 0.4s ease;}

  /* ===== Hero redesign: illustrated globe / nature backdrop (Save-Earth inspired) ===== */
  .hero{position:relative; overflow:hidden; border-radius:0 0 28px 28px; isolation:isolate;}
  .hero::before{
    content:""; position:absolute; inset:0; z-index:-2;
    background:
      radial-gradient(ellipse 60% 50% at 78% 18%, rgba(58,124,140,0.16), transparent 60%),
      radial-gradient(ellipse 70% 60% at 14% 88%, rgba(214,163,64,0.15), transparent 65%),
      linear-gradient(180deg, #F6F9F7 0%, #EEF3EF 100%);
  }
  .hero-decor{position:absolute; inset:0; z-index:-1; pointer-events:none;}
  .hero-decor .leaf{
    position:absolute; font-size:26px; opacity:0.5;
    animation:leafPulse 4s ease-in-out infinite;
    transition:transform 0.15s ease-out;
    will-change:transform;
  }
  @keyframes leafPulse{
    0%,100%{opacity:0.4; scale:1;} 50%{opacity:0.65; scale:1.08;}
  }
  .hero-decor .leaf.l1{top:10%; left:6%; font-size:30px; animation-duration:8s;}
  .hero-decor .leaf.l2{top:65%; left:12%; font-size:20px; animation-duration:6s; animation-delay:0.6s;}
  .hero-decor .leaf.l3{top:20%; right:8%; font-size:22px; animation-duration:9s; animation-delay:1.1s;}
  .hero-decor .leaf.l4{bottom:12%; right:14%; font-size:28px; animation-duration:7.5s; animation-delay:0.3s;}
  .hero-decor .cloud{
    position:absolute; opacity:0.35; font-size:34px;
    animation:driftCloud 26s linear infinite;
  }
  .hero-decor .cloud.c1{top:8%; left:-8%;}
  .hero-decor .cloud.c2{top:40%; left:-14%; font-size:26px; animation-duration:34s; animation-delay:6s;}
  @keyframes driftCloud{
    from{transform:translateX(0);} to{transform:translateX(130vw);}
  }

  /* ===== Feature icon row (Ecova-inspired) ===== */
  .feature-row-wrap{max-width:1400px; margin:28px auto 0; padding:0 28px; position:relative; z-index:5;}
  .feature-row{
    display:grid; grid-template-columns:repeat(5,1fr); gap:14px;
    background:var(--sand); border:1px solid var(--line); border-radius:18px;
    padding:10px; box-shadow:0 18px 40px rgba(34,62,48,0.1);
    overflow:hidden;
  }
  .feature-tile{
    display:flex; flex-direction:column; align-items:center; gap:8px; text-align:center;
    padding:16px 10px; border-radius:14px; cursor:pointer; background:transparent;
    transition:background 0.3s ease, transform 0.25s cubic-bezier(.4,1.6,.4,1), box-shadow 0.3s ease;
    position:relative; overflow:hidden;
  }
  .feature-tile .ic{
    width:42px; height:42px; border-radius:50%; background:var(--sand-deep);
    display:flex; align-items:center; justify-content:center; font-size:20px;
    transition:background 0.3s ease, transform 0.3s ease;
  }
  .feature-tile .lbl{font-size:12.5px; font-weight:600; color:var(--ink);}
  .feature-tile:hover{transform:translateY(-4px);}
  .feature-tile:hover .ic{transform:scale(1.1) rotate(-6deg);}
  .feature-tile.active{
    background:var(--forest); box-shadow:0 12px 24px rgba(45,90,39,0.28), 0 0 0 2px var(--lime) inset;
  }
  .feature-tile.active .ic{background:rgba(255,255,255,0.18); animation:iconBump 0.5s ease;}
  .feature-tile.active .lbl{color:var(--sand);}
  @media (max-width:820px){ .feature-row{grid-template-columns:repeat(3,1fr);} }

  /* ===== Account control ===== */
  .account-btn{
    display:flex; align-items:center; gap:8px;
    background:transparent; border:1px solid var(--line); border-radius:999px;
    padding:6px 14px 6px 6px; cursor:pointer;
    font-family:'Work Sans', sans-serif; font-size:13.5px; font-weight:500; color:var(--ink);
  }
  .account-btn:hover{border-color:var(--sage);}
  .account-btn .avatar{
    width:26px; height:26px; border-radius:50%;
    background:var(--sand-deep); display:flex; align-items:center; justify-content:center;
    font-size:14px; flex-shrink:0;
  }
  .account-btn .avatar.signed-in{
    background:var(--forest); color:var(--paper); font-family:'IBM Plex Mono',monospace; font-size:11px; font-weight:600;
  }

  /* ===== Auth modal ===== */
  .modal-overlay{
    position:fixed; inset:0; background:rgba(43,45,47,0.45);
    opacity:0; pointer-events:none; transition:opacity 0.2s; z-index:110;
    display:flex; align-items:center; justify-content:center;
  }
  .modal-overlay.open{opacity:1; pointer-events:auto;}
  .auth-card{
    background:var(--paper); border-radius:16px; width:380px; max-width:90vw;
    padding:32px 28px; box-shadow:0 24px 60px rgba(43,45,47,0.25);
    transform:translateY(14px); transition:transform 0.25s;
  }
  .modal-overlay.open .auth-card{transform:translateY(0);}
  .auth-head{text-align:center; margin-bottom:22px;}
  .auth-head .leaf-mark{font-size:28px;}
  .auth-head h2{font-size:22px; color:var(--forest); margin-top:8px;}
  .auth-head p{font-size:13px; color:rgba(43,45,47,0.6); margin-top:6px; line-height:1.5;}
  .gate-note{
    background:var(--sand-deep); border:1px solid var(--line); border-radius:99px;
    padding:10px 12px; font-size:12.5px; color:var(--forest-dim); margin-bottom:20px;
    display:flex; gap:8px; align-items:flex-start;
  }
  .google-btn{
    width:100%; display:flex; align-items:center; justify-content:center; gap:10px;
    background:#FFFFFF; border:1px solid var(--line); border-radius:9px;
    padding:12px; font-size:14px; font-weight:500; color:var(--ink); cursor:pointer;
    font-family:'Work Sans', sans-serif;
  }
  .google-btn:hover{border-color:var(--sage);}
  .google-g{font-family:'Work Sans',sans-serif; font-weight:700; font-size:16px;}
  .divider{
    display:flex; align-items:center; gap:12px; margin:18px 0;
    font-size:11.5px; color:rgba(43,45,47,0.4); text-transform:uppercase; letter-spacing:0.08em;
  }
  .divider::before, .divider::after{content:""; flex:1; height:1px; background:var(--line);}
  .field-label{font-size:12.5px; font-weight:600; color:var(--ink); margin-bottom:6px; display:block;}
  .field-input{
    width:100%; border:1px solid var(--line); border-radius:8px; padding:11px 12px;
    font-size:14px; font-family:'Work Sans', sans-serif; background:#fff; color:var(--ink);
    margin-bottom:14px;
  }
  .field-input:focus{border-color:var(--sage);}
  .email-submit{
    width:100%; background:var(--forest); color:var(--paper); border:none; border-radius:9px;
    padding:12px; font-size:14px; font-weight:600; cursor:pointer; font-family:'Work Sans', sans-serif;
  }
  .email-submit:hover{background:var(--forest-dim);}
  .auth-foot{text-align:center; margin-top:16px; font-size:12px; color:rgba(43,45,47,0.5);}
  .auth-close{
    position:absolute; top:16px; right:16px; background:none; border:none;
    font-size:18px; cursor:pointer; color:var(--ink); opacity:0.5;
  }
  .auth-card{position:relative;}

  /* ===== Profile drawer ===== */
  .profile-head{
    display:flex; align-items:center; gap:14px; padding:20px 24px; border-bottom:1px solid var(--line);
  }
  .profile-avatar{
    width:52px; height:52px; border-radius:50%; background:var(--forest); color:var(--paper);
    display:flex; align-items:center; justify-content:center; font-family:'IBM Plex Mono',monospace;
    font-size:18px; font-weight:600; flex-shrink:0;
  }
  .profile-name{font-size:16.5px; font-weight:600; font-family:'Work Sans',sans-serif;}
  .profile-email{font-size:12.5px; color:rgba(43,45,47,0.55); margin-top:2px;}
  .profile-body{padding:20px 24px;}
  .profile-stat-grid{display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:24px;}
  .stat-card{
    background:var(--sand-deep); border:1px solid var(--line); border-radius:10px; padding:14px;
  }
  .stat-card .num{font-family:'IBM Plex Mono',monospace; font-size:20px; color:var(--forest); font-weight:600;}
  .stat-card .lbl{font-size:11px; text-transform:uppercase; letter-spacing:0.06em; color:rgba(43,45,47,0.55); margin-top:4px;}
  .profile-section-title{font-size:12px; text-transform:uppercase; letter-spacing:0.08em; color:var(--clay); font-weight:600; margin:0 0 12px;}
  .badge-track{display:flex; gap:10px; margin-bottom:26px;}
  .badge-tile{
    flex:1; text-align:center; padding:12px 6px; border-radius:10px; border:1px solid var(--line);
    font-size:11px; color:rgba(43,45,47,0.4);
  }
  .badge-tile.reached{background:var(--forest); border-color:var(--forest); color:var(--paper);}
  .badge-tile .ico{font-size:18px; display:block; margin-bottom:4px;}
  .signout-btn{
    width:100%; background:transparent; border:1px solid var(--line); border-radius:99px;
    padding:12px; font-size:13.5px; font-weight:600; color:var(--clay); cursor:pointer;
    font-family:'Work Sans', sans-serif; margin-top:6px;
  }
  .signout-btn:hover{background:var(--sand-deep);}

  /* ===== Product detail modal ===== */
  .pd-card{
    background:var(--paper); border-radius:18px; width:640px; max-width:92vw;
    max-height:88vh; overflow-y:auto; position:relative;
    box-shadow:0 24px 60px rgba(43,45,47,0.25);
    transform:translateY(14px); transition:transform 0.25s;
  }
  .modal-overlay.open .pd-card{transform:translateY(0);}
  .pd-grid{display:grid; grid-template-columns:1fr 1fr;}
  .pd-art{
    background:var(--sand-deep); display:flex; align-items:center; justify-content:center;
    font-size:110px; min-height:280px; border-radius:18px 0 0 18px;
  }
  .pd-info{padding:26px 26px 26px 24px;}
  .pd-cert{
    display:inline-block; background:var(--forest); color:var(--paper); font-size:10.5px; font-weight:600;
    padding:4px 9px; border-radius:6px; letter-spacing:0.03em; margin-bottom:12px;
  }
  .pd-info h2{font-size:22px; color:var(--ink); font-weight:600; font-family:'Work Sans',sans-serif;}
  .pd-cat{font-size:12.5px; color:rgba(43,45,47,0.55); margin-top:4px;}
  .pd-desc{font-size:13.5px; line-height:1.6; color:rgba(43,45,47,0.75); margin-top:14px;}
  .pd-eco{
    display:flex; align-items:center; gap:8px; background:var(--sand-deep); border:1px solid var(--line);
    border-radius:9px; padding:10px 12px; margin-top:16px; font-size:13px; color:var(--forest-dim); font-weight:600;
  }
  .pd-price-row{display:flex; align-items:center; justify-content:space-between; margin-top:20px;}
  .pd-price{font-family:'IBM Plex Mono',monospace; font-size:22px; color:var(--ink);}
  .pd-qty{display:flex; align-items:center; gap:12px; margin-top:18px;}
  .pd-qty-btn{
    width:32px; height:32px; border:1px solid var(--line); background:#fff; border-radius:7px;
    cursor:pointer; font-size:16px;
  }
  .pd-qty-num{font-family:'IBM Plex Mono',monospace; font-size:15px; min-width:16px; text-align:center;}
  .pd-add-btn{
    width:100%; margin-top:20px; background:var(--clay); color:#fff; border:none; border-radius:9px;
    padding:13px; font-size:14.5px; font-weight:600; cursor:pointer; font-family:'Work Sans', sans-serif;
  }
  @media (max-width:620px){ .pd-grid{grid-template-columns:1fr;} .pd-art{border-radius:18px 18px 0 0; min-height:200px;} }
  .pd-reviews-title{font-size:12px; text-transform:uppercase; letter-spacing:0.08em; color:var(--clay); font-weight:600; margin-top:26px; margin-bottom:10px;}
  .review-item{border-top:1px solid var(--line); padding:10px 0;}
  .review-item .stars{color:var(--clay); font-size:12px; letter-spacing:1px;}
  .review-item .who{font-size:12.5px; font-weight:600; margin:2px 0 4px;}
  .review-item .txt{font-size:12.5px; line-height:1.5; color:rgba(43,45,47,0.7);}
  .review-empty{font-size:12.5px; color:rgba(43,45,47,0.5); padding:8px 0;}

  /* ===== Order confirmation ===== */
  .conf-card{
    background:var(--paper); border-radius:18px; width:420px; max-width:92vw;
    padding:32px 28px; text-align:center;
    box-shadow:0 24px 60px rgba(43,45,47,0.25);
    transform:translateY(14px); transition:transform 0.25s;
  }
  .modal-overlay.open .conf-card{transform:translateY(0);}
  .conf-badge{
    width:74px; height:74px; border-radius:50%; background:var(--clay); color:#fff;
    display:flex; align-items:center; justify-content:center; font-size:32px;
    margin:0 auto 16px;
  }
  .conf-card h2{font-size:21px; color:var(--forest); margin-bottom:6px;}
  .conf-card .sub{font-size:13.5px; color:rgba(43,45,47,0.65); margin-bottom:20px; line-height:1.5;}
  .conf-invoice{
    background:var(--sand-deep); border:1px solid var(--line); border-radius:10px;
    padding:16px; text-align:left; margin-bottom:20px;
  }
  .conf-invoice .oid{font-family:'IBM Plex Mono',monospace; font-size:12px; color:rgba(43,45,47,0.5); margin-bottom:10px;}
  .conf-invoice .item-row{display:flex; justify-content:space-between; font-size:13px; margin-bottom:6px;}
  .conf-invoice .item-row.total{border-top:1px solid var(--line); padding-top:8px; margin-top:6px; font-weight:600;}
  .conf-invoice .v{font-family:'IBM Plex Mono',monospace;}
  .conf-actions{display:flex; gap:10px;}
  .conf-actions button{flex:1;}

  /* ===== Profile: order history & addresses ===== */
  .order-item{
    border:1px solid var(--line); border-radius:10px; padding:14px; margin-bottom:12px; background:#fff;
  }
  .order-item .top-row{display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;}
  .order-item .oid{font-family:'IBM Plex Mono',monospace; font-size:12px; color:rgba(43,45,47,0.55);}
  .order-item .amt{font-family:'IBM Plex Mono',monospace; font-size:13px; font-weight:600;}
  .status-track{display:flex; align-items:center; gap:4px; margin-top:10px;}
  .status-step{
    flex:1; text-align:center; font-size:9.5px; text-transform:uppercase; letter-spacing:0.03em;
    color:rgba(43,45,47,0.4); position:relative; padding-top:14px;
  }
  .status-step::before{
    content:""; position:absolute; top:0; left:50%; transform:translateX(-50%);
    width:9px; height:9px; border-radius:50%; background:var(--line);
  }
  .status-step.done{color:var(--forest); font-weight:600;}
  .status-step.done::before{background:var(--forest);}
  .status-step.current{color:var(--clay); font-weight:700;}
  .status-step.current::before{background:var(--clay); box-shadow:0 0 0 3px var(--clay-soft);}
  .addr-card{
    border:1px solid var(--line); border-radius:10px; padding:12px 14px; margin-bottom:10px; font-size:13px; background:#fff;
  }
  .addr-card .tag{font-size:10.5px; font-weight:700; color:var(--forest); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;}
  .orders-empty{font-size:12.5px; color:rgba(43,45,47,0.5); padding:10px 0 20px;}

  /* ===== PayHere checkout modal ===== */
  .pay-card{
    background:var(--paper); border-radius:16px; width:420px; max-width:92vw;
    padding:0; overflow:hidden;
    box-shadow:0 24px 60px rgba(43,45,47,0.25);
    transform:translateY(14px); transition:transform 0.25s;
  }
  .modal-overlay.open .pay-card{transform:translateY(0);}
  .pay-head{
    background:var(--forest); color:var(--paper); padding:20px 24px;
    display:flex; align-items:center; justify-content:space-between;
  }
  .pay-head .brand{display:flex; align-items:center; gap:8px; font-weight:600; font-size:15px;}
  .pay-head .lock{font-size:11px; opacity:0.75;}
  .pay-body{padding:22px 24px;}
  .pay-summary{
    background:var(--sand-deep); border:1px solid var(--line); border-radius:10px; padding:14px; margin-bottom:18px;
  }
  .pay-summary .row{display:flex; justify-content:space-between; font-size:13px; margin-bottom:6px;}
  .pay-summary .row:last-child{margin-bottom:0; padding-top:8px; border-top:1px solid var(--line); font-weight:600;}
  .pay-summary .row .v{font-family:'IBM Plex Mono',monospace;}
  .pay-method-row{display:flex; gap:10px; margin-bottom:16px;}
  .pay-method{
    flex:1; border:1px solid var(--line); border-radius:8px; padding:10px; text-align:center;
    font-size:12px; cursor:pointer; background:#fff;
  }
  .pay-method.active{border-color:var(--forest); background:var(--sand-deep); font-weight:600;}
  .pay-submit{
    width:100%; background:var(--forest); color:var(--paper); border:none; border-radius:9px;
    padding:13px; font-size:14.5px; font-weight:600; cursor:pointer; font-family:'Work Sans', sans-serif;
    margin-top:6px;
  }
  .pay-submit:hover{background:var(--forest-dim);}
  .pay-foot-note{text-align:center; font-size:11px; color:rgba(43,45,47,0.45); margin-top:12px;}

  /* =========================================================
     SOBA HOME — PREMIUM MOTION LAYER
     Home page visual upgrade only; existing commerce logic kept.
     ========================================================= */
  .home-page{position:relative; isolation:isolate; overflow:hidden;}
  .home-page::before{
    content:""; position:absolute; inset:0; pointer-events:none; z-index:-5;
    background:
      radial-gradient(circle at 6% 8%, rgba(168,224,112,.12), transparent 23%),
      radial-gradient(circle at 92% 20%, rgba(200,109,81,.08), transparent 24%),
      linear-gradient(180deg, rgba(255,255,255,.16), transparent 28%);
  }

  /* premium header motion */
  .topbar{box-shadow:0 5px 18px rgba(34,62,48,.08);}
  .header-top{position:relative; overflow:hidden;}
  .header-top::after{
    content:""; position:absolute; inset:0; pointer-events:none;
    background:linear-gradient(110deg, transparent 20%, rgba(255,255,255,.10) 45%, transparent 70%);
    transform:translateX(-120%); animation:headerSweep 7s ease-in-out infinite;
  }
  @keyframes headerSweep{0%,72%{transform:translateX(-120%)}100%{transform:translateX(120%)}}
  .header-nav{backdrop-filter:blur(18px);}
  .header-nav .bar{min-height:48px;}
  .header-nav .nav-link{transition:transform .22s ease,color .22s ease,opacity .22s ease;}
  .header-nav .nav-link:hover{transform:translateY(-2px);}

  /* hero */
  .hero{
    min-height:620px;
    padding-top:72px;
    padding-bottom:88px;
    border-radius:0 0 42px 42px;
    box-shadow:0 24px 70px rgba(34,62,48,.08);
  }
  .hero::after{
    content:""; position:absolute; inset:auto -10% -85px -10%; height:180px; z-index:-1;
    background:radial-gradient(ellipse, rgba(59,122,52,.12), transparent 68%);
    filter:blur(12px);
  }
  .hero h1{font-size:clamp(48px,6.1vw,82px); line-height:.92;}
  .hero h1 em{display:inline-block; animation:heroWordFloat 4.5s ease-in-out infinite;}
  @keyframes heroWordFloat{
    0%,100%{transform:translateY(0) rotate(-1deg)}
    50%{transform:translateY(-5px) rotate(1deg)}
  }
  .hero p.lede{font-size:17px; max-width:520px;}
  .hero-ctas{align-items:center;}
  .btn-primary,.btn-ghost,.btn-cta{
    position:relative; overflow:hidden;
  }
  .btn-primary::after,.btn-cta::after{
    content:""; position:absolute; inset:-30% auto -30% -40%; width:28%;
    background:rgba(255,255,255,.28); transform:skewX(-18deg) translateX(-160%);
    animation:buttonSheen 3.8s ease-in-out infinite;
  }
  @keyframes buttonSheen{
    0%,58%,100%{transform:skewX(-18deg) translateX(-160%)}
    72%{transform:skewX(-18deg) translateX(560%)}
  }

  /* floating ambient elements */
  .hero-ambient{position:absolute;inset:0;pointer-events:none;z-index:-1;overflow:hidden;}
  .orbit{
    position:absolute; border:1px solid rgba(59,122,52,.16); border-radius:50%;
    transform:rotate(-12deg);
    animation:orbitDrift 11s ease-in-out infinite;
  }
  .orbit-1{width:420px;height:420px;right:-120px;top:18px;}
  .orbit-2{width:280px;height:280px;right:28px;top:95px;border-color:rgba(200,109,81,.16);animation-delay:-3s;}
  @keyframes orbitDrift{
    0%,100%{transform:rotate(-12deg) translate(0,0)}
    50%{transform:rotate(7deg) translate(-14px,12px)}
  }
  .glow{
    position:absolute;border-radius:50%;filter:blur(42px);opacity:.55;animation:glowBreathe 8s ease-in-out infinite;
  }
  .glow-1{width:220px;height:220px;right:9%;top:21%;background:rgba(168,224,112,.20);}
  .glow-2{width:170px;height:170px;left:3%;bottom:7%;background:rgba(200,109,81,.12);animation-delay:-4s;}
  @keyframes glowBreathe{0%,100%{transform:scale(.92);opacity:.34}50%{transform:scale(1.12);opacity:.62}}
  .micro-dot{
    position:absolute;width:7px;height:7px;border-radius:50%;background:var(--clay-soft);
    box-shadow:0 0 0 5px rgba(200,109,81,.08);
    animation:dotFloat 6s ease-in-out infinite;
  }
  .d1{right:31%;top:19%;}.d2{right:7%;bottom:22%;animation-delay:-2s}.d3{left:7%;top:34%;animation-delay:-4s;}
  @keyframes dotFloat{0%,100%{transform:translateY(0) scale(1)}50%{transform:translateY(-16px) scale(1.2)}}

  /* hero illustration becomes more premium */
  .jar-wrap{filter:drop-shadow(0 22px 34px rgba(45,90,39,.14));}
  .jar-frame{transform-origin:center bottom;}
  .jar-frame::before{
    content:"";position:absolute;inset:10px -8px -4px;
    border-radius:48%;background:radial-gradient(circle,rgba(168,224,112,.18),transparent 66%);
    filter:blur(8px);z-index:-1;animation:jarAura 5s ease-in-out infinite;
  }
  @keyframes jarAura{0%,100%{transform:scale(.95);opacity:.65}50%{transform:scale(1.06);opacity:1}}
  .jar-readout .num{animation:numberGlow 2.6s ease-in-out infinite;}
  @keyframes numberGlow{0%,100%{text-shadow:0 0 0 rgba(59,122,52,0)}50%{text-shadow:0 0 18px rgba(95,174,71,.25)}}

  /* scroll cue */
  .scroll-cue{
    margin:-18px auto 10px; position:relative; z-index:8; width:max-content;
    display:flex;align-items:center;gap:9px;cursor:pointer;
    font-size:10px;text-transform:uppercase;letter-spacing:.14em;color:rgba(43,45,47,.56);
    animation:scrollCueFloat 2.8s ease-in-out infinite;
  }
  .scroll-cue-line{width:34px;height:1px;background:var(--line);}
  .scroll-cue-arrow{font-size:16px;color:var(--forest);}
  @keyframes scrollCueFloat{0%,100%{transform:translateY(0);opacity:.75}50%{transform:translateY(5px);opacity:1}}

  /* feature row */
  .feature-row-wrap{margin-top:16px;}
  .feature-row{
    border-radius:22px;
    padding:12px;
    box-shadow:0 26px 55px rgba(34,62,48,.12);
  }
  .feature-tile{min-height:112px;justify-content:center;}
  .feature-tile::before{
    content:"";position:absolute;inset:0;background:linear-gradient(120deg,transparent,rgba(255,255,255,.18),transparent);
    transform:translateX(-120%);transition:transform .7s ease;
  }
  .feature-tile:hover::before{transform:translateX(120%);}
  .feature-tile .ic{box-shadow:inset 0 0 0 1px rgba(34,62,48,.06);}
  .feature-tile:hover .lbl{color:var(--forest);}

  /* shop preview becomes clearly intentional */
  #shop{
    position:relative;
    scroll-margin-top:125px;
  }
  #shop::before{
    content:"";position:absolute;top:-20px;left:5%;right:5%;height:1px;
    background:linear-gradient(90deg,transparent,var(--line),transparent);
  }
  .filter-row{background:rgba(255,255,255,.46);backdrop-filter:blur(10px);border-radius:18px;padding:14px 16px;}
  .section-head h2{font-size:30px;}
  .section-head h2::after{
    content:"";display:block;width:48px;height:3px;border-radius:99px;background:var(--lime);margin-top:7px;
    animation:underlineGrow 3.2s ease-in-out infinite;
  }
  @keyframes underlineGrow{0%,100%{width:40px}50%{width:74px}}

  /* story */
  .story-section{scroll-margin-top:120px;}
  .story-head-enhanced{position:relative;}
  .eyebrow-pulse{
    width:8px;height:8px;border-radius:50%;display:inline-block;background:var(--clay);
    margin-right:8px;box-shadow:0 0 0 0 rgba(200,109,81,.36);vertical-align:middle;
    animation:eyebrowPulse 2s infinite;
  }
  @keyframes eyebrowPulse{70%{box-shadow:0 0 0 9px rgba(200,109,81,0)}100%{box-shadow:0 0 0 0 rgba(200,109,81,0)}}
  .step-card,.quote-card{position:relative;overflow:hidden;}
  .step-card::after,.quote-card::after{
    content:"";position:absolute;left:0;top:0;width:100%;height:2px;
    background:linear-gradient(90deg,var(--clay),var(--lime),transparent);
    transform:scaleX(.15);transform-origin:left;transition:transform .35s ease;
  }
  .step-card:hover::after,.quote-card:hover::after{transform:scaleX(1);}
  .impact-strip .impact-stat{position:relative;padding:10px 0;}
  .impact-strip .impact-stat::before{
    content:"";position:absolute;left:0;top:0;width:3px;height:100%;border-radius:10px;background:var(--lime);
    transform:scaleY(.25);transform-origin:center;transition:transform .3s ease;
  }
  .impact-strip:hover .impact-stat::before{transform:scaleY(1);}

  /* testimonials */
  .quote-grid{align-items:start;}
  .quote-card{box-shadow:0 12px 30px rgba(34,62,48,.06);}
  .quote-card .stars{animation:starShimmer 5s linear infinite;}
  @keyframes starShimmer{0%,75%,100%{opacity:.75}85%{opacity:1;transform:translateX(2px)}}

  /* closing CTA */
  .cta-band{position:relative;}
  .cta-band::before{
    content:"";position:absolute;inset:0;pointer-events:none;
    background:
      radial-gradient(circle at 16% 24%,rgba(168,224,112,.11),transparent 25%),
      radial-gradient(circle at 87% 76%,rgba(200,109,81,.15),transparent 24%);
    animation:ctaGlow 10s ease-in-out infinite;
  }
  @keyframes ctaGlow{0%,100%{transform:scale(1)}50%{transform:scale(1.06)}}
  .cta-inner{position:relative;z-index:1;}
  .cta-band h2{animation:ctaTitle 6s ease-in-out infinite;}
  @keyframes ctaTitle{0%,100%{letter-spacing:-.02em}50%{letter-spacing:-.012em}}

  /* responsive */
  @media (max-width:980px){
    .hero{grid-template-columns:1fr;min-height:auto;gap:24px;}
    .hero > div:nth-of-type(3){order:2;}
    .hero-ctas{justify-content:flex-start;}
    .search-group{max-width:none;}
  }
  @media (max-width:820px){
    .header-top .bar{flex-wrap:wrap;padding:12px 16px;}
    .deliver-to{order:3;}
    .search-group{order:4;flex-basis:100%;}
    .bar-right{margin-left:auto;}
    .hero{padding:54px 20px 72px;}
    .hero h1{font-size:48px;}
    .hero-ctas{flex-wrap:wrap;}
    .feature-row-wrap{padding:0 16px;}
    .feature-row{grid-template-columns:repeat(2,1fr);}
    .scroll-cue{margin-top:-12px;}
  }
  @media (max-width:560px){
    .header-nav .bar{padding:8px 16px;overflow-x:auto;}
    .header-nav nav{flex-wrap:nowrap;min-width:max-content;}
    .hero h1{font-size:40px;}
    .hero p.lede{font-size:15px;}
    .hero-stamp{display:none;}
    .feature-row{grid-template-columns:1fr 1fr;}
    .feature-tile{min-height:96px;}
    .scroll-cue{font-size:9px;}
  }

  @media (prefers-reduced-motion: reduce){
    .hero h1 em,.hero-ambient *,.scroll-cue,.btn-primary::after,.btn-cta::after,
    .header-top::after,.jar-frame::before,.jar-readout .num,.cta-band h2,.quote-card .stars,
    .feature-tile::before{animation:none !important;}
  }


/* ===== SOBA VISUAL REDESIGN v2 — animated eco-glass landing ===== */
:root{
  --neo:#b8ff6a; --deep:#0b2b20; --deep2:#123d2c; --glass:rgba(255,255,255,.10);
  --glass-line:rgba(255,255,255,.18); --white-soft:rgba(255,255,255,.82);
}
body{background:#071c15;overflow-x:hidden;}
body:before{content:"";position:fixed;inset:0;z-index:-5;background:
 radial-gradient(circle at 10% 10%,rgba(184,255,106,.10),transparent 28%),
 radial-gradient(circle at 88% 22%,rgba(56,189,248,.08),transparent 26%),
 radial-gradient(circle at 50% 100%,rgba(200,109,81,.10),transparent 30%),
 linear-gradient(135deg,#061610 0%,#0b241b 48%,#071a14 100%);}
#grain{opacity:.035;mix-blend-mode:screen;}
#spotlight{background:radial-gradient(360px circle at var(--mx,50%) var(--my,20%),rgba(184,255,106,.10),transparent 68%);z-index:9997;}

.topbar{background:rgba(184,255,106,.08)!important;border-bottom:1px solid rgba(184,255,106,.12);backdrop-filter:blur(14px);}
.marquee-track{color:#dfffd0!important;}
header{background:transparent!important;border-bottom:0!important;}
.header-top{background:rgba(7,28,21,.58)!important;border-bottom:1px solid rgba(255,255,255,.08);backdrop-filter:blur(20px);}
.header-nav{background:rgba(8,31,23,.70)!important;backdrop-filter:blur(18px);border-top:1px solid rgba(184,255,106,.10)!important;}
.bar{max-width:1480px!important;}
.logo .mark{color:#f5fff1!important;text-shadow:0 0 24px rgba(184,255,106,.18);}
.logo .sub{color:var(--neo)!important;}
.deliver-to,.header-top .account-btn{color:var(--white-soft)!important;}
.deliver-to b,.header-top .account-btn #account-label{color:#fff!important;}
.search-group{height:46px!important;border:1px solid rgba(255,255,255,.12);border-radius:14px!important;background:rgba(255,255,255,.08);box-shadow:0 18px 40px rgba(0,0,0,.16)!important;}
.search-group input{background:transparent!important;color:#fff!important;}
.search-group input::placeholder{color:rgba(255,255,255,.5);}
.search-go{background:var(--neo)!important;color:#102117!important;width:56px!important;}
.header-top .cart-btn{border-color:rgba(184,255,106,.35)!important;background:rgba(255,255,255,.05)!important;box-shadow:0 0 0 1px rgba(184,255,106,.06) inset;}
.header-top .cart-btn:hover{background:rgba(184,255,106,.12)!important;}

.hero{max-width:none!important;margin:0!important;padding:86px max(28px,calc((100vw - 1420px)/2)) 88px!important;min-height:720px;position:relative;isolation:isolate;border-radius:0!important;display:grid!important;grid-template-columns:minmax(0,1.04fr) minmax(420px,.96fr)!important;gap:36px!important;overflow:hidden;}
.hero:before{inset:0!important;background:
 radial-gradient(circle at 77% 28%,rgba(184,255,106,.16),transparent 22%),
 radial-gradient(circle at 80% 72%,rgba(56,189,248,.11),transparent 28%),
 linear-gradient(135deg,#0b2c20 0%,#0a2118 45%,#0e3022 100%)!important;}
.hero:after{content:"";position:absolute;inset:0;z-index:-1;background:linear-gradient(90deg,rgba(4,16,12,.34),transparent 55%,rgba(184,255,106,.04));pointer-events:none;}
.hero-decor{z-index:0!important;}
.hero-decor .leaf{filter:drop-shadow(0 12px 22px rgba(0,0,0,.25));opacity:.8!important;}
.hero-decor .cloud{opacity:.12!important;filter:blur(1px);}
.hero-stamp{right:5%!important;top:10%!important;width:138px!important;height:138px!important;filter:drop-shadow(0 18px 30px rgba(0,0,0,.3));}
.hero-stamp text{fill:#dfffcc!important;}
.hero-stamp .core{font-size:28px!important;}
.hero>div:nth-of-type(2){position:relative;z-index:2;}
.hero-eyebrow{color:var(--neo)!important;letter-spacing:.22em!important;}
.hero h1{color:#f7fff4!important;font-size:clamp(58px,7vw,108px)!important;line-height:.92!important;text-wrap:balance;}
.hero h1 em{color:var(--neo)!important;text-shadow:0 0 26px rgba(184,255,106,.17);}
.hero h1 em:after{display:none!important;}
.hero p.lede{color:rgba(240,255,246,.72)!important;max-width:600px!important;font-size:17px!important;line-height:1.75!important;}
.hero-ctas{gap:12px!important;margin-top:34px!important;}
.btn-primary{background:var(--neo)!important;color:#102117!important;border-radius:999px!important;padding:15px 24px!important;box-shadow:0 18px 36px rgba(184,255,106,.18);position:relative;overflow:hidden;}
.btn-primary:before{content:"";position:absolute;inset:0;transform:translateX(-120%);background:linear-gradient(110deg,transparent 25%,rgba(255,255,255,.55),transparent 70%);animation:shine 3.8s ease-in-out infinite;}
.btn-ghost{color:#f2fff0!important;background:rgba(255,255,255,.07)!important;border:1px solid rgba(255,255,255,.16)!important;border-radius:999px!important;backdrop-filter:blur(12px);padding:15px 24px!important;}
.btn-ghost:hover{background:rgba(255,255,255,.13)!important;}

.jar-wrap{justify-self:center;align-self:center;position:relative;z-index:2;}
.jar-wrap:before,.jar-wrap:after{content:"";position:absolute;border-radius:50%;pointer-events:none;}
.jar-wrap:before{width:410px;height:410px;background:radial-gradient(circle,rgba(184,255,106,.18),transparent 66%);filter:blur(12px);animation:pulseAura 5s ease-in-out infinite;}
.jar-wrap:after{width:250px;height:250px;border:1px solid rgba(184,255,106,.22);box-shadow:0 0 60px rgba(184,255,106,.08) inset;animation:orbitRing 12s linear infinite;}
.jar-frame{width:280px!important;height:380px!important;filter:drop-shadow(0 26px 42px rgba(0,0,0,.36));position:relative;z-index:2;}
.jar-svg{overflow:visible;}
.jar-readout{position:relative;z-index:3;margin-top:10px!important;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);padding:10px 18px;border-radius:999px;backdrop-filter:blur(12px);}
.jar-readout .num{color:var(--neo)!important;text-shadow:0 0 18px rgba(184,255,106,.18);}
.jar-readout .label{color:rgba(255,255,255,.65)!important;}

.feature-row-wrap{max-width:1420px!important;margin:-34px auto 0!important;position:relative;z-index:8;}
.feature-row{background:rgba(255,255,255,.055)!important;border:1px solid rgba(255,255,255,.13)!important;box-shadow:0 28px 70px rgba(0,0,0,.22)!important;border-radius:22px!important;backdrop-filter:blur(18px);}
.feature-tile{color:#fff!important;}
.feature-tile .ic{background:rgba(184,255,106,.09)!important;border:1px solid rgba(184,255,106,.15);box-shadow:0 10px 28px rgba(0,0,0,.14);}
.feature-tile .lbl{color:rgba(255,255,255,.8)!important;}
.feature-tile:hover .lbl,.feature-tile.active .lbl{color:#fff!important;}
.feature-tile.active{background:linear-gradient(145deg,rgba(184,255,106,.20),rgba(184,255,106,.05))!important;box-shadow:0 16px 36px rgba(0,0,0,.18),0 0 0 1px rgba(184,255,106,.34) inset!important;}

.shop-shell{max-width:1420px!important;padding-top:80px!important;}
.shop-shell .filter-row{border-color:rgba(255,255,255,.10)!important;}
.filter-group-label,.count{color:rgba(255,255,255,.48)!important;}
.select-filter,.chip{background:rgba(255,255,255,.05)!important;color:rgba(255,255,255,.85)!important;border-color:rgba(255,255,255,.12)!important;backdrop-filter:blur(10px);}
.chip.active{background:var(--neo)!important;color:#102117!important;border-color:var(--neo)!important;}
.section-head h2{color:#f5fff2!important;font-size:42px!important;}
.card{background:rgba(255,255,255,.055)!important;border:1px solid rgba(255,255,255,.10)!important;box-shadow:0 20px 50px rgba(0,0,0,.14)!important;backdrop-filter:blur(14px);}
.card:hover{box-shadow:0 28px 70px rgba(0,0,0,.28)!important;border-color:rgba(184,255,106,.28)!important;}
.card-art{background:linear-gradient(145deg,rgba(255,255,255,.06),rgba(184,255,106,.05))!important;}
.card-body h3,.price{color:#fff!important;}
.card-meta{color:rgba(255,255,255,.46)!important;}
.card-saved{color:var(--neo)!important;}
.add-btn{background:rgba(255,255,255,.08)!important;border:1px solid rgba(255,255,255,.12)!important;}
.add-btn:hover{background:var(--neo)!important;color:#112217!important;}

.story-section{max-width:none!important;padding-left:max(28px,calc((100vw - 1420px)/2))!important;padding-right:max(28px,calc((100vw - 1420px)/2))!important;background:transparent!important;}
.story-section:before{opacity:.9;}
.band-sand:before{background:linear-gradient(180deg,#0d2a20,#092017)!important;border-top:1px solid rgba(255,255,255,.05);border-bottom:1px solid rgba(255,255,255,.05);}
.band-forest:before{background:linear-gradient(135deg,#123b2b,#061710)!important;border-top:1px solid rgba(184,255,106,.08);border-bottom:1px solid rgba(184,255,106,.08);}
.story-eyebrow,.zigzag-tag{color:var(--neo)!important;}
.story-head h2,.zigzag-text h3{color:#f5fff1!important;}
.zigzag-text p{color:rgba(238,255,245,.68)!important;}
.step-card,.quote-card{background:rgba(255,255,255,.055)!important;border:1px solid rgba(255,255,255,.11)!important;color:#fff!important;backdrop-filter:blur(12px);}
.impact-stat .num{color:var(--neo)!important;text-shadow:0 0 24px rgba(184,255,106,.18);}
.impact-stat .lbl,.quote-card .who,.quote-card p{color:rgba(238,255,245,.68)!important;}
.quote-card .stars{color:var(--neo)!important;}

.cta-band{background:radial-gradient(circle at 50% 20%,rgba(184,255,106,.16),transparent 32%),linear-gradient(135deg,#0b2c20,#061710)!important;border-top:1px solid rgba(184,255,106,.12);}
.cta-band h2{color:#f5fff2!important;text-shadow:0 16px 50px rgba(0,0,0,.25);}
.cta-eyebrow{color:var(--neo)!important;}
.btn-cta{background:var(--neo)!important;color:#102117!important;box-shadow:0 20px 50px rgba(184,255,106,.14)!important;}
.cta-marquee-track{color:rgba(255,255,255,.22)!important;}
.cta-marquee-track span:nth-child(odd){color:var(--neo)!important;}
footer{background:#06150f!important;border-color:rgba(255,255,255,.08)!important;color:rgba(255,255,255,.55)!important;}
.footer-col a,.footer-col span,.footer-brand p{color:rgba(255,255,255,.62)!important;}
.footer-title{color:var(--neo)!important;}

/* floating particles */
.hero .orb{position:absolute;border-radius:50%;pointer-events:none;filter:blur(.2px);z-index:0;}
.hero .orb.a{width:10px;height:10px;background:var(--neo);top:20%;left:43%;box-shadow:0 0 24px var(--neo);animation:floatA 8s ease-in-out infinite;}
.hero .orb.b{width:7px;height:7px;background:#7dd3fc;top:62%;right:12%;box-shadow:0 0 18px #7dd3fc;animation:floatB 10s ease-in-out infinite;}
.hero .orb.c{width:14px;height:14px;background:#f7b38e;bottom:17%;left:8%;box-shadow:0 0 24px #f7b38e;animation:floatC 11s ease-in-out infinite;}
.hero .scroll-cue{position:absolute;bottom:24px;left:50%;transform:translateX(-50%);font:500 10px 'IBM Plex Mono',monospace;letter-spacing:.22em;color:rgba(255,255,255,.45);z-index:5;display:flex;flex-direction:column;align-items:center;gap:8px;text-transform:uppercase;}
.hero .scroll-cue:after{content:"";width:1px;height:44px;background:linear-gradient(var(--neo),transparent);animation:scrollLine 1.8s ease-in-out infinite;}

@keyframes shine{0%,50%{transform:translateX(-120%)}75%,100%{transform:translateX(120%)}}
@keyframes pulseAura{0%,100%{transform:scale(.92);opacity:.65}50%{transform:scale(1.06);opacity:1}}
@keyframes orbitRing{to{transform:rotate(360deg) translateX(12px) rotate(-360deg)}}
@keyframes floatA{0%,100%{transform:translate3d(0,0,0)}50%{transform:translate3d(22px,-30px,0)}}
@keyframes floatB{0%,100%{transform:translate3d(0,0,0)}50%{transform:translate3d(-18px,24px,0)}}
@keyframes floatC{0%,100%{transform:translate3d(0,0,0)}50%{transform:translate3d(26px,18px,0)}}
@keyframes scrollLine{0%,100%{opacity:.25;transform:scaleY(.7)}50%{opacity:1;transform:scaleY(1)}}

@media(max-width:900px){
  .hero{grid-template-columns:1fr!important;min-height:auto!important;padding-top:56px!important;text-align:center;}
  .hero>div:nth-of-type(2){display:flex;flex-direction:column;align-items:center;}
  .hero p.lede{max-width:680px;}
  .jar-wrap{margin-top:10px;}
  .feature-row{grid-template-columns:repeat(3,1fr)!important;}
  .section-head h2{font-size:34px!important;}
}
@media(max-width:620px){
  .topbar{display:none;}
  .header-top .deliver-to,.header-top .search-group{display:none;}
  .header-top .bar{padding:12px 16px!important;}
  .header-top .bar-right{margin-left:auto;gap:8px!important;}
  .header-top .account-btn{padding:3px 6px!important;}
  .hero{padding:44px 18px 70px!important;}
  .hero h1{font-size:48px!important;}
  .hero p.lede{font-size:15px!important;}
  .hero-stamp{display:none;}
  .jar-frame{width:220px!important;height:300px!important;}
  .feature-row-wrap{margin:-24px 14px 0!important;}
  .feature-row{grid-template-columns:repeat(2,1fr)!important;}
  .shop-shell{padding-left:18px!important;padding-right:18px!important;}
  .story-section{padding-left:18px!important;padding-right:18px!important;}
  .cta-inner{padding-left:18px!important;padding-right:18px!important;}
}


/* ===== EXTRA SCROLL CINEMA — ABOUT / REVIEWS ===== */
html{scroll-behavior:smooth;}
.story-section,.cta-band{will-change:transform,opacity;}
.story-section.reveal{opacity:0;transform:translateY(55px) scale(.985);filter:blur(5px);transition:opacity .9s cubic-bezier(.2,.7,.2,1),transform 1s cubic-bezier(.2,.7,.2,1),filter .9s ease;}
.story-section.reveal.in-view{opacity:1;transform:none;filter:none;}
.story-head{transform:translateY(18px);transition:transform .9s cubic-bezier(.2,.7,.2,1) .08s;}
.in-view .story-head{transform:none;}
.zigzag-row{opacity:0;transform:translateY(34px);transition:opacity .75s ease,transform .85s cubic-bezier(.2,.7,.2,1);}
.in-view .zigzag-row{opacity:1;transform:none;}
.in-view .zigzag-row:nth-child(1){transition-delay:.08s}.in-view .zigzag-row:nth-child(2){transition-delay:.2s}.in-view .zigzag-row:nth-child(3){transition-delay:.32s}
.zigzag-num{transition:transform .8s cubic-bezier(.2,.7,.2,1),opacity .5s ease;}
.in-view .zigzag-row:nth-child(odd) .zigzag-num{transform:translateX(18px);}
.in-view .zigzag-row:nth-child(even) .zigzag-num{transform:translateX(-18px);}
.reviews-section .quote-card{opacity:0;transform:translateY(45px) rotateX(7deg);transition:opacity .7s ease,transform .8s cubic-bezier(.2,.7,.2,1);}
.reviews-section.in-view .quote-card{opacity:1;transform:translateY(0) rotateX(0);}
.reviews-section.in-view .quote-card:nth-child(1){transition-delay:.08s}.reviews-section.in-view .quote-card:nth-child(2){transition-delay:.2s}.reviews-section.in-view .quote-card:nth-child(3){transition-delay:.32s}
.quote-card::before{animation:quoteFloat 4s ease-in-out infinite;}
@keyframes quoteFloat{0%,100%{transform:translateY(0) rotate(0)}50%{transform:translateY(-7px) rotate(-3deg)}}
.impact-strip .impact-stat{opacity:0;transform:translateY(22px) scale(.96);transition:opacity .65s ease,transform .65s cubic-bezier(.2,.7,.2,1);}
.band-forest.in-view .impact-stat{opacity:1;transform:none;}
.band-forest.in-view .impact-stat:nth-child(1){transition-delay:.05s}.band-forest.in-view .impact-stat:nth-child(2){transition-delay:.14s}.band-forest.in-view .impact-stat:nth-child(3){transition-delay:.23s}.band-forest.in-view .impact-stat:nth-child(4){transition-delay:.32s}
.reviews-section .story-eyebrow{animation:reviewPulse 2.8s ease-in-out infinite;}
@keyframes reviewPulse{0%,100%{letter-spacing:.16em;opacity:.72}50%{letter-spacing:.22em;opacity:1}}

/* ===== CINEMATIC HOME REDESIGN — NUDOT / DIGITAL-STUDIO INSPIRED ===== */
:root{
  --neo:#B8FF3D;
  --ink2:#0A0D0A;
  --cream:#EEEBDD;
  --muted:#9AA19B;
  --glass:rgba(255,255,255,.06);
  --line2:rgba(255,255,255,.13);
}
body{background:#0A0D0A;}
.cinematic-home{background:#0A0D0A;color:#F4F2E9;position:relative;overflow:hidden;}
.cinematic-home *{box-sizing:border-box;}
.cin-nav{position:absolute;top:0;left:0;right:0;z-index:80;display:grid;grid-template-columns:1fr auto 1fr;align-items:center;padding:22px 38px;border-bottom:1px solid rgba(255,255,255,.08);background:linear-gradient(180deg,rgba(0,0,0,.34),transparent);backdrop-filter:blur(10px)}
.cin-logo{font-family:'Fraunces',serif;font-size:23px;font-weight:700;letter-spacing:-.03em;color:#fff;cursor:pointer}
.cin-logo span{color:var(--neo)}
.cin-center{display:flex;gap:24px;align-items:center;justify-content:center}
.cin-center a{font-size:10px;letter-spacing:.16em;text-transform:uppercase;color:rgba(255,255,255,.64);text-decoration:none;position:relative;padding:5px 0;cursor:pointer}
.cin-center a:after{content:"";position:absolute;left:0;right:100%;bottom:0;height:1px;background:var(--neo);transition:right .35s ease}
.cin-center a:hover{color:#fff}.cin-center a:hover:after{right:0}
.cin-right{display:flex;justify-content:flex-end;gap:10px}
.cin-pill{border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.04);color:#fff;border-radius:999px;padding:9px 13px;font-size:10px;letter-spacing:.12em;text-transform:uppercase;cursor:pointer;transition:.25s}
.cin-pill:hover{border-color:var(--neo);transform:translateY(-2px)}
.cin-hero{min-height:780px;position:relative;display:grid;grid-template-columns:1.08fr .92fr;align-items:center;padding:130px 7vw 70px;isolation:isolate}
.cin-grid{position:absolute;inset:0;z-index:-3;background:
 linear-gradient(rgba(255,255,255,.035) 1px,transparent 1px),
 linear-gradient(90deg,rgba(255,255,255,.035) 1px,transparent 1px);
 background-size:72px 72px;mask-image:linear-gradient(to bottom,black 0 75%,transparent)}
.cin-noise{position:absolute;inset:0;z-index:-2;opacity:.07;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='160' height='160'%3E%3Cfilter id='n'%3E%3CfeTurbulence baseFrequency='.8' numOctaves='2' stitchTiles='stitch' type='fractalNoise'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E")}
.cin-orb-glow{position:absolute;width:56vw;height:56vw;max-width:800px;max-height:800px;right:-7vw;top:100px;border-radius:50%;background:radial-gradient(circle,rgba(184,255,61,.15),transparent 56%);filter:blur(20px);z-index:-2;animation:orbGlow 6s ease-in-out infinite}
@keyframes orbGlow{50%{transform:scale(1.08);opacity:.8}}
.cin-copy{position:relative;z-index:4}
.cin-kicker{font-size:10px;letter-spacing:.3em;text-transform:uppercase;color:var(--neo);display:flex;align-items:center;gap:11px;opacity:0;animation:cinUp .7s .1s forwards}
.cin-kicker:before{content:"";width:28px;height:1px;background:var(--neo)}
.cin-title{font-family:'Fraunces',serif;font-weight:500;font-size:clamp(68px,9vw,150px);line-height:.83;letter-spacing:-.06em;margin:22px 0 26px;color:#F4F2E9;max-width:830px;opacity:0;animation:cinUp .9s .18s forwards}
.cin-title em{font-style:italic;color:var(--neo)}
.cin-title .ghost{display:block;color:rgba(244,242,233,.16);padding-left:10vw}
.cin-lede{max-width:510px;font-size:14px;line-height:1.8;color:rgba(244,242,233,.56);opacity:0;animation:cinUp .8s .35s forwards}
.cin-actions{display:flex;gap:12px;margin-top:30px;opacity:0;animation:cinUp .8s .45s forwards}
.cin-main-btn{background:var(--neo);border:0;color:#071006;border-radius:999px;padding:14px 22px;font-weight:800;letter-spacing:.03em;cursor:pointer;transition:.25s}
.cin-main-btn:hover{transform:translateY(-3px) scale(1.03);box-shadow:0 20px 50px rgba(184,255,61,.2)}
.cin-ghost{border:1px solid rgba(255,255,255,.18);background:transparent;color:#fff;border-radius:999px;padding:14px 20px;cursor:pointer;transition:.25s}
.cin-ghost:hover{border-color:var(--neo);transform:translateY(-3px)}
.cin-meta{display:flex;gap:28px;margin-top:48px;opacity:0;animation:cinUp .8s .58s forwards}
.cin-meta .m{min-width:90px}.cin-meta .v{font-family:'IBM Plex Mono',monospace;font-size:18px;color:#fff}.cin-meta .l{font-size:9px;text-transform:uppercase;letter-spacing:.13em;color:rgba(255,255,255,.35);margin-top:5px}
.cin-stage{position:relative;height:550px;display:grid;place-items:center;perspective:1200px}
.eco-planet{width:min(32vw,440px);height:min(32vw,440px);border-radius:50%;position:relative;transform-style:preserve-3d;animation:planetFloat 8s ease-in-out infinite;box-shadow:0 0 0 1px rgba(255,255,255,.1),0 40px 130px rgba(0,0,0,.62),inset -35px -30px 75px rgba(0,0,0,.32);background:
 radial-gradient(circle at 30% 23%,#F8FFE8 0 3%,transparent 4%),
 radial-gradient(circle at 34% 28%,#7DBE44 0 11%,transparent 12%),
 radial-gradient(circle at 68% 62%,#1E4C22 0 19%,transparent 20%),
 radial-gradient(circle at 56% 32%,#6DAB42 0 10%,transparent 11%),
 radial-gradient(circle at 28% 66%,#4E8B38 0 14%,transparent 15%),
 radial-gradient(circle at 50% 50%,#2E6D31 0 48%,#0E2411 70%,#050805 100%);
 overflow:visible}
.eco-planet:before,.eco-planet:after{content:"";position:absolute;inset:-28px;border:1px solid rgba(184,255,61,.22);border-radius:50%;transform:rotateX(72deg) rotateZ(-12deg);animation:orbit 12s linear infinite}
.eco-planet:after{inset:-52px;border-color:rgba(255,255,255,.11);transform:rotateX(72deg) rotateZ(65deg);animation-duration:18s}
@keyframes orbit{to{transform:rotateX(72deg) rotateZ(348deg)}}
.planet-ridge{position:absolute;inset:14% 16%;border:1px solid rgba(255,255,255,.14);border-radius:46% 54% 50% 50%;transform:rotate(-16deg);filter:blur(.2px)}
.planet-core{position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);font-size:96px;filter:drop-shadow(0 15px 24px rgba(0,0,0,.35));animation:coreSpin 12s ease-in-out infinite}
@keyframes coreSpin{50%{transform:translate(-50%,-50%) rotate(7deg) scale(1.04)}}
.float-card{position:absolute;background:rgba(7,12,8,.72);border:1px solid rgba(255,255,255,.13);backdrop-filter:blur(16px);padding:14px 15px;border-radius:16px;min-width:155px;box-shadow:0 18px 48px rgba(0,0,0,.28);animation:floatCard 6s ease-in-out infinite}
.float-card b{display:block;font-family:'IBM Plex Mono',monospace;font-size:17px;color:#fff}.float-card span{font-size:9px;color:rgba(255,255,255,.44);text-transform:uppercase;letter-spacing:.1em}
.fc1{top:8%;right:0;animation-delay:.2s}.fc2{bottom:12%;left:2%;animation-delay:1.2s}.fc3{top:44%;right:-5%;animation-delay:2.1s}
@keyframes floatCard{50%{transform:translateY(-15px) rotate(1deg)}}
.cin-scroll{position:absolute;bottom:24px;left:7vw;display:flex;align-items:center;gap:12px;color:rgba(255,255,255,.42);font-size:9px;letter-spacing:.18em;text-transform:uppercase;animation:blink 2s ease-in-out infinite}
.cin-scroll .line{width:48px;height:1px;background:rgba(255,255,255,.28)}
@keyframes blink{50%{opacity:.45}}
.cin-marquee{border-top:1px solid rgba(255,255,255,.09);border-bottom:1px solid rgba(255,255,255,.09);overflow:hidden;background:#0F130F}
.cin-track{display:flex;gap:46px;white-space:nowrap;align-items:center;padding:15px 0;animation:cinMarq 22s linear infinite;font-family:'IBM Plex Mono',monospace;font-size:10px;letter-spacing:.18em;color:rgba(255,255,255,.43)}
.cin-track strong{color:var(--neo);font-weight:500}
@keyframes cinMarq{to{transform:translateX(-50%)}}
.cin-manifesto{background:var(--cream);color:#0B0D0B;padding:105px 7vw 120px;position:relative;overflow:hidden}
.cin-manifesto .ey{font-size:10px;letter-spacing:.28em;text-transform:uppercase;color:#687064}
.cin-big{font-family:'Fraunces',serif;font-size:clamp(52px,7vw,108px);line-height:.92;letter-spacing:-.055em;max-width:1100px;margin:18px 0 45px}
.cin-big em{color:#3B7A34}
.cin-manifesto-grid{display:grid;grid-template-columns:1.2fr .8fr;gap:60px;align-items:end}
.cin-manifesto p{max-width:520px;color:#5D625B;font-size:14px;line-height:1.9}
.cin-linkrow{display:flex;justify-content:flex-end}.cin-circle-link{width:135px;height:135px;border:1px solid #B4B9B0;border-radius:50%;display:grid;place-items:center;text-align:center;font-size:10px;letter-spacing:.15em;text-transform:uppercase;cursor:pointer;transition:.35s}.cin-circle-link:hover{background:#0B0D0B;color:#fff;transform:rotate(-10deg) scale(1.05);border-color:#0B0D0B}
.cin-cats{background:#0A0D0A;color:#fff;padding:110px 7vw}
.cin-cats-head{display:flex;justify-content:space-between;align-items:end;margin-bottom:40px}.cin-cats-head .small{font-size:10px;letter-spacing:.22em;text-transform:uppercase;color:rgba(255,255,255,.35)}.cin-cats-head h2{font-family:'Fraunces',serif;font-weight:500;font-size:clamp(42px,6vw,82px);letter-spacing:-.05em;line-height:.9;margin:0}
.cat-stack{border-top:1px solid rgba(255,255,255,.1)}
.cat-row{display:grid;grid-template-columns:70px 1fr 180px;align-items:center;gap:20px;padding:24px 0;border-bottom:1px solid rgba(255,255,255,.1);cursor:pointer;transition:.35s}
.cat-row .no{font-family:'IBM Plex Mono',monospace;font-size:10px;color:rgba(255,255,255,.3)}.cat-row .name{font-family:'Fraunces',serif;font-size:clamp(30px,4.2vw,62px);letter-spacing:-.04em}.cat-row .desc{text-align:right;font-size:10px;text-transform:uppercase;letter-spacing:.12em;color:rgba(255,255,255,.32)}
.cat-row:hover{padding-left:18px;background:linear-gradient(90deg,rgba(184,255,61,.06),transparent)}.cat-row:hover .name{color:var(--neo)}.cat-row:hover .no{color:var(--neo)}
.cin-selected{background:#F0EFE7;color:#111;padding:110px 7vw}.selected-head{display:flex;justify-content:space-between;align-items:end;margin-bottom:35px}.selected-head h2{font-family:'Fraunces',serif;font-size:clamp(44px,6vw,82px);letter-spacing:-.05em;line-height:.9;margin:0}.selected-head span{font-size:10px;letter-spacing:.18em;color:#82867E;text-transform:uppercase}
.premium-products{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}.premium-products .card{background:#fff;border:1px solid #DAD8CD;border-radius:0;transform:none!important;opacity:1!important;animation:none!important;min-height:360px}.premium-products .card:hover{transform:translateY(-8px)!important;box-shadow:0 25px 45px rgba(19,22,17,.12)}.premium-products .card-art{height:210px;background:#E9E9DE;font-size:72px}.premium-products .card-body{padding:18px}.premium-products .add-btn{background:#111}.premium-products .add-btn:hover{background:#3B7A34}
.cin-final{background:#080A08;color:#fff;padding:130px 7vw 80px;position:relative;overflow:hidden}.cin-final h2{font-family:'Fraunces',serif;font-size:clamp(70px,11vw,170px);line-height:.78;letter-spacing:-.07em;font-weight:500;margin:0}.cin-final h2 span{display:block;text-align:right;color:var(--neo);padding-right:7vw}.cin-final .frow{display:flex;justify-content:space-between;align-items:center;margin-top:65px;border-top:1px solid rgba(255,255,255,.1);padding-top:18px}.cin-final .frow p{font-size:10px;color:rgba(255,255,255,.35);text-transform:uppercase;letter-spacing:.16em;margin:0}.cin-final .fbtn{background:var(--neo);color:#081008;border:0;border-radius:999px;padding:14px 22px;font-weight:800;cursor:pointer}
@keyframes cinUp{from{opacity:0;transform:translateY(32px)}to{opacity:1;transform:none}}
@keyframes planetFloat{0%,100%{transform:translate3d(0,0,0) rotate(-2deg)}50%{transform:translate3d(0,-15px,0) rotate(3deg)}}
@media(max-width:900px){
  .cin-nav{grid-template-columns:1fr auto;padding:16px 20px;}
  .cin-center{display:none;}
  .cin-hero{grid-template-columns:1fr;min-height:auto;padding:100px 20px 60px;text-align:center;}
  .cin-kicker{justify-content:center;}
  .cin-title{font-size:clamp(46px,9vw,80px);margin:16px auto 20px;}
  .cin-title .ghost{padding-left:0;display:inline;}
  .cin-lede{margin:0 auto;}
  .cin-actions{justify-content:center;}
  .cin-meta{justify-content:center;}
  .cin-stage{height:360px;margin-top:30px;overflow:hidden;}
  .eco-planet{width:min(70vw, 300px);height:min(70vw, 300px);}
  .float-card{transform:scale(.85);}
  .fc1{right:5%;top:5%;}
  .fc2{left:5%;bottom:5%;}
  .fc3{display:none;}
  .cin-manifesto-grid{grid-template-columns:1fr;gap:30px;}
  .cin-linkrow{justify-content:flex-start;}
  .premium-products{grid-template-columns:repeat(2,1fr);gap:12px;}
  .cat-row{grid-template-columns:40px 1fr;}
  .cat-row .desc{display:none;}
}
@media(max-width:600px){
  .premium-products{grid-template-columns:1fr;}
}
@media(max-width:480px){
  .cin-nav{padding:12px 14px;}
  .cin-hero{padding:85px 14px 45px;}
  .cin-title{font-size:38px;letter-spacing:-0.04em;}
  .cin-stage{height:250px;margin-top:15px;}
  .eco-planet{width:190px;height:190px;}
  .planet-core{font-size:54px;}
  .float-card{padding:8px 10px;min-width:115px;transform:scale(0.8);}
  .float-card b{font-size:13px;}
  .float-card span{font-size:8px;}
  .cin-meta{gap:14px;flex-wrap:wrap;}
  .cin-actions{flex-direction:column;align-items:stretch;}
  .cin-big{font-size:38px;}
  .cin-selected, .cin-manifesto, .cin-cats, .cin-final{padding:60px 16px;}
  .cin-final h2{font-size:46px;line-height:0.9;}
  .cin-final h2 span{padding-right:0;text-align:left;}
  .cin-final .frow{flex-direction:column;gap:14px;text-align:center;}
}
@media(prefers-reduced-motion:reduce){.cin-* *,.eco-planet,.float-card,.cin-track{animation:none!important}}

/* ===== SOBA V4 — DEEP SCROLL CINEMA FOR ABOUT / IMPACT / REVIEWS ===== */
.about-section, #about-section,
.reviews-section, #reviews-section,
.band-forest{
  position:relative;
  isolation:isolate;
  overflow:hidden;
}

/* animated ambient light drifting behind the content */
#about-section::after,
.band-forest::after,
#reviews-section::after{
  content:"";
  position:absolute;
  width:38vw;height:38vw;
  border-radius:50%;
  pointer-events:none;
  z-index:-1;
  opacity:.22;
  filter:blur(35px);
  background:radial-gradient(circle,rgba(184,255,61,.24),transparent 66%);
  transform:translate3d(-12%,8%,0);
  animation:sectionGlowDrift 12s ease-in-out infinite alternate;
}
#reviews-section::after{
  right:-8%;left:auto;
  background:radial-gradient(circle,rgba(125,211,252,.12),transparent 66%);
  animation-delay:-4s;
}
.band-forest::after{
  left:auto;right:-5%;
  background:radial-gradient(circle,rgba(184,255,61,.16),transparent 66%);
  animation-delay:-7s;
}
@keyframes sectionGlowDrift{
  0%{transform:translate3d(-12%,8%,0) scale(.88);opacity:.12}
  50%{transform:translate3d(8%,-4%,0) scale(1.08);opacity:.24}
  100%{transform:translate3d(20%,12%,0) scale(.94);opacity:.15}
}

/* fine cinematic grid moving slowly while the section is visible */
#about-section::before,
#reviews-section::before{
  content:"";
  position:absolute;
  inset:0;
  z-index:-2;
  pointer-events:none;
  opacity:.18;
  background:
    linear-gradient(90deg,transparent 49.8%,rgba(184,255,61,.08) 50%,transparent 50.2%),
    linear-gradient(0deg,transparent 49.8%,rgba(184,255,61,.05) 50%,transparent 50.2%);
  background-size:110px 110px;
  mask-image:linear-gradient(to bottom,transparent,black 18%,black 82%,transparent);
  animation:gridDrift 18s linear infinite;
}
@keyframes gridDrift{
  to{background-position:110px 110px}
}

/* vertical story spine + travelling pulse */
#about-section .zigzag{
  position:relative;
}
#about-section .zigzag::before{
  content:"";
  position:absolute;
  top:22px;bottom:22px;
  left:50%;
  width:1px;
  transform:scaleY(0);
  transform-origin:top;
  background:linear-gradient(to bottom,transparent,var(--neo),rgba(184,255,61,.08),transparent);
  box-shadow:0 0 18px rgba(184,255,61,.18);
  transition:transform 1.4s cubic-bezier(.2,.7,.2,1) .2s;
}
#about-section.in-view .zigzag::before{transform:scaleY(1);}
#about-section .zigzag::after{
  content:"";
  position:absolute;
  left:calc(50% - 3px);
  top:24px;
  width:7px;height:7px;
  border-radius:50%;
  background:var(--neo);
  box-shadow:0 0 0 5px rgba(184,255,61,.08),0 0 22px rgba(184,255,61,.75);
  opacity:0;
}
#about-section.in-view .zigzag::after{
  opacity:1;
  animation:spineTravel 3.8s ease-in-out infinite;
}
@keyframes spineTravel{
  0%{top:7%;opacity:.15}
  12%{opacity:1}
  88%{opacity:1}
  100%{top:93%;opacity:.1}
}

/* stronger entrance for each about row */
#about-section .zigzag-row{
  perspective:900px;
  opacity:0;
  filter:blur(8px);
  transform:translateY(52px) scale(.96) rotateX(5deg);
  transition:
    opacity .9s ease,
    filter .9s ease,
    transform 1s cubic-bezier(.16,.8,.2,1);
}
#about-section.in-view .zigzag-row{
  opacity:1;
  filter:none;
  transform:none;
}
#about-section.in-view .zigzag-row:nth-child(1){transition-delay:.12s}
#about-section.in-view .zigzag-row:nth-child(2){transition-delay:.28s}
#about-section.in-view .zigzag-row:nth-child(3){transition-delay:.44s}

#about-section .zigzag-media{
  transform:scale(.82) rotate(-6deg);
  transition:transform 1s cubic-bezier(.16,.8,.2,1),filter .8s ease;
  filter:drop-shadow(0 12px 24px rgba(0,0,0,.28));
}
#about-section.in-view .zigzag-media{
  transform:scale(1) rotate(0);
}
#about-section .zigzag-row:hover .zigzag-media{
  transform:translateY(-7px) scale(1.06) rotate(2deg);
  filter:drop-shadow(0 20px 30px rgba(184,255,61,.12));
}

/* little number badge pop */
#about-section .zigzag-num{
  opacity:0;
  transform:scale(.45);
  transition:opacity .55s ease,transform .7s cubic-bezier(.2,1.5,.3,1);
}
#about-section.in-view .zigzag-num{opacity:1;transform:scale(1);transition-delay:.3s}

/* impact band becomes a cinematic counter runway */
.band-forest .impact-strip{
  position:relative;
}
.band-forest .impact-strip::before{
  content:"";
  position:absolute;
  left:0;right:0;top:50%;
  height:1px;
  background:linear-gradient(90deg,transparent,rgba(184,255,61,.22),transparent);
  transform:scaleX(0);
  transform-origin:center;
  transition:transform 1.2s ease .15s;
}
.band-forest.in-view .impact-strip::before{transform:scaleX(1);}
.band-forest .impact-stat{
  transition:transform .7s cubic-bezier(.2,1.2,.3,1),opacity .7s ease;
}
.band-forest.in-view .impact-stat:hover{
  transform:translateY(-8px) scale(1.04);
}
.band-forest .impact-stat .num{
  transition:text-shadow .35s ease,letter-spacing .35s ease;
}
.band-forest .impact-stat:hover .num{
  text-shadow:0 0 34px rgba(184,255,61,.55);
  letter-spacing:.03em;
}

/* Reviews: cinematic cards rise, rotate and glow */
#reviews-section .quote-grid{
  perspective:1200px;
}
#reviews-section .quote-card{
  position:relative;
  overflow:hidden;
  transform-style:preserve-3d;
  opacity:0;
  transform:translateY(70px) rotateX(10deg) rotateZ(var(--r,0deg)) scale(.94);
  filter:blur(7px);
  transition:
    opacity .85s ease,
    transform 1s cubic-bezier(.16,.8,.2,1),
    filter .85s ease,
    border-color .35s ease,
    box-shadow .35s ease;
}
#reviews-section .quote-card:nth-child(1){--r:-1.5deg}
#reviews-section .quote-card:nth-child(2){--r:1deg}
#reviews-section .quote-card:nth-child(3){--r:-.8deg}
#reviews-section.in-view .quote-card{
  opacity:1;
  filter:none;
  transform:translateY(0) rotateX(0) rotateZ(var(--r)) scale(1);
}
#reviews-section.in-view .quote-card:nth-child(1){transition-delay:.08s}
#reviews-section.in-view .quote-card:nth-child(2){transition-delay:.22s}
#reviews-section.in-view .quote-card:nth-child(3){transition-delay:.36s}

#reviews-section .quote-card::after{
  height:100%;
  width:45%;
  top:0;left:-70%;
  transform:none;
  opacity:0;
  background:linear-gradient(105deg,transparent,rgba(255,255,255,.14),transparent);
  animation:none;
}
#reviews-section.in-view .quote-card::after{
  opacity:1;
  animation:reviewSweep 1.4s ease .7s both;
}
@keyframes reviewSweep{
  from{left:-70%}
  to{left:125%}
}

#reviews-section .quote-card:hover{
  transform:translateY(-10px) rotateZ(0deg) rotateX(0deg) scale(1.025)!important;
  border-color:rgba(184,255,61,.35)!important;
  box-shadow:0 28px 70px rgba(0,0,0,.28),0 0 0 1px rgba(184,255,61,.08) inset!important;
}
#reviews-section .quote-card .stars{
  display:inline-block;
  transform-origin:left center;
  animation:starGlow 3.5s ease-in-out infinite;
}
@keyframes starGlow{
  0%,100%{transform:scale(1);filter:drop-shadow(0 0 0 rgba(184,255,61,0));}
  50%{transform:scale(1.08);filter:drop-shadow(0 0 9px rgba(184,255,61,.42));}
}

/* quote mark / ambient orb */
#reviews-section .quote-card::before{
  content:"“";
  position:absolute;
  right:18px;
  top:8px;
  font-family:Georgia,serif;
  font-size:68px;
  line-height:1;
  color:rgba(184,255,61,.07);
  pointer-events:none;
  transform:translateY(12px);
  transition:transform .5s ease,color .5s ease;
}
#reviews-section .quote-card:hover::before{
  transform:translateY(0) rotate(-4deg);
  color:rgba(184,255,61,.15);
}

/* heading text gently resolves after section arrives */
#about-section .story-head h2,
#reviews-section .story-head h2{
  opacity:0;
  transform:translateY(18px);
  clip-path:inset(0 0 100% 0);
  transition:opacity .7s ease .05s,transform .8s cubic-bezier(.2,.7,.2,1) .05s,clip-path .9s ease .05s;
}
#about-section.in-view .story-head h2,
#reviews-section.in-view .story-head h2{
  opacity:1;
  transform:none;
  clip-path:inset(0 0 0 0);
}

/* floating particles specific to the story sections */
.story-particle{
  position:absolute;
  width:5px;height:5px;
  border-radius:50%;
  background:var(--neo);
  box-shadow:0 0 16px rgba(184,255,61,.65);
  pointer-events:none;
  opacity:0;
  z-index:0;
}
.story-particle.p1{left:12%;top:22%;animation:particleA 7s ease-in-out infinite}
.story-particle.p2{right:16%;top:38%;width:3px;height:3px;animation:particleB 9s ease-in-out infinite}
.story-particle.p3{left:44%;bottom:14%;width:4px;height:4px;animation:particleC 8s ease-in-out infinite}
.in-view .story-particle{opacity:.65}
@keyframes particleA{0%,100%{transform:translate3d(0,0,0)}50%{transform:translate3d(28px,-34px,0)}}
@keyframes particleB{0%,100%{transform:translate3d(0,0,0)}50%{transform:translate3d(-24px,30px,0)}}
@keyframes particleC{0%,100%{transform:translate3d(0,0,0)}50%{transform:translate3d(18px,-24px,0)}}

@media(max-width:900px){
  #about-section .zigzag::before,#about-section .zigzag::after{display:none;}
  #reviews-section .quote-card{transform:translateY(48px) scale(.97);}
}
@media(max-width:620px){
  #about-section::before,#reviews-section::before{background-size:70px 70px;}
  #about-section .story-head h2,#reviews-section .story-head h2{clip-path:none;}
}
@media(prefers-reduced-motion:reduce){
  #about-section::after,#reviews-section::after,.band-forest::after,
  #about-section::before,#reviews-section::before,
  #about-section .zigzag::after,.story-particle{animation:none!important;}
  #about-section .zigzag-row,#about-section .story-head h2,
  #reviews-section .quote-card,#reviews-section .story-head h2,
  .band-forest .impact-stat{opacity:1!important;filter:none!important;transform:none!important;transition:none!important;}
}


/* ===== FINAL VISIBILITY + CINEMATIC MOTION FIX ===== */
/* Never hide the story content if an observer fails; animation is progressive, not blocking. */
#about-section.reveal,
#reviews-section.reveal,
.band-forest.reveal{
  opacity:1 !important;
  transform:none !important;
  filter:none !important;
}
#about-section .story-head,
#reviews-section .story-head{opacity:1;transform:none;}
#about-section .story-head h2,
#reviews-section .story-head h2{opacity:1 !important;clip-path:none !important;transform:none !important;}
#about-section .zigzag-row,
#reviews-section .quote-card,
.band-forest .impact-stat{
  opacity:1 !important;
  filter:none !important;
}
#about-section .zigzag-row{transform:none;}
#reviews-section .quote-card{transform:none;}

/* The actual animation starts only after JS marks a section as ready/in view. */
#about-section.motion-play .story-head{animation:storyHeadIn .8s cubic-bezier(.2,.8,.2,1) both;}
#about-section.motion-play .zigzag-row{animation:aboutRowIn .9s cubic-bezier(.16,.8,.2,1) both;}
#about-section.motion-play .zigzag-row:nth-child(1){animation-delay:.08s;}
#about-section.motion-play .zigzag-row:nth-child(2){animation-delay:.24s;}
#about-section.motion-play .zigzag-row:nth-child(3){animation-delay:.40s;}
#about-section.motion-play .zigzag-icon-badge{animation:iconPop .8s cubic-bezier(.16,1.4,.3,1) both;}
#about-section.motion-play .zigzag-row:nth-child(1) .zigzag-icon-badge{animation-delay:.18s;}
#about-section.motion-play .zigzag-row:nth-child(2) .zigzag-icon-badge{animation-delay:.34s;}
#about-section.motion-play .zigzag-row:nth-child(3) .zigzag-icon-badge{animation-delay:.50s;}

#reviews-section.motion-play .story-head{animation:storyHeadIn .8s cubic-bezier(.2,.8,.2,1) both;}
#reviews-section.motion-play .quote-card{animation:reviewCardIn .9s cubic-bezier(.16,.8,.2,1) both;}
#reviews-section.motion-play .quote-card:nth-child(1){animation-delay:.10s;}
#reviews-section.motion-play .quote-card:nth-child(2){animation-delay:.25s;}
#reviews-section.motion-play .quote-card:nth-child(3){animation-delay:.40s;}
#reviews-section.motion-play .stars{animation:starGlow 1.1s ease both;}

.band-forest.motion-play .impact-stat{animation:impactIn .75s cubic-bezier(.16,.8,.2,1) both;}
.band-forest.motion-play .impact-stat:nth-child(1){animation-delay:.05s;}
.band-forest.motion-play .impact-stat:nth-child(2){animation-delay:.16s;}
.band-forest.motion-play .impact-stat:nth-child(3){animation-delay:.27s;}
.band-forest.motion-play .impact-stat:nth-child(4){animation-delay:.38s;}

@keyframes storyHeadIn{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:none}}
@keyframes aboutRowIn{from{opacity:0;transform:translateY(38px) scale(.97)}to{opacity:1;transform:none}}
@keyframes iconPop{from{opacity:0;transform:scale(.65) rotate(-10deg)}to{opacity:1;transform:scale(1) rotate(0)}}
@keyframes reviewCardIn{from{opacity:0;transform:translateY(46px) rotateX(7deg) scale(.96)}to{opacity:1;transform:translateY(0) rotateX(0) scale(1)}}
@keyframes impactIn{from{opacity:0;transform:translateY(22px) scale(.94)}to{opacity:1;transform:none}}
@keyframes starGlow{from{opacity:.2;filter:blur(3px);letter-spacing:4px}to{opacity:1;filter:none;letter-spacing:1px}}

/* More breathing room and visual scale for the two requested sections. */
#about-section .story-head,
#reviews-section .story-head{position:relative;z-index:3;max-width:900px;margin:0 auto;}
#about-section .zigzag,
#reviews-section .quote-grid{position:relative;z-index:3;max-width:1120px;margin-left:auto;margin-right:auto;}
#about-section .story-head h2,
#reviews-section .story-head h2{font-size:clamp(30px,3vw,48px)!important;line-height:1.12!important;}
#about-section .zigzag-text h3{font-size:clamp(19px,1.5vw,25px)!important;}
#about-section .zigzag-text p,
#reviews-section .quote-card p{font-size:clamp(13px,1vw,16px)!important;}
#reviews-section .quote-card{min-height:190px;}

/* Ambient light makes the dark sections read as intentional instead of empty. */
#about-section::after,
#reviews-section::after,
.band-forest::after{
  content:"";position:absolute;inset:-20%;pointer-events:none;z-index:1;
  background:
    radial-gradient(circle at 18% 35%,rgba(184,255,61,.08),transparent 22%),
    radial-gradient(circle at 82% 65%,rgba(125,211,252,.055),transparent 20%);
  animation:ambientDrift 10s ease-in-out infinite alternate;
}
@keyframes ambientDrift{from{transform:translate3d(-1%,0,0) scale(1)}to{transform:translate3d(1%,1.5%,0) scale(1.04)}}

@media(max-width:820px){
  #about-section .zigzag,#reviews-section .quote-grid{max-width:100%;}
  #reviews-section .quote-card{min-height:0;}
}
@media(prefers-reduced-motion:reduce){
  #about-section.motion-play .story-head,
  #about-section.motion-play .zigzag-row,
  #about-section.motion-play .zigzag-icon-badge,
  #reviews-section.motion-play .story-head,
  #reviews-section.motion-play .quote-card,
  #reviews-section.motion-play .stars,
  .band-forest.motion-play .impact-stat{animation:none!important;}
  #about-section::after,#reviews-section::after,.band-forest::after{animation:none!important;}
}


/* ===== V5 FINAL — ALWAYS-VISIBLE CINEMATIC MOTION + LIVE COUNTERS ===== */
#about-section .zigzag-row{
  opacity:1!important; filter:none!important; transform:none!important;
  animation:none;
}
#about-section.in-view .zigzag-row{
  animation:aboutRowIn .95s cubic-bezier(.16,.8,.2,1) both;
}
#about-section.in-view .zigzag-row:nth-child(1){animation-delay:.08s}
#about-section.in-view .zigzag-row:nth-child(2){animation-delay:.24s}
#about-section.in-view .zigzag-row:nth-child(3){animation-delay:.40s}
@keyframes aboutRowIn{
  from{opacity:.25;transform:translateY(42px) scale(.96) rotateX(5deg);filter:blur(5px)}
  to{opacity:1;transform:none;filter:none}
}
#about-section .story-head h2,#reviews-section .story-head h2{
  opacity:1!important;transform:none!important;
}
#about-section.in-view .story-head h2,#reviews-section.in-view .story-head h2{
  animation:headingReveal .9s cubic-bezier(.16,.8,.2,1) both;
}
@keyframes headingReveal{from{opacity:.15;transform:translateY(24px);filter:blur(6px)}to{opacity:1;transform:none;filter:none}}

.band-forest .impact-stat{
  opacity:1!important;transform:none!important;filter:none!important;
}
.band-forest.in-view .impact-stat{
  animation:statPop .75s cubic-bezier(.2,1.45,.3,1) both;
}
.band-forest.in-view .impact-stat:nth-child(1){animation-delay:.05s}
.band-forest.in-view .impact-stat:nth-child(2){animation-delay:.16s}
.band-forest.in-view .impact-stat:nth-child(3){animation-delay:.27s}
.band-forest.in-view .impact-stat:nth-child(4){animation-delay:.38s}
@keyframes statPop{from{opacity:.3;transform:translateY(25px) scale(.88)}to{opacity:1;transform:translateY(0) scale(1)}}
.impact-stat .num{
  position:relative;display:inline-block;min-width:1.5ch;
  animation:counterGlow 2.8s ease-in-out infinite;
}
@keyframes counterGlow{0%,100%{text-shadow:0 0 12px rgba(184,255,61,.08)}50%{text-shadow:0 0 30px rgba(184,255,61,.42);transform:translateY(-2px)}}

#reviews-section .quote-card{
  opacity:1!important;transform:none!important;filter:none!important;
}
#reviews-section.in-view .quote-card{
  animation:reviewCardIn .9s cubic-bezier(.16,.8,.2,1) both;
}
#reviews-section.in-view .quote-card:nth-child(1){animation-delay:.08s}
#reviews-section.in-view .quote-card:nth-child(2){animation-delay:.24s}
#reviews-section.in-view .quote-card:nth-child(3){animation-delay:.40s}
@keyframes reviewCardIn{
  from{opacity:.25;transform:translateY(45px) rotateX(8deg) scale(.96);filter:blur(5px)}
  to{opacity:1;transform:none;filter:none}
}
#reviews-section .quote-card::before{
  animation:reviewShine 4.5s ease-in-out infinite;
}
@keyframes reviewShine{0%,55%,100%{transform:translateX(-120%) rotate(12deg);opacity:0}65%{transform:translateX(120%) rotate(12deg);opacity:.35}75%{opacity:0}}

/* floating particles become visible once their section enters the viewport */
.story-particle{animation:particleFloat 7s ease-in-out infinite!important}
.story-particle.p2{animation-delay:-2.2s!important}.story-particle.p3{animation-delay:-4.1s!important}
@keyframes particleFloat{0%,100%{transform:translate3d(0,0,0) scale(1);opacity:.18}50%{transform:translate3d(18px,-24px,0) scale(1.25);opacity:.62}}

/* fallback: content never disappears if an observer/API is unavailable */
@media(prefers-reduced-motion:reduce){
  #about-section .zigzag-row,#reviews-section .quote-card,.band-forest .impact-stat{opacity:1!important;transform:none!important;filter:none!important;animation:none!important}
  .impact-stat .num,.story-particle,#reviews-section .quote-card::before{animation:none!important}
}


/* Selected objects: compact spacing after removing the plastic-saved label */
.cin-selected .premium-products .card-body { gap: 10px; }
.cin-selected .premium-products .card-foot { padding-top: 12px; }

/* ===== Selected objects: readable text on light premium product cards ===== */
.cin-selected .premium-products .card {
  background: #FFFFFF !important;
  border: 1px solid #DEDCD2 !important;
  color: #242C24 !important;
  box-shadow: 0 12px 30px rgba(23, 40, 27, 0.08) !important;
  cursor: pointer;
}
.cin-selected .premium-products .card:hover {
  border-color: #7A9A77 !important;
  box-shadow: 0 20px 42px rgba(23, 40, 27, 0.16) !important;
}
.cin-selected .premium-products .card-body {
  background: #FFFFFF !important;
}
.cin-selected .premium-products .card-body h3 {
  color: #1C2D24 !important;
  font-weight: 650;
  line-height: 1.4;
}
.cin-selected .premium-products .card-meta {
  color: #687269 !important;
  opacity: 1 !important;
  line-height: 1.5;
}
.cin-selected .premium-products .card-saved,
.cin-selected .premium-products .card-saved .mono {
  color: #397D38 !important;
}
.cin-selected .premium-products .card-foot .price {
  color: #1C2D24 !important;
  font-weight: 700;
  letter-spacing: -0.02em;
}
/* Selected products: lime-green Buy Now inspired Add to cart button */
.cin-selected .premium-products .add-btn {
  background: #B8FF6A !important;
  border: 1px solid #B8FF6A !important;
  color: #142218 !important;
  border-radius: 12px;
  padding: 12px 22px;
  font-size: 13px;
  font-weight: 800;
  box-shadow: 0 5px 14px rgba(95, 154, 43, 0.16);
  transition: background .2s, transform .2s, box-shadow .2s !important;
}
.cin-selected .premium-products .add-btn:hover {
  background: #A3F34B !important;
  border-color: #A3F34B !important;
  color: #142218 !important;
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(95, 154, 43, .26);
}
.cin-selected .premium-products .add-btn:focus-visible {
  outline: 2px solid #244F35;
  outline-offset: 3px;
}
.cin-selected .premium-products .add-btn:active { transform: translateY(0); }

/* ===== SOBA responsive refinement (tablet + phones) ===== */
html,body{max-width:100%;overflow-x:clip;}
.cinematic-home,.cin-hero,.cin-copy,.cin-stage{min-width:0;}
.cinematic-home img,.cin-selected img{max-width:100%;}
@media (max-width:900px){
  .header-top .bar{display:grid!important;grid-template-columns:minmax(0,1fr) auto;gap:10px!important;padding:12px 18px!important;align-items:center;}
  header.shrink .header-top .bar{padding:10px 18px!important;}
  .header-top .logo{min-width:0;flex-wrap:wrap;gap:4px 8px;}
  .header-top .bar-right{margin:0!important;min-width:0;}
  .header-top .deliver-to,.header-top .search-group{display:none!important;}
  .header-nav .bar{padding:10px 16px!important;overflow-x:auto;scrollbar-width:none;}
  .header-nav .bar::-webkit-scrollbar{display:none;}
  .header-nav nav{display:flex!important;flex-wrap:nowrap!important;justify-content:space-between;min-width:100%;gap:clamp(10px,3vw,26px)!important;}
  .header-nav nav a{font-size:12px;white-space:nowrap;flex-shrink:0;}
  .cin-nav{display:none!important;}
  .cin-hero{display:flex!important;flex-direction:column;align-items:stretch;min-height:auto!important;text-align:center;padding:62px clamp(18px,5vw,40px) 62px!important;gap:16px;}
  .cin-copy{width:100%;max-width:680px;margin:0 auto;}
  .cin-kicker{justify-content:center;font-size:9px;letter-spacing:.18em;line-height:1.5;}
  .cin-title{font-size:clamp(50px,10.5vw,84px)!important;line-height:.98!important;letter-spacing:-.045em!important;max-width:100%;margin:24px 0 18px!important;overflow-wrap:normal;}
  .cin-title .ghost{display:block!important;padding:0!important;margin-top:8px;font-size:.66em;line-height:1.1;letter-spacing:-.045em;white-space:normal;}
  .cin-lede{font-size:14px;line-height:1.7;max-width:520px;margin:0 auto;}
  .cin-actions{justify-content:center;margin-top:24px;gap:10px;}
  .cin-meta{justify-content:center;margin-top:28px;gap:16px;}
  .cin-stage{height:340px!important;margin-top:8px;overflow:visible!important;width:100%;}
  .eco-planet{width:min(54vw,260px);height:min(54vw,260px);}
  .fc1{right:3%;top:7%;}.fc2{left:3%;bottom:5%;}.fc3{display:none;}
  .cin-scroll{display:none;}
  .cin-cats,.cin-selected,.cin-manifesto,.cin-final{padding-left:clamp(18px,5vw,42px)!important;padding-right:clamp(18px,5vw,42px)!important;}
  .selected-head{align-items:start;gap:12px;}
  .selected-head h2{font-size:clamp(42px,7vw,70px);}
  .premium-products{grid-template-columns:repeat(2,minmax(0,1fr));}
  .premium-products .card{min-width:0;}
  .premium-products .card-art{height:200px;}
  .cin-final h2{overflow-wrap:normal;}
}
@media (max-width:600px){
  .header-top .logo .mark{font-size:22px;}
  .header-top .logo .sub{font-size:8px;letter-spacing:.08em;}
  .header-top .account-btn .hello{font-size:10px;}
  .header-top .account-btn #account-label{font-size:12px;}
  .header-nav .bar{padding:11px 12px!important;}
  .header-nav nav{gap:clamp(9px,2.8vw,18px)!important;}
  .header-nav nav a{font-size:clamp(10px,2.7vw,12px);}
  .cin-hero{padding:50px 20px 50px!important;}
  .cin-title{font-size:clamp(48px,12.5vw,72px)!important;line-height:1!important;margin:20px 0 16px!important;}
  .cin-title .ghost{font-size:.61em;line-height:1.15;margin-top:10px;}
  .cin-kicker{font-size:9px;letter-spacing:.12em;}
  .cin-kicker:before{width:18px;flex-shrink:0;}
  .cin-lede{font-size:13px!important;line-height:1.75;}
  .cin-actions{display:grid;grid-template-columns:1fr;gap:10px;max-width:390px;margin:22px auto 0;}
  .cin-main-btn,.cin-ghost{width:100%;min-height:46px;font-size:12px;padding:12px 16px;}
  .cin-meta{gap:0;display:grid;grid-template-columns:repeat(3,minmax(0,1fr));width:100%;margin:26px auto 0;}
  .cin-meta .m{min-width:0;padding:0 6px;}
  .cin-meta .m+.m{border-left:1px solid rgba(255,255,255,.11);}
  .cin-meta .v{font-size:16px;}
  .cin-meta .l{font-size:8px;line-height:1.4;letter-spacing:.06em;}
  .cin-stage{height:268px!important;margin-top:8px;}
  .eco-planet{width:min(52vw,205px);height:min(52vw,205px);}
  .planet-core{font-size:56px;}
  .float-card{min-width:0;max-width:118px;padding:9px 10px;}
  .float-card b{font-size:13px;}.float-card span{font-size:7px;line-height:1.5;display:block;}
  .fc1{top:1%;right:0;}.fc2{bottom:0;left:0;}
  .cin-cats,.cin-selected,.cin-manifesto,.cin-final{padding-top:66px!important;padding-bottom:66px!important;}
  .cin-cats-head,.selected-head{flex-direction:column;align-items:flex-start;gap:15px;}
  .cin-cats-head h2,.selected-head h2{font-size:clamp(44px,11vw,64px)!important;}
  .cat-row{grid-template-columns:28px minmax(0,1fr);gap:10px;padding:20px 0;}
  .cat-row .name{font-size:clamp(26px,7.5vw,40px);}
  .premium-products{grid-template-columns:minmax(0,1fr);gap:18px;}
  .premium-products .card-art{height:clamp(195px,52vw,280px);}
  .premium-products .card{min-height:0;}
  .premium-products .card-foot{gap:12px;flex-wrap:wrap;}
  .cin-manifesto-grid{grid-template-columns:1fr;}
  .cin-big{overflow-wrap:break-word;font-size:clamp(36px,9.2vw,56px)!important;}
  .cin-final h2{font-size:clamp(42px,11vw,66px)!important;line-height:.98!important;}
  .cin-final .frow{flex-direction:column;align-items:flex-start;gap:16px;}
  .footer-inner{grid-template-columns:1fr 1fr;gap:25px;}
}
@media(max-width:390px){
  .header-top .bar{padding:10px 12px!important;gap:6px!important;}
  .header-top .logo .sub{display:block;font-size:7px;}
  .header-top .account-btn{padding:3px!important;}
  .header-nav .bar{padding:9px 7px!important;}
  .header-nav nav{gap:8px!important;}
  .header-nav nav a{font-size:10px;}
  .cin-hero{padding:42px 16px 45px!important;}
  .cin-title{font-size:clamp(43px,12.4vw,49px)!important;letter-spacing:-.055em!important;}
  .cin-title .ghost{font-size:.59em;}
  .cin-stage{height:240px!important;}
  .eco-planet{width:168px;height:168px;}
  .float-card{max-width:105px;padding:7px 8px;}
  .float-card b{font-size:12px;}.float-card span{font-size:6.5px;}
  .footer-inner{grid-template-columns:1fr;}
}
@media(max-width:340px){
  .header-top .logo .sub{display:none;}
  .cin-kicker{letter-spacing:.045em;font-size:8px;}
  .cin-title{font-size:40px!important;}
  .header-nav nav a{font-size:9px;}
  .cin-meta .l{font-size:7px;}
  .eco-planet{width:150px;height:150px;}
}


/* Mobile scroll animation repair: animate each step as it enters the viewport,
   rather than firing all three when the containing section first appears. */
@media (max-width:900px) and (prefers-reduced-motion:no-preference){
  #about-section.motion-play .zigzag-row,
  #about-section.in-view .zigzag-row,
  #about-section.motion-play .zigzag-icon-badge,
  .band-forest.motion-play .impact-stat,
  .band-forest.in-view .impact-stat,
  #reviews-section.motion-play .quote-card,
  #reviews-section.in-view .quote-card{
    animation:none !important;
  }
  #about-section .zigzag-row.mobile-motion-visible .zigzag-media{
    animation:sobaMobileIcon .8s cubic-bezier(.2,1.15,.3,1) both !important;
  }
  #about-section .zigzag-row.mobile-motion-visible .zigzag-text{
    animation:sobaMobileText .8s cubic-bezier(.2,.8,.2,1) both !important;
  }
  .band-forest .impact-stat.mobile-motion-visible,
  #reviews-section .quote-card.mobile-motion-visible{
    animation:sobaMobileText .8s cubic-bezier(.2,.8,.2,1) both !important;
  }
  @keyframes sobaMobileIcon{
    from{opacity:.2;transform:translateY(24px) scale(.75) rotate(-8deg);filter:blur(3px)}
    to{opacity:1;transform:translateY(0) scale(1) rotate(0);filter:none}
  }
  @keyframes sobaMobileText{
    from{opacity:.15;transform:translateY(34px);filter:blur(4px)}
    to{opacity:1;transform:translateY(0);filter:none}
  }
}

</style>
</head>
<body>
<div id="grain"></div>
<div id="spotlight"></div>

<div id="route-bar"></div>

<div class="topbar">
  <div class="marquee-track">
    <span>🌿 Free delivery over Rs. 5,000 island-wide</span>
    <span>🌍 12 Sri Lankan makers, zero single-use void-fill</span>
    <span>🍃 New: Cork Yoga Mat — 480g of plastic avoided per mat</span>
    <span>🌿 Free delivery over Rs. 5,000 island-wide</span>
    <span>🌍 12 Sri Lankan makers, zero single-use void-fill</span>
    <span>🍃 New: Cork Yoga Mat — 480g of plastic avoided per mat</span>
  </div>
</div>

<header id="site-header">
  <div class="header-top">
    <div class="bar">
      <div class="logo" onclick="navTo('home-top')">
        <span class="mark">Soba</span>
        <span class="sub">sobadahama · nature</span>
      </div>
      <div class="deliver-to" onclick="navTo('contact-section')">
        <span class="pin">📍</span>
        <span>Deliver to<b>Colombo, LK</b></span>
      </div>
      <div class="search-group">
        <input type="text" id="search-input" placeholder="Search Soba…" onkeydown="if(event.key==='Enter') goToSearch()">
        <button class="search-go" onclick="goToSearch()">🔍</button>
      </div>
      <div class="bar-right">
        <button class="account-btn" id="account-btn" onclick="handleAccountClick()">
          <span class="hello">Hello, sign in</span>
          <span class="avatar" id="account-avatar">👤</span>
          <span id="account-label">Sign in</span>
        </button>
      </div>
    </div>
  </div>
  <div class="header-nav">
    <div class="bar">
      <nav id="navbar">
        <a class="nav-link active home-link" data-nav="home" onclick="navTo('home-top')">Home</a>
        <a class="nav-link" data-nav="shop" href="modules/customer/products.php">Shop</a>
        <a class="nav-link" data-nav="about" onclick="navTo('about-section')">About</a>
        <a class="nav-link" data-nav="reviews" onclick="navTo('reviews-section')">Reviews</a>
        <a class="nav-link" data-nav="contact" onclick="navTo('contact-section')">Contact</a>
        <div class="nav-indicator" id="nav-indicator"></div>
      </nav>
    </div>
  </div>
</header>

<main id="home" class="home-page">

<section class="cinematic-home" id="home-top">
  <div class="cin-nav">
    <div class="cin-logo" onclick="navTo('home-top')"></div>
  </div>

  <div class="cin-hero">
    <div class="cin-grid"></div><div class="cin-noise"></div><div class="cin-orb-glow"></div>
    <div class="cin-copy">
      <div class="cin-kicker">Sri Lanka · Zero-waste marketplace</div>
      <h1 class="cin-title">LIVE<br><em>LIGHT.</em><span class="ghost">WASTE LESS.</span></h1>
      <p class="cin-lede">Soba turns everyday shopping into a visible act of change — locally made objects, better materials and every gram of plastic you skip counted in real time.</p>
      <div class="cin-actions">
        <button class="cin-main-btn" onclick="navTo('shop')">Explore the collection ↗</button>
        <button class="cin-ghost" onclick="navTo('about-section')">How Soba works</button>
      </div>
      <div class="cin-meta">
        <div class="m"><div class="v">12+</div><div class="l">Local makers</div></div>
        <div class="m"><div class="v">2kg+</div><div class="l">Plastic target</div></div>
        <div class="m"><div class="v">04</div><div class="l">Core categories</div></div>
      </div>
    </div>
    <div class="cin-stage" id="cin-stage">
      <div class="eco-planet" id="eco-planet">
        <div class="planet-ridge"></div>
        <div class="planet-core">🌿</div>
      </div>
      <div class="float-card fc1"><b>340g</b><span>plastic skipped / bowl set</span></div>
      <div class="float-card fc2"><b>100%</b><span>plastic-free void fill</span></div>
      <div class="float-card fc3"><b>LOCAL</b><span>made by Sri Lankan makers</span></div>
    </div>
    <div class="cin-scroll"><span class="line"></span> Scroll to explore</div>
  </div>

  <div class="cin-marquee">
    <div class="cin-track">
      <span>SOBA</span><strong>ZERO WASTE</strong><span>•</span><span>SHOP SMALL</span><strong>MADE IN SRI LANKA</strong><span>•</span><span>PLASTIC AVOIDED</span><strong>SOBA</strong><span>•</span>
      <span>SOBA</span><strong>ZERO WASTE</strong><span>•</span><span>SHOP SMALL</span><strong>MADE IN SRI LANKA</strong><span>•</span><span>PLASTIC AVOIDED</span><strong>SOBA</strong><span>•</span>
    </div>
  </div>

  <section class="cin-manifesto">
    <div class="ey">01 / The Soba idea</div>
    <div class="cin-big">What you buy matters.<br><em>What you avoid</em> matters too.</div>
    <div class="cin-manifesto-grid">
      <p>We pair each product with the single-use plastic it replaces. So instead of an abstract “eco” claim, you get a number you can actually see, shop with and remember.</p>
      <div class="cin-linkrow"><div class="cin-circle-link" onclick="navTo('shop')">View<br>collection ↗</div></div>
    </div>
  </section>

  <section class="cin-cats" id="shop">
    <div class="cin-cats-head"><div class="small">02 / Shop by intention</div><h2>Choose<br>your swap.</h2></div>
    <div class="cat-stack">
    <div class="cat-row" onclick="setCat('kitchen')">
        <span class="no">01</span>
        <span class="name">Kitchen & Dining</span>
        <span class="desc">Coconut · bamboo · reusable</span>
    </div>
    <div class="cat-row" onclick="setCat('personal')">
        <span class="no">02</span>
        <span class="name">Personal Care</span>
        <span class="desc">Bars · refills · plant fibre</span>
    </div>
    <div class="cat-row" onclick="setCat('stationery')">
        <span class="no">03</span>
        <span class="name">Stationery</span>
        <span class="desc">Recycled · tree-free · plantable</span>
    </div>
    <div class="cat-row" onclick="setCat('Lifestyle')">
        <span class="no">04</span>
        <span class="name">Lifestyle</span>
        <span class="desc">Upcycled · cork · cotton</span>
    </div>
</div>
  </section>

  <section class="cin-selected">
    <div class="selected-head"><h2>Selected<br>objects.</h2><span>Handpicked from the catalogue</span></div>
    <div class="premium-products" id="cin-products"></div>
  </section>

  <section class="cin-final">
    <h2>MAKE A<br><span>BETTER SWAP.</span></h2>
    <div class="frow"><p>Less packaging. More intention.<br>Sri Lanka, one cart at a time.</p><button class="fbtn" onclick="navTo('shop')">Start shopping ↗</button></div>
  </section>
</section>

<section class="story-section band-sand reveal" id="about-section">
  <div class="blob blob-a"></div>
  <div class="story-head story-head-enhanced">
    <span class="eyebrow-pulse"></span>
    <span class="story-eyebrow">About Soba</span>
    <h2>From your cart to the coastline, in three honest steps.</h2>
  </div>
  <div class="zigzag reveal-stagger reveal">
    <div class="zigzag-row">
      <div class="zigzag-media"><span class="zigzag-num">01</span><div class="zigzag-icon-badge">🧴</div></div>
      <div class="zigzag-text">
        <span class="zigzag-tag">Step one</span>
        <h3>Pick a plastic to retire</h3>
        <p>Every product is paired with the single-use plastic it's built to replace — a bottle, a wrapper, a bag.</p>
      </div>
    </div>
    <div class="zigzag-row reverse">
      <div class="zigzag-media"><span class="zigzag-num">02</span><div class="zigzag-icon-badge">🫙</div></div>
      <div class="zigzag-text">
        <span class="zigzag-tag">Step two</span>
        <h3>Watch the jar fill</h3>
        <p>Add it to your cart and the grams count for real, right in the jar at the top of the page.</p>
      </div>
    </div>
    <div class="zigzag-row">
      <div class="zigzag-media"><span class="zigzag-num">03</span><div class="zigzag-icon-badge">📦</div></div>
      <div class="zigzag-text">
        <span class="zigzag-tag">Step three</span>
        <h3>It ships, and it lasts</h3>
        <p>Local artisans and small workshops pack every order — no plastic void-fill, ever.</p>
      </div>
    </div>
  </div>
</section>

<section class="story-section band-forest reveal">
  <div class="blob blob-c"></div>
  <div class="impact-strip" style="background:transparent; box-shadow:none; padding:0;">
    <div class="impact-stat"><div class="num mono" data-count="12">0</div><div class="lbl">Sri Lankan makers</div></div>
    <div class="impact-stat"><div class="num mono" data-count="2000" data-suffix="g+">0</div><div class="lbl">avoided per full jar</div></div>
    <div class="impact-stat"><div class="num mono" data-count="4">0</div><div class="lbl">product categories</div></div>
    <div class="impact-stat"><div class="num mono" data-count="0">0</div><div class="lbl">single-use void fill</div></div>
  </div>
</section>

<section class="story-section band-sand reveal reviews-section" id="reviews-section">
  <div class="blob blob-b"></div>
  <div class="story-head">
    <span class="story-eyebrow">What people say</span>
    <h2>Real feedback from people who made the switch.</h2>
  </div>
  <div class="quote-grid reveal-stagger reveal">
    <div class="quote-card">
      <div class="stars">★★★★★</div>
      <p>"Swapped my whole kitchen counter over one order at a time. The coconut bowls are my favourite thing I own now."</p>
      <div class="who">— Dilani K., Colombo</div>
    </div>
    <div class="quote-card">
      <div class="stars">★★★★☆</div>
      <p>"Loved seeing the jar fill up as I shopped — made the impact feel real instead of abstract."</p>
      <div class="who">— Ashan R., Kandy</div>
    </div>
    <div class="quote-card">
      <div class="stars">★★★★★</div>
      <p>"Beeswax wraps replaced an entire roll of cling film for us. Ordering a second set already."</p>
      <div class="who">— Ishara W., Galle</div>
    </div>
  </div>
</section>

<section class="cta-band reveal">
  <div class="cta-inner">
    <span class="cta-eyebrow">Ready when you are</span>
    <h2>Your first swap<br>could start today.</h2>
    <button class="btn-cta" onclick="navTo('shop')">Browse the catalogue →</button>
  </div>
  <div class="cta-marquee">
    <div class="cta-marquee-track">
      <span>ZERO WASTE</span><span>•</span><span>MADE IN SRI LANKA</span><span>•</span><span>SHOP SMALL</span><span>•</span>
      <span>ZERO WASTE</span><span>•</span><span>MADE IN SRI LANKA</span><span>•</span><span>SHOP SMALL</span><span>•</span>
    </div>
  </div>
</section>

</main>

<footer id="contact-section">
  <div class="footer-inner">
    <div class="footer-col footer-brand">
      <div class="logo" onclick="setCat('All')" style="cursor:pointer;">
        <span class="mark">Soba</span>
        <span class="sub">sobadahama · nature</span>
      </div>
      <p>A zero-waste marketplace pairing everyday products with the single-use plastic they replace — made with Sri Lankan makers.</p>
    </div>
    <div class="footer-col">
      <div class="footer-title">Shop</div>
      <a onclick="setCat('All')">All Products</a>
      <a onclick="setCat('Kitchenware')">Kitchenware</a>
      <a onclick="setCat('Personal Care')">Personal Care</a>
      <a onclick="setCat('Stationery')">Stationery</a>
      <a onclick="setCat('Accessories')">Accessories</a>
    </div>
    <div class="footer-col">
      <div class="footer-title">Company</div>
      <a onclick="navTo('about-section')">About</a>
      <a onclick="navTo('shop')">Our impact</a>
    </div>
    <div class="footer-col">
      <div class="footer-title">Contact</div>
      <a href="mailto:hello@soba.lk">✉️ hello@soba.lk</a>
      <a href="tel:+94112345678">📞 +94 11 234 5678</a>
      <span>📍 Colombo, Sri Lanka</span>
    </div>
  </div>
  <div class="footer-bottom"> Soba © 2026 · Sustainable choices. Better living. </div>
</footer>

<div class="overlay" id="overlay" onclick="closeCart()"></div>
<div class="drawer" id="drawer">
  <div class="drawer-head">
    <h2>Your cart</h2>
    <button class="drawer-close" onclick="closeCart()">✕</button>
  </div>
  <div class="drawer-body" id="drawer-body">
    <div class="drawer-empty">Your cart is empty. Add something that skips the plastic.</div>
  </div>
  <div class="drawer-foot" id="drawer-foot" style="display:none;">
    <div class="eco-strip">
      <div class="row"><span>Plastic avoided</span><span class="val mono" id="foot-grams">0 g</span></div>
      <div class="row"><span>Items diverted</span><span class="val mono" id="foot-items">0</span></div>
    </div>
    <div class="total-row"><span>Subtotal</span><span class="val" id="foot-total">Rs. 0</span></div>
    <label class="plant-row"><input type="checkbox" id="plant-tree"> Add Rs. 150 to plant a tree</label>
    <button class="checkout-btn" onclick="checkout()">Proceed to checkout</button>
  </div>
</div>





<!-- Product Details modal -->
<div class="modal-overlay" id="pd-overlay" onclick="if(event.target===this) closeProductDetail()">
  <div class="pd-card">
    <button class="auth-close" onclick="closeProductDetail()">✕</button>
    <div class="pd-grid">
      <div class="pd-art" id="pd-art"></div>
      <div class="pd-info">
        <span class="pd-cert" id="pd-cert"></span>
        <h2 id="pd-name"></h2>
        <div class="pd-cat" id="pd-cat"></div>
        <p class="pd-desc" id="pd-desc"></p>
        <div class="pd-eco">🌱 <span id="pd-saved"></span> of single-use plastic avoided per unit</div>
        <div class="pd-price-row">
          <span class="pd-price" id="pd-price"></span>
          <div class="pd-qty">
            <button class="pd-qty-btn" onclick="pdChangeQty(-1)">−</button>
            <span class="pd-qty-num" id="pd-qty">1</span>
            <button class="pd-qty-btn" onclick="pdChangeQty(1)">+</button>
          </div>
        </div>
        <button class="pd-add-btn" id="pd-add-btn" onclick="pdAddToCart()">Add to cart</button>
        <div class="pd-reviews-title">Customer reviews</div>
        <div id="pd-reviews"></div>
      </div>
    </div>
  </div>
</div>

<!-- PayHere checkout modal -->
<div class="modal-overlay" id="pay-overlay" onclick="if(event.target===this) closePayHere()">
  <div class="pay-card">
    <div class="pay-head">
      <div class="brand">💳 PayHere Checkout</div>
      <span class="lock">🔒 Secured</span>
    </div>
    <div class="pay-body">
      <div class="pay-summary" id="pay-summary"></div>
      <div class="pay-method-row">
        <div class="pay-method active" onclick="selectPayMethod(this)">Card</div>
        <div class="pay-method" onclick="selectPayMethod(this)">Bank</div>
        <div class="pay-method" onclick="selectPayMethod(this)">Cash on Delivery</div>
      </div>
      <label class="field-label">Card number</label>
      <input class="field-input" placeholder="4111 1111 1111 1111" maxlength="19">
      <label class="field-label">Delivery address</label>
      <input class="field-input" id="pay-address" placeholder="Your address">
      <button class="pay-submit" onclick="completePayment()">Pay &amp; verify with PayHere</button>
      <div class="pay-foot-note">Mock gateway for this prototype — no real card data is sent.</div>
    </div>
  </div>
</div>

<script>
const dbProductsFromPhp = <?php echo json_encode($dbProducts ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
const fallbackProducts = [
  {id:1, name:"Coconut Shell Bowl Set", cat:"Kitchenware", material:"Coconut Shell", icon:"🥥", price:2450, saved:340, cert:"Handmade LK", desc:"A set of four bowls carved from reclaimed coconut shells by artisans in Matara. Each piece is sanded smooth and sealed with food-safe coconut oil — no two bowls are identical.", images:["https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=800&q=80"]},
  {id:2, name:"Bamboo Cutlery Travel Kit", cat:"Kitchenware", material:"Bamboo", icon:"🥢", price:1200, saved:85, cert:"FSC Bamboo", desc:"A fork, knife, spoon, and chopsticks in a cotton roll pouch. Replaces disposable cutlery on every takeaway order.", images:["https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?auto=format&fit=crop&w=800&q=80"]},
  {id:3, name:"Beeswax Food Wraps (3pk)", cat:"Kitchenware", material:"Cotton & Beeswax", icon:"🍯", price:1650, saved:160, cert:"Organic", desc:"Cotton cloth infused with beeswax, tree resin, and jojoba oil. Warm it in your hands to wrap leftovers instead of reaching for cling film.", images:["https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=800&q=80"]},
  {id:4, name:"Loofah Dish Sponge", cat:"Kitchenware", material:"Plant Fibre", icon:"🧽", price:450, saved:60, cert:"Compostable", desc:"Grown, not manufactured. This natural loofah scrubs pots clean and composts fully at the end of its life.", images:["https://images.unsplash.com/photo-1585751119414-ef2636f8aede?auto=format&fit=crop&w=800&q=80"]},
  {id:5, name:"Shampoo Bar — Neem & Lime", cat:"Personal Care", material:"Plant Fibre", icon:"🧼", price:890, saved:210, cert:"Vegan", desc:"A concentrated bar that replaces roughly three plastic shampoo bottles. Lathers well for all hair types.", images:["https://images.unsplash.com/photo-1608248597359-0021b369c0d3?auto=format&fit=crop&w=800&q=80"]},
  {id:6, name:"Bamboo Toothbrush (4pk)", cat:"Personal Care", material:"Bamboo", icon:"🪥", price:760, saved:120, cert:"FSC Bamboo", desc:"Soft-bristle brushes with handles from sustainably farmed bamboo. A family pack for a full plastic-free bathroom.", images:["https://images.unsplash.com/photo-1607613009820-a29f7bb81c04?auto=format&fit=crop&w=800&q=80"]},
  {id:7, name:"Reef-Safe Solid Sunscreen", cat:"Personal Care", material:"Plant Fibre", icon:"☀️", price:1350, saved:95, cert:"Reef-Safe", desc:"Mineral SPF 30 in a compostable paper tube — no plastic bottle, and no oxybenzone to harm coral reefs.", images:["https://images.unsplash.com/photo-1602143407151-7111542de6e8?auto=format&fit=crop&w=800&q=80"]},
  {id:8, name:"Refillable Deodorant Case", cat:"Personal Care", material:"Metal & Paper", icon:"🧴", price:1050, saved:130, cert:"Zero-Waste", desc:"A metal case that takes compostable cardboard refills, so you throw away the paper insert, never the case.", images:["https://images.unsplash.com/photo-1585751119414-ef2636f8aede?auto=format&fit=crop&w=800&q=80"]},
  {id:9, name:"Handmade Paper Notebook", cat:"Stationery", material:"Recycled Paper", icon:"📓", price:680, saved:40, cert:"Tree-Free", desc:"Pages pressed from elephant dung fibre, a Sri Lankan craft that keeps forests standing and pulp mills idle.", images:["https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=800&q=80"]},
  {id:10, name:"Seed Pencils (5pk)", cat:"Stationery", material:"Recycled Paper", icon:"✏️", price:520, saved:55, cert:"Plantable", desc:"Write until the pencil is a stub, then plant the capsule end — each one grows into an herb or flower.", images:["https://images.unsplash.com/photo-1585336261026-7f573f055375?auto=format&fit=crop&w=800&q=80"]}
];

let PRODUCTS = (Array.isArray(dbProductsFromPhp) && dbProductsFromPhp.length > 0) ? dbProductsFromPhp : fallbackProducts;

const REVIEWS = {
  1:[{name:"Dilani K.", stars:5, text:"Beautiful finish and genuinely useful — using it for rice every day."},{name:"Ashan R.", stars:4, text:"Lovely bowls, slightly smaller than I expected but great quality."}],
  2:[{name:"Sanduni P.", stars:5, text:"Keeps in my bag for every food delivery order now."}],
  3:[{name:"Rukshan F.", stars:5, text:"Holds shape well and smells faintly of honey — nice touch."},{name:"Ishara W.", stars:4, text:"Works great for cheese and bread, less so for very wet leftovers."}],
  5:[{name:"Nadeesha G.", stars:5, text:"Lasted me almost 3 months of daily washing."}],
  6:[{name:"Kasun L.", stars:4, text:"Bristles are a touch firm but the handle feels sturdy."}],
  11:[{name:"Hasini D.", stars:5, text:"Fits a full week of vegetables without straining at the seams."}],
  12:[{name:"Tharindu S.", stars:5, text:"Grip is better than my old PVC mat, especially in hot weather."},{name:"Malsha J.", stars:4, text:"A bit heavier to carry than foam mats, but worth it."}],
};

let activeCat = "All";
let activeMaterial = "All";
let cart = {}; // id -> qty
let wishlist = new Set(); // session-only favorites

// Sync cart from shared localStorage so cart persists across home and shop pages
try {
  const storedCart = JSON.parse(localStorage.getItem('soba_cart') || '[]');
  if (Array.isArray(storedCart)) {
    storedCart.forEach(item => {
      if (item && item.id) cart[item.id] = (cart[item.id] || 0) + (item.quantity || 1);
    });
  }
} catch(e) {}


function renderFilters(){
  const materials = ["All", ...new Set(PRODUCTS.map(p=>p.material))];
  const chipsEl = document.getElementById('material-chips');
  if(chipsEl) chipsEl.innerHTML = materials.map(m =>
    `<button class="chip ${m===activeMaterial?'active':''}" onclick="setMaterial('${m}')">${m}</button>`
  ).join('');

  // Update feature-icon row active state (Ecova-style highlighted tile)
  document.querySelectorAll('.feature-tile').forEach(tile => {
    tile.classList.toggle('active', tile.getAttribute('data-cat') === activeCat);
  });
}

function goToSearch() {
  const q = document.getElementById('search-input')?.value.trim() || '';
  window.location.href = q 
    ? `modules/customer/products.php?search=${encodeURIComponent(q)}` 
    : 'modules/customer/products.php';
}

function setCat(c) {
  const categoryName = (c === 'All') ? '' : c;
  
  // Folder structure එකට ගැලපෙන විදිහට products.php එකට category එක යවනවා
  const url = categoryName 
    ? `modules/customer/products.php?category=${encodeURIComponent(categoryName)}` 
    : `modules/customer/products.php`;
    
  window.location.href = url;
}

function setMaterial(m){ activeMaterial = m; renderFilters(); renderGrid(); }

function matchesFilters(p){
  if(activeCat!=="All" && p.cat!==activeCat) return false;
  if(activeMaterial!=="All" && p.material!==activeMaterial) return false;

  const priceRange = document.getElementById('price-filter')?.value || 'all';
  if(priceRange!=='all'){
    const [lo,hi] = priceRange.split('-').map(Number);
    if(p.price < lo || p.price > hi) return false;
  }
  const savedRange = document.getElementById('saved-filter')?.value || 'all';
  if(savedRange!=='all'){
    const [lo,hi] = savedRange.split('-').map(Number);
    if(p.saved < lo || p.saved > hi) return false;
  }
  const q = document.getElementById('search-input')?.value.trim().toLowerCase() || '';
  if(q && !(p.name.toLowerCase().includes(q) || p.cat.toLowerCase().includes(q) || p.material.toLowerCase().includes(q))) return false;
  return true;
}


let currentUser = <?php echo json_encode($sessionUser, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
if (!currentUser) {
  try { currentUser = JSON.parse(localStorage.getItem('soba_user') || 'null'); } catch(e) { currentUser = null; }
}
let lifetimeGrams = 0;
let orderCount = 0;
let orders = []; // {id, date, items, total, grams, status}
const STATUS_STEPS = ["Pending","Processing","Shipped","Delivered"];
const LEVELS = [
  {name:"Eco Starter", ico:"🌱", min:0},
  {name:"Eco Explorer", ico:"🌱", min:200},
  {name:"Zero-Waste Warrior", ico:"🌿", min:700},
  {name:"Planet Guardian", ico:"🌳", min:1500},
];

function renderGrid(){
  const gridEl = document.getElementById('product-grid');
  if(!gridEl) return;
  const list = PRODUCTS.filter(matchesFilters);
  const resultCountEl = document.getElementById('result-count');
  if(resultCountEl) resultCountEl.textContent = list.length + " items";
  const catHeadEl = document.getElementById('category-head-title');
  gridEl.innerHTML = list.length ? list.map((p,idx) => {
    const primaryImg = (p.images && p.images.length > 0) ? p.images[0] : (p.image || null);
    const artHtml = primaryImg
      ? `<img src="${primaryImg}" alt="${p.name}" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';"><span style="display:none;">${p.icon || '🌱'}</span>`
      : `<span>${p.icon || '🌱'}</span>`;

    return `
    <div class="card" onclick="goToProductDetails(${p.id})" style="cursor:pointer; --i:${idx};">
      <div class="card-art">
        <span class="card-cert">${p.cert}</span>
        <button class="card-wish ${wishlist.has(p.id)?'active':''}" onclick="event.stopPropagation(); toggleWish(${p.id})">${wishlist.has(p.id) ? '♥' : '♡'}</button>
        ${artHtml}
      </div>
      <div class="card-body">
        <h3>${p.name}</h3>
        <div class="card-meta">${p.cat} · ${p.material}</div>
        <div class="card-saved">🌱 <span class="mono">${p.saved}g</span> plastic saved</div>
        <div class="card-foot">
          <span class="price">Rs. ${p.price.toLocaleString()}</span>
          <button class="add-btn" id="btn-${p.id}" onclick="event.stopPropagation(); addToCart(${p.id})">Add to cart</button>
        </div>
      </div>
    </div>`;
  }).join('') : `<div class="orders-empty" style="grid-column:1/-1; text-align:center; padding:40px 0;">No products match — try clearing a filter or search term.</div>`;
}

function toggleWish(id){
  if(wishlist.has(id)) wishlist.delete(id); else wishlist.add(id);
  renderGrid();
  const badge = document.getElementById('wish-badge');
  if(badge){
    badge.textContent = wishlist.size;
    badge.classList.toggle('show', wishlist.size > 0);
  }
  const wishBtn = document.getElementById('wish-btn');
  if(wishBtn){ wishBtn.classList.remove('bump'); void wishBtn.offsetWidth; wishBtn.classList.add('bump'); }
}
function toggleWishPanel(){
  if(wishlist.size === 0){ document.getElementById('shop')?.scrollIntoView({behavior:'smooth'}); return; }
  activeMaterial = "All";
  document.getElementById('shop')?.scrollIntoView({behavior:'smooth'});
}

function addToCart(id){
  cart[id] = (cart[id]||0) + 1;
  const btn = document.getElementById('btn-'+id);
  if(btn){
    btn.textContent = "Added ✓";
    btn.classList.add('added');
    setTimeout(()=>{ btn.textContent="Add to cart"; btn.classList.remove('added'); }, 900);
  }
  const cartBtn = document.getElementById('cart-btn');
  if(cartBtn){ cartBtn.classList.remove('bump'); void cartBtn.offsetWidth; cartBtn.classList.add('bump'); }
  updateAll();
}
function changeQty(id, delta){
  cart[id] = (cart[id]||0) + delta;
  if(cart[id]<=0) delete cart[id];
  updateAll();
}
function removeItem(id){ delete cart[id]; updateAll(); }

function cartStats(){
  let items=0, grams=0, total=0;
  for(const id in cart){
    const p = PRODUCTS.find(p=>p.id==id);
    if(!p) continue;
    const q = cart[id];
    items += q; grams += p.saved*q; total += p.price*q;
  }
  return {items, grams, total};
}

function badgeFor(grams){
  return levelFor(grams).name;
}

function updateAll(){
  const {items, grams, total} = cartStats();
  const cartCountEl = document.getElementById('cart-count');
  if(cartCountEl) cartCountEl.textContent = items;
  const gramsEl = document.getElementById('grams-saved');
  if(gramsEl) gramsEl.textContent = grams.toLocaleString();

  // Save cart state into shared localStorage so cart page stays in sync
  try {
    const list = [];
    for (const id in cart) {
      const p = PRODUCTS.find(x => x.id == id);
      if (p) {
        list.push({ ...p, quantity: cart[id], selected: true });
      }
    }
    localStorage.setItem('soba_cart', JSON.stringify(list));
  } catch(e) {}

  // jar fill: cap visual at 2000g for full jar
  const pct = Math.min(grams / 2000, 1);
  const jarHeight = 228;
  const h = jarHeight * pct;
  const liquid = document.getElementById('jarLiquid');
  const top = document.getElementById('jarLiquidTop');
  if(liquid && top){
    liquid.setAttribute('y', 246 - h);
    liquid.setAttribute('height', h);
    top.setAttribute('y', 246 - h - 4);
  }

  renderDrawer(items, grams, total);
}

function renderDrawer(items, grams, total){
  const body = document.getElementById('drawer-body');
  const foot = document.getElementById('drawer-foot');
  if(!body || !foot) return;
  if(items===0){
    body.innerHTML = `<div class="drawer-empty">Your cart is empty. Add something that skips the plastic.</div>`;
    foot.style.display = 'none';
    return;
  }
  foot.style.display = 'block';
  body.innerHTML = Object.keys(cart).map(id=>{
    const p = PRODUCTS.find(p=>p.id==id);
    if (!p) return '';
    const q = cart[id];
    const pImg = (p.images && p.images.length > 0) ? p.images[0] : (p.image || null);
    const artContent = pImg 
      ? `<img src="${pImg}" alt="${p.name}" style="width:100%; height:100%; object-fit:cover; border-radius:8px;" onerror="this.parentElement.textContent='${p.icon || '🌱'}';">`
      : `<span>${p.icon || '🌱'}</span>`;

    return `
      <div class="cart-item">
        <div class="art" style="overflow:hidden; display:flex; align-items:center; justify-content:center;">${artContent}</div>
        <div class="info">
          <h4>${p.name}</h4>
          <div class="sub">Rs. ${p.price.toLocaleString()} · ${p.saved}g saved each</div>
          <div class="qty-row">
            <button class="qty-btn" onclick="changeQty(${p.id},-1)">−</button>
            <span class="qty-num">${q}</span>
            <button class="qty-btn" onclick="changeQty(${p.id},1)">+</button>
            <button class="remove-x" onclick="removeItem(${p.id})">remove</button>
          </div>
        </div>
      </div>`;
  }).join('');

  document.getElementById('foot-grams').textContent = grams.toLocaleString()+" g";
  document.getElementById('foot-items').textContent = items;
  document.getElementById('foot-total').textContent = "Rs. "+total.toLocaleString();
}

function openCart(){
  document.getElementById('drawer')?.classList.add('open');
  document.getElementById('overlay')?.classList.add('open');
}
function closeCart(){
  document.getElementById('drawer')?.classList.remove('open');
  document.getElementById('overlay')?.classList.remove('open');
}
function checkout(){
  const {items} = cartStats();
  if(items===0) return;

  if(!currentUser){
    closeCart();
    window.location.href = 'login.php';
    return;
  }

  closeCart();
  openPayHere();
}

/* ===== PayHere payment ===== */
function openPayHere(){
  const {items, total, grams} = cartStats();
  const tree = document.getElementById('plant-tree')?.checked ? 150 : 0;
  document.getElementById('pay-summary').innerHTML = `
    <div class="row"><span>Items</span><span class="v">${items}</span></div>
    <div class="row"><span>Plastic avoided</span><span class="v">${grams.toLocaleString()} g</span></div>
    ${tree ? `<div class="row"><span>Plant-a-tree add-on</span><span class="v">Rs. 150</span></div>` : ''}
    <div class="row"><span>Total to pay</span><span class="v">Rs. ${(total+tree).toLocaleString()}</span></div>
  `;
  const addr = document.getElementById('pay-address');
  if(addr) addr.value = "";
  document.getElementById('pay-overlay')?.classList.add('open');
}
function closePayHere(){
  document.getElementById('pay-overlay')?.classList.remove('open');
}
function selectPayMethod(el){
  document.querySelectorAll('.pay-method').forEach(m=>m.classList.remove('active'));
  el.classList.add('active');
}
function completePayment(){
  const address = document.getElementById('pay-address')?.value.trim();
  if(!address){ sobaToast("Please add a delivery address.", true); return; }
  const {items,total,grams} = cartStats();
  const tree = document.getElementById('plant-tree')?.checked ? 150 : 0;

  const order = {
    id: "SOBA-" + Math.floor(10000 + Math.random()*89999),
    date: new Date().toLocaleDateString('en-GB', {day:'2-digit', month:'short', year:'numeric'}),
    items: Object.keys(cart).map(id=>{
      const p = PRODUCTS.find(p=>p.id==id);
      return {name:p.name, qty:cart[id]};
    }),
    total: total + tree,
    grams,
    address,
    status: STATUS_STEPS[0],
  };
  orders.unshift(order);
  if(!currentUser.addresses) currentUser.addresses = [];
  if(!currentUser.addresses.includes(address)) currentUser.addresses.unshift(address);

  closePayHere();
  lifetimeGrams += grams;
  orderCount += 1;
  cart = {};
  updateAll();
  openConfirmation(order);
}

/* ===== Order confirmation ===== */
function openConfirmation(order){
  const sub = document.getElementById('conf-sub');
  if(sub) sub.textContent = `You just saved ${order.grams.toLocaleString()}g of plastic!`;
  const invoice = document.getElementById('conf-invoice');
  if(invoice){
    invoice.innerHTML = `
      <div class="oid">Order ${order.id} · ${order.date}</div>
      ${order.items.map(i => `<div class="item-row"><span>${i.name} × ${i.qty}</span></div>`).join('')}
      <div class="item-row total"><span>Total paid</span><span class="v">Rs. ${order.total.toLocaleString()}</span></div>
    `;
  }
  document.getElementById('conf-overlay')?.classList.add('open');
}
function closeConfirmation(){
  document.getElementById('conf-overlay')?.classList.remove('open');
}

/* Add the selected product to the existing shared cart, then open the existing cart page. */
function addSelectedToCartAndGo(id) {
  addToCart(id); // updateAll() saves to localStorage key 'soba_cart'
  window.location.href = 'modules/customer/cart.php';
}

/* Open the EXISTING customer product details page. No new page is created. */
function goToProductDetails(id) {
  window.location.href = 'modules/customer/product_details.php?id=' + encodeURIComponent(id);
}

/* ===== Product Detail modal ===== */
let pdProduct = null, pdQty = 1;
function openProductDetail(id){
  pdProduct = PRODUCTS.find(p=>p.id===id);
  pdQty = 1;
  const pdArtEl = document.getElementById('pd-art');
  const pdImg = (pdProduct.images && pdProduct.images.length > 0) ? pdProduct.images[0] : (pdProduct.image || null);
  if (pdImg) {
    pdArtEl.innerHTML = `<img src="${pdImg}" alt="${pdProduct.name}" style="width:100%; height:100%; object-fit:cover; border-radius:12px;" onerror="this.parentElement.textContent='${pdProduct.icon || '🌱'}'">`;
  } else {
    pdArtEl.textContent = pdProduct.icon || '🌱';
  }
  document.getElementById('pd-cert').textContent = pdProduct.cert;
  document.getElementById('pd-name').textContent = pdProduct.name;
  document.getElementById('pd-cat').textContent = pdProduct.cat;
  document.getElementById('pd-desc').textContent = pdProduct.desc;
  document.getElementById('pd-saved').textContent = pdProduct.saved + "g";
  document.getElementById('pd-price').textContent = "Rs. " + pdProduct.price.toLocaleString();
  document.getElementById('pd-qty').textContent = pdQty;
  const revs = REVIEWS[id] || [];
  document.getElementById('pd-reviews').innerHTML = revs.length ? revs.map(r => `
    <div class="review-item">
      <div class="stars">${'★'.repeat(r.stars)}${'☆'.repeat(5-r.stars)}</div>
      <div class="who">${r.name}</div>
      <div class="txt">${r.text}</div>
    </div>
  `).join('') : `<div class="review-empty">No reviews yet — verified buyers can be the first.</div>`;
  document.getElementById('pd-overlay')?.classList.add('open');
}
function closeProductDetail(){
  document.getElementById('pd-overlay')?.classList.remove('open');
}
function pdChangeQty(delta){
  pdQty = Math.max(1, pdQty + delta);
  document.getElementById('pd-qty').textContent = pdQty;
}
function pdAddToCart(){
  cart[pdProduct.id] = (cart[pdProduct.id]||0) + pdQty;
  updateAll();
  const btn = document.getElementById('pd-add-btn');
  btn.textContent = "Added ✓";
  setTimeout(()=>{ btn.textContent = "Add to cart"; closeProductDetail(); }, 700);
}

/* ===== Auth & Navigation ===== */
function openAuth(){
  window.location.href = 'login.php';
}
function completeSignIn(name, email){
  currentUser = {name, email};
  try { localStorage.setItem('soba_user', JSON.stringify(currentUser)); } catch(e) {}
  updateAccountUI();
  updateAll();
  const {items} = cartStats();
  if(items > 0) openPayHere();
}
function signOut(){
  currentUser = null;
  try { localStorage.removeItem('soba_user'); } catch(e) {}
  closeProfile();
  updateAccountUI();
}
function handleAccountClick(){
  if (currentUser) {
    if (currentUser.role === 'admin') {
      window.location.href = 'modules/admin/dashboard.php';
    } else {
      window.location.href = 'modules/customer/user_dashboard.php';
    }
  } else {
    window.location.href = 'login.php';
  }
}
function updateAccountUI(){
  const avatar = document.getElementById('account-avatar');
  const label = document.getElementById('account-label');
  const helloEl = document.querySelector('#account-btn .hello');
  if(currentUser){
    avatar?.classList.add('signed-in');
    if(avatar) avatar.textContent = currentUser.name.charAt(0).toUpperCase();
    if(label) label.textContent = currentUser.name.split(' ')[0];
    if(helloEl) helloEl.textContent = "Hello, " + currentUser.name.split(' ')[0];
  } else {
    avatar?.classList.remove('signed-in');
    if(avatar) avatar.textContent = "👤";
    if(label) label.textContent = "Sign in";
    if(helloEl) helloEl.textContent = "Hello, sign in";
  }
}

/* ===== Profile drawer ===== */
function openProfile(){
  renderProfile();
  document.getElementById('profile-drawer')?.classList.add('open');
  document.getElementById('profile-overlay')?.classList.add('open');
}
function closeProfile(){
  document.getElementById('profile-drawer')?.classList.remove('open');
  document.getElementById('profile-overlay')?.classList.remove('open');
}
function levelFor(grams){
  let level = LEVELS[0];
  for(const l of LEVELS){ if(grams >= l.min) level = l; }
  return level;
}
function renderProfile(){
  if(!currentUser) return;
  document.getElementById('profile-avatar').textContent = currentUser.name.charAt(0).toUpperCase();
  document.getElementById('profile-name').textContent = currentUser.name;
  document.getElementById('profile-email').textContent = currentUser.email;
  document.getElementById('profile-grams').textContent = lifetimeGrams.toLocaleString()+"g";
  document.getElementById('profile-orders').textContent = orderCount;

  const current = levelFor(lifetimeGrams);
  document.getElementById('badge-track').innerHTML = LEVELS.map(l => `
    <div class="badge-tile ${l.min <= lifetimeGrams ? 'reached':''}">
      <span class="ico">${l.ico}</span>${l.name}
    </div>
  `).join('');

  const addrs = currentUser.addresses || [];
  document.getElementById('addr-list').innerHTML = addrs.length ? addrs.map((a,i) => `
    <div class="addr-card"><div class="tag">${i===0 ? 'Default' : 'Saved'}</div>${a}</div>
  `).join('') : `<div class="orders-empty">No saved addresses yet — they're added at checkout.</div>`;

  document.getElementById('order-list').innerHTML = orders.length ? orders.map(o => {
    const curIdx = STATUS_STEPS.indexOf(o.status);
    return `
    <div class="order-item">
      <div class="top-row">
        <span class="oid">${o.id} · ${o.date}</span>
        <span class="amt">Rs. ${o.total.toLocaleString()}</span>
      </div>
      <div style="font-size:12px; color:rgba(43,45,47,0.6);">${o.items.map(i=>i.name+' ×'+i.qty).join(', ')}</div>
      <div class="status-track">
        ${STATUS_STEPS.map((s,i) => `<span class="status-step ${i<curIdx?'done':''} ${i===curIdx?'current':''}">${s}</span>`).join('')}
      </div>
    </div>`;
  }).join('') : `<div class="orders-empty">No orders yet — your first order will show up here.</div>`;
}

renderFilters();
renderGrid();
updateAccountUI();
updateAll();

// Load live products from database
async function loadHomeProductsFromDB() {
  try {
    const res = await fetch('/php/web/Soba-Project/backend/api/customer/get_products.php');
    const data = await res.json();
    if (data && data.success && Array.isArray(data.products) && data.products.length > 0) {
      PRODUCTS = data.products.map(p => ({
        id: p.id,
        name: p.name,
        cat: p.category_name || p.category || 'Eco Products',
        material: p.material || 'Sustainable Material',
        icon: p.icon || '🌱',
        price: p.price,
        saved: p.plastic_saved_g || 50,
        cert: p.cert || 'Zero-Waste',
        desc: p.description || 'Eco-friendly certified sustainable product.',
        images: p.images || []
      }));
      renderFilters();
      renderGrid();
      updateAll();
    }
  } catch(e) {
    console.warn("DB product fetch fallback in home.php", e);
  }
}
loadHomeProductsFromDB();

/* =====================================================================
   RESPONSIVE DYNAMIC NAVBAR UNDERLINE (Tracks Hover, Click & Scroll)
   ===================================================================== */
const navSectionMap = [
  { id: 'home-top', nav: 'home' },
  { id: 'shop', nav: 'shop' },
  { id: 'about-section', nav: 'about' },
  { id: 'reviews-section', nav: 'reviews' },
  { id: 'contact-section', nav: 'contact' }
];

let isProgrammaticScroll = false;
let scrollTimer = null;

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

  // Responsive mobile sync: keeps active item centered inside horizontal scroll
  const navBar = document.querySelector('.header-nav .bar');
  if (navBar && navBar.scrollWidth > navBar.clientWidth) {
    const center = left + width / 2 - navBar.clientWidth / 2;
    navBar.scrollTo({ left: Math.max(0, center), behavior: isSmooth ? 'smooth' : 'auto' });
  }
}

function setActiveNav(navKey, moveLine = true) {
  const links = document.querySelectorAll('#navbar .nav-link');
  let activeLink = null;
  links.forEach(a => {
    if (a.dataset.nav === navKey) {
      a.classList.add('active');
      activeLink = a;
    } else {
      a.classList.remove('active');
    }
  });
  if (moveLine && activeLink) {
    moveNavIndicator(activeLink, true);
  }
}

function navTo(targetId) {
  const sec = document.getElementById(targetId);
  if (!sec) return;

  const found = navSectionMap.find(item => item.id === targetId);
  if (found) {
    setActiveNav(found.nav, true);
  }

  isProgrammaticScroll = true;
  clearTimeout(scrollTimer);

  sec.scrollIntoView({ behavior: 'smooth' });

  scrollTimer = setTimeout(() => {
    isProgrammaticScroll = false;
    moveNavIndicator(null, true);
  }, 850);
}

// Hover event: line glides to whichever item you hover on!
document.querySelectorAll('#navbar .nav-link').forEach(link => {
  link.addEventListener('mouseenter', () => {
    moveNavIndicator(link, true);
  });
});

// Mouse leaves navbar: line returns smoothly to active link!
document.getElementById('navbar')?.addEventListener('mouseleave', () => {
  const activeLink = document.querySelector('#navbar .nav-link.active');
  moveNavIndicator(activeLink, true);
});

// Responsive resize listener: recalculates position dynamically on any screen width!
window.addEventListener('resize', () => moveNavIndicator(null, false));
window.addEventListener('orientationchange', () => setTimeout(() => moveNavIndicator(null, false), 150));

/* ===== Header Shrink + Scroll Spy ===== */
(function(){
  function update(){
    const siteHeader = document.getElementById('site-header');
    if(siteHeader) siteHeader.classList.toggle('shrink', window.scrollY > 40);

    if (isProgrammaticScroll) return;

    const headerHeight = siteHeader?.offsetHeight || 80;
    const scrollPos = window.scrollY + headerHeight + 50;

    // Contact (bottom of page)
    if ((window.innerHeight + window.scrollY) >= document.documentElement.scrollHeight - 50) {
      setActiveNav('contact', true);
      return;
    }

    // Check visible section backwards
    for (let i = navSectionMap.length - 1; i >= 0; i--) {
      const section = document.getElementById(navSectionMap[i].id);
      if (section && section.offsetTop <= scrollPos) {
        setActiveNav(navSectionMap[i].nav, true);
        return;
      }
    }
    setActiveNav('home', true);
  }
  window.addEventListener('scroll', update, {passive:true});
  update();
})();

/* ===== Ambient spotlight follows cursor disabled ===== */

/* ===== Scroll-reveal for below-the-fold sections ===== */
const revealObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if(entry.isIntersecting){
      entry.target.classList.add('in-view');
      revealObserver.unobserve(entry.target);
    }
  });
}, {threshold:0, rootMargin:"0px 0px -10% 0px"});
document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));
window.addEventListener('load', () => {
  setTimeout(() => {
    document.querySelectorAll('.reveal:not(.in-view)').forEach(el => el.classList.add('in-view'));
  }, 1200);
});

/* ===== Subtle tilt on feature tiles + step / zigzag cards ===== */
function addTilt(selector, strength){
  document.querySelectorAll(selector).forEach(card => {
    card.addEventListener('mousemove', (e) => {
      const r = card.getBoundingClientRect();
      const px = (e.clientX - r.left) / r.width - 0.5;
      const py = (e.clientY - r.top) / r.height - 0.5;
      card.style.transform = `perspective(600px) rotateX(${-py*strength}deg) rotateY(${px*strength}deg) translateY(-4px)`;
    });
    card.addEventListener('mouseleave', () => { card.style.transform = ''; });
  });
}
addTilt('.feature-tile', 8);
addTilt('.step-card', 4);

// ===== Cinematic home interactions =====
(function(){
  const featured=[1,3,5];
  const holder=document.getElementById('cin-products');
  if(holder && typeof PRODUCTS!=='undefined' && PRODUCTS.length){
    // pick either featured IDs or first 3 products
    const displayList = PRODUCTS.filter(x => featured.includes(x.id)).length >= 3 
      ? PRODUCTS.filter(x => featured.includes(x.id)) 
      : PRODUCTS.slice(0, 3);
    holder.innerHTML = displayList.map(p => {
      const pImg = (p.images && p.images.length > 0) ? p.images[0] : (p.image || null);
      const artHtml = pImg 
        ? `<img src="${pImg}" alt="${p.name}" style="width:100%;height:100%;object-fit:cover;" onerror="this.style.display='none';this.nextElementSibling.style.display='inline-block';"><span style="display:none;font-size:3.5rem;">${p.icon || '🌱'}</span>` 
        : `<span>${p.icon || '🌱'}</span>`;
      return `<div class="card" onclick="goToProductDetails(${p.id})">
        <div class="card-art" style="overflow:hidden;position:relative;">${artHtml}</div>
        <div class="card-body">
          <h3>${p.name}</h3>
          <div class="card-meta">${p.cat || 'Eco'} · ${p.material || 'Sustainable'}</div>
          <div class="card-foot"><span class="price">Rs. ${(p.price || 0).toLocaleString()}</span><button class="add-btn" onclick="event.stopPropagation();addSelectedToCartAndGo(${p.id})">Add to cart</button></div>
        </div></div>`;
    }).join('');
  }
  const p=document.getElementById('eco-planet'), stage=document.getElementById('cin-stage');
  if(stage && p && matchMedia('(pointer:fine)').matches){
    stage.addEventListener('mousemove',e=>{
      const r=stage.getBoundingClientRect(), x=(e.clientX-r.left)/r.width-.5, y=(e.clientY-r.top)/r.height-.5;
      p.style.transform=`translate3d(${x*18}px,${y*12}px,0) rotateY(${x*8}deg) rotateX(${-y*6}deg)`;
    });
    stage.addEventListener('mouseleave',()=>p.style.transform='');
  }
})();

/* ===== FINAL STORY OBSERVER ===== */
(function(){
  const targets=[
    document.getElementById('about-section'),
    document.querySelector('.band-forest'),
    document.getElementById('reviews-section')
  ].filter(Boolean);
  const play=(el)=>el.classList.add('motion-play');
  if(!('IntersectionObserver' in window)){
    targets.forEach(play);
    return;
  }
  const io=new IntersectionObserver((entries)=>{
    entries.forEach(entry=>{
      if(entry.isIntersecting){
        play(entry.target);
        io.unobserve(entry.target);
      }
    });
  },{threshold:.08,rootMargin:'0px 0px -5% 0px'});
  targets.forEach(el=>io.observe(el));
})();

/* ===== FINAL LIVE COUNTER ENGINE ===== */
(function(){
  const stats=[...document.querySelectorAll('.band-forest [data-count]')];
  const reduced=matchMedia('(prefers-reduced-motion: reduce)').matches;
  const running=new WeakSet();
  function finish(el){
    const target=Number(el.dataset.count)||0;
    const suffix=el.dataset.suffix||'';
    el.textContent=target.toLocaleString()+suffix;
  }
  function count(el){
    if(running.has(el)) return;
    running.add(el);
    const target=Number(el.dataset.count)||0, suffix=el.dataset.suffix||'';
    if(reduced){ finish(el); return; }
    const startValue=0, duration=1700, start=performance.now();
    function frame(now){
      const p=Math.min(1,(now-start)/duration);
      const eased=1-Math.pow(1-p,4);
      el.textContent=Math.floor(startValue+(target-startValue)*eased).toLocaleString()+suffix;
      if(p<1) requestAnimationFrame(frame); else { finish(el); el.classList.add('counted'); }
    }
    requestAnimationFrame(frame);
  }
  if(!('IntersectionObserver' in window)) stats.forEach(finish);
  else {
    const io=new IntersectionObserver(entries=>entries.forEach(entry=>{
      if(entry.isIntersecting){ count(entry.target); io.unobserve(entry.target); }
    }),{threshold:.1});
    stats.forEach(el=>io.observe(el));
    // Do not complete the counters before the user scrolls to this section.
    // IntersectionObserver starts them only when visible.
  }
})();


/* Phone/tablet: trigger animations per item, not on the whole section. */
(function(){
  if (!matchMedia('(max-width:900px)').matches) return;
  const targets = document.querySelectorAll(
    '#about-section .zigzag-row, .band-forest .impact-stat, #reviews-section .quote-card'
  );
  if (matchMedia('(prefers-reduced-motion: reduce)').matches || !('IntersectionObserver' in window)) {
    targets.forEach(el => el.classList.add('mobile-motion-visible'));
    return;
  }
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      entry.target.classList.add('mobile-motion-visible');
      observer.unobserve(entry.target);
    });
  }, {threshold:0.12, rootMargin:'0px 0px -25px 0px'});
  targets.forEach(el => observer.observe(el));
})();

// Page load initialization: positions line under active item
window.addEventListener('DOMContentLoaded', () => moveNavIndicator(null, false));
window.addEventListener('load', () => moveNavIndicator(null, false));
</script>
<script src="assets/js/main.js"></script>
</body>
</html>
