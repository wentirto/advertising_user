<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Daftar Playlist - CMS GikVision</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <style>
    .sisa-badge{display:inline-flex;align-items:center;justify-content:center;width:54px;height:54px;border-radius:50%;font-size:11px;font-weight:700;line-height:1.2;text-align:center;flex-direction:column}
    .sisa-badge.aman{background:#28a745;color:#fff}
    .sisa-badge.warning{background:#ffc107;color:#333}
    .sisa-badge.danger{background:#dc3545;color:#fff}
    .sisa-badge .hari-num{font-size:16px;font-weight:800;line-height:1}
    .status-pending{background:#ffc107;color:#333;font-size:11px;padding:3px 10px;border-radius:10px;font-weight:700;display:inline-block}
    .status-approved{background:#28a745;color:#fff;font-size:11px;padding:3px 10px;border-radius:10px;font-weight:700;display:inline-block}
    .status-rejected{background:#dc3545;color:#fff;font-size:11px;padding:3px 10px;border-radius:10px;font-weight:700;display:inline-block}
    #toast-container{position:fixed;top:20px;right:20px;z-index:99999;display:flex;flex-direction:column;gap:8px}
    .toast-notif{display:flex;align-items:center;gap:10px;padding:12px 18px;border-radius:8px;font-size:14px;color:#fff;min-width:260px;max-width:400px;box-shadow:0 4px 16px rgba(0,0,0,.3);animation:slideIn .3s ease;transition:opacity .35s ease}
    .toast-notif.success{background:#28a745}.toast-notif.error{background:#dc3545}.toast-notif.warning{background:#ffc107;color:#333}.toast-notif.hide{opacity:0}
    @keyframes slideIn{from{transform:translateX(110%);opacity:0}to{transform:translateX(0);opacity:1}}
    .btn-action{padding:4px 9px;font-size:12px}
    .content-row{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.1);border-radius:8px;padding:14px 16px;margin-bottom:12px;position:relative}
    .content-row .btn-remove-row{position:absolute;top:8px;right:8px}
    .badge-video{background:#6f42c1;color:#fff;font-size:10px;padding:2px 7px;border-radius:10px}
    .badge-image{background:#17a2b8;color:#fff;font-size:10px;padding:2px 7px;border-radius:10px}
    .remaining-field{background:rgba(255,255,255,.06)!important;cursor:not-allowed;font-weight:600;color:#17a2b8!important}
    .konten-item{border:1px solid rgba(255,255,255,.1);border-radius:8px;padding:10px;margin-bottom:10px;background:rgba(255,255,255,.03)}
    .konten-thumb{width:100%;max-height:140px;object-fit:cover;border-radius:6px;cursor:pointer;transition:opacity .2s}
    .konten-thumb:hover{opacity:.85}
    .konten-thumb-video{width:100%;max-height:140px;border-radius:6px;cursor:pointer}
    .small-box{border-radius:10px;overflow:hidden;box-shadow:0 4px 10px rgba(0,0,0,.25);position:relative}
    .small-box .inner{padding:25px 20px}
    .small-box h3{font-size:38px;font-weight:700;margin:0;line-height:1}
    .small-box p{font-size:17px;margin-top:6px;margin-bottom:0}
    .small-box .icon{position:absolute;top:10px;right:15px;font-size:70px;opacity:.3;pointer-events:none}
    .small-box-footer{display:block;padding:8px;text-align:center;color:#fff;background:rgba(0,0,0,.15);text-decoration:none;font-size:13px}
    .small-box-footer:hover{color:#fff;background:rgba(0,0,0,.25)}
    .msg-item{display:flex;align-items:center;gap:10px;padding:10px 14px;border-bottom:1px solid rgba(255,255,255,.08);cursor:pointer}
    .msg-item:hover{background:rgba(255,255,255,.06)}
    .msg-item img{width:38px;height:38px;border-radius:50%;object-fit:cover}
    .msg-info{flex:1;min-width:0}.msg-name{font-size:13px;font-weight:600}
    .msg-preview{font-size:12px;color:#aaa;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:180px}
    .msg-time{font-size:11px;color:#aaa}
    .notif-item{display:flex;align-items:flex-start;gap:10px;padding:10px 14px;border-bottom:1px solid rgba(255,255,255,.08)}
    .notif-icon{width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .notif-text{font-size:13px;line-height:1.4}.notif-time{font-size:11px;color:#aaa;margin-top:2px}
    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input{background:rgba(255,255,255,.08);color:#fff;border:1px solid rgba(255,255,255,.2);border-radius:4px}
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate .paginate_button{color:#ccc !important}
    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover{background:#007bff !important;border-color:#007bff !important;color:#fff !important}
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover{background:rgba(255,255,255,.1) !important;color:#fff !important}
    table.dataTable thead th{border-bottom:2px solid rgba(255,255,255,.15) !important}
  </style>
</head>

<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">
  <div id="toast-container"></div>

  {{-- NAVBAR --}}
  <nav class="main-header navbar navbar-expand navbar-dark">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a></li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#"><i class="far fa-comments"></i><span class="badge badge-danger navbar-badge">3</span></a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="background:#1e2230;border:1px solid rgba(255,255,255,.1)">
          <span class="dropdown-item dropdown-header" style="color:#aaa;font-size:12px">3 Messages</span>
          <div class="msg-item"><img src="dist/img/user1-128x128.jpg" alt=""><div class="msg-info"><div class="msg-name">Vendor A <span class="float-right msg-time">4 Jam lalu</span></div><div class="msg-preview">Playlist sudah diupload, mohon review.</div></div></div>
          <div class="msg-item"><img src="dist/img/user3-128x128.jpg" alt=""><div class="msg-info"><div class="msg-name">Admin <span class="float-right msg-time">1 Jam lalu</span></div><div class="msg-preview">Contract baru menunggu persetujuan.</div></div></div>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item text-center" style="color:#17a2b8;font-size:13px">Lihat Semua Pesan</a>
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#"><i class="far fa-bell"></i><span class="badge badge-warning navbar-badge">5</span></a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="background:#1e2230;border:1px solid rgba(255,255,255,.1)">
          <span class="dropdown-item dropdown-header" style="color:#aaa;font-size:12px">5 Notifikasi</span>
          <div class="notif-item"><div class="notif-icon bg-warning"><i class="fas fa-exclamation text-dark fa-sm"></i></div><div><div class="notif-text">Kontrak <strong>LED Showroom</strong> hampir habis</div><div class="notif-time"><i class="far fa-clock mr-1"></i>3 Jam lalu</div></div></div>
          <div class="notif-item"><div class="notif-icon bg-success"><i class="fas fa-check text-white fa-sm"></i></div><div><div class="notif-text">Playlist <strong>Promotion</strong> diapprove</div><div class="notif-time"><i class="far fa-clock mr-1"></i>5 Jam lalu</div></div></div>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item text-center" style="color:#17a2b8;font-size:13px">Lihat Semua Notifikasi</a>
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
            <a href="{{ route('dashboard.list') }}" class="nav-link">
              <i class="nav-icon fas fa-chart-line"></i><p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('playlist.index') }}" class="nav-link active">
              <i class="nav-icon fas fa-film"></i><p>Daftar Playlist</p>
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
          <div class="col-sm-6"><h1 class="m-0"><i class="fas fa-film mr-2"></i>Daftar Playlist</h1></div>
          <div class="col-sm-6 text-right">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('dashboard.list') }}">Dashboard</a></li>
              <li class="breadcrumb-item active">Daftar Playlist</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">

        @php
          use Carbon\Carbon;
          $deviceMap = collect($devices ?? [])->keyBy('id');
          $vendorMap = collect($vendors ?? [])->keyBy('id');

          $totalPlaylist = count($playlists);
          $amanCount = $warningCount = $dangerCount = 0;
          foreach ($playlists as $pl) {
            $d = (int) Carbon::now()->diffInDays(Carbon::parse($pl['end_contract']), false);
            if ($d >= 30)     $amanCount++;
            elseif ($d >= 11) $warningCount++;
            else              $dangerCount++;
          }
        @endphp

        {{-- CARD INDIKATOR --}}
        <div class="row mb-3">
          <div class="col-lg-3 col-6">
            <div class="small-box bg-secondary">
              <div class="inner"><h3>{{ $totalPlaylist }}</h3><p>Total Playlist</p></div>
              <div class="icon"><i class="fas fa-film"></i></div>
              <a href="#" class="small-box-footer">Data Playlist</a>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
              <div class="inner"><h3>{{ $amanCount }}</h3><p>Kontrak Aman</p></div>
              <div class="icon"><i class="fas fa-check-circle"></i></div>
              <a href="#" class="small-box-footer">≥ 30 Hari</a>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
              <div class="inner"><h3>{{ $warningCount }}</h3><p>Perlu Perhatian</p></div>
              <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
              <a href="#" class="small-box-footer">11 - 29 Hari</a>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
              <div class="inner"><h3>{{ $dangerCount }}</h3><p>Hampir Habis</p></div>
              <div class="icon"><i class="fas fa-times-circle"></i></div>
              <a href="#" class="small-box-footer">≤ 10 Hari</a>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-12">
            <div class="card card-dark">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-film mr-2"></i>Daftar Playlist</h3>
                <div class="card-tools">
                  <span class="badge badge-secondary mr-2">Total: {{ count($playlists) }}</span>
                </div>
              </div>
              <div class="card-body">
                <table id="playlistTable" class="table table-bordered table-hover table-striped">
                  <thead class="thead-dark">
                    <tr>
                      <th style="width:40px">No</th>
                      <th>Nama Playlist</th>
                      <th>Vendor</th>
                      <th>Device</th>
                      <th>Mulai</th>
                      <th>Habis</th>
                      <th style="width:80px">Sisa</th>
                      <th style="width:110px">Status</th>
                      <th>Nilai Kontrak</th>
                      <th style="width:90px">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($playlists as $i => $p)
                      @php
                        $end   = Carbon::parse($p['end_contract']);
                        $diff  = (int) Carbon::now()->diffInDays($end, false);
                        if ($diff >= 30)     { $sisaClass = 'aman'; }
                        elseif ($diff >= 11) { $sisaClass = 'warning'; }
                        else                 { $sisaClass = 'danger'; }
                        $sisaLabel  = $diff > 0 ? $diff.' hari' : abs($diff).' hari (lewat)';
                        $val        = $p['contract_value'] ?? 0;
                        // Coba vendor_id dulu, fallback ke cust_id
                        $vName = $vendorMap[$p['vendor_id'] ?? '']['nama']
                              ?? $vendorMap[$p['cust_id']   ?? '']['nama']
                              ?? ($p['vendor_id'] ?? '-');
                        $dName = $deviceMap[$p['device_id'] ?? '']['device_name'] ?? '-';
                        $status = match((string)($p['status'] ?? '')) {
                            '2','approved' => 'approved',
                            '3','rejected' => 'rejected',
                            default        => 'pending',
                        };
                        $statusClass = match($status) {
                            'approved' => 'status-approved',
                            'rejected' => 'status-rejected',
                            default    => 'status-pending',
                        };
                        $playlistId = $p['id'] ?? $i;

                        
                        $resolvedItems = $p['resolved_items'] ?? [];
                        $kontenJson = htmlspecialchars(
                            json_encode(array_values($resolvedItems)),
                            ENT_QUOTES
                        );
                      @endphp
                      <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><strong>{{ $p['playlist_name'] }}</strong></td>
                        <td>{{ $vName }}</td>
                        <td>{{ $dName }}</td>
                        <td>{{ Carbon::parse($p['start_contract'])->format('d M Y') }}</td>
                        <td>{{ Carbon::parse($p['end_contract'])->format('d M Y') }}</td>
                        <td class="text-center">
                          <span class="sisa-badge {{ $sisaClass }}">
                            <span class="hari-num">{{ abs($diff) }}</span>
                            <span>hari</span>
                          </span>
                        </td>
                        <td class="text-center"><span class="{{ $statusClass }}">{{ strtoupper($status) }}</span></td>
                        <td>Rp {{ number_format($val, 0, ',', '.') }}</td>
                        <td class="text-center">
                          <button class="btn btn-info btn-action mb-1" title="Preview Data & Konten"
                            data-id="{{ $playlistId }}"
                            data-name="{{ addslashes($p['playlist_name']) }}"
                            data-vendor="{{ addslashes($vName) }}"
                            data-start="{{ $p['start_contract'] }}"
                            data-end="{{ $p['end_contract'] }}"
                            data-value="{{ $val }}"
                            data-sisa="{{ $sisaLabel }}"
                            data-sisaclass="{{ $sisaClass }}"
                            data-status="{{ $status }}"
                            data-konten="{{ $kontenJson }}"
                            onclick="bukaPreview(this)">
                            <i class="fas fa-eye"></i>
                          </button>
                          <button class="btn btn-danger btn-action" title="Hapus"
                            onclick="konfirmasiHapus('{{ $playlistId }}','{{ addslashes($p['playlist_name']) }}')">
                            <i class="fas fa-trash"></i>
                          </button>
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="10" class="text-center py-4 text-muted">
                          <i class="fas fa-inbox fa-2x mb-2 d-block"></i>Belum ada playlist.
                        </td>
                      </tr>
                    @endforelse
                  </tbody>
                  <tfoot>
                    <tr>
                      <th>No</th><th>Nama Playlist</th><th>Vendor</th><th>Device</th>
                      <th>Mulai</th><th>Habis</th><th>Sisa</th><th>Status</th>
                      <th>Nilai Kontrak</th><th>Aksi</th>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        </div>

      </div>
    </section>
  </div>

  {{-- ===== MODAL PREVIEW PLAYLIST + KONTEN ===== --}}
  <div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-info">
          <h4 class="modal-title text-white"><i class="fas fa-eye mr-2"></i>Preview Playlist</h4>
          <button class="close text-white" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">

          {{-- Info Data --}}
          <div class="card card-secondary card-outline mb-3">
            <div class="card-header py-2">
              <h6 class="card-title mb-0"><i class="fas fa-info-circle mr-1"></i> Informasi Playlist</h6>
            </div>
            <div class="card-body p-0">
              <table class="table table-borderless table-sm mb-0">
                <tr><th style="width:140px" class="pl-3">Nama Playlist</th><td>: <span id="prev_name"></span></td></tr>
                <tr><th class="pl-3">Vendor</th><td>: <span id="prev_vendor"></span></td></tr>
                <tr><th class="pl-3">Start Contract</th><td>: <span id="prev_start"></span></td></tr>
                <tr><th class="pl-3">End Contract</th><td>: <span id="prev_end"></span></td></tr>
                <tr><th class="pl-3">Contract Value</th><td>: Rp <span id="prev_value"></span></td></tr>
                <tr><th class="pl-3">Sisa Kontrak</th><td>: <span id="prev_remaining_badge"></span></td></tr>
                <tr><th class="pl-3">Status</th><td>: <span id="prev_status"></span></td></tr>
              </table>
            </div>
          </div>

          {{-- Konten diupload --}}
          <div class="card card-secondary card-outline mb-0">
            <div class="card-header py-2 d-flex align-items-center">
              <h6 class="card-title mb-0"><i class="fas fa-photo-video mr-1"></i> Konten Diupload</h6>
              <span id="kontenCount" class="badge badge-secondary ml-2"></span>
              <span id="kontenLoadingSpinner" class="ml-2" style="display:none;">
                <i class="fas fa-spinner fa-spin text-muted"></i>
              </span>
            </div>
            <div class="card-body" id="kontenContainer">
              <p class="text-muted text-center mb-0">
                <i class="fas fa-spinner fa-spin mr-1"></i>Memuat konten...
              </p>
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>

  {{-- MODAL KONFIRMASI HAPUS --}}
  <div class="modal fade" id="modalHapus" tabindex="-1">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header bg-danger">
          <h5 class="modal-title text-white"><i class="fas fa-trash mr-2"></i>Hapus Playlist</h5>
          <button class="close text-white" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body text-center">
          <p>Yakin ingin menghapus playlist <strong id="hapus_nama"></strong>?</p>
          <p class="text-muted small">Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
          <button class="btn btn-danger btn-sm" id="btnKonfirmasiHapus">
            <i class="fas fa-trash mr-1"></i>Hapus
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- MODAL TAMBAH PLAYLIST --}}
  <div class="modal fade" id="modalTambahPlaylist" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header bg-dark">
          <h4 class="modal-title"><i class="fas fa-plus-circle mr-2"></i>Tambah Playlist</h4>
          <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="cust_id" value="1">

          <div class="card card-secondary card-outline mb-3">
            <div class="card-header py-2"><h6 class="card-title mb-0"><i class="fas fa-film mr-1"></i> Info Playlist</h6></div>
            <div class="card-body pb-0">
              <div class="row">
                <div class="col-md-6"><div class="form-group"><label>Nama Playlist <span class="text-danger">*</span></label><input type="text" id="playlist_name" class="form-control" placeholder="Masukkan nama playlist"></div></div>
                <div class="col-md-6"><div class="form-group"><label>Vendor <span class="text-danger">*</span></label>
                  <select id="vendor_select" name="vendor_id" class="form-control">
                    <option value="">-- Pilih Vendor --</option>
                    @foreach($vendors as $v)
                      <option value="{{ $v['id'] }}">{{ $v['nama'] }}</option>
                    @endforeach
                  </select>
                </div></div>
              </div>
            </div>
          </div>

          <div class="card card-secondary card-outline mb-3">
            <div class="card-header py-2"><h6 class="card-title mb-0"><i class="fas fa-tv mr-1"></i> Info Device</h6></div>
            <div class="card-body pb-0">
              <div class="row">
                <div class="col-md-3"><div class="form-group"><label>Device <span class="text-danger">*</span></label><select id="device_id" class="form-control"><option value="">-- Pilih Vendor dulu --</option></select></div></div>
                <div class="col-md-2"><div class="form-group"><label>Provinsi</label><input type="text" id="provinsi" class="form-control" readonly placeholder="Otomatis"></div></div>
                <div class="col-md-2"><div class="form-group"><label>Kota / Kabupaten</label><input type="text" id="kota" class="form-control" readonly placeholder="Otomatis"><input type="hidden" id="location"></div></div>
                <div class="col-md-2"><div class="form-group"><label>Ukuran (H x W)</label><input type="text" id="ukuran" class="form-control" readonly placeholder="Otomatis"></div></div>
                <div class="col-md-3"><div class="form-group"><label>Contract Value <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                    <input type="number" id="contract_value" class="form-control" placeholder="Otomatis dari vendor" readonly>
                  </div>
                  <small class="text-muted"><i class="fas fa-info-circle"></i> Otomatis terisi saat device dipilih</small>
                </div></div>
              </div>
            </div>
          </div>

          <div class="card card-secondary card-outline mb-3">
            <div class="card-header py-2"><h6 class="card-title mb-0"><i class="fas fa-file-contract mr-1"></i> Info Kontrak</h6></div>
            <div class="card-body pb-0">
              <div class="row">
                <div class="col-md-6"><div class="form-group"><label>Start Contract <span class="text-danger">*</span></label><div class="input-group"><input type="date" id="start_contract" class="form-control"><div class="input-group-append"><span class="input-group-text"><i class="far fa-calendar-alt"></i></span></div></div></div></div>
                <div class="col-md-6"><div class="form-group"><label>End Contract <span class="text-danger">*</span></label><div class="input-group"><input type="date" id="end_contract" class="form-control"><div class="input-group-append"><span class="input-group-text"><i class="far fa-calendar-alt"></i></span></div></div></div></div>
              </div>
            </div>
          </div>

          <div class="card card-secondary card-outline mb-0">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
              <h6 class="card-title mb-0"><i class="fas fa-photo-video mr-1"></i> Upload Konten</h6>
              <div class="d-flex align-items-center">
                <small class="text-warning mr-2"><i class="fas fa-info-circle"></i> Video → H.264 MP4</small>
                <button type="button" class="btn btn-sm btn-outline-primary" id="btnTambahKonten">
                  <i class="fas fa-plus"></i> Tambah Konten
                </button>
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

  {{-- MODAL PREVIEW FILE FULLSCREEN --}}
  <div class="modal fade" id="filePreviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title"><i class="fas fa-photo-video mr-2"></i>Preview File</h5><button class="close" data-dismiss="modal">&times;</button></div>
        <div class="modal-body text-center" id="filePreviewContent"><p class="text-muted">Tidak ada file.</p></div>
        <div class="modal-footer"><button class="btn btn-secondary" data-dismiss="modal">Tutup</button></div>
      </div>
    </div>
  </div>

  <footer class="main-footer"><strong>CMS GikVision</strong> &copy; 2026</footer>
</div>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
<script>
let hapusId  = null;
let rowCount = 0;
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

// ── DataTable init ──
$(function () {
  $('#playlistTable').DataTable({
    responsive: true, autoWidth: false,
    language: {
      search: "Cari:", lengthMenu: "Tampilkan _MENU_ data",
      info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
      infoEmpty: "Tidak ada data", zeroRecords: "Data tidak ditemukan",
      paginate: { first:"Pertama", last:"Terakhir", next:"›", previous:"‹" }
    },
    columnDefs: [{ orderable: false, targets: [6, 9] }],
    order: [[0, 'asc']]
  });
});

function showToast(msg, type='success', dur=4000) {
  const icons = { success:'<i class="fas fa-check-circle"></i>', error:'<i class="fas fa-times-circle"></i>', warning:'<i class="fas fa-exclamation-triangle"></i>' };
  const t = document.createElement('div');
  t.className = `toast-notif ${type}`;
  t.innerHTML = `${icons[type]||''} <span>${msg}</span>`;
  document.getElementById('toast-container').appendChild(t);
  setTimeout(() => { t.classList.add('hide'); setTimeout(() => t.remove(), 400); }, dur);
}

// ================================================================
// PREVIEW PLAYLIST
// Strategi:
// 1. Tampilkan modal segera dengan data dari data-attribute (cepat)
// 2. Fetch fresh dari /api/playlist/{id} untuk dapat konten terbaru
// 3. Kalau fetch gagal, fallback ke resolved_items yang sudah ada di data-konten
// ================================================================
function bukaPreview(btn) {
  const id        = btn.dataset.id;
  const name      = btn.dataset.name;
  const vendor    = btn.dataset.vendor;
  const start     = btn.dataset.start;
  const end       = btn.dataset.end;
  const value     = btn.dataset.value;
  const sisaLabel = btn.dataset.sisa;
  const sisaClass = btn.dataset.sisaclass;
  const status    = btn.dataset.status;

  // Isi info data langsung
  document.getElementById('prev_name').textContent   = name;
  document.getElementById('prev_vendor').textContent = vendor;
  document.getElementById('prev_start').textContent  = start;
  document.getElementById('prev_end').textContent    = end;
  document.getElementById('prev_value').textContent  = Number(value).toLocaleString('id-ID');
  document.getElementById('prev_remaining_badge').innerHTML =
    `<span class="sisa-badge ${sisaClass} d-inline-flex" style="width:auto;border-radius:20px;padding:4px 14px;">${sisaLabel}</span>`;
  const sc = status === 'approved' ? 'status-approved' : (status === 'rejected' ? 'status-rejected' : 'status-pending');
  document.getElementById('prev_status').innerHTML = `<span class="${sc}">${status.toUpperCase()}</span>`;

  // Tampilkan loading
  document.getElementById('kontenContainer').innerHTML =
    '<p class="text-muted text-center mb-0"><i class="fas fa-spinner fa-spin mr-1"></i>Memuat konten dari server...</p>';
  document.getElementById('kontenCount').textContent = '';
  $('#previewModal').modal('show');

  // Coba ambil data fresh via AJAX
  // Controller sudah build resolved_items: playlist_items + contents + file_url
  fetch('/api/playlist/' + id, {
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json'
    }
  })
  .then(function(res) {
    if (!res.ok) throw new Error('HTTP ' + res.status);
    return res.json();
  })
  .then(function(data) {
    if (data.status !== 'success') throw new Error(data.message || 'Response tidak sukses');
    tampilkanKonten(data.data.resolved_items || []);
  })
  .catch(function(err) {
    console.warn('[Preview] Fetch API gagal (' + err.message + '), pakai data lokal.');
    // Fallback: pakai resolved_items dari data-attribute (sudah di-build controller saat render)
    let fallback = [];
    try { fallback = JSON.parse(btn.dataset.konten || '[]'); } catch(e) { fallback = []; }
    if (fallback.length > 0) {
      tampilkanKonten(fallback);
    } else {
      document.getElementById('kontenContainer').innerHTML =
        '<div class="alert alert-warning mb-0">' +
        '<i class="fas fa-exclamation-triangle mr-1"></i>' +
        'Gagal memuat konten: ' + err.message +
        '</div>';
    }
  });
}

// ================================================================
// Render konten ke dalam modal
// Setiap item dari resolved_items (dibangun controller) punya:
//   - file_url   : URL penuh (sudah include /storage/)
//   - file_type  : 'image' atau 'video'
//   - days       : array (sudah di-decode dari JSON string)
//   - content    : object dari tabel contents (punya field 'title', 'file', 'type')
//   - start_date, end_date, start_time, end_time, priority_mode
// ================================================================
function tampilkanKonten(kontenList) {
  const container = document.getElementById('kontenContainer');
  const countEl   = document.getElementById('kontenCount');
  countEl.textContent = (kontenList.length || 0) + ' konten';

  if (!kontenList || kontenList.length === 0) {
    container.innerHTML =
      '<p class="text-muted text-center mb-0">' +
      '<i class="fas fa-inbox mr-1"></i>Belum ada konten untuk playlist ini.</p>';
    return;
  }

  let html = '<div class="row">';
  kontenList.forEach(function(k) {
    const fileUrl  = k.file_url || '';
    const fileType = k.file_type || (k.content ? k.content.type : '') || '';
    const isVideo  = fileType === 'video' || /\.(mp4|mov|avi|mkv|webm)/i.test(fileUrl);
    const isImage  = fileType === 'image' || /\.(jpg|jpeg|png|gif|webp|bmp)/i.test(fileUrl);

    // Nama file
    const namaFile = (k.content && k.content.title) ? k.content.title : 'File konten';
    const tipeBadge = isVideo
      ? '<span class="badge-video ml-1"><i class="fas fa-film"></i> video</span>'
      : (isImage ? '<span class="badge-image ml-1"><i class="fas fa-image"></i> gambar</span>' : '');

    // Thumbnail
    let thumbHtml = '';
    if (fileUrl) {
      if (isImage) {
        // onerror: tampilkan placeholder kalau gambar tidak bisa dimuat
        thumbHtml = `<img src="${fileUrl}" class="konten-thumb"
          onclick="previewFile('${fileUrl}','image')"
          onerror="this.closest('.thumb-wrap').innerHTML='<div class=\\'text-center py-3\\'><i class=\\'fas fa-image fa-2x text-muted\\'></i><br><small class=\\'text-muted\\'>Gambar tidak bisa dimuat</small></div>'"
          title="Klik untuk preview">`;
      } else if (isVideo) {
        thumbHtml = `<video class="konten-thumb-video"
          onclick="previewFile('${fileUrl}','video')"
          title="Klik untuk preview" preload="metadata">
          <source src="${fileUrl}">
        </video>`;
      } else {
        thumbHtml = `<div class="text-center py-3">
          <i class="fas fa-file fa-2x text-muted"></i>
          <br><small class="text-muted">Tipe file tidak dikenali</small>
        </div>`;
      }
    } else {
      thumbHtml = `<div class="text-center py-3">
        <i class="fas fa-file-upload fa-2x text-muted"></i>
        <br><small class="text-muted">File URL tidak tersedia</small>
      </div>`;
    }

    // Days: sudah array dari controller
    let daysArr = k.days || [];
    if (typeof daysArr === 'string') {
      try { daysArr = JSON.parse(daysArr); } catch(e) { daysArr = [daysArr]; }
    }
    const daysText = (Array.isArray(daysArr) && daysArr.length > 0) ? daysArr.join(', ') : '-';

    const prio = k.priority_mode
      ? '<span class="badge badge-warning">Prioritas Aktif</span>'
      : '<span class="text-muted small">Tidak aktif</span>';

    html += `
    <div class="col-md-6 mb-3">
      <div class="konten-item">
        <div class="mb-1 d-flex align-items-center justify-content-between">
          <small class="text-muted text-truncate" style="max-width:80%;" title="${namaFile}">${namaFile}</small>
          ${tipeBadge}
        </div>
        <div class="thumb-wrap mb-2">${thumbHtml}</div>
        <table class="table table-borderless table-sm mb-0" style="font-size:12px;">
          <tr>
            <th style="width:90px">Start Date</th>
            <td>${k.start_date || '-'}</td>
            <th>End Date</th>
            <td>${k.end_date || '-'}</td>
          </tr>
          <tr>
            <th>Start Time</th>
            <td>${k.start_time || '-'}</td>
            <th>End Time</th>
            <td>${k.end_time || '-'}</td>
          </tr>
          <tr><th>Hari Tayang</th><td colspan="3">${daysText}</td></tr>
          <tr><th>Prioritas</th><td colspan="3">${prio}</td></tr>
        </table>
      </div>
    </div>`;
  });

  html += '</div>';
  container.innerHTML = html;
}

// Preview file fullscreen
function previewFile(url, type) {
  const cont = document.getElementById('filePreviewContent');
  if (type === 'image') {
    cont.innerHTML = `<img src="${url}" class="img-fluid rounded" onerror="this.parentElement.innerHTML='<p class=\\'text-muted\\'>Gambar tidak bisa dimuat</p>'">`;
  } else {
    cont.innerHTML = `<video controls class="w-100" style="max-height:450px"><source src="${url}"></video>`;
  }
  $('#filePreviewModal').modal('show');
}

function konfirmasiHapus(id, nama) {
  hapusId = id;
  document.getElementById('hapus_nama').textContent = nama;
  $('#modalHapus').modal('show');
}

function previewFilePicker(input, idx) {
  const pv = document.getElementById('filePreview_'+idx);
  const ne = document.getElementById('fileName_'+idx);
  const te = document.getElementById('fileType_'+idx);
  if (input.files && input.files[0]) {
    const f = input.files[0];
    ne.textContent = f.name;
    te.innerHTML = f.type.startsWith('video/')
      ? '<span class="badge-video"><i class="fas fa-film"></i> video→H.264</span>'
      : f.type.startsWith('image/')
        ? '<span class="badge-image"><i class="fas fa-image"></i> gambar</span>' : '';
    pv.style.display = 'block';
  } else {
    pv.style.display = 'none';
  }
}

function removeRow(btn) { btn.closest('.content-row').remove(); }

function calcRemaining(idx) {
  const sv = document.querySelector(`#contentRow_${idx} input[name="start_time[]"]`).value;
  const ev = document.querySelector(`#contentRow_${idx} input[name="end_time[]"]`).value;
  const el = document.getElementById('remaining_'+idx);
  if (!sv || !ev) { el.value = ''; return; }
  const [sh, sm] = sv.split(':').map(Number), [eh, em] = ev.split(':').map(Number);
  let diff = (eh*60+em) - (sh*60+sm);
  if (diff <= 0) diff += 1440;
  const h = Math.floor(diff/60), m = diff%60;
  el.value = h > 0 ? (m > 0 ? `${h} jam ${m} menit` : `${h} jam`) : `${m} menit`;
}

$(document).on('click', '.btn-preview-file-picker', function() {
  const idx  = $(this).data('idx');
  const fi   = document.querySelector(`#contentRow_${idx} input[type="file"]`);
  const cont = document.getElementById('filePreviewContent');
  cont.innerHTML = '';
  if (!fi || !fi.files[0]) {
    cont.innerHTML = '<p class="text-muted">Belum ada file dipilih.</p>';
  } else {
    const file = fi.files[0], url = URL.createObjectURL(file);
    cont.innerHTML = file.type.startsWith('image/')
      ? `<img src="${url}" class="img-fluid rounded">`
      : file.type.startsWith('video/')
        ? `<video controls class="w-100" style="max-height:400px"><source src="${url}" type="${file.type}"></video>`
        : '<p class="text-muted">Preview tidak tersedia.</p>';
  }
  $('#filePreviewModal').modal('show');
});

$(document).ready(function() {

  // ── HAPUS ──
  $('#btnKonfirmasiHapus').click(function() {
    if (!hapusId) return;
    const btn = $(this);
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Menghapus...');
    $.ajax({
      url: '/playlist/delete/' + hapusId, method: 'DELETE',
      success: function(res) {
        $('#modalHapus').modal('hide');
        showToast(res.message || 'Berhasil dihapus!', 'success');
        setTimeout(() => location.reload(), 1500);
      },
      error: function(err) {
        $('#modalHapus').modal('hide');
        btn.prop('disabled', false).html('<i class="fas fa-trash mr-1"></i>Hapus');
        showToast(err.responseJSON?.message || 'Gagal menghapus!', 'error');
      }
    });
  });

  // ── TAMBAH ROW KONTEN ──
  $('#btnTambahKonten').click(function() {
    const idx = rowCount;
    const hariList = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    const hariHTML = hariList.map(h =>
      `<label class="mr-2" style="font-size:13px;"><input type="checkbox" name="days[${idx}][]" value="${h}" class="mr-1">${h}</label>`
    ).join('');
    const row = `
    <div class="content-row" id="contentRow_${idx}">
      <button type="button" class="btn btn-xs btn-danger btn-remove-row" onclick="removeRow(this)"><i class="fas fa-times"></i></button>
      <div class="row align-items-end">
        <div class="col-md-3">
          <div class="form-group mb-2">
            <label class="small">File <span class="text-danger">*</span></label>
            <div class="custom-file">
              <input type="file" name="file[]" class="custom-file-input" accept="video/*,image/*"
                onchange="previewFilePicker(this,${idx});$(this).next().text(this.files[0]?this.files[0].name:'Pilih file')">
              <label class="custom-file-label" style="font-size:12px;">Pilih file</label>
            </div>
          </div>
          <div id="filePreview_${idx}" style="display:none;">
            <small class="text-success"><i class="fas fa-check-circle"></i> <span id="fileName_${idx}"></span> <span id="fileType_${idx}" class="ml-1"></span></small>
          </div>
        </div>
        <div class="col-md-2"><div class="form-group mb-2"><label class="small">Start Date</label><input type="date" name="start_date[]" class="form-control form-control-sm"></div></div>
        <div class="col-md-2"><div class="form-group mb-2"><label class="small">End Date</label><input type="date" name="end_date[]" class="form-control form-control-sm"></div></div>
        <div class="col-md-1" style="min-width:130px;"><div class="form-group mb-2"><label class="small">Start Time</label><input type="time" name="start_time[]" class="form-control form-control-sm" onchange="calcRemaining(${idx})"></div></div>
        <div class="col-md-1" style="min-width:130px;"><div class="form-group mb-2"><label class="small">End Time</label><input type="time" name="end_time[]" class="form-control form-control-sm" onchange="calcRemaining(${idx})"></div></div>
        <div class="col-md-1" style="min-width:120px;"><div class="form-group mb-2"><label class="small">Remaining</label><input type="text" id="remaining_${idx}" class="form-control form-control-sm remaining-field" readonly placeholder="Otomatis"></div></div>
        <div class="col-md-1"><div class="form-group mb-2"><label class="small d-block">Prioritas</label>
          <div class="icheck-primary d-inline">
            <input type="checkbox" name="priority_mode[${idx}]" id="prio_${idx}" value="1">
            <label for="prio_${idx}"> Aktif</label>
          </div>
        </div></div>
        <div class="col-auto"><div class="form-group mb-2" style="padding-top:20px;">
          <button type="button" class="btn btn-sm btn-outline-info btn-preview-file-picker" data-idx="${idx}"><i class="fas fa-eye"></i></button>
        </div></div>
      </div>
      <div class="mt-2"><small class="text-muted mr-2"><i class="fas fa-calendar-week mr-1"></i>Hari Tayang:</small>${hariHTML}</div>
    </div>`;
    document.getElementById('contentRows').style.display = 'block';
    document.getElementById('contentRows').insertAdjacentHTML('beforeend', row);
    rowCount++;
  });

  // ── VENDOR CHANGE ──
  $('#vendor_select').change(function() {
    const vId = $(this).val();
    $('#device_id').html('<option value="">-- Pilih Vendor dulu --</option>');
    $('#provinsi,#kota,#ukuran').val(''); $('#location,#contract_value').val('');
    if (!vId) return;
    $('#device_id').html('<option value="">Loading...</option>');
    $.ajax({
      url: '/get-devices/' + vId, method: 'GET',
      success: function(data) {
        let html = '<option value="">-- Pilih Device --</option>';
        data.forEach(d => {
          html += `<option value="${d.id}"
            data-provinsi="${d.provinsi||''}"
            data-kota="${d.kota||''}"
            data-width="${d.r_width||''}"
            data-height="${d.r_height||''}"
            data-price="${d.price||d.contract_value||''}">${d.device_name}</option>`;
        });
        $('#device_id').html(html);
        if (!data.length) {
          $('#device_id').html('<option value="">-- Vendor belum punya device --</option>');
          showToast('Vendor ini belum punya device!', 'warning');
        }
      },
      error: function(xhr) {
        $('#device_id').html('<option value="">-- Gagal load --</option>');
        showToast('Gagal ambil device (' + xhr.status + ')', 'error');
      }
    });
  });

  // ── DEVICE CHANGE ──
  $('#device_id').change(function() {
    const sel = $(this).find(':selected');
    const provinsi = sel.data('provinsi') || '', kota = sel.data('kota') || '';
    $('#provinsi').val(provinsi); $('#kota').val(kota);
    $('#ukuran').val(sel.data('width') && sel.data('height') ? sel.data('height') + ' x ' + sel.data('width') : '');
    $('#location').val(provinsi && kota ? provinsi + ', ' + kota : (provinsi || kota));
    const price = sel.data('price') || '';
    if (price) {
      $('#contract_value').val(price);
    } else {
      const devId = $(this).val();
      if (devId) {
        $.ajax({
          url: '/get-device/' + devId, method: 'GET',
          success: function(d) {
            if (d && (d.price || d.contract_value)) {
              $('#contract_value').val(d.price || d.contract_value);
            }
          }
        });
      }
    }
  });

  // ── SIMPAN PLAYLIST ──
  $('#btnSimpan').click(function() {
    const btn  = $(this);
    const pName = $('#playlist_name').val().trim();
    const vId   = $('#vendor_select').val();
    const dId   = $('#device_id').val();
    const sc    = $('#start_contract').val();
    const ec    = $('#end_contract').val();
    const cv    = $('#contract_value').val();

    if (!pName) { showToast('Nama playlist wajib diisi!', 'error'); return; }
    if (!vId)   { showToast('Vendor wajib dipilih!', 'error'); return; }
    if (!dId)   { showToast('Device wajib dipilih!', 'error'); return; }
    if (!sc)    { showToast('Start contract wajib diisi!', 'error'); return; }
    if (!ec)    { showToast('End contract wajib diisi!', 'error'); return; }
    if (!cv)    { showToast('Contract value wajib diisi!', 'error'); return; }

    // Cek minimal 1 file ada
    let adaFile = false;
    $('input[name="file[]"]').each(function() {
      if (this.files && this.files[0]) { adaFile = true; return false; }
    });
    if (!adaFile) { showToast('Minimal upload 1 file konten!', 'error'); return; }

    const fd = new FormData();
    fd.append('_token', $('meta[name="csrf-token"]').attr('content'));
    fd.append('playlist_name', pName);
    fd.append('vendor_id', vId);
    fd.append('device_id', dId);
    fd.append('start_contract', sc);
    fd.append('end_contract', ec);
    fd.append('contract_value', cv);
    fd.append('location', $('#location').val() || '');
    fd.append('cust_id', $('#cust_id').val() || '1');

    // Iterasi per content-row supaya index konsisten antara file dan data
    // FIX: kirim "start_date[0]", "start_date[1]", ... (indexed)
    // bukan "start_date[]" (flat) supaya temen bisa akses $request->start_date[$index]
    $('.content-row').each(function(i) {
      const row = $(this);
      const fileInput = row.find('input[name="file[]"]')[0];
      if (fileInput && fileInput.files && fileInput.files[0]) {
        fd.append('file[]', fileInput.files[0]);
      }
      fd.append('start_date[' + i + ']', row.find('input[name="start_date[]"]').val() || '');
      fd.append('end_date[' + i + ']',   row.find('input[name="end_date[]"]').val()   || '');
      fd.append('start_time[' + i + ']', row.find('input[name="start_time[]"]').val() || '');
      fd.append('end_time[' + i + ']',   row.find('input[name="end_time[]"]').val()   || '');

      // Days: days[i][]
      row.find('input[type="checkbox"][name^="days"]').each(function() {
        if ($(this).is(':checked')) {
          fd.append('days[' + i + '][]', $(this).val());
        }
      });

      // FIX priority_mode: kirim key HANYA kalau checked
      // saveplaylist temen pakai isset() — kirim '0' tetap dianggap ada → priority = 1 (salah!)
      if (row.find('input[name^="priority_mode"]').is(':checked')) {
        fd.append('priority_mode[' + i + ']', '1');
      }
      // Kalau tidak checked: SKIP, jangan append — isset() = false di temen → priority = 0
    });

    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Menyimpan...');

    $.ajax({
      url: '{{ route("playlist.store") }}',
      method: 'POST', data: fd, contentType: false, processData: false, timeout: 600000,
      success: function(res) {
        showToast(res.message || 'Playlist berhasil disimpan!', 'success');
        $('#modalTambahPlaylist').modal('hide');
        setTimeout(() => location.reload(), 1600);
      },
      error: function(err) {
        const r = err.responseJSON;
        let msg = 'Error ' + err.status;
        if (err.status === 419)   msg = 'Session expired, refresh halaman!';
        else if (r?.errors)       msg = Object.values(r.errors)[0][0];
        else if (r?.message)      msg = r.message;
        showToast(msg, 'error');
        console.error('Store error:', err.responseText);
      },
      complete: function() {
        btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i>Simpan Playlist');
      }
    });
  });

  // ── RESET MODAL saat ditutup ──
  $('#modalTambahPlaylist').on('hidden.bs.modal', function() {
    $('#playlist_name,#start_contract,#end_contract,#contract_value').val('');
    $('#vendor_select').val('');
    $('#device_id').html('<option value="">-- Pilih Vendor dulu --</option>');
    $('#provinsi,#kota,#ukuran,#location').val('');
    document.getElementById('contentRows').innerHTML = '';
    document.getElementById('contentRows').style.display = 'none';
    rowCount = 0;
  });
});
</script>
</body>
</html>