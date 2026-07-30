@extends('layout.master')

@section('title', 'Invoice ' . $invoice_no)

@push('styles')
<style>
  /* ==========================================================
   EFEK NEON GLOWING UNTUK CARD ADMINLTE
========================================================== */

/* 1. Efek Glow Biru (Card Total Pengeluaran) */
.bg-primary, .bg-info {
    box-shadow: 0 0 15px rgba(0, 123, 255, 0.6) !important;
    border: 1px solid rgba(0, 123, 255, 0.4); 
    transition: all 0.3s ease-in-out;
}
.bg-primary:hover, .bg-info:hover {
    box-shadow: 0 0 25px rgba(0, 123, 255, 0.9), inset 0 0 10px rgba(0, 123, 255, 0.3) !important;
    transform: translateY(-2px); /* Bikin card naik dikit pas disorot */
}

/* 2. Efek Glow Hijau (Card Paid) */
.bg-success {
    box-shadow: 0 0 15px rgba(40, 167, 69, 0.6) !important;
    border: 1px solid rgba(40, 167, 69, 0.4);
    transition: all 0.3s ease-in-out;
}
.bg-success:hover {
    box-shadow: 0 0 25px rgba(40, 167, 69, 0.9), inset 0 0 10px rgba(40, 167, 69, 0.3) !important;
    transform: translateY(-2px);
}

/* 3. Efek Glow Merah (Card Unpaid) */
.bg-danger {
    box-shadow: 0 0 15px rgba(220, 53, 69, 0.6) !important;
    border: 1px solid rgba(220, 53, 69, 0.4);
    transition: all 0.3s ease-in-out;
}
.bg-danger:hover {
    box-shadow: 0 0 25px rgba(220, 53, 69, 0.9), inset 0 0 10px rgba(220, 53, 69, 0.3) !important;
    transform: translateY(-2px);
}

/* 4. Efek Glow Abu-abu/Putih Redup (Card Total Playlist) */
.bg-secondary, .bg-gray {
    box-shadow: 0 0 15px rgba(108, 117, 125, 0.5) !important;
    border: 1px solid rgba(108, 117, 125, 0.3);
    transition: all 0.3s ease-in-out;
}
.bg-secondary:hover, .bg-gray:hover {
    box-shadow: 0 0 25px rgba(108, 117, 125, 0.8), inset 0 0 10px rgba(108, 117, 125, 0.2) !important;
    transform: translateY(-2px);
}
  /* 1. Bikin Background Jadi Biru Gelap Nyaris Hitam (Biar Neon-nya Nyala) */
body, .content-wrapper, .main-header, .main-sidebar, .control-sidebar {
    background-color: #0b1120 !important; 
    color: #e2e8f0 !important;
}

/* 2. Ubah Warna Navbar dan Sidebar Biar Menyatu */
.main-header {
    border-bottom: 1px solid #00f3ff;
    background-color: rgba(11, 17, 32, 0.9) !important;
}

/* 3. Bikin Card/Kotak-kotak Punya Efek Glowing Neon Biru */
.card {
    background-color: #111827 !important;
    border: 1px solid #00f3ff;
    box-shadow: 0 0 10px rgba(0, 243, 255, 0.2), inset 0 0 5px rgba(0, 243, 255, 0.1);
    color: #e2e8f0;
}

/* 4. Efek Bercahaya Pas Tombol atau Card Di-hover (Disorot Mouse) */
.card:hover, .btn-primary {
    box-shadow: 0 0 20px rgba(0, 243, 255, 0.6), inset 0 0 10px rgba(0, 243, 255, 0.3);
    transition: all 0.3s ease-in-out;
}

/* 5. Ganti Warna Text Penting Jadi Biru Terang (Cyan) */
h1, h2, h3, h4, .card-title, a {
    color: #00f3ff !important;
    text-shadow: 0 0 5px rgba(0, 243, 255, 0.5);
}
  /* Pindahin font dan warna ke bungkusnya aja biar gak ngerusak tema utama Dashboard */
  .page-wrap { max-width: 780px; margin: 32px auto; padding: 0 16px 60px; font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; color: #222; }

  /* ── CARD ── */
  .inv-card { background: #fff; border-radius: 10px; box-shadow: 0 4px 24px rgba(0,0,0,.10); overflow: hidden; }

  /* ── HEADER ── */
  .inv-header { background: #1e2d4f; color: #fff; padding: 28px 36px; display: flex; justify-content: space-between; align-items: center; }
  .inv-header .logo-wrap { display: flex; align-items: center; gap: 12px; }
  .inv-header .logo-img { height: 48px; width: auto; }
  .inv-header .company-info { font-size: 12px; color: #b8c9e8; line-height: 1.6; }
  .inv-header .company-info .company-name { font-size: 18px; font-weight: 700; color: #fff; }
  .inv-header .inv-title-block { text-align: right; }
  .inv-header .inv-title-block h1 { font-size: 32px; font-weight: 800; letter-spacing: 3px; color: #fff; }
  .inv-header .inv-title-block .inv-no { font-size: 13px; color: #b8c9e8; margin-top: 4px; }

  /* ── BODY ── */
  .inv-body { padding: 32px 36px; }

  /* ── BILLED TO + DATE ROW ── */
  .inv-meta { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 28px; }
  .inv-meta .billed-label { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #888; margin-bottom: 6px; }
  .inv-meta .billed-name { font-size: 15px; font-weight: 700; color: #1e2d4f; }
  .inv-meta .billed-detail { font-size: 12px; color: #555; line-height: 1.7; margin-top: 3px; }
  .inv-meta .date-block { text-align: right; }
  .inv-meta .date-block .date-label { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #888; margin-bottom: 4px; }
  .inv-meta .date-block .date-val { font-size: 14px; font-weight: 600; color: #1e2d4f; }
  .status-badge { display: inline-block; padding: 4px 14px; border-radius: 20px; font-size: 11px; font-weight: 700; letter-spacing: .5px; margin-top: 8px; }
  .status-paid   { background: #d4edda; color: #155724; }
  .status-unpaid { background: #f8d7da; color: #721c24; }

  /* ── DIVIDER ── */
  .inv-divider { border: none; border-top: 1px solid #e9ecef; margin: 0 0 24px; }

  /* ── TABLE ── */
  .inv-table { width: 100%; border-collapse: collapse; }
  .inv-table thead tr { background: #1e2d4f; color: #fff; }
  .inv-table thead th { padding: 11px 14px; font-size: 12px; font-weight: 600; text-align: left; }
  .inv-table thead th:last-child { text-align: right; }
  .inv-table tbody tr { border-bottom: 1px solid #f0f2f5; }
  .inv-table tbody tr:hover { background: #fafbff; }
  .inv-table tbody td { padding: 12px 14px; font-size: 12px; vertical-align: top; }
  .inv-table tbody td:last-child { text-align: right; font-weight: 600; }
  .inv-table .content-title { font-weight: 600; color: #1e2d4f; }
  .inv-table .content-file  { font-size: 11px; color: #888; margin-top: 2px; word-break: break-all; }

  /* ── TOTAL BLOCK ── */
  .total-block { margin-top: 20px; display: flex; justify-content: flex-end; }
  .total-inner { min-width: 280px; }
  .total-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 13px; border-bottom: 1px solid #f0f2f5; }
  .total-row.grand { background: #1e2d4f; color: #fff; border-radius: 8px; padding: 12px 16px; margin-top: 8px; font-size: 15px; font-weight: 700; border: none; }

  /* ── CATATAN ── */
  .inv-note { margin-top: 28px; padding: 16px 20px; background: #f8f9ff; border-radius: 8px; border-left: 3px solid #1e2d4f; }
  .inv-note p { font-size: 12px; color: #555; line-height: 1.7; }

  /* ── ACTIONS ── */
  .inv-actions { margin-top: 28px; display: flex; gap: 12px; justify-content: center; }
  .btn-print { background: #1e2d4f; color: #fff; border: none; padding: 10px 28px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; }
  .btn-back  { background: #6c757d; color: #fff; border: none; padding: 10px 28px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; display: flex; align-items: center; gap: 8px; }
  .btn-print:hover { background: #16213a; }
  .btn-back:hover  { background: #5a6268; }

  /* ── MODE CETAK (PRINT) ── */
  @media print {
    body { background: #fff !important; }
    .inv-actions { display: none !important; }
    .page-wrap { margin: 0; padding: 0; max-width: 100%; }
    .inv-card { box-shadow: none; border-radius: 0; }
    
    /* SEMBUNYIKAN SIDEBAR & HEADER BAWAAN LAYOUT SAAT DIPRINT */
    .main-header, .main-sidebar, .main-footer { display: none !important; }
    .content-wrapper { margin-left: 0 !important; background-color: #fff !important; padding: 0 !important; }
  }
</style>
@endpush

@section('content')

@php
  use Carbon\Carbon;
  $vendorName    = $vendor['nama']          ?? $vendor['name']         ?? 'GIK Vision';
  $vendorCompany = $vendor['company']       ?? '';
  $vendorLoc     = $vendor['location']      ?? '';
  $vendorEmail   = $vendor['email']         ?? '';
  $custName      = trim(($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? ''));
  if (!$custName) $custName = $customer['name'] ?? '-';
  $custEmail     = $customer['email']       ?? '-';
  $custPhone     = $customer['phone_number'] ?? '-';
  $custCompany   = $customer['company_name'] ?? '';
  $custCity      = $customer['city']         ?? '';
  $deviceName    = $device['device_name']    ?? '-';
  $deviceLoc     = trim(($device['kota'] ?? '') . ', ' . ($device['provinsi'] ?? ''), ', ');
  $startContract = $playlist['start_contract'] ?? null;
  $endContract   = $playlist['end_contract']   ?? null;
  $periodeTayang = $startContract && $endContract
    ? Carbon::parse($startContract)->format('d/m/Y') . ' – ' . Carbon::parse($endContract)->format('d/m/Y')
    : '-';
  $tanggalTerbit = now()->format('d F Y');
@endphp

<div class="page-wrap">
  <div class="inv-card">

    {{-- ── HEADER INVOICE ── --}}
    <div class="inv-header">
      <div class="logo-wrap">
        <img src="{{ asset('dist/img/gikvision-logo.png') }}"
             alt="GIK Vision"
             class="logo-img"
             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
        <div style="display:none;flex-direction:column;">
          <span style="font-size:22px;font-weight:800;color:#fff;letter-spacing:1px;">GIK VISION</span>
        </div>
        <div class="company-info" style="margin-left:4px;">
          <div class="company-name">GIK Vision</div>
          <div>Digital Signage Network</div>
          <div>support@gikvision.id &nbsp;|&nbsp; www.gikvision.id</div>
        </div>
      </div>
      <div class="inv-title-block">
        <h1>INVOICE</h1>
        <div class="inv-no">No. {{ $invoice_no }}</div>
      </div>
    </div>

    {{-- ── BODY INVOICE ── --}}
    <div class="inv-body">

      {{-- Billed To + Tanggal --}}
      <div class="inv-meta">
        <div>
          <div class="billed-label">Ditagihkan Kepada</div>
          <div class="billed-name">{{ $custName }}</div>
          <div class="billed-detail">
            @if($custCompany) {{ $custCompany }}<br> @endif
            {{ $custEmail }}<br>
            {{ $custPhone }}
            @if($custCity) <br>{{ $custCity }} @endif
          </div>
        </div>
        <div class="date-block">
          <div class="date-label">Tanggal Terbit</div>
          <div class="date-val">{{ $tanggalTerbit }}</div>
          <div>
            <span class="status-badge {{ $is_paid ? 'status-paid' : 'status-unpaid' }}">
              {{ $status }}
            </span>
          </div>
        </div>
      </div>

      <hr class="inv-divider">

      {{-- Tabel Konten --}}
      <table class="inv-table">
        <thead>
          <tr>
            <th style="width:35%">Deskripsi</th>
            <th>Device</th>
            <th>Periode Tayang</th>
            <th>Jam Tayang</th>
            <th style="text-align:right">Jumlah</th>
          </tr>
        </thead>
        <tbody>
          @forelse($items as $item)
          @php
            $contentTitle = $item['content']['title'] ?? '-';
            $contentFile  = basename($item['content']['file'] ?? '');
            $itemStart    = $item['start_date'] ?? ($startContract ? substr($startContract,0,10) : '-');
            $itemEnd      = $item['end_date']   ?? ($endContract   ? substr($endContract,0,10)   : '-');
            $itemPeriode  = ($itemStart && $itemEnd && $itemStart !== '-')
              ? Carbon::parse($itemStart)->format('d/m/Y') . ' – ' . Carbon::parse($itemEnd)->format('d/m/Y')
              : $periodeTayang;
            $jamMulai  = $item['start_time'] ?? '-';
            $jamSelesai= $item['end_time']   ?? '-';
            $subTotal  = $item['pricing']->sub_total ?? $grand_total;
          @endphp
          <tr>
            <td>
              <div class="content-title">{{ $playlist['playlist_name'] ?? '-' }}</div>
              <div class="content-file">{{ $contentFile ?: $contentTitle }}</div>
            </td>
            <td>{{ $deviceName }}</td>
            <td>{{ $itemPeriode }}</td>
            <td>{{ $jamMulai }} – {{ $jamSelesai }}</td>
            <td>Rp {{ number_format($subTotal, 0, ',', '.') }}</td>
          </tr>
          @empty
          <tr>
            <td colspan="5" style="text-align:center;color:#888;padding:24px;">
              Belum ada data konten.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>

      {{-- Total --}}
      <div class="total-block">
        <div class="total-inner">
          <div class="total-row">
            <span>Subtotal</span>
            <span>Rp {{ number_format($grand_total, 0, ',', '.') }}</span>
          </div>
          <div class="total-row grand">
            <span>TOTAL TAGIHAN</span>
            <span>Rp {{ number_format($grand_total, 0, ',', '.') }}</span>
          </div>
        </div>
      </div>

      {{-- Catatan --}}
      <div class="inv-note">
        <p><strong>CATATAN</strong><br>
        Invoice ini dibuat otomatis berdasarkan kontrak playlist iklan yang aktif.
        Mohon lakukan pembayaran sesuai nominal kontrak di atas.</p>
      </div>

      {{-- Actions --}}
      <div class="inv-actions">
        <button class="btn-print" onclick="window.print()">
          <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
            <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
            <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
          </svg>
          Cetak Invoice
        </button>
        {{-- Tombol kembali pakai route terbaru lu --}}
        <a href="{{ route('vendor.playlist') }}" class="btn-back">
          <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
          </svg>
          Kembali ke Daftar
        </a>
      </div>

    </div>
  </div>
</div>

@endsection