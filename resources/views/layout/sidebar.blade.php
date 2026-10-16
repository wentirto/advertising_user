<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="/" class="brand-link"><span class="brand-text font-weight-light">CMS GikVision</span></a>
  <div class="sidebar">
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <a href="{{ route('user.profile') }}" title="Profile">
          <img src="{{ asset('dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User">
        </a>
      </div>
      <div class="info"><a href="{{ route('user.profile') }}" class="d-block">Customer</a></div>
    </div>
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
          <a href="{{ route('vendor.dashboard') }}" class="nav-link {{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}">
            <i class="nav-icon fas fa-chart-line"></i><p>Dashboard</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('vendor.playlist') }}" class="nav-link {{ request()->routeIs('vendor.playlist') ? 'active' : '' }}">
            <i class="nav-icon fas fa-film"></i><p>Daftar Playlist</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('vendor.permintaan') }}" class="nav-link {{ request()->routeIs('vendor.permintaan') ? 'active' : '' }}">
            <i class="nav-icon fas fa-clipboard-list"></i><p>Daftar Permintaan</p>
          </a>
        </li>
      </ul>
    </nav>
  </div>
</aside>
