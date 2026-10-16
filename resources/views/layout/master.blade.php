<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  
  <title>@yield('title') - CMS GikVision</title>
  
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
  
  {{-- ==========================================================
       CSS GLOBAL CYBERPUNK (BERLAKU DI SEMUA HALAMAN)
  ========================================================== --}}
  <style>
    /* 1. BACKGROUND & HEADER */
    body, .content-wrapper, .main-header, .main-sidebar, .control-sidebar { background-color: #030710 !important; color: #a8b8d0 !important; }
    .main-header { border-bottom: 1px solid rgba(0, 180, 200, 0.2); background-color: rgba(3, 7, 16, 0.97) !important; }

    /* 2. MENU SIDEBAR AKTIF (SINKRON WARNA BIRU NEON) */
    .nav-sidebar .nav-item > .nav-link.active {
        background-color: rgba(0, 80, 140, 0.15) !important; 
        border-left: 3px solid rgba(0, 180, 200, 0.5) !important; 
        color: #5fb8cc !important; 
        text-shadow: 0 0 3px rgba(0, 180, 200, 0.2) !important; 
    }
    .nav-sidebar .nav-item > .nav-link.active i, .nav-sidebar .nav-item > .nav-link.active p {
        color: #5fb8cc !important; text-shadow: 0 0 3px rgba(0, 180, 200, 0.2) !important;
    }
    .nav-sidebar .nav-item > .nav-link:hover { color: #5fb8cc !important; background-color: rgba(0, 180, 200, 0.03) !important; }

    /* ==========================================================
   3. EFEK GLOWING BOX BACKGROUND & HOVER (TERMASUK KUNING)
========================================================== */
.bg-primary, .bg-info { background-color: #007bff !important; box-shadow: 0 0 15px rgba(0, 123, 255, 0.6) !important; border: 1px solid rgba(0, 123, 255, 0.4) !important; transition: all 0.3s ease-in-out; }
.bg-primary:hover, .bg-info:hover { box-shadow: 0 0 25px rgba(0, 123, 255, 0.9), inset 0 0 10px rgba(0, 123, 255, 0.3) !important; transform: translateY(-4px); }

.bg-success { background-color: #28a745 !important; box-shadow: 0 0 15px rgba(40, 167, 69, 0.6) !important; border: 1px solid rgba(40, 167, 69, 0.4) !important; transition: all 0.3s ease-in-out; }
.bg-success:hover { box-shadow: 0 0 25px rgba(40, 167, 69, 0.9), inset 0 0 10px rgba(40, 167, 69, 0.3) !important; transform: translateY(-4px); }

.bg-danger { background-color: #dc3545 !important; box-shadow: 0 0 15px rgba(220, 53, 69, 0.6) !important; border: 1px solid rgba(220, 53, 69, 0.4) !important; transition: all 0.3s ease-in-out; }
.bg-danger:hover { box-shadow: 0 0 25px rgba(220, 53, 69, 0.9), inset 0 0 10px rgba(220, 53, 69, 0.3) !important; transform: translateY(-4px); }

.bg-warning { background-color: #ffc107 !important; box-shadow: 0 0 15px rgba(255, 193, 7, 0.5) !important; border: 1px solid rgba(255, 193, 7, 0.4) !important; transition: all 0.3s ease-in-out; }
.bg-warning:hover { box-shadow: 0 0 25px rgba(255, 193, 7, 0.8), inset 0 0 10px rgba(255, 193, 7, 0.3) !important; transform: translateY(-4px); }

.bg-secondary, .bg-gray, .bg-dark { background-color: #6c757d !important; box-shadow: 0 0 15px rgba(108, 117, 125, 0.5) !important; border: 1px solid rgba(108, 117, 125, 0.3) !important; transition: all 0.3s ease-in-out; }
.bg-secondary:hover, .bg-gray:hover, .bg-dark:hover { box-shadow: 0 0 25px rgba(108, 117, 125, 0.8), inset 0 0 10px rgba(108, 117, 125, 0.2) !important; transform: translateY(-4px); }
    /* 4. EFEK TEKS ANGKA NEON SESUAI WARNA CARD */
    .small-box.bg-primary h3, .small-box.bg-info h3, .small-box.bg-primary h5.rp-kecil, .small-box.bg-info h5.rp-kecil { color: #00f3ff !important; text-shadow: 0 0 10px rgba(0, 243, 255, 0.8), 0 0 20px rgba(0, 243, 255, 0.6), 0 0 30px rgba(0, 243, 255, 0.4) !important; font-weight: 900; }
    .small-box.bg-success h3 { color: #39ff14 !important; text-shadow: 0 0 10px rgba(57, 255, 20, 0.8), 0 0 20px rgba(57, 255, 20, 0.6), 0 0 30px rgba(57, 255, 20, 0.4) !important; font-weight: 900; }
    .small-box.bg-danger h3 { color: #ff2a55 !important; text-shadow: 0 0 10px rgba(255, 42, 85, 0.8), 0 0 20px rgba(255, 42, 85, 0.6), 0 0 30px rgba(255, 42, 85, 0.4) !important; font-weight: 900; }
    .small-box.bg-warning h3 { color: #fffb00 !important; text-shadow: 0 0 10px rgba(255, 251, 0, 0.8), 0 0 20px rgba(255, 251, 0, 0.6), 0 0 30px rgba(255, 251, 0, 0.4) !important; font-weight: 900; }
    .small-box.bg-secondary h3, .small-box.bg-gray h3 { color: #ffffff !important; text-shadow: 0 0 10px rgba(255, 255, 255, 0.6), 0 0 20px rgba(255, 255, 255, 0.4), 0 0 30px rgba(255, 255, 255, 0.2) !important; font-weight: 900; }
    .small-box.bg-warning p { color: #e2e8f0 !important; font-weight: 600; } /* Teks label card kuning jadi putih */

    /* ==========================================================
   5. TINGGI CARD DAN ICON (FIX PEOT & TEKS KECIL)
========================================================== */
.small-box { 
    border-radius: 10px; 
    overflow: hidden; 
    position: relative; 
    height: 100%; /* Kunci sakti biar tinggi kotak selalu sama rata sejajar! */
}
.small-box .inner { padding: 35px 20px !important; }

/* Ukuran standar buat card hijau, merah, abu, kuning */
.small-box h3 { font-size: 37px; font-weight: 900; margin: 0; line-height: 1; }

/* INI YANG KETINGGALAN BRO: Bikin teks Rp 0 jadi gede lagi */
.small-box h5.rp-kecil { font-size: 32px !important; font-weight: 900; margin: 0; line-height: 1.1; }

.small-box p { font-size: 15px; margin-top: 6px; margin-bottom: 0; }

/* Bikin icon background otomatis pas di tengah, gak peduli teks lu segede apa */
.small-box .icon { 
    position: absolute; 
    top: 50% !important; 
    transform: translateY(-50%); 
    right: 20px !important; 
    opacity: 0.8; 
    pointer-events: none; 
}
.small-box .icon > i { 
    font-size: 70px !important; 
    color: rgba(255, 255, 255, 0.12) !important; 
    transition: transform 0.3s ease; /* Biar animasi zoom-nya mulus */
}
.small-box:hover .icon > i { 
    transform: scale(1.15); /* Animasi icon membesar pas disorot mouse */
}
    /* 6. TABEL & KOTAK LAINNYA */
    .card { background-color: #070f1e !important; border: 1px solid rgba(0, 180, 200, 0.12); box-shadow: 0 0 6px rgba(0, 180, 200, 0.06), inset 0 0 3px rgba(0, 180, 200, 0.03); color: #a8b8d0; }
    h1, h2, h3, h4, .card-title, a.view-more-link { color: #5fb8cc !important; text-shadow: 0 0 3px rgba(0, 180, 200, 0.2); }
    .table { color: #a8b8d0; }
    .table td, .table th { border-color: rgba(0, 180, 200, 0.08); }
    .thead-dark th { background-color: rgba(0, 0, 0, 0.6) !important; border-color: rgba(0, 180, 200, 0.12) !important; }
    .table-hover tbody tr { transition: background-color 0.15s ease, box-shadow 0.15s ease; }
    .table-hover tbody tr:hover { background-color: rgba(0, 150, 180, 0.10) !important; box-shadow: inset 0 0 8px rgba(0, 150, 180, 0.15), inset 4px 0 0 rgba(0, 170, 200, 0.45); }
    .table-hover tbody tr:hover td { color: #b8dce8 !important; }

    /* 7. CUSTOM UI (DARI PERMINTAAN.BLADE.PHP) */
    .sisa-badge{display:inline-flex;align-items:center;justify-content:center;width:54px;height:54px;border-radius:50%;font-size:11px;font-weight:700;line-height:1.2;text-align:center;flex-direction:column}
    .sisa-badge.aman{background:#28a745;color:#fff; box-shadow: 0 0 10px rgba(40,167,69,0.5);}
    .sisa-badge.warning{background:#ffc107;color:#333; box-shadow: 0 0 10px rgba(255,193,7,0.5);}
    .sisa-badge.danger{background:#dc3545;color:#fff; box-shadow: 0 0 10px rgba(220,53,69,0.5);}
    .sisa-badge .hari-num{font-size:16px;font-weight:800;line-height:1}
    .badge-video{background:#6f42c1;color:#fff;font-size:10px;padding:2px 7px;border-radius:10px}
    .badge-image{background:#17a2b8;color:#fff;font-size:10px;padding:2px 7px;border-radius:10px}
    .remaining-field{background:rgba(0,30,60,.4)!important;border:1px solid rgba(0,217,255,.45)!important;cursor:not-allowed;font-weight:600;color:#6fe3ff!important;box-shadow:0 0 10px rgba(0,217,255,.2),inset 0 0 8px rgba(0,217,255,.07)}
    .neon-field{background:rgba(0,30,60,.4)!important;border:1px solid rgba(0,217,255,.45)!important;color:#6fe3ff!important;font-weight:600;box-shadow:0 0 10px rgba(0,217,255,.2),inset 0 0 8px rgba(0,217,255,.07);transition:border-color .15s,box-shadow .15s,color .15s}
    .neon-field:focus{border-color:rgba(0,243,255,.9)!important;box-shadow:0 0 18px rgba(0,243,255,.5),inset 0 0 10px rgba(0,243,255,.12)!important;color:#eaffff!important}
    .neon-field::placeholder{color:rgba(150,215,240,.45)!important}
    .btn-neon{background:linear-gradient(135deg,#00b4c8,#007bff)!important;border:1px solid rgba(0,243,255,.6)!important;color:#eaffff!important;font-weight:700;box-shadow:0 0 14px rgba(0,217,255,.45),inset 0 0 8px rgba(0,243,255,.15);transition:all .2s ease}
    .btn-neon:hover{box-shadow:0 0 22px rgba(0,243,255,.75),inset 0 0 12px rgba(0,243,255,.25);transform:translateY(-2px);color:#fff!important}
    .content-row{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.1);border-radius:8px;padding:14px 16px;margin-bottom:12px;position:relative}
    .content-row .btn-remove-row{position:absolute;top:8px;right:8px}
    #wizardSteps{position:sticky;top:57px;z-index:100;background:#1e2230;padding:10px 0;margin-bottom:0}
    #wizardSteps .nav-link{background:rgba(255,255,255,.05);color:#aaa;border-radius:6px;margin:0 3px;font-size:13px;font-weight:600;cursor:default}
    #wizardSteps .nav-link.active{background:#007bff;color:#fff}
    #wizardSteps .nav-link.done{background:#28a745;color:#fff}
    #wizardSteps .nav-link.disabled{opacity:.45}
    .lokasi-card{background:rgba(255,255,255,.04);border:2px solid rgba(255,255,255,.1);transition:border-color .15s,transform .15s;cursor:pointer;border-radius:8px}
    .lokasi-card:hover{border-color:#17a2b8;transform:translateY(-2px)}
    .lokasi-card.selected{border-color:#28a745;box-shadow:0 0 0 2px rgba(40,167,69,.4)}
    .lokasi-thumb{height:120px;border-radius:6px 6px 0 0;background:rgba(255,255,255,.07);display:flex;align-items:center;justify-content:center;overflow:hidden}
    .drum-picker-wrap{display:inline-flex;align-items:center;gap:10px;background:linear-gradient(180deg,rgba(0,40,70,.6),rgba(4,10,22,.9));border:1px solid rgba(0,217,255,.5);border-radius:14px;padding:10px 14px;box-shadow:0 0 16px rgba(0,217,255,.25),inset 0 0 14px rgba(0,217,255,.08)}
    .drum-col-wrap{position:relative;width:52px;height:120px;border-radius:8px;background:rgba(0,12,26,.55);overflow:hidden}
    .drum-col{width:52px;height:120px;overflow-y:scroll;scroll-snap-type:y mandatory;scrollbar-width:none;-webkit-mask-image:linear-gradient(180deg,transparent 0,#000 34px,#000 86px,transparent 100%);mask-image:linear-gradient(180deg,transparent 0,#000 34px,#000 86px,transparent 100%)}
    .drum-col::-webkit-scrollbar{display:none}
    .drum-highlight{position:absolute;top:40px;left:2px;right:2px;height:40px;background:linear-gradient(180deg,rgba(0,217,255,.14),rgba(0,123,255,.3));border-radius:8px;pointer-events:none;border-top:1px solid rgba(0,217,255,.85);border-bottom:1px solid rgba(0,217,255,.85);box-shadow:0 0 16px rgba(0,217,255,.55),inset 0 0 12px rgba(0,217,255,.35);z-index:2}
    .drum-sep{font-size:26px;font-weight:900;color:#00f3ff;margin:0 3px;line-height:128px;text-shadow:0 0 8px rgba(0,243,255,.95),0 0 20px rgba(0,243,255,.5)}
    .drum-item{height:40px;display:flex;align-items:center;justify-content:center;scroll-snap-align:center;font-size:16px;font-weight:700;color:rgba(160,200,225,.32);user-select:none;transition:color .12s,font-size .12s,text-shadow .12s}
    .drum-item.active{color:#eaffff;font-size:20px;font-weight:900;text-shadow:0 0 6px rgba(0,243,255,1),0 0 16px rgba(0,243,255,.8),0 0 28px rgba(0,243,255,.5);animation:drumGlowPulse 1.3s ease-in-out infinite}
    .drum-col-wrap.drum-scroll .drum-highlight{animation:drumHighlightFlash .45s ease-out}
    @keyframes drumGlowPulse{0%,100%{text-shadow:0 0 6px rgba(0,243,255,1),0 0 16px rgba(0,243,255,.8),0 0 28px rgba(0,243,255,.5)}50%{text-shadow:0 0 4px rgba(0,243,255,1),0 0 24px rgba(0,243,255,1),0 0 44px rgba(0,243,255,.85)}}
    @keyframes drumHighlightFlash{0%{box-shadow:0 0 12px rgba(0,243,255,.4),inset 0 0 8px rgba(0,243,255,.25)}40%{box-shadow:0 0 30px rgba(0,243,255,.95),inset 0 0 18px rgba(0,243,255,.6)}100%{box-shadow:0 0 16px rgba(0,217,255,.55),inset 0 0 12px rgba(0,217,255,.35)}}
    .cal-grid-table{width:100%;table-layout:fixed;border-collapse:collapse}
    .cal-grid-table th{text-align:center;font-size:12px;font-weight:600;padding:6px 2px;color:#9aa0ae;border-bottom:1px solid rgba(255,255,255,.1)}
    .cal-grid-table td{vertical-align:top;height:72px;padding:4px;border:1px solid rgba(255,255,255,.06);font-size:13px;cursor:pointer;transition:background .1s;position:relative}
    .cal-grid-table td.other-month{opacity:.3;cursor:default;pointer-events:none}
    .cal-grid-table td.past-date{opacity:.35;cursor:default;pointer-events:none}
    .cal-grid-table td.today-cell .day-num{color:#17a2b8;font-weight:800}
    .cal-grid-table td:not(.other-month):not(.past-date):hover{background:rgba(255,255,255,.07)}
    .cal-grid-table td.in-range{background:rgba(0,123,255,.18)!important}
    .cal-grid-table td.range-start{background:rgba(0,123,255,.65)!important;border-radius:6px 0 0 6px}
    .cal-grid-table td.range-end{background:rgba(0,123,255,.65)!important;border-radius:0 6px 6px 0}
    .cal-grid-table td.range-start.range-end{border-radius:6px}
    .booking-bar{height:16px;border-radius:3px;margin-top:2px;font-size:9px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;padding:2px 4px;line-height:12px;color:#fff}
    .day-num{font-size:12px;font-weight:600;line-height:1.2}
    .step-section{padding-top:16px}
    .status-paid{background:#28a745;color:#fff;font-size:10px;padding:2px 8px;border-radius:8px;font-weight:700;display:inline-block}
    .status-unpaid{background:#dc3545;color:#fff;font-size:10px;padding:2px 8px;border-radius:8px;font-weight:700;display:inline-block}
    .sisa-mini{display:inline-flex;flex-direction:column;align-items:center;justify-content:center;width:44px;height:44px;border-radius:50%;font-size:10px;font-weight:800;line-height:1.1}
    .sisa-mini.aman{background:#28a745;color:#fff}
    .sisa-mini.warning{background:#ffc107;color:#333}
    .sisa-mini.danger{background:#dc3545;color:#fff}
    .satuan-snap-wrap{display:flex;align-items:center;gap:6px}
    .satuan-snap-wrap input[type=number]{width:80px}
    .satuan-snap-badge{font-size:12px;font-weight:700;padding:3px 8px;border-radius:6px;background:rgba(0,123,255,.2);color:#64b5f6;white-space:nowrap}
    .info-konten-box{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:8px;padding:10px 14px}
    .info-konten-box .info-label{font-size:11px;color:#9aa0ae;margin-bottom:2px}
    .info-konten-box .info-val{font-size:15px;font-weight:700;color:#fff}
    .contract-display{font-size:18px;font-weight:800;color:#28a745;letter-spacing:.5px}
    /* 8. TOAST NOTIFICATION */
    #toast-container { position: fixed; top: 20px; right: 20px; z-index: 99999; display: flex; flex-direction: column; gap: 8px; }
    .toast-notif { display: flex; align-items: center; gap: 10px; min-width: 260px; max-width: 380px; padding: 12px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; color: #fff; box-shadow: 0 4px 20px rgba(0,0,0,0.4); opacity: 1; transform: translateX(0); transition: opacity 0.4s ease, transform 0.4s ease; animation: toastSlideIn .5s cubic-bezier(.22,1,.36,1) backwards; }
    .toast-notif.success { background: linear-gradient(135deg, #1a5c2a, #28a745); border-left: 4px solid #39ff14; }
    .toast-notif.error   { background: linear-gradient(135deg, #5c1a1a, #dc3545); border-left: 4px solid #ff2a55; }
    .toast-notif.warning { background: linear-gradient(135deg, #5c4a00, #ffc107); border-left: 4px solid #fffb00; color: #333; }
    .toast-notif.hide { opacity: 0; transform: translateX(40px); }
    @keyframes toastSlideIn{0%{opacity:0;transform:translateX(120%)}60%{opacity:1;transform:translateX(-8px)}100%{opacity:1;transform:translateX(0)}}

    /* ==========================================================
   9. ANIMASI MUNCUL (FADE IN UP & EFEK BERGELOMBANG SMOOTH)
========================================================== */
/* Bikin racikan efek gelombang: Naik ngesut -> kelewatan dikit -> turun -> pas tengah */
@keyframes waveUpCyberpunk {
    0% { opacity: 0; top: 80px; animation-timing-function: ease-out; }
    45% { opacity: 1; top: -12px; animation-timing-function: ease-in-out; }
    70% { top: 5px; animation-timing-function: ease-in-out; }
    85% { top: -2px; animation-timing-function: ease-in-out; }
    100% { opacity: 1; top: 0; }
}

/* 1. TABEL DAFTAR PLAYLIST (.card) - MUNCUL DULUAN */
.card {
    position: relative;
    /* Durasi 1s bikin pergerakan tabelnya ngebut tapi tetap smooth */
    animation: waveUpCyberpunk 1s forwards;
    opacity: 0; 
    animation-delay: 0.1s; /* Nyaris langsung muncul pas halaman diload */
}

/* 2. CARD SUMMARY ATAS (.small-box) - MUNCUL BELAKANGAN & PERGERAKAN LEBIH LAMBAT */
.small-box {
    position: relative;
    /* Durasi 1.5s bikin pergerakannya agak melayang/lambat sesuai request lu */
    animation: waveUpCyberpunk 1.5s forwards;
    opacity: 0; 
}

/* Bikin 4 kotak summary atas munculnya berurutan dari kiri ke kanan (Staggering) */
.col-lg-3:nth-child(1) .small-box { animation-delay: 0.25s; }
.col-lg-3:nth-child(2) .small-box { animation-delay: 0.40s; }
.col-lg-3:nth-child(3) .small-box { animation-delay: 0.55s; }
.col-lg-3:nth-child(4) .small-box { animation-delay: 0.70s; }
  </style>

  @stack('styles')
</head>

<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">
  <div id="toast-container"></div>

  @include('layout.header')
  @include('layout.sidebar')

  <div class="content-wrapper">
    @yield('content')
  </div>

  @include('layout.footer')
</div>

<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('dist/js/adminlte.min.js') }}"></script>

@stack('scripts')
</body>
</html>