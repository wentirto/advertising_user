{{-- 1. Panggil kerangka utama --}}
@extends('layout.master')

{{-- 2. Kasih judul halamannya --}}
@section('title', 'Dashboard')

{{-- 3. JEPIT CSS lu pake @push('styles') biar disuntik ke <head> --}}
@push('styles')

@endpush

{{-- 4. JEPIT HTML KONTEN lu pake @section('content') biar disuntik ke <body> --}}
@section('content')
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1 class="m-0"><i class="fas fa-chart-line mr-2"></i>Dashboard</h1></div>
        <div class="col-sm-6 text-right">
          <ol class="breadcrumb float-sm-right"><li class="breadcrumb-item active" style="color: #00f3ff;">Dashboard</li></ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      @php
        use Carbon\Carbon;

        $paid = $unpaid = 0;
        $totalPengeluaran = 0;

        foreach ($playlists as $p) {
          $inv        = $p->invoice;
          $apiStatus  = strtolower((string)($p->status ?? ''));
          $localStatus = $inv ? strtolower($inv->status) : 'unpaid';
          $isPaid      = in_array($apiStatus, ['paid', 'approved', '1']) || in_array($localStatus, ['paid', 'approved', '1']);

          if ($isPaid) {
              $paid++;
              $totalPengeluaran += (float)($inv ? $inv->amount : ($p->contract_value ?? 0));
          } else {
              $unpaid++;
          }
        }

        $deviceMap = $devices->keyBy('id');
        $vendorMap = $vendors->keyBy('id');
      @endphp
{{-- SUMMARY CARDS --}}
      <div class="row mb-3">
        <div class="col-lg-3 col-6">
          <div class="small-box bg-primary">
            <div class="inner">
              <h5 class="rp-kecil">Rp {{ number_format($totalPengeluaran,0,',','.') }}</h5>
              <p>Total Pengeluaran</p>
            </div>
            <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="small-box bg-success">
            <div class="inner"><h3>{{ $paid }}</h3><p>Paid</p></div>
            <div class="icon"><i class="fas fa-check-circle"></i></div>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="small-box bg-danger">
            <div class="inner"><h3>{{ $unpaid }}</h3><p>Unpaid</p></div>
            <div class="icon"><i class="fas fa-times-circle"></i></div>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="small-box bg-secondary">
            <div class="inner"><h3>{{ count($playlists) }}</h3><p>Total Playlist</p></div>
            <div class="icon"><i class="fas fa-film"></i></div>
          </div>
        </div>
      </div>

      {{-- PREVIEW TABLE --}}
      <div class="row">
        <div class="col-12">
          <div class="card">
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
                    <th style="width:90px; text-align:center;">Sisa</th>
                    <th>Nilai Kontrak</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($playlists->take(5) as $i => $p)
                    @php
                      $end   = Carbon::parse($p->end_contract);
                      $diff  = (int) Carbon::now()->diffInDays($end, false);
                      if ($diff >= 30) { $sisaClass = 'aman'; }
                      elseif ($diff >= 11) { $sisaClass = 'warning'; }
                      else { $sisaClass = 'danger'; }
                      $sisaLabel = $diff > 0 ? $diff.' hari' : abs($diff).' hari (lewat)';
                      $inv  = $p->invoice;
                      $val  = $inv ? $inv->amount : ($p->contract_value ?? 0);
                      $vName = optional($p->vendor)->nama ?? optional($p->vendor)->name ?? '-';
                      $dName = optional($p->device)->device_name ?? '-';
                    @endphp
                    <tr>
                      <td class="text-center">{{ $i + 1 }}</td>
                      <td><strong>{{ $p->playlist_name }}</strong></td>
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
  console.log('Dashboard Cyberpunk diload!');
</script>
@endpush