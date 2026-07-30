@extends('layout.master')

@section('title', 'Daftar Permintaan')

@push('styles')
@endpush

@section('content')
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6"><h1 class="m-0"><i class="fas fa-clipboard-list mr-2"></i>Daftar Permintaan</h1></div>
          <div class="col-sm-6 text-right">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('vendor.dashboard') }}">Dashboard</a></li>
              <li class="breadcrumb-item active">Daftar Permintaan</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">

        <input type="hidden" id="cust_id" value="1">
        <input type="hidden" id="selected_device_id">
        <input type="hidden" id="location">
        <input type="hidden" id="s2_start_date">
        <input type="hidden" id="s2_end_date">
        <input type="hidden" id="s2_start_time" value="00:00">
        <input type="hidden" id="s2_end_time" value="00:00">

        {{-- STEP INDICATOR --}}
        <ul class="nav nav-pills nav-justified mb-0" id="wizardSteps">
          <li class="nav-item"><span class="nav-link active" data-step="1"><i class="fas fa-map-marker-alt mr-1"></i>1. Pilih Lokasi</span></li>
          <li class="nav-item"><span class="nav-link disabled" data-step="2"><i class="fas fa-calendar-alt mr-1"></i>2. Jadwal Device</span></li>
          <li class="nav-item"><span class="nav-link disabled" data-step="3"><i class="fas fa-file-alt mr-1"></i>3. Detail Playlist</span></li>
        </ul>

        {{-- ===== STEP 1: PILIH LOKASI ===== --}}
        <div id="stepLokasi" class="step-section">
          <div class="card card-secondary card-outline mb-0">
            <div class="card-header py-2"><h6 class="card-title mb-0"><i class="fas fa-map-marker-alt mr-1"></i>Lokasi yang Tersedia</h6></div>
            <div class="card-body">
              <p class="text-muted mb-3">Klik salah satu lokasi untuk melanjutkan ke jadwal.</p>
              <div class="row" id="lokasiContainer">
                <div class="col-12 text-center text-muted py-5"><i class="fas fa-spinner fa-spin fa-2x mb-2 d-block"></i>Memuat lokasi...</div>
              </div>
            </div>
          </div>

          <div id="playlistUnpaidSection" class="card card-secondary card-outline mt-3" style="display:none;">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
              <h6 class="card-title mb-0"><i class="fas fa-clipboard-list mr-1"></i>Playlist Aktif di <span id="unpaidDeviceName">-</span></h6>
              <span class="badge badge-danger" id="unpaidCount">0 unpaid</span>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                  <thead class="thead-dark">
                    <tr>
                      <th style="width:36px">No</th>
                      <th>Nama Playlist</th>
                      <th>Mulai</th>
                      <th>Selesai</th>
                      <th>Jam Tayang</th>
                      <th style="width:80px">Sisa</th>
                      <th style="width:80px">Status</th>
                      <th>Nilai Kontrak</th>
                      <th style="width:90px">Aksi</th>
                    </tr>
                  </thead>
                  <tbody id="unpaidPlaylistBody">
                    <tr><td colspan="9" class="text-center text-muted py-3">Pilih lokasi terlebih dahulu.</td></tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="card-footer py-2 d-flex justify-content-end">
              <button type="button" class="btn btn-primary" id="btnLanjutStep2">
                Lanjut Buat Playlist Baru <i class="fas fa-arrow-right ml-1"></i>
              </button>
            </div>
          </div>
        </div>

        {{-- ===== STEP 2: JADWAL DEVICE ===== --}}
        <div id="stepJadwal" class="step-section" style="display:none;">
          <div class="card card-secondary card-outline mb-3">
            <div class="card-body d-flex justify-content-between align-items-center">
              <div><h5 class="mb-0" id="deviceInfoName">-</h5><small class="text-muted" id="deviceInfoLoc">-</small></div>
              <button type="button" class="btn btn-sm btn-outline-secondary" onclick="gotoStep(1)"><i class="fas fa-exchange-alt mr-1"></i>Ganti Lokasi</button>
            </div>
          </div>

          <div class="card card-secondary card-outline mb-3">
            <div class="card-header py-2"><h6 class="card-title mb-0"><i class="fas fa-calendar-alt mr-1"></i>Kalender Ketersediaan — Klik untuk Pilih Tanggal</h6></div>
            <div class="card-body" id="calendarContainer" style="min-height:200px;">
              <p class="text-center text-muted py-4"><i class="fas fa-spinner fa-spin mr-1"></i>Memuat kalender...</p>
            </div>
            <div id="bookingInfoPanel" class="card-footer py-2" style="display:none;">
              <small class="text-muted"><i class="fas fa-info-circle mr-1"></i></small>
              <span id="bookingInfoText" class="small"></span>
            </div>
          </div>

          <div class="card card-secondary card-outline mb-3">
            <div class="card-header py-2"><h6 class="card-title mb-0"><i class="fas fa-calendar-check mr-1"></i>Jadwal Yang Sudah Terisi</h6></div>
            <div class="card-body p-0" style="max-height:180px;overflow-y:auto;">
              <table class="table table-sm table-striped mb-0">
                <thead><tr><th>Playlist</th><th>Mulai</th><th>Selesai</th><th>Jam Mulai</th><th>Jam Selesai</th></tr></thead>
                <tbody id="jadwalTerisiBody"></tbody>
              </table>
            </div>
          </div>

          <div class="card card-secondary card-outline mb-3">
            <div class="card-header py-2"><h6 class="card-title mb-0"><i class="fas fa-clock mr-1"></i>Pilih Jam Tayang</h6></div>
            <div class="card-body">
              <div class="row align-items-start">
                <div class="col-md-4">
                  <label class="small d-block mb-1">Jam Mulai</label>
                  <div class="drum-picker-wrap">
                    <div class="drum-col-wrap"><div class="drum-highlight"></div><div class="drum-col" id="drumStartH"></div></div>
                    <span class="drum-sep">:</span>
                    <div class="drum-col-wrap"><div class="drum-highlight"></div><div class="drum-col" id="drumStartM"></div></div>
                  </div>
                </div>
                <div class="col-md-4">
                  <label class="small d-block mb-1">Jam Selesai</label>
                  <div class="drum-picker-wrap">
                    <div class="drum-col-wrap"><div class="drum-highlight"></div><div class="drum-col" id="drumEndH"></div></div>
                    <span class="drum-sep">:</span>
                    <div class="drum-col-wrap"><div class="drum-highlight"></div><div class="drum-col" id="drumEndM"></div></div>
                  </div>
                </div>
                <div class="col-md-4 d-flex align-items-center pt-2">
                  <div id="durasiTayangInfo" class="alert alert-secondary py-2 mb-0 w-100" style="font-size:13px;">
                    <i class="fas fa-stopwatch mr-1"></i> <span>Pilih jam mulai &amp; selesai</span>
                  </div>
                </div>
              </div>
              <small class="text-muted mt-2 d-block"><i class="fas fa-info-circle mr-1"></i>Klik tanggal di kalender untuk memilih rentang tayang, lalu atur jam di sini.</small>
            </div>
          </div>

          <div id="rangeSelectedAlert" class="alert alert-info py-2 mb-3" style="display:none;">
            <i class="fas fa-calendar-check mr-1"></i>
            <strong>Jadwal dipilih:</strong> <span id="rangeSelectedText">-</span>
          </div>

          <div class="d-flex justify-content-end mb-4">
            <button type="button" class="btn btn-primary btn-lg" onclick="handleStep2Continue()">
              Lanjutkan <i class="fas fa-arrow-right ml-1"></i>
            </button>
          </div>
        </div>

        {{-- ===== STEP 3: DETAIL PLAYLIST ===== --}}
        <div id="stepForm" class="step-section" style="display:none;">

          <div id="s3TayangInfo" class="alert alert-secondary py-2 mb-3">
            <i class="fas fa-calendar-check mr-1"></i> Jadwal: <strong id="s3TayangText">-</strong>
          </div>

          {{-- Info Playlist --}}
          <div class="card card-secondary card-outline mb-3">
            <div class="card-header py-2"><h6 class="card-title mb-0"><i class="fas fa-film mr-1"></i> Info Playlist</h6></div>
            <div class="card-body pb-0">
              <div class="row">
                <div class="col-md-12"><div class="form-group"><label>Nama Playlist <span class="text-danger">*</span></label><input type="text" id="playlist_name" class="form-control" placeholder="Masukkan nama playlist"></div></div>
              </div>
            </div>
          </div>

          {{-- Info Device --}}
          <div class="card card-secondary card-outline mb-3">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
              <h6 class="card-title mb-0"><i class="fas fa-tv mr-1"></i> Info Device Terpilih</h6>
              <button type="button" class="btn btn-link btn-sm p-0" onclick="gotoStep(2)"><i class="fas fa-exchange-alt mr-1"></i>Ganti Lokasi</button>
            </div>
            <div class="card-body pb-0">
              <div class="row">
                <div class="col-md-3"><div class="form-group"><label>Nama Lokasi</label><input type="text" id="device_name_display" class="form-control" readonly></div></div>
                <div class="col-md-2"><div class="form-group"><label>Provinsi</label><input type="text" id="provinsi" class="form-control" readonly></div></div>
                <div class="col-md-2"><div class="form-group"><label>Kota / Kabupaten</label><input type="text" id="kota" class="form-control" readonly></div></div>
                <div class="col-md-2"><div class="form-group"><label>Ukuran (H x W)</label><input type="text" id="ukuran" class="form-control" readonly></div></div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Contract Value</label>
                    <input type="hidden" id="contract_value">
                    <div class="contract-display" id="contract_value_display">Rp 0</div>
                    <small class="text-muted">Total estimasi berdasarkan slot waktu</small>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-3"><div class="form-group"><label>Start Contract</label><input type="date" id="start_contract" class="form-control remaining-field" readonly></div></div>
                <div class="col-md-3"><div class="form-group"><label>End Contract</label><input type="date" id="end_contract" class="form-control remaining-field" readonly></div></div>
              </div>
            </div>
          </div>

          {{-- Upload Konten --}}
          <div class="card card-secondary card-outline mb-3">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
              <h6 class="card-title mb-0"><i class="fas fa-photo-video mr-1"></i> Upload Konten</h6>
              <div class="d-flex align-items-center">
                <small class="text-warning mr-3"><i class="fas fa-info-circle"></i> Video &rarr; H.264 MP4</small>
                <button type="button" class="btn btn-sm btn-outline-primary" id="btnTambahKonten"><i class="fas fa-plus mr-1"></i>Tambah Konten</button>
              </div>
            </div>
            <div class="card-body" id="contentRows" style="display:none;"></div>
          </div>

          <div class="d-flex justify-content-between mb-5">
            <button type="button" class="btn btn-secondary" onclick="gotoStep(2)"><i class="fas fa-arrow-left mr-1"></i>Kembali</button>
            <button type="button" class="btn btn-success btn-lg" id="btnSimpan"><i class="fas fa-save mr-1"></i>Simpan Playlist</button>
          </div>
        </div>

      </div>
    </section>
    
    {{-- MODAL PREVIEW PLAYLIST --}}
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
                  <tr><th class="pl-3">Start Contract</th><td>: <span id="prev_start"></span></td></tr>
                  <tr><th class="pl-3">End Contract</th><td>: <span id="prev_end"></span></td></tr>
                  <tr><th class="pl-3">Jam Tayang</th><td>: <span id="prev_jam"></span></td></tr>
                  <tr><th class="pl-3">Contract Value</th><td>: <span id="prev_value"></span></td></tr>
                  <tr><th class="pl-3">Sisa Kontrak</th><td>: <span id="prev_remaining_badge"></span></td></tr>
                  <tr><th class="pl-3">Status</th><td>: <span id="prev_status"></span></td></tr>
                </table>
              </div>
            </div>
            <div class="card card-secondary card-outline mb-0">
              <div class="card-header py-2 d-flex align-items-center">
                <h6 class="card-title mb-0"><i class="fas fa-photo-video mr-1"></i> Konten Diupload</h6>
                <span id="prev_konten_count" class="badge badge-secondary ml-2"></span>
              </div>
              <div class="card-body" id="prev_konten_container">
                <p class="text-muted text-center mb-0"><i class="fas fa-spinner fa-spin mr-1"></i>Memuat konten...</p>
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
var rowCount = 0;
var selectedDevice = null;
var deviceBookings = [];
var drumReady = false;
var rangeStart = null;
var rangeEnd   = null;
var calYear, calMonth;
var slotDurasiDetik = 0; 
var calColorPalette = ['#4db6ac','#64b5f6','#ba68c8','#ffb74d','#81c784','#f06292','#4dd0e1','#aed581','#ff8a65','#90a4ae'];

$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

function showToast(msg, type, dur) {
  type = type || 'success'; dur = dur || 4000;
  var icons = { success:'<i class="fas fa-check-circle"></i>', error:'<i class="fas fa-times-circle"></i>', warning:'<i class="fas fa-exclamation-triangle"></i>' };
  var t = document.createElement('div');
  t.className = 'toast-notif ' + type;
  t.innerHTML = (icons[type]||'') + ' <span>' + msg + '</span>';
  document.getElementById('toast-container').appendChild(t);
  setTimeout(function(){ t.classList.add('hide'); setTimeout(function(){ t.remove(); }, 400); }, dur);
}

function formatTanggalID(dateStr) {
  if (!dateStr) return '-';
  var datePart = String(dateStr).split(' ')[0].split('T')[0];
  var p = datePart.split('-').map(Number);
  if (p.length < 3 || isNaN(p[0]) || isNaN(p[1]) || isNaN(p[2])) return dateStr;
  var bln = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
  return String(p[2]).padStart(2,'0') + ' ' + bln[p[1]-1] + ' ' + p[0];
}

function toDateStr(d) {
  return d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0');
}

function colorForName(name) {
  var h = 0;
  for (var i = 0; i < name.length; i++) h = (h*31 + name.charCodeAt(i)) >>> 0;
  return calColorPalette[h % calColorPalette.length];
}

function formatRupiah(angka) {
  return 'Rp ' + Math.round(angka).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

// ================================================================
// RUMUS BARU: 50.000 (Disesuaikan dengan permintaan)
// ================================================================
function hitungHargaRow(slotDtk, durasiKontenDtk) {
  if (!slotDtk) return 0;
  var durFix = durasiKontenDtk > 0 ? durasiKontenDtk : 15;
  
  // 1. Total durasi konten ÷ 15 detik = blok
  var blok = Math.ceil(durFix / 15);
  
  // 2. Blok × harga per 15 detik (50.000) = hasil
  var hargaSatuTayang = blok * 50000;
  
  // 3. Hasil × total pengulangan konten = harga yang harus dibayar
  var jumlahPutar = Math.floor(slotDtk / durFix);
  
  return hargaSatuTayang * jumlahPutar;
}

function hitungSlotDurasi() {
  var startTime = $('#s2_start_time').val() || '00:00';
  var endTime   = $('#s2_end_time').val()   || '00:00';
  var sp = startTime.split(':').map(Number);
  var ep = endTime.split(':').map(Number);
  var sMin = sp[0]*60 + sp[1];
  var eMin = ep[0]*60 + ep[1];
  var diffMin = eMin - sMin;
  if (diffMin <= 0) diffMin += 1440;

  var jumlahHari = 1;
  if (rangeStart && rangeEnd) {
    var d1 = new Date(rangeStart), d2 = new Date(rangeEnd);
    jumlahHari = Math.max(1, Math.round((d2 - d1) / 86400000) + 1);
  }

  slotDurasiDetik = diffMin * 60 * jumlahHari;
  return slotDurasiDetik;
}

function updateContractValue() {
  hitungSlotDurasi();
  var grandTotal = 0;
  
  document.querySelectorAll('.content-row').forEach(function(row) {
    var idx = row.id.replace('contentRow_', '');
    var durMnt = parseInt(row.querySelector('input[name="durasi_konten_menit[]"]').value) || 0;
    var durDtk = parseInt(row.querySelector('input[name="durasi_konten_detik[]"]').value) || 0;
    var durKonten = (durMnt * 60) + durDtk;
    
    // Panggil rumus baru 50rb
    grandTotal += hitungHargaRow(slotDurasiDetik, durKonten);
    
    updateInvoiceRow(idx);
  });

  $('#contract_value').val(Math.round(grandTotal));
  $('#contract_value_display').text(formatRupiah(grandTotal));
}

function updateInvoiceRow(idx) {
  var row = document.getElementById('contentRow_' + idx);
  if (!row) return;

  var durMntEl  = row.querySelector('input[name="durasi_konten_menit[]"]');
  var durDtkEl  = row.querySelector('input[name="durasi_konten_detik[]"]');
  var tayangEl  = document.getElementById('durasi_tayang_' + idx);
  var jumlahEl  = document.getElementById('jumlah_putar_' + idx);
  var hargaEl   = document.getElementById('harga_konten_' + idx);

  var durMnt = parseInt(durMntEl ? durMntEl.value : 0) || 0;
  var durDtk = parseInt(durDtkEl ? durDtkEl.value : 0) || 0;
  var durKontenDtk = durMnt * 60 + durDtk;

  var slotDtk = slotDurasiDetik || hitungSlotDurasi();
  var jamSlot = Math.floor(slotDtk / 3600);
  var menitSlot = Math.floor((slotDtk % 3600) / 60);
  if (tayangEl) tayangEl.textContent = jamSlot + ' jam ' + menitSlot + ' menit (' + slotDtk + ' detik)';

  var durFix = durKontenDtk > 0 ? durKontenDtk : 15;
  var jumlahPutar = Math.floor(slotDtk / durFix);
  if (jumlahEl) {
    if (durKontenDtk > 0) { jumlahEl.textContent = jumlahPutar + ' kali'; } 
    else { jumlahEl.textContent = '-'; }
  }

  // Panggil rumus baru 50rb
  var harga = hitungHargaRow(slotDtk, durKontenDtk);
  if (hargaEl) hargaEl.innerHTML = '<strong>' + formatRupiah(harga) + '</strong>';
}

function gotoStep(n) {
  $('#stepLokasi, #stepJadwal, #stepForm').hide();
  if (n === 1) $('#stepLokasi').show();
  if (n === 2) { $('#stepJadwal').show(); setTimeout(initAllDrums, 60); }
  if (n === 3) {
    $('#stepForm').show();
    if (rangeStart) $('#start_contract').val(rangeStart);
    if (rangeEnd)   $('#end_contract').val(rangeEnd);
    hitungSlotDurasi();
    var jamMulai   = $('#s2_start_time').val() || '00:00';
    var jamSelesai = $('#s2_end_time').val()   || '00:00';
    var tgl = (rangeStart && rangeEnd) ? formatTanggalID(rangeStart) + ' — ' + formatTanggalID(rangeEnd) + ', Pukul ' + jamMulai + ' – ' + jamSelesai : 'Belum dipilih';
    $('#s3TayangText').text(tgl);
    updateContractValue();
  }
  $('#wizardSteps .nav-link').removeClass('active done').addClass('disabled');
  for (var i = 1; i < n; i++) $('#wizardSteps .nav-link[data-step="' + i + '"]').removeClass('disabled').addClass('done');
  $('#wizardSteps .nav-link[data-step="' + n + '"]').removeClass('disabled').addClass('active');
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function handleStep2Continue() {
  if (!rangeStart || !rangeEnd) { showToast('Pilih rentang tanggal tayang di kalender dulu!', 'error'); return; }
  gotoStep(3);
}

function loadLokasi() {
  document.getElementById('lokasiContainer').innerHTML = '<div class="col-12 text-center text-muted py-5"><i class="fas fa-spinner fa-spin fa-2x mb-2 d-block"></i>Memuat lokasi...</div>';
  fetch('/get-locations')
    .then(function(r){ return r.json(); })
    .then(function(list){
      if (!Array.isArray(list) || !list.length) {
        document.getElementById('lokasiContainer').innerHTML = '<div class="col-12 text-center text-muted py-5"><i class="fas fa-tv fa-2x mb-2 d-block"></i>Belum ada lokasi tersedia.</div>';
        return;
      }
      var html = '';
      list.forEach(function(d){
        var ukuran = (d.r_width && d.r_height) ? (d.r_height + ' × ' + d.r_width) : '';
        html += '<div class="col-md-4 col-lg-3 mb-4">'
          + '<div class="card lokasi-card h-100 p-0"'
          + ' data-id="' + d.id + '"'
          + ' data-name="' + (d.device_name||'').replace(/"/g,'&quot;') + '"'
          + ' data-provinsi="' + (d.provinsi||'').replace(/"/g,'&quot;') + '"'
          + ' data-kota="' + (d.kota||'').replace(/"/g,'&quot;') + '"'
          + ' data-width="' + (d.r_width||'') + '" data-height="' + (d.r_height||'') + '">'
          + '<div class="lokasi-thumb">'
          + (d.thumbnail ? '<img src="' + d.thumbnail + '" style="width:100%;height:100%;object-fit:cover;">' : '<i class="fas fa-tv fa-3x text-muted"></i>')
          + '</div><div class="card-body">'
          + '<h6 class="mb-1">' + d.device_name + '</h6>'
          + '<small class="text-muted d-block">' + (d.kota||'-') + ', ' + (d.provinsi||'-') + '</small>'
          + (ukuran ? '<span class="text-info font-weight-bold mt-1 d-block">' + ukuran + '</span>' : '')
          + '</div></div></div>';
      });
      document.getElementById('lokasiContainer').innerHTML = html;
    })
    .catch(function(err){
      document.getElementById('lokasiContainer').innerHTML = '<div class="col-12 text-center text-danger py-5">Gagal memuat lokasi: ' + err.message + '</div>';
    });
}

$(document).on('click', '.lokasi-card', function(){
  $('.lokasi-card').removeClass('selected');
  $(this).addClass('selected');
  var d = this.dataset;
  selectedDevice = d;
  $('#selected_device_id').val(d.id);
  $('#provinsi').val(d.provinsi);
  $('#kota').val(d.kota);
  $('#location').val((d.provinsi||'') + ', ' + (d.kota||''));
  $('#ukuran').val(d.width && d.height ? d.height + ' x ' + d.width : '');
  $('#device_name_display').val(d.name);
  $('#deviceInfoName').text(d.name);
  $('#deviceInfoLoc').text((d.kota||'-') + ', ' + (d.provinsi||'-'));
  
  $('#unpaidDeviceName').text(d.name);
  $('#unpaidPlaylistBody').html('<tr><td colspan="9" class="text-center py-3"><i class="fas fa-spinner fa-spin mr-1"></i>Memuat playlist...</td></tr>');
  $('#playlistUnpaidSection').show();
  
  fetch('/get-playlists-by-device/' + d.id)
    .then(function(r){ return r.json(); })
    .then(function(res){
      if (res.status !== 'success') throw new Error(res.message||'Gagal ambil playlist');
      renderPlaylistUnpaid(res.playlists || []);
    })
    .catch(function(err){
      $('#unpaidPlaylistBody').html('<tr><td colspan="9" class="text-center text-danger py-3">Gagal memuat: ' + err.message + '</td></tr>');
    });
});

$(document).on('click', '#btnLanjutStep2', function(){
  if (!selectedDevice) { showToast('Pilih lokasi/device terlebih dahulu!', 'error'); return; }
  rangeStart = null; rangeEnd = null;
  updateRangeAlert();
  document.getElementById('calendarContainer').innerHTML = '<p class="text-center text-muted py-4"><i class="fas fa-spinner fa-spin mr-1"></i>Memuat kalender...</p>';
  $('#jadwalTerisiBody').html('<tr><td colspan="5" class="text-center py-2"><i class="fas fa-spinner fa-spin mr-1"></i>Memuat...</td></tr>');
  fetch('/get-device-schedule/' + selectedDevice.id)
    .then(function(r){ return r.json(); })
    .then(function(res){
      if (res.status !== 'success') throw new Error(res.message||'Gagal ambil jadwal');
      deviceBookings = res.bookings || [];
      renderJadwalTable(deviceBookings);
      var now = new Date();
      calYear = now.getFullYear(); calMonth = now.getMonth();
      renderCalendar();
      gotoStep(2);
    })
    .catch(function(err){ showToast('Gagal ambil jadwal: ' + err.message, 'error'); });
});

function renderPlaylistUnpaid(playlists) {
  if (!playlists.length) {
    $('#unpaidPlaylistBody').html('<tr><td colspan="9" class="text-center text-muted py-3"><i class="fas fa-inbox fa-2x mb-2 d-block"></i>Tidak ada playlist unpaid di device ini.</td></tr>');
    $('#unpaidCount').text('0 unpaid');
    return;
  }
  
  $('#unpaidCount').text(playlists.length + ' unpaid');
  var html = '';
  playlists.forEach(function(p, i){
    var start = formatTanggalID(p.start_contract);
    var end   = formatTanggalID(p.end_contract);
    var jamTayang = (p.start_time||'00:00') + ' – ' + (p.end_time||'00:00');
    var val = parseFloat(p.contract_value||0);
    
    var endDate = new Date(p.end_contract);
    var now = new Date();
    var diffDays = Math.floor((endDate - now) / 86400000);
    var sisaClass = diffDays >= 30 ? 'aman' : diffDays >= 11 ? 'warning' : 'danger';
    var sisaLabel = diffDays > 0 ? diffDays : 0;
    
    var rawStatus = String(p.status||'unpaid').toLowerCase();
    // PERBAIKAN: Baca angka '1' sebagai paid juga di Unpaid list
    var status = (['paid','approved','1'].indexOf(rawStatus) !== -1) ? 'paid' : 'unpaid';
    var statusClass = status === 'paid' ? 'status-paid' : 'status-unpaid';
    
    html += '<tr>'
      + '<td>' + (i+1) + '</td>'
      + '<td><strong>' + (p.playlist_name||'-') + '</strong></td>'
      + '<td>' + start + '</td>'
      + '<td>' + end + '</td>'
      + '<td>' + jamTayang + '</td>'
      + '<td class="text-center"><div class="sisa-mini ' + sisaClass + '"><div style="font-size:16px">' + sisaLabel + '</div><div style="font-size:9px">hari</div></div></td>'
      + '<td class="text-center"><span class="' + statusClass + '">' + status.toUpperCase() + '</span></td>'
      + '<td>Rp ' + Math.round(val).toLocaleString('id-ID') + '</td>'
      + '<td class="text-center">'
      + '<button class="btn btn-xs btn-info mr-1 btn-preview-unpaid" data-id="' + (p.id||'') + '" data-name="' + (p.playlist_name||'').replace(/"/g,'&quot;') + '" data-start="' + (p.start_contract||'') + '" data-end="' + (p.end_contract||'') + '" data-start-time="' + (p.start_time||'00:00') + '" data-end-time="' + (p.end_time||'00:00') + '" data-value="' + (p.contract_value||0) + '" data-status="' + status + '"><i class="fas fa-eye"></i></button>'
      + '<a href="/vendor/invoice/' + (p.id||'') + '" target="_blank" class="btn btn-xs btn-success"><i class="fas fa-file-invoice-dollar"></i></a>'
      + '</td></tr>';
  });
  $('#unpaidPlaylistBody').html(html);
}

function renderJadwalTable(bookings) {
  if (!bookings.length) { $('#jadwalTerisiBody').html('<tr><td colspan="5" class="text-center text-muted py-2">Belum ada jadwal terisi.</td></tr>'); return; }
  var html = '';
  bookings.forEach(function(b){
    html += '<tr><td>' + b.playlist_name + '</td><td>' + formatTanggalID(b.start_contract) + '</td><td>' + formatTanggalID(b.end_contract) + '</td><td>' + b.start_time + '</td><td>' + b.end_time + '</td></tr>';
  });
  $('#jadwalTerisiBody').html(html);
}

function renderCalendar() {
  var now = new Date();
  if (calMonth === undefined || calMonth === null) { calMonth = now.getMonth(); calYear = now.getFullYear(); }
  var firstDay = new Date(calYear, calMonth, 1);
  var startOffset = firstDay.getDay();
  var daysInMonth = new Date(calYear, calMonth+1, 0).getDate();
  var totalWeeks = Math.ceil((startOffset + daysInMonth) / 7);
  var monthNames = ['JANUARI','FEBRUARI','MARET','APRIL','MEI','JUNI','JULI','AGUSTUS','SEPTEMBER','OKTOBER','NOVEMBER','DESEMBER'];
  var todayStr = toDateStr(now);

  var html = '<div class="d-flex justify-content-between align-items-center mb-2">'
    + '<h6 class="mb-0">' + monthNames[calMonth] + ' ' + calYear + '</h6>'
    + '<div><button type="button" class="btn btn-sm btn-primary mr-1" id="btnCalToday">Hari Ini</button>'
    + '<button type="button" class="btn btn-sm btn-outline-secondary" id="btnCalPrev"><i class="fas fa-chevron-left"></i></button>'
    + '<button type="button" class="btn btn-sm btn-outline-secondary" id="btnCalNext"><i class="fas fa-chevron-right"></i></button></div></div>'
    + '<table class="cal-grid-table"><thead><tr>';
  ['Min','Sen','Sel','Rab','Kam','Jum','Sab'].forEach(function(d){ html += '<th>' + d + '</th>'; });
  html += '</tr></thead><tbody>';

  var dc = 1 - startOffset;
  for (var w = 0; w < totalWeeks; w++) {
    html += '<tr>';
    for (var dow = 0; dow < 7; dow++) {
      var dt = new Date(calYear, calMonth, dc); dc++;
      var isCur = dt.getMonth() === calMonth;
      var cs = toDateStr(dt);
      var isPast = cs < todayStr;
      var cls = '';
      if (!isCur) cls += ' other-month';
      else if (isPast) cls += ' past-date';
      else if (cs === todayStr) cls += ' today-cell';
      if (rangeStart && rangeEnd && cs >= rangeStart && cs <= rangeEnd && isCur && !isPast) {
        if (cs === rangeStart && cs === rangeEnd) cls += ' range-start range-end';
        else if (cs === rangeStart) cls += ' range-start';
        else if (cs === rangeEnd) cls += ' range-end';
        else cls += ' in-range';
      }
      html += '<td class="' + cls + '" data-date="' + cs + '"><div class="day-num">' + dt.getDate() + '</div>';
      if (isCur && !isPast) {
        deviceBookings.forEach(function(b){
          if (b.start_contract && b.end_contract) {
            var bs = b.start_contract.slice(0,10), be = b.end_contract.slice(0,10);
            if (cs >= bs && cs <= be) html += '<div class="booking-bar" style="background:' + colorForName(b.playlist_name) + ';" title="' + b.playlist_name + '">' + b.playlist_name + '</div>';
          }
        });
      }
      html += '</td>';
    }
    html += '</tr>';
  }
  html += '</tbody></table>';
  document.getElementById('calendarContainer').innerHTML = html;

  document.getElementById('btnCalToday').onclick = function(){ calMonth = now.getMonth(); calYear = now.getFullYear(); renderCalendar(); };
  document.getElementById('btnCalPrev').onclick  = function(){ calMonth--; if (calMonth<0){calMonth=11;calYear--;} renderCalendar(); };
  document.getElementById('btnCalNext').onclick  = function(){ calMonth++; if (calMonth>11){calMonth=0;calYear++;} renderCalendar(); };

  $('#calendarContainer').off('click', '.cal-grid-table td');
  $('#calendarContainer').on('click', '.cal-grid-table td', function(){
    if ($(this).hasClass('other-month') || $(this).hasClass('past-date')) return;
    var clicked = $(this).data('date');
    if (!clicked) return;
    if (!rangeStart) {
      rangeStart = clicked; rangeEnd = clicked;
    } else if (!rangeEnd || rangeStart === rangeEnd) {
      if (clicked < rangeStart) { rangeEnd = rangeStart; rangeStart = clicked; }
      else { rangeEnd = clicked; }
    } else {
      rangeStart = clicked; rangeEnd = clicked;
    }
    $('#s2_start_date').val(rangeStart);
    $('#s2_end_date').val(rangeEnd);
    updateRangeAlert();
    renderCalendar();
    checkBookingOnDate(clicked);
  });
}

function updateRangeAlert() {
  if (rangeStart && rangeEnd) {
    var jm = $('#s2_start_time').val() || '00:00';
    var js = $('#s2_end_time').val() || '00:00';
    $('#rangeSelectedText').text(formatTanggalID(rangeStart) + ' — ' + formatTanggalID(rangeEnd) + ', Pukul ' + jm + ' – ' + js);
    $('#rangeSelectedAlert').show();
  } else { $('#rangeSelectedAlert').hide(); }
}

function checkBookingOnDate(dateStr) {
  var found = [];
  deviceBookings.forEach(function(b){
    if (b.start_contract && b.end_contract) {
      var bs = b.start_contract.slice(0,10), be = b.end_contract.slice(0,10);
      if (dateStr >= bs && dateStr <= be) found.push(b);
    }
  });
  if (found.length) {
    var txt = found.map(function(b){ return b.playlist_name + ' (' + b.start_time + '–' + b.end_time + ')'; }).join(', ');
    $('#bookingInfoText').html('<strong>Tanggal ' + formatTanggalID(dateStr) + ':</strong> ' + txt);
    $('#bookingInfoPanel').show();
  } else { $('#bookingInfoPanel').hide(); }
}

function initDrumCol(colId, min, max) {
  var col = document.getElementById(colId); if (!col) return;
  col.innerHTML = '';
  col.insertAdjacentHTML('beforeend','<div style="height:40px;flex-shrink:0;"></div>');
  for (var v = min; v <= max; v++) {
    var s = String(v).padStart(2,'0');
    var item = document.createElement('div');
    item.className = 'drum-item'; item.dataset.val = s; item.textContent = s;
    col.appendChild(item);
  }
  col.insertAdjacentHTML('beforeend','<div style="height:40px;flex-shrink:0;"></div>');
  col.scrollTop = 0;
  col.addEventListener('scroll', function(){
    var idx = Math.round(this.scrollTop/40);
    this.querySelectorAll('.drum-item').forEach(function(it,i){
      it.style.color = i===idx ? '#ffffff' : 'rgba(255,255,255,.28)';
      it.style.fontSize = i===idx ? '18px' : '15px';
    });
    updateDrumHidden();
    updateRangeAlert();
  }, { passive: true });
  var first = col.querySelector('.drum-item');
  if (first) { first.style.color = '#ffffff'; first.style.fontSize = '18px'; }
}

function getDrumVal(colId) {
  var col = document.getElementById(colId); if (!col) return '00';
  var idx = Math.round(col.scrollTop/40);
  var items = col.querySelectorAll('.drum-item');
  return items[idx] ? items[idx].dataset.val : '00';
}

function updateDrumHidden() {
  $('#s2_start_time').val(getDrumVal('drumStartH') + ':' + getDrumVal('drumStartM'));
  $('#s2_end_time').val(getDrumVal('drumEndH') + ':' + getDrumVal('drumEndM'));
}

function initAllDrums() {
  if (drumReady) return; drumReady = true;
  initDrumCol('drumStartH',0,23); initDrumCol('drumStartM',0,59);
  initDrumCol('drumEndH',0,23);   initDrumCol('drumEndM',0,59);
}

function removeRow(btn) {
  btn.closest('.content-row').remove();
  updateContractValue();
}

function previewFilePicker(input, idx) {
  var pv = document.getElementById('filePreview_' + idx);
  var ne = document.getElementById('fileName_' + idx);
  var te = document.getElementById('fileType_' + idx);
  if (input.files && input.files[0]) {
    var f = input.files[0];
    ne.textContent = f.name;
    te.innerHTML = f.type.startsWith('video/')
      ? '<span class="badge-video"><i class="fas fa-film"></i> video→H.264</span>'
      : f.type.startsWith('image/')
        ? '<span class="badge-image"><i class="fas fa-image"></i> gambar</span>'
        : '';
    pv.style.display = 'block';
  } else { pv.style.display = 'none'; }
}

$(document).on('click', '.btn-preview-file-picker', function(){
  var idx = $(this).data('idx');
  var fi = document.querySelector('#contentRow_' + idx + ' input[type="file"]');
  var cont = document.getElementById('filePreviewContent');
  cont.innerHTML = '';
  if (!fi || !fi.files[0]) { cont.innerHTML = '<p class="text-muted">Belum ada file dipilih.</p>'; }
  else {
    var file = fi.files[0], url = URL.createObjectURL(file);
    cont.innerHTML = file.type.startsWith('image/')
      ? '<img src="' + url + '" class="img-fluid rounded">'
      : file.type.startsWith('video/')
        ? '<video controls class="w-100" style="max-height:400px"><source src="' + url + '" type="' + file.type + '"></video>'
        : '<p class="text-muted">Preview tidak tersedia.</p>';
  }
  $('#filePreviewModal').modal('show');
});

// Trigger updateInvoiceRow saat durasi berubah
$(document).on('input', 'input[name="durasi_konten_menit[]"], input[name="durasi_konten_detik[]"]', function(){
  var row = $(this).closest('.content-row');
  if (row.length) updateInvoiceRow(row[0].id.replace('contentRow_',''));
  updateContractValue();
});

$(document).ready(function() {
  loadLokasi();

  $('#btnTambahKonten').click(function() {
    var idx = rowCount;
    var row = '<div class="content-row" id="contentRow_' + idx + '">'
      + '<button type="button" class="btn btn-xs btn-danger btn-remove-row" onclick="removeRow(this)"><i class="fas fa-times"></i></button>'
      + '<div class="row mb-3">'
      + '<div class="col-md-12">'
      + '<label class="small d-block mb-1">File <span class="text-danger">*</span></label>'
      + '<div class="custom-file">'
      + '<input type="file" name="file[]" class="custom-file-input" accept="video/*,image/*" onchange="previewFilePicker(this,' + idx + ');$(this).next().text(this.files[0]?this.files[0].name:\'Pilih file\')">'
      + '<label class="custom-file-label" style="font-size:12px;">Pilih file</label>'
      + '</div>'
      + '<div id="filePreview_' + idx + '" style="display:none;margin-top:4px;">'
      + '<small class="text-success"><i class="fas fa-check-circle"></i> <span id="fileName_' + idx + '"></span> <span id="fileType_' + idx + '" class="ml-1"></span></small>'
      + '</div>'
      + '<div class="mt-2 text-right"><button type="button" class="btn btn-xs btn-outline-info btn-preview-file-picker" data-idx="' + idx + '"><i class="fas fa-eye mr-1"></i>Preview</button></div>'
      + '</div></div>'
      + '<div class="row mb-2">'
      + '<div class="col-md-4">'
      + '<label class="small d-block mb-1">Total Durasi Video Anda <span class="text-danger">*</span></label>'
      + '<div class="d-flex align-items-center">'
      + '<input type="number" min="0" name="durasi_konten_menit[]" class="form-control form-control-sm mr-1" placeholder="Menit" style="width:80px;">'
      + '<span class="mr-2">mnt</span>'
      + '<input type="number" min="0" max="59" name="durasi_konten_detik[]" class="form-control form-control-sm" placeholder="Detik" style="width:80px;">'
      + '<span class="ml-2">dtk</span>'
      + '</div>'
      + '</div></div>'
      + '<div class="row">'
      + '<div class="col-md-4"><div class="info-konten-box"><div class="info-label">Total Durasi Tayang sesuai kontrak</div><div class="info-val" id="durasi_tayang_' + idx + '">-</div></div></div>'
      + '<div class="col-md-4"><div class="info-konten-box"><div class="info-label">Jumlah Putar (pengulangan video anda)</div><div class="info-val" id="jumlah_putar_' + idx + '">-</div></div></div>'
      + '<div class="col-md-4"><div class="info-konten-box"><div class="info-label">Estimasi Harga Konten Ini</div><div class="info-val" id="harga_konten_' + idx + '">-</div></div></div>'
      + '</div>'
      + '</div>';

    document.getElementById('contentRows').style.display = 'block';
    document.getElementById('contentRows').insertAdjacentHTML('beforeend', row);
    updateInvoiceRow(idx);
    rowCount++;
  });

  $('#btnSimpan').click(function() {
    var btn = $(this);
    var pName = $('#playlist_name').val().trim();
    var devId = $('#selected_device_id').val();
    var sc = $('#start_contract').val();
    var ec = $('#end_contract').val();

    if (!pName) { showToast('Nama playlist wajib diisi!', 'error'); return; }
    if (!devId) { showToast('Lokasi/device wajib dipilih!', 'error'); return; }
    if (!sc) { showToast('Start contract wajib diisi!', 'error'); return; }
    if (!ec) { showToast('End contract wajib diisi!', 'error'); return; }

    var adaFile = false;
    $('input[name="file[]"]').each(function(){ if (this.files && this.files[0]) { adaFile = true; return false; } });
    if (!adaFile) { showToast('Minimal upload 1 file konten!', 'error'); return; }

    var durasiKosong = false;
    $('.content-row').each(function(){
      var mnt = parseInt($(this).find('input[name="durasi_konten_menit[]"]').val()) || 0;
      var dtk = parseInt($(this).find('input[name="durasi_konten_detik[]"]').val()) || 0;
      if (mnt === 0 && dtk === 0) durasiKosong = true;
    });
    if (durasiKosong) { showToast('Durasi konten asli wajib diisi di setiap baris!', 'error'); return; }

    updateContractValue();

    var fd = new FormData();
    fd.append('_token', $('meta[name="csrf-token"]').attr('content'));
    fd.append('playlist_name', pName);
    fd.append('device_id', devId);
    fd.append('start_contract', sc);
    fd.append('end_contract', ec);
    fd.append('contract_value', $('#contract_value').val() || 0);
    fd.append('location', $('#location').val() || '');
    fd.append('cust_id', $('#cust_id').val() || '1');
    fd.append('start_time', $('#s2_start_time').val() || '00:00');
    fd.append('end_time', $('#s2_end_time').val() || '00:00');

    $('.content-row').each(function(i){
      var row = $(this);
      var fi = row.find('input[name="file[]"]')[0];
      if (fi && fi.files && fi.files[0]) fd.append('file[]', fi.files[0]);
      var durMnt = parseInt(row.find('input[name="durasi_konten_menit[]"]').val()) || 0;
      var durDtk = parseInt(row.find('input[name="durasi_konten_detik[]"]').val()) || 0;
      fd.append('content_duration_seconds[' + i + ']', durMnt * 60 + durDtk);
      fd.append('price_unit[' + i + ']', 15);
    });

    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Menyimpan...');
    $.ajax({
      url: '{{ route("vendor.playlist.store") }}',
      method: 'POST',
      data: fd,
      contentType: false,
      processData: false,
      timeout: 600000,
      success: function(res) {
        showToast(res.message || 'Playlist berhasil disimpan!', 'success');
        setTimeout(function(){ window.location.href = '{{ route("vendor.playlist") }}'; }, 1600);
      },
      error: function(err) {
        var r = err.responseJSON;
        var msg = 'Error ' + err.status;
        if (err.status === 419) msg = 'Session expired, refresh halaman!';
        else if (r && r.errors) msg = Object.values(r.errors)[0][0];
        else if (r && r.message) msg = r.message;
        showToast(msg, 'error');
        btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i>Simpan Playlist');
      }
    });
  });
});

$(document).on('click', '.btn-preview-unpaid', function() {
  var btn = this;
  var id        = btn.dataset.id;
  var name      = btn.dataset.name;
  var start     = btn.dataset.start;
  var end       = btn.dataset.end;
  var startTime = btn.dataset.startTime || '00:00';
  var endTime   = btn.dataset.endTime   || '00:00';
  var value     = btn.dataset.value     || '0';
  
  // PERBAIKAN: Baca angka '1' sebagai paid
  var rawStatus = String(btn.dataset.status || 'unpaid').toLowerCase();
  var status = (['paid','approved','1'].indexOf(rawStatus) !== -1) ? 'paid' : 'unpaid';

  var endDate  = new Date(end.split(' ')[0]);
  var now      = new Date();
  var diffDays = Math.floor((endDate - now) / 86400000);
  var sisaClass = diffDays >= 30 ? 'aman' : diffDays >= 11 ? 'warning' : 'danger';
  var sisaLabel = (diffDays > 0 ? diffDays : 0) + ' hari';

  document.getElementById('prev_name').textContent  = name || '-';
  document.getElementById('prev_start').textContent = formatTanggalID(start);
  document.getElementById('prev_end').textContent   = formatTanggalID(end);
  document.getElementById('prev_jam').textContent   = startTime + ' \u2013 ' + endTime;
  document.getElementById('prev_value').textContent = 'Rp ' + Math.round(parseFloat(value)).toLocaleString('id-ID');
  document.getElementById('prev_remaining_badge').innerHTML = '<span class="sisa-mini ' + sisaClass + '" style="width:auto;border-radius:20px;padding:4px 14px;display:inline-flex;">' + sisaLabel + '</span>';
  document.getElementById('prev_status').innerHTML = '<span class="' + (status === 'paid' ? 'status-paid' : 'status-unpaid') + '">' + status.toUpperCase() + '</span>';

  document.getElementById('prev_konten_container').innerHTML = '<p class="text-muted text-center mb-0"><i class="fas fa-spinner fa-spin mr-1"></i>Memuat konten dari server...</p>';
  document.getElementById('prev_konten_count').textContent = '';
  $('#previewModal').modal('show');

  fetch('/api/playlist/' + id, { headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' } })
  .then(function(res) { if (!res.ok) throw new Error('HTTP ' + res.status); return res.json(); })
  .then(function(data) {
    if (data.status !== 'success') throw new Error(data.message || 'Gagal');
    tampilkanKontenPermintaan(data.data.resolved_items || []);
  })
  .catch(function(err) {
    document.getElementById('prev_konten_container').innerHTML = '<div class="alert alert-warning mb-0"><i class="fas fa-exclamation-triangle mr-1"></i>Gagal memuat konten: ' + err.message + '</div>';
  });
});

function tampilkanKontenPermintaan(kontenList) {
  var container = document.getElementById('prev_konten_container');
  var countEl   = document.getElementById('prev_konten_count');
  countEl.textContent = (kontenList.length || 0) + ' konten';

  if (!kontenList || !kontenList.length) {
    container.innerHTML = '<p class="text-muted text-center mb-0"><i class="fas fa-inbox mr-1"></i>Belum ada konten untuk playlist ini.</p>';
    return;
  }

  var html = '<div class="row">';
  kontenList.forEach(function(k) {
    var fileUrl  = k.file_url  || '';
    var fileType = k.file_type || (k.content ? k.content.type : '') || '';
    var fileName = (k.content  ? k.content.file : '') || '';
    var isVideo  = fileType === 'video' || /\.(mp4|mov|avi|mkv|webm)$/i.test(fileName);
    var isImage  = fileType === 'image' || /\.(jpg|jpeg|png|gif|webp)$/i.test(fileName);
    var startDate = k.start_date || '-';
    var endDate   = k.end_date   || '-';
    var startTime = k.start_time || '-';
    var endTime   = k.end_time   || '-';
    var days      = Array.isArray(k.days) ? (k.days.length ? k.days.join(', ') : '-') : (k.days || '-');
    var prioritas = (k.priority_mode == 1 || k.priority_mode === '1') ? 'Aktif' : 'Tidak aktif';

    var mediaHtml = '';
    if (fileUrl) {
      if (isVideo) mediaHtml = '<video controls class="w-100" style="max-height:200px;border-radius:6px;background:#000;"><source src="' + fileUrl + '"><p class="text-muted p-2">Browser tidak support video.</p></video>';
      else if (isImage) mediaHtml = '<img src="' + fileUrl + '" class="img-fluid rounded" style="max-height:200px;object-fit:contain;">';
      else mediaHtml = '<div class="text-muted text-center py-3"><i class="fas fa-file fa-2x mb-2 d-block"></i>' + fileName.split('/').pop() + '</div>';
    } else mediaHtml = '<div class="text-muted text-center py-3"><i class="fas fa-image fa-2x mb-2 d-block"></i>Tidak ada file</div>';

    var shortName = fileName.split('/').pop() || 'File';
    var typeBadge = isVideo ? '<span style="background:#6f42c1;color:#fff;font-size:10px;padding:2px 6px;border-radius:6px;">&#9654; video</span>' : isImage ? '<span style="background:#17a2b8;color:#fff;font-size:10px;padding:2px 6px;border-radius:6px;">&#128247; gambar</span>' : '';

    html += '<div class="col-md-6 mb-3"><div class="card card-secondary card-outline h-100"><div class="card-header py-2 d-flex justify-content-between align-items-center"><small class="text-truncate" style="max-width:75%;" title="' + shortName + '">' + shortName + '</small>' + typeBadge + '</div><div class="card-body p-2" style="background:rgba(0,0,0,.3);">' + mediaHtml + '</div><div class="card-footer p-2"><table class="table table-borderless table-sm mb-0" style="font-size:12px;"><tr><td class="py-0 text-muted" style="width:90px">Start Date</td><td class="py-0">' + startDate + '</td><td class="py-0 text-muted" style="width:70px">End Date</td><td class="py-0">' + endDate + '</td></tr><tr><td class="py-0 text-muted">Start Time</td><td class="py-0">' + startTime + '</td><td class="py-0 text-muted">End Time</td><td class="py-0">' + endTime + '</td></tr><tr><td class="py-0 text-muted">Hari Tayang</td><td class="py-0" colspan="3">' + days + '</td></tr><tr><td class="py-0 text-muted">Prioritas</td><td class="py-0" colspan="3">' + prioritas + '</td></tr></table></div></div></div>';
  });
  html += '</div>';
  container.innerHTML = html;
}
</script>
@endpush