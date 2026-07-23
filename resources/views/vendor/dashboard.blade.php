{{-- 1. Panggil kerangka utama --}}
@extends('layout.master')

{{-- 2. Kasih judul halamannya --}}
@section('title', 'Dashboard')

{{-- 3. JEPIT CSS lu pake @push('styles') biar disuntik ke <head> --}}
@push('styles')
<style>
    /* ── Sisa badge ── */
    .sisa-badge{display:inline-flex;align-items:center;justify-content:center;width:54px;height:54px;border-radius:50%;font-size:11px;font-weight:700;line-height:1.2;text-align:center;flex-direction:column}
    .sisa-badge.aman{background:#28a745;color:#fff}
    .sisa-badge.warning{background:#ffc107;color:#333}
    .sisa-badge.danger{background:#dc3545;color:#fff}
    .sisa-badge .hari-num{font-size:16px;font-weight:800;line-height:1}

    .status-paid{background:#28a745;color:#fff;font-size:11px;padding:3px 10px;border-radius:10px;font-weight:700;display:inline-block}
    .status-unpaid{background:#dc3545;color:#fff;font-size:11px;padding:3px 10px;border-radius:10px;font-weight:700;display:inline-block}

    .small-box{border-radius:10px;overflow:hidden;box-shadow:6 4px 10px rgba(0,0,0,.25);position:relative}
    .small-box .inner{padding:26px 17px}
    .small-box h3{font-size:37px;font-weight:900;margin:0;line-height:1}
    .small-box h5.rp-kecil{font-size:28px!important;}
    .small-box p{font-size:15px;margin-top:6px;margin-bottom:0}
    .small-box .icon{position:absolute;top:6px;right:15px;font-size:70px;opacity:.9;pointer-events:none}
    .small-box-footer{display:block;padding:8px;text-align:center;color:#fff;background:rgba(0,0,0,.15);text-decoration:none;font-size:13px}
    .small-box-footer:hover{color:#fff;background:rgba(0,0,0,.25)}

    .view-more-link{color:#17a2b8;font-size:13px;font-weight:600;text-decoration:none}
    .view-more-link:hover{text-decoration:underline;color:#117a8b}
</style>
@endpush

{{-- 4. JEPIT HTML KONTEN lu pake @section('content') biar disuntik ke <body> --}}
@section('content')
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
          
          $apiStatus = strtolower((string)($p['status'] ?? ''));
          $localStatus = $inv ? strtolower($inv->status) : 'unpaid';
          $isPaid = in_array($apiStatus, ['paid', 'approved', '1']) || in_array($localStatus, ['paid', 'approved', '1']);
          
          $p['local_status'] = $isPaid ? 'paid' : 'unpaid';
          $p['local_amount'] = $inv ? $inv->amount : ($p['contract_value'] ?? 0);

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
            <a href="{{ route('vendor.playlist') }}" class="small-box-footer">Dari playlist paid <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="small-box bg-success">
            <div class="inner"><h3>{{ $paid }}</h3><p>Paid</p></div>
            <div class="icon"><i class="fas fa-check-circle"></i></div>
            <a href="{{ route('vendor.playlist') }}" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="small-box bg-danger">
            <div class="inner"><h3>{{ $unpaid }}</h3><p>Unpaid</p></div>
            <div class="icon"><i class="fas fa-times-circle"></i></div>
            <a href="{{ route('vendor.playlist') }}" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="small-box" style="background:linear-gradient(135deg,#555,#777);color:#fff;">
            <div class="inner"><h3>{{ count($playlists) }}</h3><p>Total Playlist</p></div>
            <div class="icon"><i class="fas fa-film"></i></div>
            <a href="{{ route('vendor.playlist') }}" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
      </div>

      {{-- PREVIEW TABLE --}}
      <div class="row">
        <div class="col-12">
          <div class="card card-dark">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h3 class="card-title"><i class="fas fa-film mr-2"></i>Daftar Playlist</h3>
              <a href="{{ route('vendor.playlist') }}" class="view-more-link"><i class="fas fa-external-link-alt mr-1"></i>View More</a>
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
@endsection

{{-- 5. JEPIT SCRIPT lu pake @push('scripts') biar disuntik ke bagian bawah <body> --}}
@push('scripts')
<script>
  // Kalo ada custom Javascript buat dashboard taruh disini
  console.log('Dashboard diload!');
</script>
@endpush