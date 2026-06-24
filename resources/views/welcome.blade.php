<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ config('app.name', 'ICH Pendidikan') }}</title>
<link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=Nunito+Sans:wght@400;600;700&family=Raleway:wght@700;800&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root {
    --green:   #4A9E5C;
    --green-d: #3B8249;
    --teal:    #3087A2;
    --yellow:  #F5A623;
    --ink:     #101828;
    --ink-2:   #344153;
    --muted:   #687182;
    --border:  #E5E7EB;
    --bg:      #F5F6FA;
  }
  html { scroll-behavior: smooth; }
  body { font-family: 'Inter', sans-serif; color: var(--ink); background: #fff; overflow-x: hidden; }

  /* —— NAV —— */
  .nav {
    position: fixed; top: 0; left: 0; right: 0; z-index: 100;
    background: rgba(0,0,0,0.42);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    padding: 0 5%;
    height: 80px;
    display: flex; align-items: center; justify-content: space-between;
    transition: background 0.3s;
  }
  .nav.scrolled { background: rgba(16,24,40,0.92); }
  .nav-logo { display: flex; align-items: center; gap: 12px; text-decoration: none; }
  .nav-logo .mark { width: 46px; height: 46px; border-radius: 12px; background: #fff; display: flex; align-items: center; justify-content: center; font-family: 'Poppins', sans-serif; font-weight: 800; font-size: 16px; color: var(--green); }
  .nav-logo .wordmark { display: flex; flex-direction: column; }
  .nav-logo .wordmark strong { font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 15px; color: #fff; line-height: 1.1; }
  .nav-logo .wordmark span { font-family: 'Inter', sans-serif; font-size: 11px; color: rgba(255,255,255,0.75); margin-top: 1px; }
  .nav-links { display: flex; align-items: center; gap: 32px; }
  .nav-links a { font-family: 'Inter', sans-serif; font-weight: 500; font-size: 14px; color: rgba(255,255,255,0.9); text-decoration: none; transition: color 0.2s; }
  .nav-links a:hover { color: #fff; }
  .nav-cta { display: flex; gap: 10px; }
  .btn-nav-outline { padding: 10px 22px; border-radius: 6px; border: 1.5px solid rgba(255,255,255,0.6); background: transparent; color: #fff; font-family: 'Inter', sans-serif; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; }
  .btn-nav-outline:hover { border-color: #fff; background: rgba(255,255,255,0.1); }
  .btn-nav-solid { padding: 10px 22px; border-radius: 6px; border: none; background: #fff; color: var(--teal); font-family: 'Inter', sans-serif; font-weight: 700; font-size: 14px; cursor: pointer; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; }
  .btn-nav-solid:hover { background: #f0f0f0; }
  .nav-hamburger { display: none; background: none; border: none; cursor: pointer; flex-direction: column; gap: 5px; }
  .nav-hamburger span { display: block; width: 24px; height: 2px; background: #fff; border-radius: 2px; }

  /* —— HERO —— */
  .hero {
    position: relative; min-height: 100vh;
    display: flex; align-items: center; justify-content: center;
    text-align: center;
    overflow: hidden;
  }
  .hero-bg { position: absolute; inset: 0; background: url('{{ asset('images/hero-students.jpg') }}') center/cover no-repeat; background-color: #1a4a1a; }
  .hero-wash { position: absolute; inset: 0; background: #4A9E5C; opacity: 0.25; }
  .hero-content { position: relative; z-index: 2; padding: 80px 24px 60px; max-width: 820px; }
  .hero-badge { display: inline-flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.35); backdrop-filter: blur(4px); padding: 8px 18px; border-radius: 999px; color: #fff; font-family: 'Inter', sans-serif; font-size: 13px; font-weight: 600; margin-bottom: 28px; }
  .hero-badge::before { content: ''; width: 8px; height: 8px; border-radius: 50%; background: #F5A623; }
  .hero-title { font-family: 'Poppins', sans-serif; font-weight: 800; font-size: clamp(36px, 6vw, 68px); line-height: 1.06; color: #fff; text-shadow: 0 4px 24px rgba(0,0,0,0.3); margin-bottom: 22px; }
  .hero-sub { font-family: 'Inter', sans-serif; font-size: clamp(15px, 2vw, 18px); color: rgba(255,255,255,0.92); line-height: 1.65; max-width: 600px; margin: 0 auto 40px; }
  .hero-btns { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }
  .btn-primary { padding: 15px 32px; background: var(--green); color: #fff; border: none; border-radius: 8px; font-family: 'Inter', sans-serif; font-weight: 700; font-size: 15px; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 16px rgba(61,167,70,0.4); transition: transform 0.15s, box-shadow 0.15s; }
  .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(61,167,70,0.45); }
  .btn-secondary { padding: 15px 32px; background: #fff; color: var(--teal); border: none; border-radius: 8px; font-family: 'Inter', sans-serif; font-weight: 700; font-size: 15px; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 4px rgba(0,0,0,0.15); transition: transform 0.15s; }
  .btn-secondary:hover { transform: translateY(-2px); }
  .hero-strip { position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); width: 90%; max-width: 1100px; background: #fff; border-radius: 16px 16px 0 0; padding: 28px 40px; display: flex; gap: 0; box-shadow: 0 -4px 40px rgba(0,0,0,0.08); z-index: 3; }
  .hero-stat { flex: 1; display: flex; align-items: center; gap: 16px; padding: 0 24px; border-right: 1px solid var(--border); }
  .hero-stat:first-child { padding-left: 0; }
  .hero-stat:last-child { border-right: none; }
  .stat-icon { width: 52px; height: 52px; border-radius: 14px; background: rgba(68,252,81,0.18); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
  .stat-icon svg { color: var(--green); }
  .stat-text strong { font-family: 'Nunito Sans', sans-serif; font-weight: 700; font-size: 15px; color: var(--ink); display: block; }
  .stat-text span { font-family: 'Inter', sans-serif; font-size: 12px; color: var(--muted); margin-top: 2px; display: block; }

  /* —— SECTIONS —— */
  section { padding: 96px 5%; }
  .section-label { font-family: 'Inter', sans-serif; font-weight: 600; font-size: 12px; color: var(--green); letter-spacing: 0.1em; text-transform: uppercase; margin-bottom: 12px; }
  .section-title { font-family: 'Poppins', sans-serif; font-weight: 700; font-size: clamp(26px, 3.5vw, 42px); line-height: 1.15; color: var(--ink); margin-bottom: 16px; }
  .section-sub { font-family: 'Inter', sans-serif; font-size: 16px; color: var(--muted); line-height: 1.7; max-width: 600px; }
  .center { text-align: center; }
  .center .section-sub { margin: 0 auto; }

  /* —— ABOUT —— */
  .about { background: #fff; }
  .about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center; max-width: 1200px; margin: 0 auto; }
  .about-img { position: relative; border-radius: 20px; overflow: hidden; aspect-ratio: 4/3; }
  .about-img img { width: 100%; height: 100%; object-fit: cover; display: block; }
  .about-img-badge { position: absolute; bottom: 20px; left: 20px; background: #fff; border-radius: 12px; padding: 12px 18px; box-shadow: 0 8px 24px rgba(0,0,0,0.12); display: flex; align-items: center; gap: 10px; }
  .about-img-badge .num { font-family: 'Poppins', sans-serif; font-weight: 800; font-size: 28px; color: var(--green); }
  .about-img-badge .txt { font-family: 'Inter', sans-serif; font-size: 11px; color: var(--muted); line-height: 1.4; }
  .about-features { display: flex; flex-direction: column; gap: 16px; margin-top: 32px; }
  .about-feat { display: flex; align-items: flex-start; gap: 14px; }
  .about-feat .dot { width: 22px; height: 22px; border-radius: 50%; background: #EEF6F0; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px; }
  .about-feat p { font-family: 'Inter', sans-serif; font-size: 14px; color: var(--muted); line-height: 1.6; }
  .about-feat p strong { color: var(--ink); font-weight: 600; }

  /* —— PROGRAMS —— */
  .programs { background: var(--bg); }
  .programs-inner { max-width: 1200px; margin: 0 auto; }
  .programs-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 52px; }
  .prog-card { background: #fff; border-radius: 16px; padding: 28px 24px; box-shadow: 0 1px 4px rgba(89,96,120,0.1); transition: transform 0.2s, box-shadow 0.2s; cursor: default; border: 1px solid transparent; }
  .prog-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(61,167,70,0.12); border-color: #EEF6F0; }
  .prog-icon { width: 54px; height: 54px; border-radius: 14px; background: rgba(68,252,81,0.18); display: flex; align-items: center; justify-content: center; margin-bottom: 18px; }
  .prog-title { font-family: 'Nunito Sans', sans-serif; font-weight: 700; font-size: 16px; color: var(--ink); margin-bottom: 8px; }
  .prog-desc { font-family: 'Inter', sans-serif; font-size: 13px; color: var(--muted); line-height: 1.6; }

  /* —— ACTIVITIES —— */
  .activities { background: #fff; }
  .activities-inner { max-width: 1200px; margin: 0 auto; }
  .acts-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-top: 52px; }
  .act-card { border-radius: 16px; overflow: hidden; box-shadow: 0 2px 8px rgba(16,24,40,0.08); transition: transform 0.2s; }
  .act-card:hover { transform: translateY(-4px); }
  .act-img { height: 200px; background: var(--bg); position: relative; overflow: hidden; }
  .act-img img { width: 100%; height: 100%; object-fit: cover; }
  .act-img-placeholder { width: 100%; height: 100%; background: linear-gradient(135deg, #EEF6F0 0%, #F5F6FA 100%); display: flex; align-items: center; justify-content: center; }
  .act-body { padding: 20px; background: #fff; }
  .act-tag { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 999px; background: #EEF6F0; color: var(--green); font-family: 'Nunito Sans', sans-serif; font-weight: 700; font-size: 11px; margin-bottom: 10px; }
  .act-title { font-family: 'Nunito Sans', sans-serif; font-weight: 700; font-size: 15px; color: var(--ink); line-height: 1.35; }
  .act-meta { font-family: 'Inter', sans-serif; font-size: 12px; color: var(--muted); margin-top: 8px; }

  /* —— TESTIMONIALS —— */
  .testimonials { background: var(--green); overflow: hidden; }
  .testimonials-inner { max-width: 1200px; margin: 0 auto; }
  .testimonials .section-label { color: rgba(255,255,255,0.75); }
  .testimonials .section-title { color: #fff; }
  .testi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 52px; }
  .testi-card { background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2); border-radius: 16px; padding: 28px; }
  .testi-stars { display: flex; gap: 4px; margin-bottom: 16px; }
  .testi-stars span { color: var(--yellow); font-size: 16px; }
  .testi-text { font-family: 'Inter', sans-serif; font-size: 14px; color: rgba(255,255,255,0.92); line-height: 1.7; font-style: italic; margin-bottom: 20px; }
  .testi-author { display: flex; align-items: center; gap: 12px; }
  .testi-avatar { width: 42px; height: 42px; border-radius: 50%; overflow: hidden; background: rgba(255,255,255,0.2); flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-family: 'Poppins', sans-serif; font-weight: 800; font-size: 16px; color: #fff; }
  .testi-avatar img { width: 100%; height: 100%; object-fit: cover; }
  .testi-name { font-family: 'Nunito Sans', sans-serif; font-weight: 700; font-size: 13px; color: #fff; }
  .testi-role { font-family: 'Inter', sans-serif; font-size: 11px; color: rgba(255,255,255,0.75); margin-top: 2px; }

  /* —— CTA —— */
  .cta-section { background: #fff; text-align: center; padding: 100px 5%; }
  .cta-box { background: linear-gradient(135deg, var(--green) 0%, #3B8249 100%); border-radius: 24px; padding: 72px 40px; max-width: 900px; margin: 0 auto; position: relative; overflow: hidden; }
  .cta-box::before { content: ''; position: absolute; top: -60px; right: -60px; width: 260px; height: 260px; border-radius: 50%; background: rgba(255,255,255,0.08); }
  .cta-box::after { content: ''; position: absolute; bottom: -80px; left: -40px; width: 200px; height: 200px; border-radius: 50%; background: rgba(255,255,255,0.06); }
  .cta-title { font-family: 'Poppins', sans-serif; font-weight: 700; font-size: clamp(24px, 3vw, 38px); color: #fff; margin-bottom: 16px; position: relative; z-index: 1; }
  .cta-sub { font-family: 'Inter', sans-serif; font-size: 16px; color: rgba(255,255,255,0.88); margin-bottom: 40px; position: relative; z-index: 1; }
  .cta-btns { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; position: relative; z-index: 1; }
  .btn-white { padding: 15px 32px; background: #fff; color: var(--green); border: none; border-radius: 8px; font-family: 'Inter', sans-serif; font-weight: 700; font-size: 15px; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: transform 0.15s; }
  .btn-white:hover { transform: translateY(-2px); }
  .btn-yellow { padding: 15px 32px; background: var(--yellow); color: #fff; border: none; border-radius: 8px; font-family: 'Inter', sans-serif; font-weight: 700; font-size: 15px; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 16px rgba(253,182,37,0.35); transition: transform 0.15s; }
  .btn-yellow:hover { transform: translateY(-2px); }

  /* —— FOOTER —— */
  footer { background: var(--ink); color: rgba(255,255,255,0.75); padding: 72px 5% 36px; }
  .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 48px; max-width: 1200px; margin: 0 auto; padding-bottom: 48px; border-bottom: 1px solid rgba(255,255,255,0.1); }
  .footer-brand .mark { width: 48px; height: 48px; border-radius: 12px; background: var(--green); display: flex; align-items: center; justify-content: center; font-family: 'Poppins', sans-serif; font-weight: 800; font-size: 16px; color: #fff; margin-bottom: 14px; }
  .footer-brand p { font-family: 'Inter', sans-serif; font-size: 13px; line-height: 1.7; max-width: 280px; }
  .footer-brand .socials { display: flex; gap: 10px; margin-top: 20px; }
  .footer-brand .socials a { width: 34px; height: 34px; border-radius: 8px; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; text-decoration: none; transition: background 0.2s; }
  .footer-brand .socials a:hover { background: var(--green); }
  .footer-col h4 { font-family: 'Nunito Sans', sans-serif; font-weight: 700; font-size: 14px; color: #fff; margin-bottom: 18px; }
  .footer-col ul { list-style: none; display: flex; flex-direction: column; gap: 12px; }
  .footer-col ul li a { font-family: 'Inter', sans-serif; font-size: 13px; color: rgba(255,255,255,0.65); text-decoration: none; transition: color 0.2s; }
  .footer-col ul li a:hover { color: #fff; }
  .footer-col ul li { font-family: 'Inter', sans-serif; font-size: 13px; color: rgba(255,255,255,0.65); line-height: 1.6; }
  .footer-bottom { max-width: 1200px; margin: 32px auto 0; display: flex; align-items: center; justify-content: space-between; font-family: 'Inter', sans-serif; font-size: 12px; color: rgba(255,255,255,0.45); flex-wrap: wrap; gap: 10px; }

  /* —— MOBILE NAV —— */
  .mobile-menu { display: none; position: fixed; inset: 0; background: rgba(16,24,40,0.97); z-index: 200; flex-direction: column; align-items: center; justify-content: center; gap: 28px; }
  .mobile-menu.open { display: flex; }
  .mobile-menu a { font-family: 'Poppins', sans-serif; font-weight: 600; font-size: 22px; color: #fff; text-decoration: none; }
  .mobile-menu-close { position: absolute; top: 24px; right: 24px; background: none; border: none; color: #fff; cursor: pointer; font-size: 28px; }

  /* —— RESPONSIVE —— */
  @media (max-width: 1024px) {
    .hero-strip { display: none; }
    .about-grid { grid-template-columns: 1fr; gap: 40px; }
    .programs-grid { grid-template-columns: repeat(2, 1fr); }
    .acts-grid { grid-template-columns: repeat(2, 1fr); }
    .testi-grid { grid-template-columns: 1fr; max-width: 560px; margin-left: auto; margin-right: auto; }
    .footer-grid { grid-template-columns: 1fr 1fr; }
  }
  @media (max-width: 768px) {
    .nav-links, .nav-cta { display: none; }
    .nav-hamburger { display: flex; }
    section { padding: 64px 20px; }
    .programs-grid { grid-template-columns: 1fr; }
    .acts-grid { grid-template-columns: 1fr; }
    .footer-grid { grid-template-columns: 1fr; }
  }
</style>
</head>
<body>

<!-- NAV -->
<nav class="nav" id="nav">
  <a href="#" class="nav-logo">
    <div class="mark" style="overflow:hidden;padding:4px;"><img src="{{ asset('images/Logo.png') }}" alt="ICH Logo" style="width:100%;height:100%;object-fit:contain;"></div>
    <div class="wordmark">
      <strong>IQRA' Creative House</strong>
      <span>Pendidikan Islam Kreatif</span>
    </div>
  </a>
  <div class="nav-links">
    <a href="#tentang">Tentang Kami</a>
    <a href="#program">Program</a>
    <a href="#aktivitas">Aktivitas</a>
    <a href="#testimoni">Testimoni</a>
    <a href="https://wa.me/081360765971" target="_blank" rel="noopener">Kontak</a>
  </div>
  <div class="nav-cta">
    <a href="{{ route('login') }}" class="btn-nav-outline">Masuk</a>
    <a href="{{ route('register') }}" class="btn-nav-solid">Daftar</a>
  </div>
  <button class="nav-hamburger" onclick="document.querySelector('.mobile-menu').classList.toggle('open')">
    <span></span><span></span><span></span>
  </button>
</nav>

<!-- MOBILE MENU -->
<div class="mobile-menu">
  <button class="mobile-menu-close" onclick="document.querySelector('.mobile-menu').classList.remove('open')">✕</button>
  <a href="#tentang" onclick="document.querySelector('.mobile-menu').classList.remove('open')">Tentang Kami</a>
  <a href="#program"  onclick="document.querySelector('.mobile-menu').classList.remove('open')">Program</a>
  <a href="#aktivitas" onclick="document.querySelector('.mobile-menu').classList.remove('open')">Aktivitas</a>
  <a href="#testimoni" onclick="document.querySelector('.mobile-menu').classList.remove('open')">Testimoni</a>
  <a href="https://wa.me/081360765971" target="_blank" rel="noopener" onclick="document.querySelector('.mobile-menu').classList.remove('open')" style="color:#F5A623">Kontak</a>
</div>

<!-- HERO -->
<section class="hero" id="beranda">
  <div class="hero-bg"></div>
  <div class="hero-wash"></div>
  <div class="hero-content">
    <div class="hero-badge">PG &amp; TK Islam Terpadu · Tahun Ajaran 2025/2026</div>
    <h1 class="hero-title">Membangun Generasi<br>Qurani &amp; Kreatif</h1>
    <p class="hero-sub">IQRA' Creative House membimbing anak-anak dengan ilmu, akhlak, dan kreativitas melalui pendekatan islami yang menyenangkan.</p>
    <div class="hero-btns">
      <a href="#program" class="btn-primary">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h7a4 4 0 014 4v12H8a4 4 0 01-4-4z"/><path d="M20 4h-7a4 4 0 00-4 4v12h7a4 4 0 004-4z"/></svg>
        Lihat Program
      </a>
      <a href="#tentang" class="btn-secondary">
        Tentang Kami
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
      </a>
    </div>
  </div>
  <div class="hero-strip">
    <div class="hero-stat">
      <div class="stat-icon">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#4A9E5C" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h7a4 4 0 014 4v12H8a4 4 0 01-4-4z"/><path d="M20 4h-7a4 4 0 00-4 4v12h7a4 4 0 004-4z"/></svg>
      </div>
      <div class="stat-text"><strong>Belajar Al-Qur'an</strong><span>Mengaji, Tahsin &amp; Tahfidz</span></div>
    </div>
    <div class="hero-stat">
      <div class="stat-icon">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#4A9E5C" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l10 5-10 5L2 8z"/><path d="M6 10v5c0 2 3 3 6 3s6-1 6-3v-5"/></svg>
      </div>
      <div class="stat-text"><strong>Program Terstruktur</strong><span>Kurikulum Islami Terintegrasi</span></div>
    </div>
    <div class="hero-stat">
      <div class="stat-icon">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#4A9E5C" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.5"/><path d="M3 20c0-3 3-5 6-5s6 2 6 5"/><circle cx="17" cy="9" r="2.5"/><path d="M15 20c0-2 2-3.5 4-3.5s2.5 1 2.5 2"/></svg>
      </div>
      <div class="stat-text"><strong>Bimbingan Akhlak</strong><span>Pendidikan Karakter Islami</span></div>
    </div>
  </div>
</section>

<!-- TENTANG -->
<section class="about" id="tentang">
  <div class="about-grid">
    <div class="about-img">
      <img src="{{ asset('images/activity-2.jpg') }}"
           alt="Kegiatan belajar siswa ICH"
           loading="lazy"
           onerror="this.src='https://placehold.co/800x600/3DA746/white?text=ICH+Pendidikan'">
      <div class="about-img-badge">
        <div class="num">10+</div>
        <div class="txt">Tahun Pengalaman<br>Pendidikan Islam</div>
      </div>
    </div>
    <div>
      <div class="section-label">Tentang Kami</div>
      <h2 class="section-title">Kami Membimbing dengan<br>Ilmu &amp; Akhlak</h2>
      <p style="font-family:'Inter',sans-serif;font-size:15px;color:#687182;line-height:1.75;margin-bottom:24px;">
        IQRA' Creative House berfokus pada pembelajaran Al-Qur'an, pembentukan akhlak, dan pengembangan potensi peserta didik dengan pendekatan islami dan kreatif. Kami percaya setiap anak adalah amanah yang perlu dibimbing dengan kasih sayang.
      </p>
      <div class="about-features">
        <div class="about-feat">
          <div class="dot">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#4A9E5C" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5L20 7"/></svg>
          </div>
          <p><strong>Metode belajar menyenangkan</strong> — belajar sambil bermain agar anak tidak bosan dan mudah menyerap ilmu.</p>
        </div>
        <div class="about-feat">
          <div class="dot">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#4A9E5C" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5L20 7"/></svg>
          </div>
          <p><strong>Ustadzah berpengalaman</strong> — pengajar terlatih yang sabar, berdedikasi, dan memahami psikologi anak usia dini.</p>
        </div>
        <div class="about-feat">
          <div class="dot">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#4A9E5C" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5L20 7"/></svg>
          </div>
          <p><strong>Laporan perkembangan berkala</strong> — orang tua mendapat update kehadiran, nilai, dan aktivitas anak secara real-time.</p>
        </div>
      </div>
      <a href="{{ route('register') }}" class="btn-primary" style="margin-top:32px;display:inline-flex;">Daftar Sekarang</a>
    </div>
  </div>
</section>

<!-- PROGRAM -->
<section class="programs" id="program">
  <div class="programs-inner">
    <div class="center">
      <div class="section-label">Program Unggulan</div>
      <h2 class="section-title">Kurikulum Islami Terpadu</h2>
      <p class="section-sub">Setiap program dirancang untuk membentuk generasi yang cerdas, berakhlak mulia, dan mencintai Al-Qur'an sejak dini.</p>
    </div>
    <div class="programs-grid">
      <div class="prog-card">
        <div class="prog-icon">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#4A9E5C" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h7a4 4 0 014 4v12H8a4 4 0 01-4-4z"/><path d="M20 4h-7a4 4 0 00-4 4v12h7a4 4 0 004-4z"/></svg>
        </div>
        <div class="prog-title">Mengaji &amp; Tahsin</div>
        <div class="prog-desc">Belajar membaca Al-Qur'an dari iqra hingga hafalan, dengan bimbingan tajwid yang benar dan menyenangkan.</div>
      </div>
      <div class="prog-card">
        <div class="prog-icon">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#4A9E5C" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l2.7 5.5 6 .9-4.3 4.2 1 6L12 17l-5.4 2.6 1-6L3.3 9.4l6-.9z"/></svg>
        </div>
        <div class="prog-title">Tahfidz Al-Qur'an</div>
        <div class="prog-desc">Program hafalan Al-Qur'an terstruktur dengan metode muroja'ah harian dan pemantauan progress setiap minggu.</div>
      </div>
      <div class="prog-card">
        <div class="prog-icon">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#4A9E5C" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l10 5-10 5L2 8z"/><path d="M6 10v5c0 2 3 3 6 3s6-1 6-3v-5"/></svg>
        </div>
        <div class="prog-title">Pendidikan Akhlak</div>
        <div class="prog-desc">Membentuk karakter islami — adab terhadap orang tua, guru, teman, dan lingkungan — melalui kisah dan praktek langsung.</div>
      </div>
      <div class="prog-card">
        <div class="prog-icon">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#4A9E5C" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
        </div>
        <div class="prog-title">Kajian Islami</div>
        <div class="prog-desc">Pengenalan sejarah Islam, doa sehari-hari, dan nilai-nilai keislaman yang disampaikan dengan cerita yang menarik bagi anak.</div>
      </div>
      <div class="prog-card">
        <div class="prog-icon">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#4A9E5C" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18 M7 15l4-4 3 3 5-6"/></svg>
        </div>
        <div class="prog-title">Akademik Terpadu</div>
        <div class="prog-desc">Matematika, Bahasa Indonesia, IPA, dan IPS dikemas dalam kurikulum tematik islami yang integratif dan kontekstual.</div>
      </div>
      <div class="prog-card" style="background:linear-gradient(135deg,#3DA746 0%,#3B8249 100%);">
        <div class="prog-icon" style="background:rgba(255,255,255,0.2);">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/></svg>
        </div>
        <div class="prog-title" style="color:#fff;">Kreativitas &amp; Dakwah</div>
        <div class="prog-desc" style="color:rgba(255,255,255,0.85);">Menulis, seni islami, dan kegiatan kreatif yang membangun rasa percaya diri dan semangat berdakwah sejak dini.</div>
      </div>
    </div>
  </div>
</section>

<!-- AKTIVITAS -->
<section class="activities" id="aktivitas">
  <div class="activities-inner">
    <div class="center">
      <div class="section-label">Aktivitas Terbaru</div>
      <h2 class="section-title">Kegiatan Belajar Anak</h2>
      <p class="section-sub">Potret keseharian santri IQRA' Creative House yang penuh semangat belajar dan berkreasi.</p>
    </div>
    <div class="acts-grid">
      @foreach ([
        ['img' => 'activity-1.jpg', 'tag' => 'Al-Qur\'an', 'title' => 'Belajar Mengaji Dari Nol',       'meta' => 'Kelas PG A · 18 Feb 2026'],
        ['img' => 'activity-3.jpg', 'tag' => 'Tahfidz',    'title' => "Tahfidz Iqra & Muroja'ah",      'meta' => 'Kelas TK B · 15 Feb 2026'],
        ['img' => 'activity-4.jpg', 'tag' => 'Akhlak',     'title' => 'Adab & Akhlak Islami',          'meta' => 'Seluruh Kelas · 10 Feb 2026'],
        ['img' => 'activity-2.jpg', 'tag' => 'Tahsin',     'title' => 'Tahsin & Tajwid Intensif',      'meta' => 'Kelas TK A · 8 Feb 2026'],
        ['img' => 'activity-5.jpg', 'tag' => 'Kajian',     'title' => 'Kajian Islam & Sirah Nabawi',   'meta' => 'Kelas TK B · 5 Feb 2026'],
        ['img' => null,             'tag' => 'Kreativitas','title' => 'Menulis & Dakwah Kreatif',       'meta' => 'Kelas TK A & B · 1 Feb 2026'],
      ] as $act)
      <div class="act-card">
        <div class="act-img">
          @if($act['img'])
            <img src="{{ asset('images/' . $act['img']) }}"
                 alt="{{ $act['title'] }}"
                 loading="lazy"
                 onerror="this.parentElement.innerHTML='<div class=\'act-img-placeholder\'><svg width=\'48\' height=\'48\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'#3DA746\' stroke-width=\'1.5\'><path d=\'M4 20h4l11-11-4-4L4 16z\'/></svg></div>'">
          @else
            <div class="act-img-placeholder">
              <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#4A9E5C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h4l11-11-4-4L4 16z"/></svg>
            </div>
          @endif
        </div>
        <div class="act-body">
          <div class="act-tag">{{ $act['tag'] }}</div>
          <div class="act-title">{{ $act['title'] }}</div>
          <div class="act-meta">{{ $act['meta'] }}</div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

<!-- TESTIMONI -->
<section class="testimonials" id="testimoni">
  <div class="testimonials-inner">
    <div class="center">
      <div class="section-label">Testimoni</div>
      <h2 class="section-title">Kata Mereka</h2>
    </div>
    <div class="testi-grid">
      <div class="testi-card">
        <div class="testi-stars"><span>★</span><span>★</span><span>★</span><span>★</span><span>★</span></div>
        <p class="testi-text">"Aku senang belajar di ICH. Ustadzahnya baik, aku bisa mengaji dan hafal doa. Belajarnya sambil main, jadi tidak bosan."</p>
        <div class="testi-author">
          <div class="testi-avatar">F</div>
          <div>
            <div class="testi-name">Fatimah, 5 tahun</div>
            <div class="testi-role">Siswa TK A · 2025/2026</div>
          </div>
        </div>
      </div>
      <div class="testi-card">
        <div class="testi-stars"><span>★</span><span>★</span><span>★</span><span>★</span><span>★</span></div>
        <p class="testi-text">"Alhamdulillah, anak saya progress hafalannya luar biasa. Dalam 3 bulan sudah hafal 5 surat pendek. Ustadzahnya sabar dan penuh kasih."</p>
        <div class="testi-author">
          <div class="testi-avatar">
            <img src="{{ asset('images/testimonial-girl.jpg') }}"
                 alt="Ibu Siti"
                 onerror="this.outerHTML='<span>S</span>'">
          </div>
          <div>
            <div class="testi-name">Ibu Siti Nurhaliza</div>
            <div class="testi-role">Orang Tua Siswa · Kelas TK B</div>
          </div>
        </div>
      </div>
      <div class="testi-card">
        <div class="testi-stars"><span>★</span><span>★</span><span>★</span><span>★</span><span>★</span></div>
        <p class="testi-text">"Sistem laporan orang tua sangat membantu. Saya bisa pantau kehadiran dan nilai anak langsung dari ponsel. ICH benar-benar inovatif!"</p>
        <div class="testi-author">
          <div class="testi-avatar">B</div>
          <div>
            <div class="testi-name">Bapak Rizal Ahmad</div>
            <div class="testi-role">Orang Tua Siswa · Kelas PG A</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<div class="cta-section">
  <div class="cta-box">
    <h2 class="cta-title">Daftarkan Putra-Putri Anda<br>Bersama Kami</h2>
    <p class="cta-sub">Tahun ajaran 2025/2026 masih menerima pendaftaran. Tempat terbatas — jangan sampai terlewat!</p>
    <div class="cta-btns">
      <a href="https://wa.me/081360765971" target="_blank" class="btn-white">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4A9E5C" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 005 5L15 13l5 2v4a2 2 0 01-2 2A16 16 0 013 6a2 2 0 012-2z"/></svg>
        Hubungi Kami
      </a>
      <a href="{{ route('register') }}" class="btn-yellow">
        Daftar Sekarang
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
      </a>
    </div>
  </div>
</div>

<!-- FOOTER -->
<footer id="kontak">
  <div class="footer-grid">
    <div class="footer-brand">
      <div class="mark" style="overflow:hidden;padding:4px;"><img src="{{ asset('images/Logo.png') }}" alt="ICH Logo" style="width:100%;height:100%;object-fit:contain;"></div>
      <p>IQRA' Creative House adalah lembaga pendidikan PG-TK Islam yang berfokus pada pembentukan generasi Qurani dan kreatif.</p>
      <p style="margin-top:12px;">✉ info@ich.sch.id</p>
      <div class="socials">
        <a href="#">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 8a6 6 0 01-8 8 6 6 0 010-8"/><circle cx="12" cy="12" r="3"/></svg>
        </a>
        <a href="#">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a4 4 0 00-4 4v3H7v4h4v8h4v-8h3l1-4h-4V6a1 1 0 011-1h3z"/></svg>
        </a>
        <a href="#">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22.54 6.42a2.78 2.78 0 00-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46A2.78 2.78 0 001.46 6.42 29 29 0 001 12a29 29 0 00.46 5.58 2.78 2.78 0 001.95 1.96C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 001.95-1.96A29 29 0 0023 12a29 29 0 00-.46-5.58z"/><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02" fill="#fff" stroke="none"/></svg>
        </a>
      </div>
    </div>
    <div class="footer-col">
      <h4>Program</h4>
      <ul>
        <li><a href="#">Mengaji &amp; Tahsin</a></li>
        <li><a href="#">Tahfidz Al-Qur'an</a></li>
        <li><a href="#">Pendidikan Akhlak</a></li>
        <li><a href="#">Kajian Islami</a></li>
        <li><a href="#">Kreativitas</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Informasi</h4>
      <ul>
        <li><a href="#">Tentang Kami</a></li>
        <li><a href="#">Struktur Yayasan</a></li>
        <li><a href="{{ route('register') }}">Pendaftaran</a></li>
        <li><a href="#">Kalender Akademik</a></li>
        <li><a href="#">Galeri</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Kontak</h4>
      <ul>
        <li><a href="mailto:info@ich.sch.id">info@ich.sch.id</a></li>
        <li>Senin – Sabtu<br>07.00 – 14.00 WIB</li>
        <li>0813-6076-5971</li>
        <li style="margin-top:8px;">
          <a href="https://wa.me/081360765971" target="_blank" rel="noopener" style="display:inline-flex;align-items:center;gap:6px;background:#25D366;color:#fff;padding:8px 14px;border-radius:8px;font-weight:600;font-size:12px;text-decoration:none;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="#fff"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            Chat WhatsApp
          </a>
        </li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <span>Copyright &copy; {{ date('Y') }} IQRA' CREATIVE GROUP. All Rights Reserved.</span>
    <span>ICH GROUP · Membangun Generasi Qurani &amp; Kreatif</span>
  </div>
</footer>

<script>
  window.addEventListener('scroll', () => {
    document.getElementById('nav').classList.toggle('scrolled', window.scrollY > 60);
  });
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', () => {
      document.querySelector('.mobile-menu').classList.remove('open');
    });
  });
</script>
</body>
</html>
