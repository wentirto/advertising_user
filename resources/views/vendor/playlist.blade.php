@extends('layout.master')

@section('title', 'Daftar Playlist')

@push('styles')
<style>
    .sisa-badge{display:inline-flex;align-items:center;justify-content:center;width:54px;height:54px;border-radius:50%;font-size:11px;font-weight:700;line-height:1.2;text-align:center;flex-direction:column}
    .sisa-badge.aman{background:#28a745;color:#fff}
    .sisa-badge.warning{background:#ffc107;color:#333}
    .sisa-badge.danger{background:#dc3545;color:#fff}
    .sisa-badge .hari-num{font-size:16px;font-weight:800;line-height:1}
    .status-paid{background:#28a745;color:#fff;font-size:11px;padding:3px 10px;border-radius:10px;font-weight:700;display:inline-block}
    .status-unpaid{background:#dc3545;color:#fff;font-size:11px;padding:3px 10px;border-radius:10px;font-weight:700;display:inline-block}
    .status-pending{background:#ffc107;color:#333;font-size:11px;padding:3px 10px;border-radius:10px;font-weight:700;display:inline-block}
    .status-approved{background:#28a745;color:#fff;font-size:11px;padding:3px 10px;border-radius:10px;font-weight:700;display:inline-block}
    .status-rejected{background:#dc3545;color:#fff;font-size:11px;padding:3px 10px;border-radius:10px;font-weight:700;display:inline-block}
    .btn-action{padding:4px 9px;font-size:12px}
    .badge-video{background:#6f42c1;color:#fff;font-size:10px;padding:2px 7px;border-radius:10px}
    .badge-image{background:#17a2b8;color:#fff;font-size:10px;padding:2px 7px;border-radius:10px}
    .konten-item{border:1px solid rgba(255,255,255,.1);border-radius:8px;padding:10px;margin-bottom:10px;background:rgba(255,255,255,.03)}
    .konten-thumb{width:100%;max-height:140px;object-fit:cover;border-radius:6px;cursor:pointer;transition:opacity .2s}
    .konten-thumb:hover{opacity:.85}
    .konten-thumb-video{width:100%;max-height:140px;border-radius:6px;cursor:pointer}
    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input{background:rgba(255,255,255,.08);color:#fff;border:1px solid rgba(255,255,255,.2);border-radius:4px}
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate .paginate_button{color:#ccc !important}
    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover{background:#007bff !important;border-color:#007bff !important;color:#fff !important}
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover{background:rgba(255,255,255,.1) !important;color:#fff !important}
    table.dataTable thead th{border-bottom:2px solid rgba(255,255,255,.15) !important}
</style>
@endpush

@section('content')
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6"><h1 class="m-0"><i class="fas fa-film mr-2"></i>Daftar Playlist</h1></div>
          <div class="col-sm-6 text-right">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('vendor.dashboard') }}">Dashboard</a></li>
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
          use App\Models\Invoice;

          $deviceMap = collect($devices ?? [])->keyBy('id');
          $vendorMap = collect($vendors ?? [])->keyBy('id');

          $localInvoices = Invoice::all()->keyBy('playlist_id');

          // CUMA LOLOSIN YANG PAID / 1
          $playlists = array_values(array_filter($playlists, function($p) use ($localInvoices) {
            $apiStatus = strtolower((string)($p['status'] ?? ''));
            $inv = $localInvoices->get($p['id']);
            $localStatus = $inv ? strtolower($inv->status) : 'unpaid';

            return in_array($apiStatus, ['paid', 'approved', '1']) || in_array($localStatus, ['paid', 'approved', '1']); 
          }));

          $totalPlaylist = count($playlists);
          $amanCount = $warningCount = $dangerCount = 0;
          
          foreach ($playlists as &$p) { 
            $inv = $localInvoices->get($p['id']);
            $p['contract_value'] = $inv ? $inv->amount : ($p['contract_value'] ?? 0);

            $d = (int) Carbon::now()->diffInDays(Carbon::parse($p['end_contract']), false);
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
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
              <div class="inner"><h3>{{ $amanCount }}</h3><p>Kontrak Aman</p></div>
              <div class="icon"><i class="fas fa-check-circle"></i></div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
              <div class="inner"><h3>{{ $warningCount }}</h3><p>Perlu Perhatian</p></div>
              <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
              <div class="inner"><h3>{{ $dangerCount }}</h3><p>Hampir Habis</p></div>
              <div class="icon"><i class="fas fa-times-circle"></i></div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-12">
            <div class="card card-dark">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-film mr-2"></i>Daftar Playlist</h3>
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
                      <th style="width:110px">Aksi</th>
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
                        
                        $vName = $vendorMap[$p['vendor_id'] ?? '']['nama'] ?? $vendorMap[$p['cust_id'] ?? '']['nama'] ?? '-';
                        $dName = $deviceMap[$p['device_id'] ?? '']['device_name'] ?? '-';
                        
                        $apiStatus = strtolower((string)($p['status'] ?? ''));
                        $invStatus = isset($localInvoices[$p['id']]) ? strtolower($localInvoices[$p['id']]->status) : 'unpaid';
                        
                        $isPaid = in_array($apiStatus, ['paid', 'approved', '1']) || in_array($invStatus, ['paid', 'approved', '1']);
                        
                        $status = $isPaid ? 'paid' : 'unpaid';
                        $statusClass = $isPaid ? 'status-paid' : 'status-unpaid';
                        
                        $playlistId = $p['id'] ?? $i;
                        $resolvedItems = $p['resolved_items'] ?? [];
                        $kontenJson = htmlspecialchars(json_encode(array_values($resolvedItems)), ENT_QUOTES);
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
                          <a href="{{ route('vendor.invoice', $playlistId) }}" target="_blank" class="btn btn-success btn-action mb-1" title="Lihat Invoice">
                            <i class="fas fa-file-invoice-dollar"></i>
                          </a>
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
                </table>
              </div>
            </div>
          </div>
        </div>

      </div>
    </section>

    {{-- MODAL PREVIEW PLAYLIST + KONTEN --}}
    <div class="modal fade" id="previewModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header bg-info">
            <h4 class="modal-title text-white"><i class="fas fa-eye mr-2"></i>Preview Playlist</h4>
            <button class="close text-white" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">

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

            <div class="card card-secondary card-outline mb-0">
              <div class="card-header py-2 d-flex align-items-center">
                <h6 class="card-title mb-0"><i class="fas fa-photo-video mr-1"></i> Konten Diupload</h6>
                <span id="kontenCount" class="badge badge-secondary ml-2"></span>
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
@endsection

@push('scripts')
<script>
$(function () {
  $('#playlistTable').DataTable({
    responsive: true, autoWidth: false,
    language: {
      search: "Cari:", lengthMenu: "Tampilkan _MENU_ data",
      info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
      infoEmpty: "Tidak ada data", zeroRecords: "Data tidak ditemukan",
      paginate: { first:"Pertama", last:"Terakhir", next:"\u203a", previous:"\u2039" }
    },
    columnDefs: [{ orderable: false, targets: [6, 9] }],
    order: [[0, 'asc']]
  });
});

function bukaPreview(btn) {
  const id=btn.dataset.id, name=btn.dataset.name, vendor=btn.dataset.vendor;
  const start=btn.dataset.start, end=btn.dataset.end, value=btn.dataset.value;
  const sisaLabel=btn.dataset.sisa, sisaClass=btn.dataset.sisaclass, status=btn.dataset.status;

  document.getElementById('prev_name').textContent   = name;
  document.getElementById('prev_vendor').textContent = vendor;
  document.getElementById('prev_start').textContent  = start;
  document.getElementById('prev_end').textContent    = end;
  document.getElementById('prev_value').textContent  = Number(value).toLocaleString('id-ID');
  document.getElementById('prev_remaining_badge').innerHTML =
    `<span class="sisa-badge ${sisaClass} d-inline-flex" style="width:auto;border-radius:20px;padding:4px 14px;">${sisaLabel}</span>`;
  
  const sc = status==='paid'?'status-paid':(status==='approved'?'status-approved':(status==='rejected'?'status-rejected':'status-pending'));
  document.getElementById('prev_status').innerHTML = `<span class="${sc}">${status.toUpperCase()}</span>`;

  document.getElementById('kontenContainer').innerHTML = '<p class="text-muted text-center mb-0"><i class="fas fa-spinner fa-spin mr-1"></i>Memuat konten dari server...</p>';
  document.getElementById('kontenCount').textContent = '';
  $('#previewModal').modal('show');

  fetch('/api/playlist/' + id, {
    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
  })
  .then(res => { if (!res.ok) throw new Error('HTTP ' + res.status); return res.json(); })
  .then(data => {
    if (data.status !== 'success') throw new Error(data.message || 'Response tidak sukses');
    tampilkanKonten(data.data.resolved_items || []);
  })
  .catch(err => {
    let fallback = [];
    try { fallback = JSON.parse(btn.dataset.konten || '[]'); } catch(e) {}
    if (fallback.length > 0) { tampilkanKonten(fallback); } 
    else { document.getElementById('kontenContainer').innerHTML = `<div class="alert alert-warning mb-0"><i class="fas fa-exclamation-triangle mr-1"></i>Gagal memuat konten: ${err.message}</div>`; }
  });
}

function tampilkanKonten(kontenList) {
  const container = document.getElementById('kontenContainer');
  const countEl   = document.getElementById('kontenCount');
  countEl.textContent = (kontenList.length || 0) + ' konten';
  if (!kontenList || kontenList.length === 0) {
    container.innerHTML = '<p class="text-muted text-center mb-0"><i class="fas fa-inbox mr-1"></i>Belum ada konten untuk playlist ini.</p>';
    return;
  }
  let html = '<div class="row">';
  kontenList.forEach(function(k) {
    const fileUrl  = k.file_url || '';
    const fileType = k.file_type || (k.content ? k.content.type : '') || '';
    const isVideo  = fileType==='video' || /\.(mp4|mov|avi|mkv|webm)/i.test(fileUrl);
    const isImage  = fileType==='image' || /\.(jpg|jpeg|png|gif|webp|bmp)/i.test(fileUrl);
    const namaFile = (k.content && k.content.title) ? k.content.title : 'File konten';
    const tipeBadge = isVideo ? '<span class="badge-video ml-1"><i class="fas fa-film"></i> video</span>' : (isImage ? '<span class="badge-image ml-1"><i class="fas fa-image"></i> gambar</span>' : '');
    let thumbHtml = '';
    if (fileUrl) {
      if (isImage) { thumbHtml = `<img src="${fileUrl}" class="konten-thumb" onclick="previewFile('${fileUrl}','image')" onerror="this.closest('.thumb-wrap').innerHTML='<div class=\\'text-center py-3\\'><i class=\\'fas fa-image fa-2x text-muted\\'></i></div>'" title="Klik untuk preview">`; } 
      else if (isVideo) { thumbHtml = `<video class="konten-thumb-video" onclick="previewFile('${fileUrl}','video')" title="Klik untuk preview" preload="metadata"><source src="${fileUrl}"></video>`; } 
      else { thumbHtml = '<div class="text-center py-3"><i class="fas fa-file fa-2x text-muted"></i></div>'; }
    } else { thumbHtml = '<div class="text-center py-3"><i class="fas fa-file-upload fa-2x text-muted"></i></div>'; }
    
    let daysArr = k.days || [];
    if (typeof daysArr === 'string') { try { daysArr = JSON.parse(daysArr); } catch(e) { daysArr = [daysArr]; } }
    const daysText = (Array.isArray(daysArr) && daysArr.length > 0) ? daysArr.join(', ') : '-';
    const prio = k.priority_mode ? '<span class="badge badge-warning">Prioritas Aktif</span>' : '<span class="text-muted small">Tidak aktif</span>';
    html += `<div class="col-md-6 mb-3"><div class="konten-item"><div class="mb-1 d-flex align-items-center justify-content-between"><small class="text-muted text-truncate" style="max-width:80%;" title="${namaFile}">${namaFile}</small>${tipeBadge}</div><div class="thumb-wrap mb-2">${thumbHtml}</div><table class="table table-borderless table-sm mb-0" style="font-size:12px;"><tr><th style="width:90px">Start Date</th><td>${k.start_date||'-'}</td><th>End Date</th><td>${k.end_date||'-'}</td></tr><tr><th>Start Time</th><td>${k.start_time||'-'}</td><th>End Time</th><td>${k.end_time||'-'}</td></tr><tr><th>Hari Tayang</th><td colspan="3">${daysText}</td></tr><tr><th>Prioritas</th><td colspan="3">${prio}</td></tr></table></div></div>`;
  });
  html += '</div>';
  container.innerHTML = html;
}

function previewFile(url, type) {
  const cont = document.getElementById('filePreviewContent');
  if (type === 'image') { cont.innerHTML = `<img src="${url}" class="img-fluid rounded" onerror="this.parentElement.innerHTML='<p class=\\'text-muted\\'>Gambar tidak bisa dimuat</p>'">`; } 
  else { cont.innerHTML = `<video controls class="w-100" style="max-height:450px"><source src="${url}"></video>`; }
  $('#filePreviewModal').modal('show');
}
</script>
@endpush