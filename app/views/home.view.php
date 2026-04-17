<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>AgroLink</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet" />
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --green-deep:   #1a3a1f;
      --green-mid:    #2d6a35;
      --green-fresh:  #4caf50;
      --green-pale:   #e8f5e9;
      --amber:        #f5a623;
      --amber-light:  #fff3cd;
      --soil:         #6b4226;
      --cream:        #faf8f2;
      --white:        #ffffff;
      --text-dark:    #111a0f;
      --text-mid:     #3b4d39;
      --text-light:   #7a8c77;
      --radius:       16px;
      --shadow:       0 4px 32px rgba(26,58,31,0.10);
    }

    html { scroll-behavior: smooth; }

    body {
      font-family: 'DM Sans', sans-serif;
      background: var(--cream);
      color: var(--text-dark);
      overflow-x: hidden;
    }

    /* ── NOISE TEXTURE OVERLAY ── */
    body::before {
      content: '';
      position: fixed; inset: 0; z-index: 0; pointer-events: none;
      background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.025'/%3E%3C/svg%3E");
      opacity: 0.4;
    }

    /* ── NAV ── */
    nav {
      position: fixed; top: 0; left: 0; right: 0; z-index: 100;
      display: flex; align-items: center; justify-content: space-between;
      padding: 18px 5vw;
      background: rgba(250,248,242,0.85);
      backdrop-filter: blur(14px);
      border-bottom: 1px solid rgba(77,175,80,0.12);
      transition: box-shadow .3s;
    }
    nav.scrolled { box-shadow: 0 2px 24px rgba(26,58,31,0.10); }
    .nav-logo {
      font-family: 'Playfair Display', serif;
      font-size: 1.6rem; font-weight: 900;
      color: var(--green-deep);
      letter-spacing: -0.5px;
      display: flex; align-items: center; gap: 8px;
      text-decoration: none;
    }
    .nav-logo span { color: var(--green-fresh); }
    .nav-leaf { font-size: 1.3rem; }
    .nav-links { display: flex; gap: 32px; list-style: none; }
    .nav-links a {
      font-size: .9rem; font-weight: 500; letter-spacing: .3px;
      color: var(--text-mid); text-decoration: none;
      transition: color .2s;
    }
    .nav-links a:hover { color: var(--green-mid); }
    .nav-cta { display: flex; gap: 10px; }
    .btn {
      display: inline-flex; align-items: center; gap: 6px;
      padding: 10px 22px; border-radius: 50px;
      font-family: 'DM Sans', sans-serif; font-size: .9rem; font-weight: 500;
      text-decoration: none; cursor: pointer; border: none;
      transition: all .25s cubic-bezier(.4,0,.2,1);
    }
    .btn-outline {
      background: transparent;
      border: 1.5px solid var(--green-mid);
      color: var(--green-mid);
    }
    .btn-outline:hover { background: var(--green-mid); color: #fff; }
    .btn-primary {
      background: var(--green-mid);
      color: #fff;
      box-shadow: 0 2px 12px rgba(45,106,53,0.25);
    }
    .btn-primary:hover {
      background: var(--green-deep);
      transform: translateY(-1px);
      box-shadow: 0 6px 20px rgba(26,58,31,0.28);
    }
    .btn-amber {
      background: var(--amber);
      color: var(--text-dark);
      font-weight: 600;
      box-shadow: 0 2px 12px rgba(245,166,35,0.30);
    }
    .btn-amber:hover { background: #e6941a; transform: translateY(-2px); }
    .btn-lg { padding: 15px 34px; font-size: 1rem; }

    /* hamburger hidden on desktop */
    .hamburger { display: none; }

    /* ── HERO ── */
    #home {
      min-height: 100vh;
      display: grid;
      grid-template-columns: 1fr 1fr;
      align-items: center;
      padding: 120px 5vw 80px;
      gap: 60px;
      position: relative;
      overflow: hidden;
    }

    .hero-bg-shapes {
      position: absolute; inset: 0; pointer-events: none; z-index: 0;
    }
    .hero-bg-shapes circle, .hero-bg-shapes ellipse {
      animation: float 8s ease-in-out infinite;
    }
    @keyframes float {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-20px); }
    }

    .hero-text { position: relative; z-index: 1; }
    .hero-badge {
      display: inline-flex; align-items: center; gap: 8px;
      background: var(--green-pale);
      border: 1px solid rgba(76,175,80,0.3);
      padding: 6px 16px; border-radius: 50px;
      font-size: .8rem; font-weight: 500; color: var(--green-mid);
      margin-bottom: 24px;
      animation: fadeInUp .6s both;
    }
    .hero-badge .dot {
      width: 7px; height: 7px; border-radius: 50%;
      background: var(--green-fresh);
      animation: pulse 2s infinite;
    }
    @keyframes pulse {
      0%, 100% { opacity: 1; transform: scale(1); }
      50% { opacity: .6; transform: scale(1.4); }
    }
    h1.headline {
      font-family: 'Playfair Display', serif;
      font-size: clamp(2.6rem, 5vw, 4.2rem);
      font-weight: 900; line-height: 1.08;
      color: var(--green-deep);
      margin-bottom: 24px;
      animation: fadeInUp .7s .1s both;
    }
    h1.headline em { color: var(--green-mid); font-style: italic; }
    .hero-sub {
      font-size: 1.1rem; line-height: 1.75;
      color: var(--text-mid);
      max-width: 480px;
      margin-bottom: 36px;
      font-weight: 300;
      animation: fadeInUp .7s .2s both;
    }
    .hero-actions {
      display: flex; gap: 14px; flex-wrap: wrap;
      animation: fadeInUp .7s .3s both;
    }
    .hero-stats {
      display: flex; gap: 36px; margin-top: 52px;
      animation: fadeInUp .7s .4s both;
    }
    .stat { display: flex; flex-direction: column; gap: 2px; }
    .stat-num {
      font-family: 'Playfair Display', serif;
      font-size: 2rem; font-weight: 700; color: var(--green-deep);
      line-height: 1;
    }
    .stat-label { font-size: .8rem; color: var(--text-light); letter-spacing: .5px; text-transform: uppercase; }

    /* Hero visual card */
    .hero-visual {
      position: relative; z-index: 1;
      animation: fadeInRight .8s .2s both;
    }
    .hero-card-main {
      background: var(--white);
      border-radius: 24px;
      overflow: hidden;
      box-shadow: 0 20px 60px rgba(26,58,31,0.18);
      position: relative;
    }
    .hero-card-main .img-area {
      height: 300px;
      background: linear-gradient(135deg, #2d6a35 0%, #4caf50 60%, #81c784 100%);
      display: flex; align-items: center; justify-content: center;
      font-size: 6rem;
      position: relative; overflow: hidden;
    }
    .img-area-overlay {
      position: absolute; inset: 0;
      background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 300'%3E%3Cellipse cx='200' cy='280' rx='250' ry='80' fill='rgba(255,255,255,0.05)'/%3E%3Cellipse cx='50' cy='50' rx='80' ry='80' fill='rgba(255,255,255,0.06)'/%3E%3Cellipse cx='350' cy='80' rx='60' ry='60' fill='rgba(255,255,255,0.04)'/%3E%3C/svg%3E") center/cover;
    }
    .hero-card-info {
      padding: 24px;
    }
    .product-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-bottom: 18px; }
    .product-chip {
      background: var(--green-pale);
      border-radius: 10px; padding: 10px;
      text-align: center; font-size: .82rem;
      color: var(--green-deep); font-weight: 500;
      transition: transform .2s, background .2s;
      cursor: default;
    }
    .product-chip:hover { transform: scale(1.04); background: #c8e6c9; }
    .product-chip .chip-emoji { font-size: 1.4rem; display: block; margin-bottom: 4px; }
    .card-footer-row {
      display: flex; align-items: center; justify-content: space-between;
    }
    .live-badge {
      display: flex; align-items: center; gap: 6px;
      font-size: .78rem; color: var(--green-mid); font-weight: 500;
    }
    .live-dot {
      width: 8px; height: 8px; border-radius: 50%; background: #4caf50;
      animation: pulse 1.8s infinite;
    }

    /* floating mini cards */
    .float-card {
      position: absolute;
      background: var(--white);
      border-radius: 14px;
      padding: 12px 16px;
      box-shadow: 0 8px 28px rgba(26,58,31,0.14);
      font-size: .82rem;
      font-weight: 500;
    }
    .float-card-1 {
      top: -18px; left: -24px;
      animation: floatCard1 5s ease-in-out infinite;
    }
    .float-card-2 {
      bottom: 60px; right: -28px;
      animation: floatCard2 6s ease-in-out infinite;
    }
    .float-icon { font-size: 1.2rem; margin-right: 6px; }
    @keyframes floatCard1 { 0%,100%{transform:translateY(0) rotate(-2deg)} 50%{transform:translateY(-10px) rotate(-2deg)} }
    @keyframes floatCard2 { 0%,100%{transform:translateY(0) rotate(2deg)} 50%{transform:translateY(-8px) rotate(2deg)} }

    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(30px); }
      to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeInRight {
      from { opacity: 0; transform: translateX(40px); }
      to   { opacity: 1; transform: translateX(0); }
    }

    /* ── SECTIONS SHARED ── */
    section { padding: 96px 5vw; position: relative; z-index: 1; }
    .section-tag {
      display: inline-flex; align-items: center; gap: 8px;
      font-family: 'DM Mono', monospace;
      font-size: .75rem; letter-spacing: 1.5px;
      text-transform: uppercase; color: var(--green-mid);
      margin-bottom: 14px;
    }
    .section-tag::before {
      content: '';
      display: block; width: 24px; height: 2px; background: var(--green-fresh);
    }
    h2.section-title {
      font-family: 'Playfair Display', serif;
      font-size: clamp(2rem, 4vw, 3rem);
      font-weight: 900; color: var(--green-deep);
      line-height: 1.12;
    }
    h2.section-title em { color: var(--green-mid); font-style: italic; }
    .section-sub {
      font-size: 1.05rem; color: var(--text-mid);
      max-width: 540px; line-height: 1.7; font-weight: 300;
      margin-top: 14px;
    }
    .section-header { margin-bottom: 60px; }

    /* ── HOW IT WORKS ── */
    #about { background: var(--white); }
    .section-header-row {
      display: flex; justify-content: space-between; align-items: flex-end;
      flex-wrap: wrap; gap: 24px;
    }
    .steps-track {
      display: grid; grid-template-columns: 1fr 1fr 1fr;
      gap: 32px; position: relative;
    }
    .steps-track::before {
      content: '';
      position: absolute; top: 36px; left: 10%; right: 10%; height: 2px;
      background: linear-gradient(90deg, var(--green-pale), var(--green-fresh), var(--green-pale));
      z-index: 0;
    }
    .step-card {
      background: var(--cream);
      border-radius: var(--radius);
      padding: 36px 28px;
      text-align: center;
      border: 1px solid rgba(76,175,80,0.12);
      position: relative; z-index: 1;
      transition: transform .3s, box-shadow .3s;
    }
    .step-card:hover { transform: translateY(-6px); box-shadow: var(--shadow); }
    .step-num {
      width: 56px; height: 56px; border-radius: 50%;
      background: var(--green-mid); color: #fff;
      font-family: 'Playfair Display', serif;
      font-size: 1.4rem; font-weight: 700;
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 20px;
      box-shadow: 0 4px 14px rgba(45,106,53,0.3);
    }
    .step-card h3 {
      font-family: 'Playfair Display', serif;
      font-size: 1.2rem; color: var(--green-deep); margin-bottom: 10px;
    }
    .step-card p { font-size: .9rem; color: var(--text-mid); line-height: 1.65; font-weight: 300; }

    /* ── FEATURES ── */
    #features { background: var(--green-deep); color: #fff; }
    #features .section-tag { color: var(--green-fresh); }
    #features .section-tag::before { background: var(--green-fresh); }
    #features h2.section-title { color: #fff; }
    #features .section-sub { color: rgba(255,255,255,0.65); }
    .features-grid {
      display: grid; grid-template-columns: 1fr 1fr;
      gap: 2px;
      border-radius: var(--radius);
      overflow: hidden;
      background: rgba(255,255,255,0.06);
    }
    .feature-cell {
      background: rgba(255,255,255,0.04);
      padding: 40px 36px;
      border: 1px solid rgba(255,255,255,0.07);
      transition: background .3s;
      cursor: default;
    }
    .feature-cell:hover { background: rgba(76,175,80,0.12); }
    .feature-icon {
      font-size: 2rem; margin-bottom: 16px;
      display: block;
    }
    .feature-cell h3 {
      font-family: 'Playfair Display', serif;
      font-size: 1.2rem; color: #fff; margin-bottom: 10px;
    }
    .feature-cell p { font-size: .9rem; color: rgba(255,255,255,0.6); line-height: 1.65; font-weight: 300; }

    /* ── ROLES ── */
    #roles { background: var(--cream); }
    .roles-grid {
      display: grid; grid-template-columns: repeat(3, 1fr);
      gap: 24px;
    }
    .role-card {
      background: var(--white);
      border-radius: 20px;
      padding: 36px 28px;
      border: 1.5px solid rgba(76,175,80,0.12);
      transition: transform .3s, box-shadow .3s, border-color .3s;
      position: relative; overflow: hidden;
    }
    .role-card::before {
      content: '';
      position: absolute; top: 0; left: 0; right: 0; height: 4px;
      background: linear-gradient(90deg, var(--green-mid), var(--green-fresh));
      transform: scaleX(0); transform-origin: left;
      transition: transform .4s cubic-bezier(.4,0,.2,1);
    }
    .role-card:hover { transform: translateY(-8px); box-shadow: 0 16px 48px rgba(26,58,31,0.12); border-color: var(--green-fresh); }
    .role-card:hover::before { transform: scaleX(1); }
    .role-emoji { font-size: 2.6rem; margin-bottom: 20px; display: block; }
    .role-card h3 {
      font-family: 'Playfair Display', serif;
      font-size: 1.4rem; color: var(--green-deep); margin-bottom: 16px;
    }
    .role-card ul { list-style: none; }
    .role-card li {
      font-size: .9rem; color: var(--text-mid);
      padding: 7px 0;
      border-bottom: 1px solid rgba(76,175,80,0.08);
      display: flex; align-items: center; gap: 8px;
      font-weight: 300;
    }
    .role-card li:last-child { border: none; }
    .role-card li::before {
      content: '✓';
      color: var(--green-fresh); font-weight: 700; font-size: .85rem;
      flex-shrink: 0;
    }
    .roles-cta { text-align: center; margin-top: 52px; }

    /* ── TESTIMONIALS ── */
    #testimonials { background: var(--white); }
    .testimonials-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 28px; }
    .testi-card {
      background: var(--cream);
      border-radius: 20px;
      padding: 36px;
      border: 1px solid rgba(76,175,80,0.10);
      position: relative;
      transition: box-shadow .3s;
    }
    .testi-card:hover { box-shadow: var(--shadow); }
    .quote-mark {
      font-family: 'Playfair Display', serif;
      font-size: 5rem; line-height: 0.6;
      color: var(--green-pale);
      position: absolute; top: 24px; left: 28px;
      font-weight: 900;
    }
    .testi-card p {
      font-size: 1.05rem; line-height: 1.75; color: var(--text-mid);
      font-style: italic; font-weight: 300;
      margin-bottom: 24px; padding-top: 32px;
      position: relative; z-index: 1;
    }
    .testi-author {
      display: flex; align-items: center; gap: 14px;
    }
    .testi-avatar {
      width: 44px; height: 44px; border-radius: 50%;
      background: var(--green-mid);
      display: flex; align-items: center; justify-content: center;
      color: #fff; font-weight: 700; font-size: 1rem;
    }
    .testi-name { font-weight: 600; color: var(--green-deep); font-size: .95rem; }
    .testi-role { font-size: .8rem; color: var(--text-light); }
    .stars { color: var(--amber); font-size: .9rem; margin-bottom: 6px; }

    /* ── FAQ ── */
    #faq { background: var(--cream); }
    .faq-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; max-width: 960px; margin: 0 auto; }
    .faq-item {
      background: var(--white);
      border-radius: 14px;
      border: 1px solid rgba(76,175,80,0.10);
      overflow: hidden;
    }
    .faq-q {
      padding: 20px 24px;
      font-weight: 500; font-size: .95rem; color: var(--green-deep);
      cursor: pointer; display: flex; justify-content: space-between; align-items: center;
      transition: background .2s;
    }
    .faq-q:hover { background: var(--green-pale); }
    .faq-arrow {
      font-size: 1.1rem; color: var(--green-mid);
      transition: transform .3s;
      flex-shrink: 0;
    }
    .faq-item.open .faq-arrow { transform: rotate(180deg); }
    .faq-a {
      display: none;
      padding: 0 24px 20px;
      font-size: .9rem; color: var(--text-mid); line-height: 1.65; font-weight: 300;
      border-top: 2px solid var(--green-fresh);
    }
    .faq-item.open .faq-a { display: block; }

    /* ── CONTACT ── */
    #contact { background: var(--white); }
    .contact-grid { display: grid; grid-template-columns: 1fr 1.4fr; gap: 48px; align-items: start; }
    .contact-info h3 {
      font-family: 'Playfair Display', serif;
      font-size: 1.6rem; color: var(--green-deep); margin-bottom: 24px;
    }
    .contact-row {
      display: flex; align-items: flex-start; gap: 16px;
      padding: 18px 0;
      border-bottom: 1px solid rgba(76,175,80,0.10);
    }
    .contact-row:last-child { border: none; }
    .contact-icon {
      width: 44px; height: 44px; border-radius: 12px;
      background: var(--green-pale);
      display: flex; align-items: center; justify-content: center;
      font-size: 1.2rem; flex-shrink: 0;
    }
    .contact-row-text { font-size: .9rem; color: var(--text-mid); line-height: 1.5; }
    .contact-row-text strong { color: var(--green-deep); font-size: .85rem; text-transform: uppercase; letter-spacing: .5px; display: block; margin-bottom: 3px; }

    .contact-form {
      background: var(--cream);
      border-radius: 20px;
      padding: 40px;
      border: 1px solid rgba(76,175,80,0.12);
    }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .form-group { margin-bottom: 20px; }
    .form-group label {
      display: block; font-size: .82rem; font-weight: 500;
      color: var(--text-mid); margin-bottom: 8px;
      letter-spacing: .3px;
    }
    .form-group input, .form-group textarea {
      width: 100%; padding: 12px 16px;
      border: 1.5px solid rgba(76,175,80,0.18);
      border-radius: 10px; font-family: 'DM Sans', sans-serif;
      font-size: .95rem; background: var(--white);
      color: var(--text-dark);
      transition: border-color .2s, box-shadow .2s;
      outline: none;
    }
    .form-group input:focus, .form-group textarea:focus {
      border-color: var(--green-fresh);
      box-shadow: 0 0 0 3px rgba(76,175,80,0.12);
    }
    .form-group textarea { resize: vertical; min-height: 120px; }

    /* ── FOOTER ── */
    footer {
      background: var(--green-deep); color: rgba(255,255,255,0.8);
      padding: 72px 5vw 36px;
    }
    .footer-grid { display: grid; grid-template-columns: 1.5fr 1fr 1fr 1fr; gap: 40px; margin-bottom: 56px; }
    .footer-brand-name {
      font-family: 'Playfair Display', serif;
      font-size: 1.8rem; font-weight: 900; color: #fff; margin-bottom: 14px;
    }
    .footer-brand-name span { color: var(--green-fresh); }
    .footer-desc { font-size: .9rem; line-height: 1.65; font-weight: 300; color: rgba(255,255,255,0.55); }
    .footer-col h4 {
      font-size: .8rem; letter-spacing: 1.5px; text-transform: uppercase;
      color: rgba(255,255,255,0.45); margin-bottom: 20px;
      font-family: 'DM Mono', monospace;
    }
    .footer-col a {
      display: block; color: rgba(255,255,255,0.65); text-decoration: none;
      font-size: .9rem; margin-bottom: 11px; transition: color .2s;
      font-weight: 300;
    }
    .footer-col a:hover { color: var(--green-fresh); }
    .footer-bottom {
      border-top: 1px solid rgba(255,255,255,0.08);
      padding-top: 28px;
      display: flex; justify-content: space-between; align-items: center;
      flex-wrap: wrap; gap: 12px;
    }
    .footer-bottom p { font-size: .82rem; color: rgba(255,255,255,0.35); }

    /* ── SCROLL REVEAL ── */
    .reveal {
      opacity: 0; transform: translateY(32px);
      transition: opacity .7s cubic-bezier(.4,0,.2,1), transform .7s cubic-bezier(.4,0,.2,1);
    }
    .reveal.visible { opacity: 1; transform: translateY(0); }

    /* ── SUCCESS TOAST ── */
    .toast {
      position: fixed; bottom: 32px; right: 32px; z-index: 999;
      background: var(--green-deep); color: #fff;
      padding: 14px 22px; border-radius: 12px;
      font-size: .9rem; font-weight: 500;
      box-shadow: 0 8px 32px rgba(26,58,31,0.3);
      transform: translateY(80px); opacity: 0;
      transition: all .4s cubic-bezier(.4,0,.2,1);
      display: flex; align-items: center; gap: 10px;
    }
    .toast.show { transform: translateY(0); opacity: 1; }

    /* ── RESPONSIVE ── */
    @media (max-width: 1024px) {
      #home { grid-template-columns: 1fr; padding-top: 100px; }
      .hero-visual { max-width: 500px; margin: 0 auto; }
      .steps-track { grid-template-columns: 1fr; }
      .steps-track::before { display: none; }
      .roles-grid { grid-template-columns: 1fr 1fr; }
      .features-grid { grid-template-columns: 1fr; }
      .footer-grid { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 768px) {
      .nav-links, .nav-cta { display: none; }
      .hamburger {
        display: flex; flex-direction: column; gap: 5px; cursor: pointer; padding: 4px;
      }
      .hamburger span { display: block; width: 22px; height: 2px; background: var(--green-deep); border-radius: 2px; }
      .testimonials-grid, .faq-grid, .contact-grid { grid-template-columns: 1fr; }
      .roles-grid { grid-template-columns: 1fr; }
      .form-row { grid-template-columns: 1fr; }
      .hero-stats { flex-wrap: wrap; gap: 24px; }
      .footer-grid { grid-template-columns: 1fr; }
      .footer-bottom { flex-direction: column; text-align: center; }
    }
  </style>
</head>
<body>

  <!-- NAV -->
  <nav id="navbar">
    <a href="#home" class="nav-logo">
      <span class="nav-leaf"><img src="<?= ROOT ?>/assets/imgs/Logo 2.svg" alt="AgroLink Logo" style="width: 150px;"></span>
    </a>
    <ul class="nav-links">
      <li><a href="#about">How It Works</a></li>
      <li><a href="#features">Features</a></li>
      <li><a href="#roles">Who It's For</a></li>
      <li><a href="#contact">Contact</a></li>
    </ul>
    <div class="nav-cta">
      <a href="#login" class="btn btn-outline">Login</a>
      <a href="#register" class="btn btn-primary">Get Started</a>
    </div>
    <button class="hamburger" onclick="toggleMenu()" aria-label="Menu">
      <span></span><span></span><span></span>
    </button>
  </nav>

  <!-- HERO -->
  <section id="home">
    <!-- Background shapes -->
    <svg class="hero-bg-shapes" viewBox="0 0 1200 700" fill="none" xmlns="http://www.w3.org/2000/svg">
      <circle cx="1100" cy="100" r="200" fill="#e8f5e9" opacity="0.5"/>
      <circle cx="900" cy="600" r="120" fill="#c8e6c9" opacity="0.35"/>
      <ellipse cx="100" cy="600" rx="180" ry="120" fill="#e8f5e9" opacity="0.4"/>
    </svg>

    <div class="hero-text">
      <div class="hero-badge">
        <span class="dot"></span> Sri Lanka's Agricultural Marketplace
      </div>
      <h1 class="headline">Trade Smarter.<br><em>Deliver Faster.</em></h1>
      <p class="hero-sub">A centralized digital marketplace connecting farmers, buyers, and transporters to streamline agricultural trade across Sri Lanka.</p>
      <div class="hero-actions">
        <a href="#register" class="btn btn-primary btn-lg">Start for Free</a>
        <a href="#about" class="btn btn-outline btn-lg">See How It Works</a>
      </div>
      <div class="hero-stats">
        <div class="stat">
          <span class="stat-num">3</span>
          <span class="stat-label">User Roles</span>
        </div>
        <div class="stat">
          <span class="stat-num">100%</span>
          <span class="stat-label">Free to Use</span>
        </div>
        <div class="stat">
          <span class="stat-num">🇱🇰</span>
          <span class="stat-label">Sri Lanka</span>
        </div>
      </div>
    </div>

    <div class="hero-visual">
      <div class="hero-card-main">
        <div class="img-area">
          <div class="img-area-overlay"></div>
          <span style="position:relative;z-index:1;filter:drop-shadow(0 4px 16px rgba(0,0,0,0.3))">🌾</span>
        </div>
        <div class="hero-card-info">
          <div class="product-grid">
            <div class="product-chip"><span class="chip-emoji">🥕</span>Carrots</div>
            <div class="product-chip"><span class="chip-emoji">🍅</span>Tomatoes</div>
            <div class="product-chip"><span class="chip-emoji">🌶️</span>Chillies</div>
            <div class="product-chip"><span class="chip-emoji">🥬</span>Greens</div>
            <div class="product-chip"><span class="chip-emoji">🧅</span>Onions</div>
            <div class="product-chip"><span class="chip-emoji">🥔</span>Potatoes</div>
          </div>
          <div class="card-footer-row">
            <span class="live-badge"><span class="live-dot"></span> Live marketplace</span>
            <a href="#register" class="btn btn-amber" style="padding:8px 18px;font-size:.85rem;">Browse Produce →</a>
          </div>
        </div>
      </div>
      <!-- floating cards -->
      <div class="float-card float-card-1">
        <span class="float-icon">🚚</span> Delivery tracked
      </div>
      <div class="float-card float-card-2">
        <span class="float-icon">⭐</span> 4.9 Avg. rating
      </div>
    </div>
  </section>

  <!-- HOW IT WORKS -->
  <section id="about">
    <div class="section-header reveal">
      <span class="section-tag">Process</span>
      <h2 class="section-title">How <em>AgroLink</em> Works</h2>
      <p class="section-sub">From listing to doorstep delivery — a simple, powerful three-step journey for everyone involved.</p>
    </div>
    <div class="steps-track">
      <div class="step-card reveal">
        <div class="step-num">1</div>
        <h3>List & Browse Produce</h3>
        <p>Farmers list fresh products with detailed info. Buyers browse regionally available produce and compare options with ease.</p>
      </div>
      <div class="step-card reveal" style="transition-delay:.12s">
        <div class="step-num">2</div>
        <h3>Order & Schedule Delivery</h3>
        <p>Buyers order directly from farmers. Transporters coordinate and manage delivery logistics all within one platform.</p>
      </div>
      <div class="step-card reveal" style="transition-delay:.24s">
        <div class="step-num">3</div>
        <h3>Rate & Review</h3>
        <p>Build trust through a feedback-driven ecosystem that ensures ongoing quality, reliability, and accountability.</p>
      </div>
    </div>
  </section>

  <!-- FEATURES -->
  <section id="features">
    <div class="section-header-row">
      <div class="reveal">
        <span class="section-tag">Features</span>
        <h2 class="section-title" style="color:#fff;">Why Use <em>AgroLink?</em></h2>
        <p class="section-sub">Everything you need to run agricultural trade, built for every stakeholder.</p>
      </div>
    </div>
    <div class="features-grid reveal" style="margin-top:48px">
      <div class="feature-cell">
        <span class="feature-icon">🤝</span>
        <h3>Direct Farm-to-Buyer Sales</h3>
        <p>Eliminate middlemen and boost farmer profits with direct connections between producers and buyers. Fair pricing for everyone.</p>
      </div>
      <div class="feature-cell">
        <span class="feature-icon">🚛</span>
        <h3>Transport Coordination</h3>
        <p>Assign and track deliveries in one place with integrated logistics management. Real-time status updates for all parties.</p>
      </div>
      <div class="feature-cell">
        <span class="feature-icon">📊</span>
        <h3>Role-Based Dashboards</h3>
        <p>Tailored tools and interfaces designed specifically for each user type — farmers, buyers, and transporters all get what they need.</p>
      </div>
      <div class="feature-cell">
        <span class="feature-icon">📈</span>
        <h3>Track Orders & Revenue</h3>
        <p>Real-time updates and comprehensive analytics to monitor your business performance and plan smarter sales strategies.</p>
      </div>
    </div>
  </section>

  <!-- ROLES -->
  <section id="roles">
    <div class="section-header reveal" style="text-align:center">
      <span class="section-tag">Who It's For</span>
      <h2 class="section-title">Built for <em>Everyone</em> in the Chain</h2>
      <p class="section-sub" style="margin:14px auto 0;">Whether you grow it, buy it, or move it — AgroLink has a role designed just for you.</p>
    </div>
    <div class="roles-grid">
      <div class="role-card reveal">
        <span class="role-emoji">👨‍🌾</span>
        <h3>Farmers</h3>
        <ul>
          <li>Easy product listing & updates</li>
          <li>Get orders instantly</li>
          <li>Track sales & payments</li>
          <li>Connect directly with buyers</li>
        </ul>
      </div>
      <div class="role-card reveal" style="transition-delay:.1s">
        <span class="role-emoji">🛒</span>
        <h3>Buyers</h3>
        <ul>
          <li>Browse fresh produce</li>
          <li>Secure online checkout</li>
          <li>Track your deliveries</li>
          <li>Request specific crops</li>
        </ul>
      </div>
      <div class="role-card reveal" style="transition-delay:.2s">
        <span class="role-emoji">🚚</span>
        <h3>Transporters</h3>
        <ul>
          <li>Accept & manage delivery tasks</li>
          <li>Update delivery status live</li>
          <li>Earn more with reliable clients</li>
        </ul>
      </div>
    </div>
    <div class="roles-cta reveal">
      <a href="#register" class="btn btn-primary btn-lg">Register Now — It's Free</a>
    </div>
  </section>

  <!-- TESTIMONIALS -->
  <section id="testimonials">
    <div class="section-header reveal" style="text-align:center">
      <span class="section-tag">Testimonials</span>
      <h2 class="section-title">What Our <em>Users Say</em></h2>
    </div>
    <div class="testimonials-grid">
      <div class="testi-card reveal">
        <div class="quote-mark">"</div>
        <div class="stars">★★★★★</div>
        <p>Finally, a platform that gives farmers full control over pricing! AgroLink has completely transformed how I sell my produce.</p>
        <div class="testi-author">
          <div class="testi-avatar">RF</div>
          <div>
            <div class="testi-name">Ranjith Fernando</div>
            <div class="testi-role">Farmer · Matale</div>
          </div>
        </div>
      </div>
      <div class="testi-card reveal" style="transition-delay:.12s">
        <div class="quote-mark">"</div>
        <div class="stars">★★★★★</div>
        <p>Our restaurant sources reliably through AgroLink — no middlemen needed! Fresh produce delivered on time, every time.</p>
        <div class="testi-author">
          <div class="testi-avatar">DR</div>
          <div>
            <div class="testi-name">Duleeka Rathnayake</div>
            <div class="testi-role">Buyer · Colombo</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- FAQ -->
  <section id="faq">
    <div class="section-header reveal" style="text-align:center">
      <span class="section-tag">FAQ</span>
      <h2 class="section-title">Frequently Asked <em>Questions</em></h2>
    </div>
    <div class="faq-grid reveal">
      <div class="faq-item">
        <div class="faq-q" onclick="toggleFaq(this)">
          Is AgroLink free to use?
          <span class="faq-arrow">▾</span>
        </div>
        <div class="faq-a">Yes, AgroLink is completely free for all registered users including farmers, buyers, and transporters.</div>
      </div>
      <div class="faq-item">
        <div class="faq-q" onclick="toggleFaq(this)">
          Can I sign up as both a buyer and a farmer?
          <span class="faq-arrow">▾</span>
        </div>
        <div class="faq-a">Yes, you can register for multiple roles using separate registrations with different email addresses.</div>
      </div>
      <div class="faq-item">
        <div class="faq-q" onclick="toggleFaq(this)">
          How do I track my deliveries?
          <span class="faq-arrow">▾</span>
        </div>
        <div class="faq-a">All users can track delivery status in real-time through their respective dashboards with updates from transporters.</div>
      </div>
      <div class="faq-item">
        <div class="faq-q" onclick="toggleFaq(this)">
          Is the platform available island-wide?
          <span class="faq-arrow">▾</span>
        </div>
        <div class="faq-a">Yes, AgroLink operates across all provinces in Sri Lanka. Delivery availability depends on transporter coverage in each region.</div>
      </div>
    </div>
  </section>

  <!-- CONTACT -->
  <section id="contact">
    <div class="section-header reveal">
      <span class="section-tag">Contact</span>
      <h2 class="section-title">Get in <em>Touch</em></h2>
    </div>
    <div class="contact-grid">
      <div class="reveal">
        <div class="contact-info">
          <h3>We're here to help</h3>
          <div class="contact-row">
            <div class="contact-icon">✉️</div>
            <div class="contact-row-text">
              <strong>Email</strong>agrolink.lk@gmail.com
            </div>
          </div>
          <div class="contact-row">
            <div class="contact-icon">📞</div>
            <div class="contact-row-text">
              <strong>Phone</strong>+94 11 2559 259
            </div>
          </div>
          <div class="contact-row">
            <div class="contact-icon">📍</div>
            <div class="contact-row-text">
              <strong>Address</strong>UCSC Building Complex, Reid Avenue,<br>Colombo 07, Sri Lanka
            </div>
          </div>
        </div>
      </div>
      <div class="contact-form reveal" style="transition-delay:.1s">
        <div class="form-row">
          <div class="form-group">
            <label>Your Name</label>
            <input type="text" id="cName" placeholder="John Doe" />
          </div>
          <div class="form-group">
            <label>Email Address</label>
            <input type="email" id="cEmail" placeholder="john@example.com" />
          </div>
        </div>
        <div class="form-group">
          <label>Message</label>
          <textarea id="cMsg" placeholder="How can we help you?"></textarea>
        </div>
        <button class="btn btn-primary btn-lg" style="width:100%" onclick="submitForm()">Send Message →</button>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer>
    <div class="footer-grid">
      <div>
        <div class="footer-brand-name"><img src="<?= ROOT ?>/assets/imgs/Logo 2.svg" alt="AgroLink Logo" style="width: 150px;"></div>
        <p class="footer-desc">Empowering agricultural trade across Sri Lanka through digital innovation. Farm to market, simplified.</p>
      </div>
      <div class="footer-col">
        <h4>Navigate</h4>
        <a href="#home">Home</a>
        <a href="#about">How It Works</a>
        <a href="#features">Features</a>
        <a href="#roles">User Roles</a>
      </div>
      <div class="footer-col">
        <h4>Join Us</h4>
        <a href="#register">Register as Farmer</a>
        <a href="#register">Register as Buyer</a>
        <a href="#register">Register as Transporter</a>
        <a href="<?= ROOT ?>/login.view.php">Login</a>
      </div>
      <div class="footer-col">
        <h4>Legal</h4>
        <a href="#terms">Terms & Conditions</a>
        <a href="#privacy">Privacy Policy</a>
        <a href="#contact">Contact Us</a>
        <a href="#support">Support</a>
      </div>
    </div>
    <div class="footer-bottom">
      <p>© 2025 AgroLink — CS22 Project. All rights reserved.</p>
      <p style="font-size:.8rem;color:rgba(255,255,255,0.25)">Built with ❤️ for Sri Lankan agriculture</p>
    </div>
  </footer>

  <!-- Toast -->
  <div class="toast" id="toast">✅ Message sent! We'll get back to you soon.</div>

  <script>
    // Nav scroll
    window.addEventListener('scroll', () => {
      document.getElementById('navbar').classList.toggle('scrolled', window.scrollY > 40);
    });

    // Scroll reveal
    const reveals = document.querySelectorAll('.reveal');
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); } });
    }, { threshold: 0.12 });
    reveals.forEach(el => observer.observe(el));

    // FAQ toggle
    function toggleFaq(el) {
      const item = el.parentElement;
      item.classList.toggle('open');
    }

    // Contact form
    function submitForm() {
      const name = document.getElementById('cName').value.trim();
      const email = document.getElementById('cEmail').value.trim();
      const msg = document.getElementById('cMsg').value.trim();
      if (!name || !email || !msg) { alert('Please fill in all fields.'); return; }
      document.getElementById('cName').value = '';
      document.getElementById('cEmail').value = '';
      document.getElementById('cMsg').value = '';
      const toast = document.getElementById('toast');
      toast.classList.add('show');
      setTimeout(() => toast.classList.remove('show'), 3500);
    }

    // Hamburger placeholder
    function toggleMenu() {}
  </script>
</body>
</html>