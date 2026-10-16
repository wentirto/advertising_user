{{-- Profile Customer --}}
@extends('layout.master')

@section('title', 'Profile')

@push('styles')
<style>
  /* ===== PROFILE CUSTOMER - NEON BLUE ===== */
  .profile-cover {
    position: relative;
    height: 180px;
    border-radius: 10px;
    overflow: hidden;
    background: linear-gradient(135deg, #001a33 0%, #003b66 40%, #007bff 100%);
    box-shadow: 0 0 25px rgba(0, 123, 255, 0.45), inset 0 0 30px rgba(0, 243, 255, 0.15);
  }
  .profile-cover::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
      radial-gradient(circle at 20% 30%, rgba(0, 243, 255, 0.35), transparent 45%),
      radial-gradient(circle at 80% 60%, rgba(0, 123, 255, 0.4), transparent 50%);
    pointer-events: none;
  }
  .profile-info-wrap {
    margin-top: -70px;
    position: relative;
    z-index: 2;
  }
  .profile-avatar {
    width: 130px;
    height: 130px;
    border-radius: 50%;
    border: 4px solid #00f3ff;
    box-shadow: 0 0 20px rgba(0, 243, 255, 0.6), 0 0 40px rgba(0, 123, 255, 0.4);
    object-fit: cover;
    background: #0a1428;
  }
  .avatar-wrapper {
    position: relative;
    display: inline-block;
  }
  .profile-name {
    color: #eaffff;
    font-size: 26px;
    font-weight: 800;
    text-shadow: 0 0 10px rgba(0, 243, 255, 0.6);
    margin-bottom: 2px;
  }
  .profile-job {
    color: #5fb8cc;
    font-size: 15px;
    font-weight: 600;
    text-shadow: 0 0 6px rgba(0, 180, 200, 0.3);
  }
  .profile-company {
    color: #a8b8d0;
    font-size: 13px;
  }
  .status-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .5px;
  }
  .status-active {
    background: rgba(40, 167, 69, 0.15);
    border: 1px solid rgba(40, 167, 69, 0.5);
    color: #39ff14;
    box-shadow: 0 0 12px rgba(40, 167, 69, 0.35);
  }
  .status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #39ff14;
    box-shadow: 0 0 8px #39ff14;
    animation: pulseDot 1.6s ease-in-out infinite;
  }
  @keyframes pulseDot {
    0%, 100% { opacity: 1; box-shadow: 0 0 8px #39ff14; }
    50% { opacity: .4; box-shadow: 0 0 16px #39ff14; }
  }

  /* Kartu statistik di bawah cover */
  .profile-stat-box {
    background: #070f1e;
    border: 1px solid rgba(0, 180, 200, 0.2);
    border-radius: 10px;
    padding: 18px 16px;
    text-align: center;
    height: 100%;
    box-shadow: 0 0 12px rgba(0, 180, 200, 0.08), inset 0 0 12px rgba(0, 180, 200, 0.04);
    transition: all 0.3s ease;
  }
  .profile-stat-box:hover {
    box-shadow: 0 0 22px rgba(0, 180, 200, 0.35), inset 0 0 16px rgba(0, 180, 200, 0.08);
    transform: translateY(-3px);
  }
  .profile-stat-box .stat-icon {
    font-size: 28px;
    color: #00f3ff;
    text-shadow: 0 0 10px rgba(0, 243, 255, 0.7);
    margin-bottom: 6px;
  }
  .profile-stat-box .stat-value {
    font-size: 26px;
    font-weight: 900;
    color: #eaffff;
    text-shadow: 0 0 8px rgba(0, 243, 255, 0.6);
    line-height: 1;
  }
  .profile-stat-box .stat-label {
    font-size: 12px;
    color: #5fb8cc;
    margin-top: 5px;
    font-weight: 600;
  }

  /* Field info (read view) */
  .info-field {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 13px 4px;
    border-bottom: 1px solid rgba(0, 180, 200, 0.08);
  }
  .info-field:last-child { border-bottom: none; }
  .info-field .field-icon {
    width: 40px;
    height: 40px;
    flex: 0 0 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    background: rgba(0, 123, 255, 0.12);
    border: 1px solid rgba(0, 180, 200, 0.25);
    color: #5fb8cc;
    font-size: 16px;
    text-shadow: 0 0 6px rgba(0, 180, 200, 0.4);
  }
  .info-field .field-label {
    font-size: 11px;
    color: #7b8ba3;
    text-transform: uppercase;
    letter-spacing: .6px;
  }
  .info-field .field-value {
    font-size: 15px;
    color: #dbe7f5;
    font-weight: 600;
    word-break: break-word;
  }

  .btn-edit-profile {
    background: linear-gradient(135deg, #00b4c8, #007bff);
    border: 1px solid rgba(0, 243, 255, 0.6);
    color: #eaffff;
    font-weight: 700;
    box-shadow: 0 0 14px rgba(0, 217, 255, 0.45), inset 0 0 8px rgba(0, 243, 255, 0.15);
    transition: all 0.2s ease;
  }
  .btn-edit-profile:hover {
    box-shadow: 0 0 22px rgba(0, 243, 255, 0.75), inset 0 0 12px rgba(0, 243, 255, 0.25);
    transform: translateY(-2px);
    color: #fff;
  }
</style>
@endpush

@section('content')
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1 class="m-0"><i class="fas fa-id-badge mr-2"></i>Profile</h1></div>
        <div class="col-sm-6 text-right">
          <ol class="breadcrumb float-sm-right"><li class="breadcrumb-item active" style="color: #00f3ff;">Profile Customer</li></ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">

      {{-- Toast sukses setelah update --}}
      @if(session('success'))
        <div class="alert alert-success" style="background: linear-gradient(135deg, #1a5c2a, #28a745); border: none; color: #fff; box-shadow: 0 0 15px rgba(40,167,69,.4); border-left: 4px solid #39ff14;">
          <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
      @endif

      <div class="row">
        <div class="col-12">
          {{-- COVER --}}
          <div class="profile-cover"></div>

          {{-- HEADER INFO --}}
          <div class="profile-info-wrap px-4">
            <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between">
              <div class="d-flex flex-column flex-md-row align-items-center align-items-md-end">
                <div class="avatar-wrapper mb-2 mb-md-0">
                  <img src="{{ asset('dist/img/user2-160x160.jpg') }}" class="profile-avatar" alt="{{ $customer->first_name }}">
                </div>
                <div class="ml-md-4 mt-2 mt-md-0 text-center text-md-left pb-2">
                  <div class="profile-name">{{ $customer->first_name }} {{ $customer->last_name }}</div>
                  <div class="profile-job">{{ $customer->job_position ?? '-' }}</div>
                  <div class="profile-company"><i class="fas fa-building mr-1"></i>{{ $customer->company_name ?? '-' }}</div>
                </div>
              </div>
              <div class="pb-2 mt-3 mt-md-0">
                @if((int)$customer->status === 2)
                  <span class="status-pill status-active"><span class="status-dot"></span>AKTIF</span>
                @else
                  <span class="status-pill" style="background:rgba(108,117,125,.2);border:1px solid rgba(108,117,125,.5);color:#a8b8d0;"><span class="status-dot" style="background:#a8b8d0;box-shadow:none;"></span>NON-AKTIF</span>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- STATISTIK --}}
      <div class="row mt-4">
        <div class="col-lg-3 col-6">
          <div class="profile-stat-box">
            <div class="stat-icon"><i class="fas fa-film"></i></div>
            <div class="stat-value">{{ $totalPlaylist }}</div>
            <div class="stat-label">Total Playlist</div>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="profile-stat-box">
            <div class="stat-icon"><i class="fas fa-video"></i></div>
            <div class="stat-value">{{ $customer->playlists->sum(fn($p) => $p->items->count()) }}</div>
            <div class="stat-label">Total Konten</div>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="profile-stat-box">
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            <div class="stat-value">{{ $customer->playlists->filter(fn($p) => optional($p->invoice)->status === 'paid' || in_array(strtolower((string)($p->status ?? '')), ['paid','approved','1']))->count() }}</div>
            <div class="stat-label">Playlist Lunas</div>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="profile-stat-box">
            <div class="stat-icon"><i class="fas fa-clock"></i></div>
            <div class="stat-value">{{ $customer->playlists->filter(fn($p) => optional($p->invoice)->status !== 'paid' && !in_array(strtolower((string)($p->status ?? '')), ['paid','approved','1']))->count() }}</div>
            <div class="stat-label">Menunggu</div>
          </div>
        </div>
      </div>

      {{-- DETAIL & EDIT --}}
      <div class="row mt-4">
        {{-- Info pribadi (read-only) --}}
        <div class="col-lg-6">
          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h3 class="card-title"><i class="fas fa-user mr-2"></i>Informasi Pribadi</h3>
            </div>
            <div class="card-body">
              <div class="info-field">
                <div class="field-icon"><i class="fas fa-envelope"></i></div>
                <div><div class="field-label">Email</div><div class="field-value">{{ $customer->email }}</div></div>
              </div>
              <div class="info-field">
                <div class="field-icon"><i class="fas fa-phone-alt"></i></div>
                <div><div class="field-label">Telepon</div><div class="field-value">{{ $customer->phone_number ?: '-' }}</div></div>
              </div>
              <div class="info-field">
                <div class="field-icon"><i class="fas fa-briefcase"></i></div>
                <div><div class="field-label">Jabatan</div><div class="field-value">{{ $customer->job_position ?: '-' }}</div></div>
              </div>
              <div class="info-field">
                <div class="field-icon"><i class="fas fa-building"></i></div>
                <div><div class="field-label">Perusahaan</div><div class="field-value">{{ $customer->company_name ?: '-' }}</div></div>
              </div>
              <div class="info-field">
                <div class="field-icon"><i class="fas fa-map-marker-alt"></i></div>
                <div><div class="field-label">Kota</div><div class="field-value">{{ $customer->city ?: '-' }}</div></div>
              </div>
              <div class="info-field">
                <div class="field-icon"><i class="fas fa-home"></i></div>
                <div><div class="field-label">Alamat</div><div class="field-value">{{ $customer->address ?: '-' }}</div></div>
              </div>
            </div>
          </div>
        </div>

        {{-- Form edit --}}
        <div class="col-lg-6">
          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h3 class="card-title"><i class="fas fa-pen mr-2"></i>Edit Profile</h3>
            </div>
            <div class="card-body">
              <form method="POST" action="{{ route('user.profile.update', ['id' => $customer->id]) }}">
                @csrf
                <div class="row">
                  <div class="col-md-6 form-group">
                    <label class="field-label">Nama Depan</label>
                    <input type="text" name="first_name" class="form-control neon-field" value="{{ old('first_name', $customer->first_name) }}">
                  </div>
                  <div class="col-md-6 form-group">
                    <label class="field-label">Nama Belakang</label>
                    <input type="text" name="last_name" class="form-control neon-field" value="{{ old('last_name', $customer->last_name) }}">
                  </div>
                </div>
                <div class="form-group">
                  <label class="field-label">Email</label>
                  <input type="email" name="email" class="form-control neon-field" value="{{ old('email', $customer->email) }}">
                </div>
                <div class="form-group">
                  <label class="field-label">Telepon</label>
                  <input type="text" name="phone_number" class="form-control neon-field" value="{{ old('phone_number', $customer->phone_number) }}">
                </div>
                <div class="form-group">
                  <label class="field-label">Jabatan</label>
                  <input type="text" name="job_position" class="form-control neon-field" value="{{ old('job_position', $customer->job_position) }}">
                </div>
                <div class="form-group">
                  <label class="field-label">Perusahaan</label>
                  <input type="text" name="company_name" class="form-control neon-field" value="{{ old('company_name', $customer->company_name) }}">
                </div>
                <div class="row">
                  <div class="col-md-6 form-group">
                    <label class="field-label">Kota</label>
                    <input type="text" name="city" class="form-control neon-field" value="{{ old('city', $customer->city) }}">
                  </div>
                  <div class="col-md-6 form-group">
                    <label class="field-label">Alamat</label>
                    <input type="text" name="address" class="form-control neon-field" value="{{ old('address', $customer->address) }}">
                  </div>
                </div>
                <button type="submit" class="btn btn-edit-profile btn-block">
                  <i class="fas fa-save mr-2"></i>Simpan Perubahan
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>
@endsection

@push('scripts')
<script>
  console.log('Profile Customer diload!');
</script>
@endpush
