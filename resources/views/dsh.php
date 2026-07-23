<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Dashboard - CMS GikVision</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <style>
    /* ── Sisa badge ── */
    .sisa-badge{display:inline-flex;align-items:center;justify-content:center;width:54px;height:54px;border-radius:50%;font-size:11px;font-weight:700;line-height:1.2;text-align:center;flex-direction:column}
    .sisa-badge.aman{background:#28a745;color:#fff}
    .sisa-badge.warning{background:#ffc107;color:#333}
    .sisa-badge.danger{background:#dc3545;color:#fff}
    .sisa-badge .hari-num{font-size:16px;font-weight:800;line-height:1}

    /* ── Status badge ── */
    .status-paid{background:#28a745;color:#fff;font-size:11px;padding:3px 10px;border-radius:10px;font-weight:700;display:inline-block}
    .status-unpaid{background:#dc3545;color:#fff;font-size:11px;padding:3px 10px;border-radius:10px;font-weight:700;display:inline-block}
    /* legacy aliases biar tidak crash kalau ada data lama */
    .status-pending{background:#ffc107;color:#333;font-size:11px;padding:3px 10px;border-radius:10px;font-weight:700;display:inline-block}
    .status-approved{background:#28a745;color:#fff;font-size:11px;padding:3px 10px;border-radius:10px;font-weight:700;display:inline-block}
    .status-rejected{background:#dc3545;color:#fff;font-size:11px;padding:3px 10px;border-radius:10px;font-weight:700;display:inline-block}

    /* ── Small box override (ukuran sama kayak gambar 3) ── */
    .small-box{border-radius:10px;overflow:hidden;box-shadow:6 4px 10px rgba(0,0,0,.25);position:relative}
    .small-box .inner{padding:26px 17px}
    .small-box h3{font-size:37px;font-weight:900;margin:0;line-height:1}
    .small-box h5.rp-kecil{font-size:28px!important;}
    .small-box p{font-size:15px;margin-top:6px;margin-bottom:0}
    .small-box .icon{position:absolute;top:6px;right:15px;font-size:70px;opacity:.9;pointer-events:none}
    .small-box-footer{display:block;padding:8px;text-align:center;color:#fff;background:rgba(0,0,0,.15);text-decoration:none;font-size:13px}
    .small-box-footer:hover{color:#fff;background:rgba(0,0,0,.25)}

    /* ── Upload overlay ── */
    #uploadOverlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:99998;flex-direction:column;align-items:center;justify-content:center}
    #uploadOverlay.show{display:flex}
    #uploadOverlay .overlay-box{background:#1e2230;border-radius:12px;padding:36px 44px;min-width:360px;text-align:center;color:#fff}

    /* ── Toast ── */
    #toast-container{position:fixed;top:20px;right:20px;z-index:99999;display:flex;flex-direction:column;gap:8px}
    .toast-notif{display:flex;align-items:center;gap:10px;padding:12px 18px;border-radius:8px;font-size:14px;color:#fff;min-width:260px;max-width:400px;box-shadow:0 4px 16px rgba(0,0,0,.3);animation:slideIn .3s ease;transition:opacity .35s ease}
    .toast-notif.success{background:#28a745}.toast-notif.error{background:#dc3545}.toast-notif.warning{background:#ffc107;color:#333}.toast-notif.hide{opacity:0}
    @keyframes slideIn{from{transform:translateX(110%);opacity:0}to{transform:translateX(0);opacity:1}}

    /* ── View more link ── */
    .view-more-link{color:#17a2b8;font-size:13px;font-weight:600;text-decoration:none}
    .view-more-link:hover{text-decoration:underline;color:#117a8b}

    /* ── Content row ── */
    .content-row{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.1);border-radius:8px;padding:14px 16px;margin-bottom:12px;position:relative}
    .content-row .btn-remove-row{position:absolute;top:8px;right:8px}
    .badge-video{background:#6f42c1;color:#fff;font-size:10px;padding:2px 7px;border-radius:10px}
    .badge-image{background:#17a2b8;color:#fff;font-size:10px;padding:2px 7px;border-radius:10px}
    .remaining-field{background:rgba(255,255,255,.06)!important;cursor:not-allowed;font-weight:600;color:#17a2b8!important}

    /* ── Nego harga ── */
    .nego-section{background:rgba(255,193,7,.07);border:1px solid rgba(255,193,7,.3);border-radius:8px;padding:14px 16px}
    .nego-badge-nett{background:#28a745;color:#fff;font-size:12px;padding:3px 10px;border-radius:8px;font-weight:700}

    /* Msg/notif */
    .msg-item{display:flex;align-items:center;gap:10px;padding:10px 14px;border-bottom:1px solid rgba(255,255,255,.08);cursor:pointer}
    .msg-item:hover{background:rgba(255,255,255,.06)}
    .msg-item img{width:38px;height:38px;border-radius:50%;object-fit:cover}
    .msg-info{flex:1;min-width:0}.msg-name{font-size:13px;font-weight:600}
    .msg-preview{font-size:12px;color:#aaa;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:180px}
    .msg-time{font-size:11px;color:#aaa}
    .notif-item{display:flex;align-items:flex-start;gap:10px;padding:10px 14px;border-bottom:1px solid rgba(255,255,255,.08)}
    .notif-icon{width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .notif-text{font-size:13px;line-height:1.4}.notif-time{font-size:11px;color:#aaa;margin-top:2px}
  </style>
</head>

<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">
  <div id="toast-container"></div>

  {{-- UPLOAD OVERLAY --}}
  <div id="uploadOverlay">
    <div class="overlay-box">
      <i class="fas fa-spinner fa-spin fa-2x text-success mb-3"></i>
      <p id="overlayStatus" class="mb-3">Mengupload konten...</p>
      <div class="progress" style="height:10px;border-radius:8px;">
        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" id="overlayBar" style="width:0%"></div>
      </div>
      <small class="text-muted mt-2 d-block">Harap tunggu, jangan tutup halaman ini</small>
    </div>
  </div>

  {{-- NAVBAR --}}
  <nav class="main-header navbar navbar-expand navbar-dark">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a></li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#"><i class="far fa-comments"></i><span class="badge badge-danger navbar-badge">3</span></a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="background:#1e2230;border:1px solid rgba(255,255,255,.1);">
          <span class="dropdown-item dropdown-header" style="color:#aaa;font-size:12px;">3 Messages</span>
          <div class="msg-item"><img src="dist/img/user1-128x128.jpg" alt=""><div class="msg-info"><div class="msg-name">Vendor A <span class="float-right msg-time">4 Jam lalu</span></div><div class="msg-preview">Playlist sudah diupload, mohon review.</div></div></div>
          <div class="msg-item"><img src="dist/img/user3-128x128.jpg" alt=""><div class="msg-info"><div class="msg-name">Admin <span class="float-right msg-time">1 Jam lalu</span></div><div class="msg-preview">Contract baru menunggu persetujuan.</div></div></div>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item text-center" style="color:#17a2b8;font-size:13px;">Lihat Semua Pesan</a>
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#"><i class="far fa-bell"></i><span class="badge badge-warning navbar-badge">5</span></a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="background:#1e2230;border:1px solid rgba(255,255,255,.1);">
          <span class="dropdown-item dropdown-header" style="color:#aaa;font-size:12px;">5 Notifikasi</span>
          <div class="notif-item"><div class="notif-icon bg-warning"><i class="fas fa-exclamation text-dark fa-sm"></i></div><div><div class="notif-text">Kontrak <strong>LED Showroom</strong> hampir habis</div><div class="notif-time"><i class="far fa-clock mr-1"></i>3 Jam lalu</div></div></div>
          <div class="notif-item"><div class="notif-icon bg-success"><i class="fas fa-check text-white fa-sm"></i></div><div><div class="notif-text">Playlist <strong>Promotion WIZZMIE</strong> diapprove</div><div class="notif-time"><i class="far fa-clock mr-1"></i>5 Jam lalu</div></div></div>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item text-center" style="color:#17a2b8;font-size:13px;">Lihat Semua Notifikasi</a>
        </div>
      </li>
      <li class="nav-item">
        <button class="btn btn-primary btn-sm mr-2" data-toggle="modal" data-target="#modalTambahPlaylist">
          <i class="fas fa-plus"></i> Tambah Playlist
        </button>
      </li>
    </ul>
  </nav>

  {{-- SIDEBAR --}}
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="/" class="brand-link"><span class="brand-text font-weight-light">CMS GikVision</span></a>
    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image"><img src="dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User"></div>
        <div class="info"><a href="#" class="d-block">Customer</a></div>
      </div>
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="{{ route('dashboard.list') }}" class="nav-link active">
              <i class="nav-icon fas fa-chart-line"></i><p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('playlist.index') }}" class="nav-link">
              <i class="nav-icon fas fa-film"></i><p>Daftar Playlist</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('permintaan.index') }}" class="nav-link">
              <i class="nav-icon fas fa-clipboard-list"></i><p>Daftar Permintaan</p>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </aside>

  {{-- CONTENT WRAPPER --}}
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6"><h1 class="m-0"><i class="fas fa-chart-line mr-2"></i>Dashboard</h1></div>
          <div class="col-sm-6 text-right">
            <ol class="breadcrumb float-sm-right"><li class="breadcrumb-item active">Dashboard</li></ol>
          </div>
        </div>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">

        @php
          use Carbon\Carbon;
          use App\Models\Invoice;

          $localInvoices = Invoice::all()->keyBy('playlist_id');

          $paid = $unpaid = 0;
          $totalPengeluaran = 0;

          foreach ($playlists as &$p) {
            $inv = $localInvoices->get($p['id']);
            
            // Ngecek gabungan dari API sama Lokal (DITAMBAHIN ANGKA '1')
            $apiStatus = strtolower((string)($p['status'] ?? ''));
            $localStatus = $inv ? strtolower($inv->status) : 'unpaid';
            $isPaid = in_array($apiStatus, ['paid', 'approved', '1']) || in_array($localStatus, ['paid', 'approved', '1']);
            
            $p['local_status'] = $isPaid ? 'paid' : 'unpaid';
            $p['local_amount'] = $inv ? $inv->amount : ($p['contract_value'] ?? 0);

            // Total duit cuma bertambah kalau lunas
            if ($p['local_status'] === 'paid') {
                $paid++;
                $totalPengeluaran += (float)$p['local_amount'];
            } else {
                $unpaid++;
            }
          }
          
          $deviceMap = collect($devices ?? [])->keyBy('id');
          $vendorMap = collect($vendors ?? [])->keyBy('id');
        @endphp

        {{-- SUMMARY CARDS --}}
        <div class="row mb-3">
          <div class="col-lg-3 col-6">
            <div class="small-box" style="background:linear-gradient(135deg,#1a3a6b,#2d5fa0);color:#fff;">
              <div class="inner">
                <h5 class="rp-kecil">Rp {{ number_format($totalPengeluaran,0,',','.') }}</h5>
                <p>Total Pengeluaran</p>
              </div>
              <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
              <a href="{{ route('playlist.index') }}" class="small-box-footer">Dari playlist paid <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
              <div class="inner"><h3>{{ $paid }}</h3><p>Paid</p></div>
              <div class="icon"><i class="fas fa-check-circle"></i></div>
              <a href="{{ route('playlist.index') }}" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
              <div class="inner"><h3>{{ $unpaid }}</h3><p>Unpaid</p></div>
              <div class="icon"><i class="fas fa-times-circle"></i></div>
              <a href="{{ route('playlist.index') }}" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box" style="background:linear-gradient(135deg,#555,#777);color:#fff;">
              <div class="inner"><h3>{{ count($playlists) }}</h3><p>Total Playlist</p></div>
              <div class="icon"><i class="fas fa-film"></i></div>
              <a href="{{ route('playlist.index') }}" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
        </div>

        {{-- PREVIEW TABLE (max 5) --}}
        <div class="row">
          <div class="col-12">
            <div class="card card-dark">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title"><i class="fas fa-film mr-2"></i>Daftar Playlist</h3>
                <a href="{{ route('playlist.index') }}" class="view-more-link"><i class="fas fa-external-link-alt mr-1"></i>View More</a>
              </div>
              <div class="card-body p-0">
                <table class="table table-bordered table-hover table-sm mb-0">
                  <thead class="thead-dark">
                    <tr>
                      <th style="width:40px">No</th>
                      <th>Nama Playlist</th>
                      <th>Vendor</th>
                      <th>Device</th>
                      <th style="width:90px">Sisa</th>
                      <th>Nilai Kontrak</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse(array_slice($playlists, 0, 5) as $i => $p)
                      @php
                        $end   = Carbon::parse($p['end_contract']);
                        $diff  = (int) Carbon::now()->diffInDays($end, false);
                        // Fix: >= 30 aman, 11-29 warning, <= 10 danger
                        if ($diff >= 30) { $sisaClass = 'aman'; }
                        elseif ($diff >= 11) { $sisaClass = 'warning'; }
                        else { $sisaClass = 'danger'; }
                        $sisaLabel = $diff > 0 ? $diff.' hari' : abs($diff).' hari (lewat)';
                        $val   = $p['local_amount'];
                        $vName = $vendorMap[$p['cust_id'] ?? '']['nama'] ?? $vendorMap[$p['vendor_id'] ?? '']['nama'] ?? ($p['cust_id'] ?? '-');
                        $dName = $deviceMap[$p['device_id'] ?? '']['device_name'] ?? '-';
                      @endphp
                      <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><strong>{{ $p['playlist_name'] }}</strong></td>
                        <td>{{ $vName }}</td>
                        <td>{{ $dName }}</td>
                        <td class="text-center">
                          <span class="sisa-badge {{ $sisaClass }}">
                            <span class="hari-num">{{ abs($diff) }}</span>
                            <span>hari</span>
                          </span>
                        </td>
                        <td>Rp {{ number_format($val,0,',','.') }}</td>
                      </tr>
                    @empty
                      <tr><td colspan="6" class="text-center py-4 text-muted"><i class="fas fa-inbox fa-2x mb-2 d-block"></i>Belum ada playlist.</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

      </div>
    </section>
  </div>

  {{-- ===== MODAL TAMBAH PLAYLIST ===== --}}
  <div class="modal fade" id="modalTambahPlaylist" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header bg-dark">
          <h4 class="modal-title"><i class="fas fa-plus-circle mr-2"></i>Tambah Playlist</h4>
          <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="cust_id" value="1">
          <input type="hidden" id="nego_status" value="none">
          {{-- none | nego | nett | agreed --}}

          {{-- INFO PLAYLIST --}}
          <div class="card card-secondary card-outline mb-3">
            <div class="card-header py-2"><h6 class="card-title mb-0"><i class="fas fa-film mr-1"></i> Info Playlist</h6></div>
            <div class="card-body pb-0">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Nama Playlist <span class="text-danger">*</span></label>
                    <input type="text" id="playlist_name" class="form-control" placeholder="Masukkan nama playlist">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Vendor <span class="text-danger">*</span></label>
                    <select id="vendor_select" name="vendor_id" class="form-control">
                      <option value="">-- Pilih Vendor --</option>
                      @foreach($vendors as $v)
                        <option value="{{ $v['id'] }}">{{ $v['nama'] }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {{-- INFO DEVICE --}}
          <div class="card card-secondary card-outline mb-3">
            <div class="card-header py-2"><h6 class="card-title mb-0"><i class="fas fa-tv mr-1"></i> Info Device</h6></div>
            <div class="card-body pb-0">
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Device <span class="text-danger">*</span></label>
                    <select id="device_id" class="form-control">
                      <option value="">-- Pilih Vendor dulu --</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label>Provinsi</label>
                    <input type="text" id="provinsi" class="form-control" readonly placeholder="Otomatis">
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label>Kota / Kabupaten</label>
                    <input type="text" id="kota" class="form-control" readonly placeholder="Otomatis">
                    <input type="hidden" id="location">
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label>Ukuran (H x W)</label>
                    <input type="text" id="ukuran" class="form-control" readonly placeholder="Otomatis">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Contract Value <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                      <input type="number" id="contract_value" class="form-control" placeholder="Otomatis dari vendor" readonly>
                    </div>
                    <small class="text-muted"><i class="fas fa-info-circle"></i> Otomatis terisi saat device dipilih</small>
                  </div>
                </div>
              </div>

              {{-- ===== NEGO HARGA ===== --}}
              <div id="negoSection" class="nego-section mt-2 mb-3" style="display:none;">
                <div class="d-flex align-items-center mb-2">
                  <i class="fas fa-handshake text-warning mr-2"></i>
                  <strong class="text-warning">Negosiasi Harga</strong>
                  <small class="text-muted ml-2">(Harga vendor: Rp <span id="hargaVendorLabel">0</span>)</small>
                </div>

                {{-- Step 1: User isi harga nego --}}
                <div id="negoStep1">
                  <div class="row align-items-end">
                    <div class="col-md-5">
                      <div class="form-group mb-0">
                        <label class="small">Harga Penawaran Anda <span class="text-danger">*</span></label>
                        <div class="input-group input-group-sm">
                          <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                          <input type="number" id="nego_value" class="form-control" placeholder="Masukkan harga nego" min="0">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <button type="button" class="btn btn-warning btn-sm" id="btnKirimNego">
                        <i class="fas fa-paper-plane mr-1"></i>Kirim Penawaran
                      </button>
                      <button type="button" class="btn btn-outline-secondary btn-sm ml-1" id="btnBatalNego">
                        Batal
                      </button>
                    </div>
                  </div>
                  <small class="text-muted d-block mt-1">
                    <i class="fas fa-info-circle"></i> Harga nego akan dikirim ke vendor. Vendor akan memutuskan menerima atau menetapkan harga NETT.
                  </small>
                </div>

                {{-- Step 2: Menunggu respons vendor --}}
                <div id="negoStep2" style="display:none;">
                  <div class="alert alert-warning mb-2 py-2">
                    <i class="fas fa-clock mr-1"></i>
                    Penawaran Rp <strong id="negoSentLabel">0</strong> telah dikirim.
                    Menunggu keputusan vendor...
                  </div>
                  <div class="row align-items-center">
                    <div class="col-auto">
                      <span class="text-muted small">Simulasi respons vendor:</span>
                    </div>
                    <div class="col-auto">
                      <button type="button" class="btn btn-success btn-sm" id="btnVendorSetuju">
                        <i class="fas fa-check mr-1"></i>Vendor Setuju (Simulasi)
                      </button>
                    </div>
                    <div class="col-auto">
                      <button type="button" class="btn btn-secondary btn-sm" id="btnVendorNett">
                        <i class="fas fa-ban mr-1"></i>Vendor NETT (Simulasi)
                      </button>
                    </div>
                  </div>
                </div>

                {{-- Step 3a: Vendor NETT — user terima atau tidak --}}
                <div id="negoStep3Nett" style="display:none;">
                  <div class="alert alert-secondary mb-2 py-2">
                    <i class="fas fa-ban mr-1"></i>
                    Vendor menetapkan harga <strong>NETT</strong>:
                    <span class="nego-badge-nett ml-1">Rp <span id="nettValueLabel">0</span> (NETT)</span>
                  </div>
                  <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-success btn-sm" id="btnSetujuiNett">
                      <i class="fas fa-check mr-1"></i>Setujui
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm ml-2" id="btnTolakNett">
                      <i class="fas fa-times mr-1"></i>Tolak
                    </button>
                    <small class="text-muted ml-2">Setujui = contract_value berubah ke harga NETT & status jadi approved</small>
                  </div>
                </div>

                {{-- Step 3b: Sepakat --}}
                <div id="negoStep3Agreed" style="display:none;">
                  <div class="alert alert-success mb-0 py-2">
                    <i class="fas fa-handshake mr-1"></i>
                    Harga disepakati: <strong>Rp <span id="agreedValueLabel">0</span></strong>.
                    Contract value & status akan diperbarui saat simpan.
                  </div>
                </div>

              </div>
              {{-- Tombol buka nego --}}
              <div id="btnNegoWrapper" style="display:none;">
                <button type="button" class="btn btn-outline-warning btn-sm mb-2" id="btnBukaNegoHarga">
                  <i class="fas fa-handshake mr-1"></i>Nego Harga
                </button>
              </div>

            </div>
          </div>

          {{-- INFO KONTRAK --}}
          <div class="card card-secondary card-outline mb-3">
            <div class="card-header py-2"><h6 class="card-title mb-0"><i class="fas fa-file-contract mr-1"></i> Info Kontrak</h6></div>
            <div class="card-body pb-0">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Start Contract <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <input type="date" id="start_contract" class="form-control">
                      <div class="input-group-append"><span class="input-group-text"><i class="far fa-calendar-alt"></i></span></div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>End Contract <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <input type="date" id="end_contract" class="form-control">
                      <div class="input-group-append"><span class="input-group-text"><i class="far fa-calendar-alt"></i></span></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {{-- UPLOAD KONTEN --}}
          <div class="card card-secondary card-outline mb-0">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
              <h6 class="card-title mb-0"><i class="fas fa-photo-video mr-1"></i> Upload Konten</h6>
              <div class="d-flex align-items-center">
                <small class="text-warning mr-2"><i class="fas fa-info-circle"></i> Video → H.264 MP4</small>
                <button type="button" class="btn btn-sm btn-outline-primary" id="btnTambahKonten"><i class="fas fa-plus"></i> Tambah Konten</button>
              </div>
            </div>
            <div class="card-body" id="contentRows" style="display:none;"></div>
          </div>

        </div>
        <div class="modal-footer bg-dark">
          <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Batal</button>
          <button type="button" class="btn btn-success" id="btnSimpan"><i class="fas fa-save mr-1"></i>Simpan Playlist</button>
        </div>
      </div>
    </div>
  </div>

  {{-- MODAL PREVIEW PLAYLIST --}}
  <div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-info"><h4 class="modal-title text-white"><i class="fas fa-eye mr-2"></i>Preview Playlist</h4><button class="close text-white" data-dismiss="modal">&times;</button></div>
        <div class="modal-body">
          <table class="table table-borderless table-sm">
            <tr><th style="width:140px">Nama Playlist</th><td>: <span id="prev_name"></span></td></tr>
            <tr><th>Vendor</th><td>: <span id="prev_vendor"></span></td></tr>
            <tr><th>Start Contract</th><td>: <span id="prev_start"></span></td></tr>
            <tr><th>End Contract</th><td>: <span id="prev_end"></span></td></tr>
            <tr><th>Contract Value</th><td>: Rp <span id="prev_value"></span></td></tr>
            <tr><th>Sisa Kontrak</th><td>: <span id="prev_remaining_badge"></span></td></tr>
            <tr><th>Status</th><td>: <span id="prev_status"></span></td></tr>
          </table>
        </div>
        <div class="modal-footer"><button class="btn btn-secondary" data-dismiss="modal">Tutup</button></div>
      </div>
    </div>
  </div>

  {{-- MODAL PREVIEW FILE --}}
  <div class="modal fade" id="filePreviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title"><i class="fas fa-photo-video mr-2"></i>Preview File</h5><button class="close" data-dismiss="modal">&times;</button></div>
        <div class="modal-body text-center" id="filePreviewContent"><p class="text-muted">Belum ada file dipilih.</p></div>
        <div class="modal-footer"><button class="btn btn-secondary" data-dismiss="modal">Tutup</button></div>
      </div>
    </div>
  </div>

  <footer class="main-footer"><strong>CMS GikVision</strong> &copy; 2026</footer>
</div>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
<script>
let rowCount = 0;
let originalPrice = 0;      // harga vendor asli
let negoFinalValue = null;  // harga final setelah nego/nett
let negoFinalStatus = null; // 'approved' jika sepakat
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

function showToast(msg, type='success', dur=4000) {
  const icons = { success:'<i class="fas fa-check-circle"></i>', error:'<i class="fas fa-times-circle"></i>', warning:'<i class="fas fa-exclamation-triangle"></i>' };
  const t = document.createElement('div');
  t.className = `toast-notif ${type}`;
  t.innerHTML = `${icons[type]||''} <span>${msg}</span>`;
  document.getElementById('toast-container').appendChild(t);
  setTimeout(() => { t.classList.add('hide'); setTimeout(() => t.remove(), 400); }, dur);
}

function formatRp(n) { return Number(n).toLocaleString('id-ID'); }

function resetNegoUI() {
  negoFinalValue = null; negoFinalStatus = null;
  $('#nego_value').val('').prop('disabled', false);
  $('#negoStep1,#negoStep3Agreed,#negoStep3Nett,#negoStep2').hide();
  $('#negoStep1').show();
  $('#negoSection').hide();
  $('#nego_status').val('none');
}

/* ── NEGO LOGIC ── */
// Buka panel nego
$(document).on('click', '#btnBukaNegoHarga', function() {
  $('#negoSection').show();
  $('#btnBukaNegoHarga').hide();
  resetNegoUI();
});

// Batal nego
$(document).on('click', '#btnBatalNego', function() {
  resetNegoUI();
  $('#btnBukaNegoHarga').show();
  // Kembalikan contract_value ke harga vendor
  $('#contract_value').val(originalPrice);
  negoFinalValue = null; negoFinalStatus = null;
});

// Kirim penawaran user
$(document).on('click', '#btnKirimNego', function() {
  const negoVal = $('#nego_value').val();
  if (!negoVal || Number(negoVal) <= 0) { showToast('Masukkan harga penawaran!','warning'); return; }
  $('#negoSentLabel').text(formatRp(negoVal));
  $('#negoStep1').hide(); $('#negoStep2').show();
  $('#nego_status').val('nego');
});

// Simulasi: Vendor setuju dengan harga nego user
$(document).on('click', '#btnVendorSetuju', function() {
  const negoVal = $('#nego_value').val();
  negoFinalValue = Number(negoVal);
  negoFinalStatus = 'approved';
  $('#agreedValueLabel').text(formatRp(negoFinalValue));
  $('#negoStep2').hide(); $('#negoStep3Agreed').show();
  // Update contract_value
  $('#contract_value').val(negoFinalValue);
  $('#nego_status').val('agreed');
  showToast('Harga disepakati! Contract value diperbarui.','success');
});

// Simulasi: Vendor NETT (tidak mau nego, tetap harga vendor)
$(document).on('click', '#btnVendorNett', function() {
  const nettVal = originalPrice;
  $('#nettValueLabel').text(formatRp(nettVal));
  $('#negoStep2').hide(); $('#negoStep3Nett').show();
  $('#nego_status').val('nett');
});

// User setujui harga NETT vendor
$(document).on('click', '#btnSetujuiNett', function() {
  negoFinalValue = originalPrice;
  negoFinalStatus = 'approved';
  $('#agreedValueLabel').text(formatRp(negoFinalValue));
  $('#negoStep3Nett').hide(); $('#negoStep3Agreed').show();
  $('#contract_value').val(negoFinalValue);
  $('#nego_status').val('agreed');
  showToast('Harga NETT disetujui! Status akan jadi Approved.','success');
});

// User tolak NETT vendor
$(document).on('click', '#btnTolakNett', function() {
  resetNegoUI();
  $('#btnBukaNegoHarga').show();
  $('#contract_value').val(originalPrice);
  showToast('Negosiasi dibatalkan.','warning');
});

function previewFile(input, idx) {
  const pv=document.getElementById('filePreview_'+idx), ne=document.getElementById('fileName_'+idx), te=document.getElementById('fileType_'+idx);
  if (input.files&&input.files[0]) {
    const f=input.files[0]; ne.textContent=f.name;
    te.innerHTML=f.type.startsWith('video/')?'<span class="badge-video"><i class="fas fa-film"></i> video→H.264</span>':f.type.startsWith('image/')?'<span class="badge-image"><i class="fas fa-image"></i> gambar</span>':'';
    pv.style.display='block';
  } else { pv.style.display='none'; }
}

function removeRow(btn) { btn.closest('.content-row').remove(); }

function calcRemaining(idx) {
  const sv=document.querySelector(`#contentRow_${idx} input[name="start_time[]"]`).value;
  const ev=document.querySelector(`#contentRow_${idx} input[name="end_time[]"]`).value;
  const el=document.getElementById('remaining_'+idx);
  if(!sv||!ev){el.value='';return;}
  const[sh,sm]=sv.split(':').map(Number),[eh,em]=ev.split(':').map(Number);
  let diff=(eh*60+em)-(sh*60+sm); if(diff<=0) diff+=1440;
  const h=Math.floor(diff/60),m=diff%60;
  el.value=h>0?(m>0?`${h} jam ${m} menit`:`${h} jam`):`${m} menit`;
}

function previewData(name, vendor, start, end, value, sisaLabel, sisaClass, status) {
  document.getElementById('prev_name').textContent   = name;
  document.getElementById('prev_vendor').textContent = vendor;
  document.getElementById('prev_start').textContent  = start;
  document.getElementById('prev_end').textContent    = end;
  document.getElementById('prev_value').textContent  = Number(value).toLocaleString('id-ID');
  document.getElementById('prev_remaining_badge').innerHTML = `<span class="sisa-badge ${sisaClass} d-inline-flex" style="width:auto;border-radius:20px;padding:3px 12px;">${sisaLabel}</span>`;
  const sc=status==='approved'?'status-approved':(status==='rejected'?'status-rejected':'status-pending');
  document.getElementById('prev_status').innerHTML = `<span class="${sc}">${status.toUpperCase()}</span>`;
  $('#previewModal').modal('show');
}

$(document).on('click','.btn-preview-file',function(){
  const idx=$(this).data('idx');
  const fi=document.querySelector(`#contentRow_${idx} input[type="file"]`);
  const cont=document.getElementById('filePreviewContent');
  cont.innerHTML='';
  if(!fi||!fi.files[0]){cont.innerHTML='<p class="text-muted">Belum ada file dipilih.</p>';}
  else{
    const file=fi.files[0],url=URL.createObjectURL(file);
    cont.innerHTML=file.type.startsWith('image/')?`<img src="${url}" class="img-fluid rounded">`:file.type.startsWith('video/')?`<video controls class="w-100" style="max-height:400px"><source src="${url}" type="${file.type}"></video>`:'<p class="text-muted">Preview tidak tersedia.</p>';
  }
  $('#filePreviewModal').modal('show');
});

$(document).ready(function() {
  // TAMBAH ROW KONTEN
  $('#btnTambahKonten').click(function() {
    const idx=rowCount;
    const hariList=['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    const hariHTML=hariList.map(h=>`<label class="mr-2" style="font-size:13px;"><input type="checkbox" name="days[${idx}][]" value="${h}" class="mr-1">${h}</label>`).join('');
    const row=`
    <div class="content-row" id="contentRow_${idx}">
      <button type="button" class="btn btn-xs btn-danger btn-remove-row" onclick="removeRow(this)"><i class="fas fa-times"></i></button>
      <div class="row align-items-end">
        <div class="col-md-3">
          <div class="form-group mb-2">
            <label class="small">File <span class="text-danger">*</span> <span class="badge-video ml-1"><i class="fas fa-film"></i> video</span> <span class="badge-image ml-1"><i class="fas fa-image"></i> gambar</span></label>
            <div class="custom-file">
              <input type="file" name="file[]" class="custom-file-input" accept="video/*,image/*" onchange="previewFile(this,${idx});$(this).next().text(this.files[0]?this.files[0].name:'Pilih file')">
              <label class="custom-file-label" style="font-size:12px;">Pilih file</label>
            </div>
          </div>
          <div id="filePreview_${idx}" style="display:none;"><small class="text-success"><i class="fas fa-check-circle"></i> <span id="fileName_${idx}"></span> <span id="fileType_${idx}" class="ml-1"></span></small></div>
        </div>
        <div class="col-md-2"><div class="form-group mb-2"><label class="small">Start Date</label><input type="date" name="start_date[]" class="form-control form-control-sm"></div></div>
        <div class="col-md-2"><div class="form-group mb-2"><label class="small">End Date</label><input type="date" name="end_date[]" class="form-control form-control-sm"></div></div>
        <div class="col-md-1" style="min-width:130px;"><div class="form-group mb-2"><label class="small">Start Time</label><input type="time" name="start_time[]" class="form-control form-control-sm" onchange="calcRemaining(${idx})"></div></div>
        <div class="col-md-1" style="min-width:130px;"><div class="form-group mb-2"><label class="small">End Time</label><input type="time" name="end_time[]" class="form-control form-control-sm" onchange="calcRemaining(${idx})"></div></div>
        <div class="col-md-1" style="min-width:120px;"><div class="form-group mb-2"><label class="small">Remaining</label><input type="text" id="remaining_${idx}" class="form-control form-control-sm remaining-field" readonly placeholder="Otomatis"></div></div>
        <div class="col-md-1"><div class="form-group mb-2"><label class="small d-block">Prioritas</label><div class="icheck-primary d-inline"><input type="checkbox" name="priority_mode[${idx}]" id="prio_${idx}" value="1"><label for="prio_${idx}"> Aktif</label></div></div></div>
        <div class="col-auto"><div class="form-group mb-2" style="padding-top:20px;"><button type="button" class="btn btn-sm btn-outline-info btn-preview-file" data-idx="${idx}"><i class="fas fa-eye"></i></button></div></div>
      </div>
      <div class="mt-2"><small class="text-muted mr-2"><i class="fas fa-calendar-week mr-1"></i>Hari Tayang:</small>${hariHTML}</div>
    </div>`;
    document.getElementById('contentRows').style.display='block';
    document.getElementById('contentRows').insertAdjacentHTML('beforeend',row);
    rowCount++;
  });

  // VENDOR CHANGE
  $('#vendor_select').change(function() {
    const vId=$(this).val();
    $('#device_id').html('<option value="">-- Pilih Vendor dulu --</option>');
    $('#provinsi,#kota,#ukuran').val(''); $('#location,#contract_value').val('');
    resetNegoUI(); $('#btnNegoWrapper').hide(); originalPrice=0;
    if(!vId) return;
    $('#device_id').html('<option value="">Loading...</option>');
    $.ajax({
      url:'/get-devices/'+vId, method:'GET',
      success: function(data) {
        let html='<option value="">-- Pilih Device --</option>';
        data.forEach(d=>{ html+=`<option value="${d.id}" data-provinsi="${d.provinsi||''}" data-kota="${d.kota||''}" data-width="${d.r_width||''}" data-height="${d.r_height||''}" data-price="${d.nego_value||d.price||d.contract_value||''}">${d.device_name}</option>`; });
        $('#device_id').html(html);
        if(data.length===0){$('#device_id').html('<option value="">-- Vendor belum punya device --</option>');showToast('Vendor ini belum punya device!','warning');}
      },
      error: function(xhr){$('#device_id').html('<option value="">-- Gagal load --</option>');showToast('Gagal ambil device ('+xhr.status+')','error');}
    });
  });

  // DEVICE CHANGE
  $('#device_id').change(function() {
    const sel=$(this).find(':selected');
    const provinsi=sel.data('provinsi')||'',kota=sel.data('kota')||'';
    $('#provinsi').val(provinsi); $('#kota').val(kota);
    $('#ukuran').val(sel.data('width')&&sel.data('height')?sel.data('height')+' x '+sel.data('width'):'');
    $('#location').val(provinsi&&kota?provinsi+', '+kota:(provinsi||kota));

    const price = sel.data('price')||'';
    if (price) {
      originalPrice = Number(price);
      $('#contract_value').val(price);
      $('#hargaVendorLabel').text(formatRp(price));
    } else {
      const devId=$(this).val();
      if(devId){
        $.ajax({url:'/get-device/'+devId,method:'GET',success:function(d){
          if(d&&(d.nego_value||d.price||d.contract_value)){
            const p = d.nego_value||d.price||d.contract_value;
            originalPrice = Number(p);
            $('#contract_value').val(p);
            $('#hargaVendorLabel').text(formatRp(p));
          }
        }});
      }
    }

    // Tampilkan tombol nego setelah device dipilih
    if($(this).val()){
      $('#btnNegoWrapper').show();
      resetNegoUI();
    } else {
      $('#btnNegoWrapper').hide();
    }
  });

  // SIMPAN PLAYLIST
  $('#btnSimpan').click(function() {
    const btn=$(this);
    const pName=$('#playlist_name').val().trim();
    const vId=$('#vendor_select').val(), dId=$('#device_id').val();
    const sc=$('#start_contract').val(), ec=$('#end_contract').val();
    let cv=$('#contract_value').val();
    if(!pName){showToast('Nama playlist wajib diisi!','error');return;}
    if(!vId){showToast('Vendor wajib dipilih!','error');return;}
    if(!dId){showToast('Device wajib dipilih!','error');return;}
    if(!sc){showToast('Start contract wajib diisi!','error');return;}
    if(!ec){showToast('End contract wajib diisi!','error');return;}
    if(!cv){showToast('Contract value wajib diisi!','error');return;}

    // Jika nego selesai, pakai harga final
    if(negoFinalValue !== null) cv = negoFinalValue;
    const statusOverride = (negoFinalStatus === 'approved') ? 'approved' : null;

    let hasVideo=false;
    $('input[name="file[]"]').each(function(){if(this.files[0]&&this.files[0].type.startsWith('video/')) hasVideo=true;});

    const fd=new FormData();
    fd.append('_token',$('meta[name="csrf-token"]').attr('content'));
    fd.append('playlist_name',pName); fd.append('vendor_id',vId); fd.append('device_id',dId);
    fd.append('start_contract',sc); fd.append('end_contract',ec); fd.append('contract_value',cv);
    fd.append('location',$('#location').val()||''); fd.append('cust_id',$('#cust_id').val()||'1');
    if(statusOverride) fd.append('status', statusOverride);
    if(negoFinalValue) fd.append('nego_value', negoFinalValue);

    $('input[name="file[]"]').each(function(i,inp){
      if(inp.files&&inp.files[0]) fd.append('file[]',inp.files[0]);
      fd.append('start_date[]',$($('input[name="start_date[]"]')[i]).val()||'');
      fd.append('end_date[]',$($('input[name="end_date[]"]')[i]).val()||'');
      fd.append('start_time[]',$($('input[name="start_time[]"]')[i]).val()||'');
      fd.append('end_time[]',$($('input[name="end_time[]"]')[i]).val()||'');
    });
    $('.content-row').each(function(i){
      fd.append('priority_mode['+i+']',$(this).find('input[name^="priority_mode"]').is(':checked')?'1':'0');
      $(this).find('input[type="checkbox"][name^="days"]').each(function(){if($(this).is(':checked')) fd.append('days['+i+'][]',$(this).val());});
    });

    document.getElementById('overlayStatus').textContent = hasVideo?'Mengupload & konversi video H.264...':'Mengupload konten...';
    document.getElementById('uploadOverlay').classList.add('show');
    btn.prop('disabled',true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Menyimpan...');

    $.ajax({
      url:'{{ route("playlist.store") }}', method:'POST', data:fd, contentType:false, processData:false, timeout:600000,
      xhr:function(){const x=new XMLHttpRequest();x.upload.addEventListener('progress',function(e){if(e.lengthComputable){const p=Math.round((e.loaded/e.total)*100);document.getElementById('overlayBar').style.width=p+'%';}});return x;},
      success:function(res){
        document.getElementById('uploadOverlay').classList.remove('show');
        showToast(res.message||'Berhasil!','success');
        // Jika ada nego yang disepakati, update contract_value via PUT
        if(negoFinalValue && res.data && res.data.id){
          $.ajax({
            url:'/playlist/update/'+res.data.id, method:'POST',
            data:{_method:'PUT',_token:$('meta[name="csrf-token"]').attr('content'),
              playlist_name:pName, vendor_id:vId, device_id:dId,
              start_contract:sc, end_contract:ec, contract_value:cv,
              status: statusOverride||'pending'},
            success:function(){showToast('Contract value & status diperbarui!','success');}
          });
        }
        $('#modalTambahPlaylist').modal('hide');
        setTimeout(()=>location.reload(),1600);
      },
      error:function(err){
        document.getElementById('uploadOverlay').classList.remove('show');
        const r=err.responseJSON;
        showToast(err.status===419?'Session expired!':(r?.errors?Object.values(r.errors)[0][0]:(r?.message||'Error '+err.status)),'error');
      },
      complete:function(){
        document.getElementById('uploadOverlay').classList.remove('show');
        btn.prop('disabled',false).html('<i class="fas fa-save mr-1"></i>Simpan Playlist');
        document.getElementById('overlayBar').style.width='0%';
      }
    });
  });

  // RESET MODAL
  $('#modalTambahPlaylist').on('hidden.bs.modal',function(){
    $('#playlist_name,#start_contract,#end_contract,#contract_value').val('');
    $('#vendor_select').val(''); $('#device_id').html('<option value="">-- Pilih Vendor dulu --</option>');
    $('#provinsi,#kota,#ukuran,#location').val('');
    document.getElementById('contentRows').innerHTML='';
    document.getElementById('contentRows').style.display='none';
    rowCount=0; originalPrice=0; negoFinalValue=null; negoFinalStatus=null;
    resetNegoUI(); $('#btnNegoWrapper').hide();
  });
});
</script>
</body>
</html>