<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  
  {{-- @yield('title') bakal nangkep judul dari masing-masing halaman --}}
  <title>@yield('title') - CMS GikVision</title>
  
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
  
  {{-- @push('styles') tempat nampung <style> dari masing-masing halaman --}}
  @stack('styles')
</head>

<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">
  <div id="toast-container"></div>

  {{-- Manggil potongan Navbar --}}
  @include('layout.header')

  {{-- Manggil potongan Sidebar --}}
  @include('layout.sidebar')

  {{-- CONTENT WRAPPER: Di sinilah HTML Dashboard / Permintaan lu disuntik --}}
  <div class="content-wrapper">
    @yield('content')
  </div>

  {{-- Manggil potongan Footer --}}
  @include('layout.footer')
</div>

<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('dist/js/adminlte.min.js') }}"></script>

{{-- @push('scripts') tempat nampung <script> Javascript dari masing-masing halaman --}}
@stack('scripts')
</body>
</html>