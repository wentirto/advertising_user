<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VendorDashboardController;

// ================= DASHBOARD =================
Route::get('/', [VendorDashboardController::class, 'dashboard'])->name('dashboard.list');
Route::get('/dashboard', [VendorDashboardController::class, 'dashboard']);

// ================= DEVICE =================
Route::get('/get-devices/{vendor_id}', [VendorDashboardController::class, 'getDevicesByVendor'])
    ->name('devices.byVendor');

Route::get('/get-device/{id}', [VendorDashboardController::class, 'getDevice'])
    ->name('device.single');

// BARU: dipakai di Step 1 wizard "Tambah Playlist" (card pilih lokasi, tanpa info vendor)
Route::get('/get-locations', [VendorDashboardController::class, 'getAvailableLocations'])
    ->name('locations.available');

// BARU: dipakai di Step 2 wizard "Tambah Playlist" (jadwal terisi + kalender pemakaian)
Route::get('/get-device-schedule/{id}', [VendorDashboardController::class, 'getDeviceSchedule'])
    ->name('device.schedule');

// BARU: playlist by device (unpaid only) — dipakai di step 1 permintaan.blade
Route::get('/get-playlists-by-device/{id}', [VendorDashboardController::class, 'getPlaylistsByDevice'])
    ->name('playlists.byDevice');

// BARU: proxy file (gambar/video) supaya gak ketahan halaman peringatan ngrok free
// — <img>/<video> di browser gak bisa kirim header custom, jadi di-proxy lewat sini.
Route::get('/file-proxy', [VendorDashboardController::class, 'fileProxy'])
    ->name('file.proxy');

// ================= PLAYLIST =================
Route::get('/playlist', [VendorDashboardController::class, 'playlistIndex'])
    ->name('playlist.index');

// Halaman Daftar Permintaan — full page wizard tambah playlist (tanpa modal)
Route::get('/permintaan', [VendorDashboardController::class, 'permintaanIndex'])
    ->name('permintaan.index');

// FIX: route ini dipake di JS previewPlaylist() → fetch('/api/playlist/'+id)
Route::get('/api/playlist/{id}', [VendorDashboardController::class, 'getPlaylistDetail'])
    ->name('playlist.detail');

Route::post('/playlist/store', [VendorDashboardController::class, 'storePlaylist'])
    ->name('playlist.store');

Route::put('/playlist/update/{id}', [VendorDashboardController::class, 'updatePlaylist'])
    ->name('playlist.update');

// Fallback POST untuk AJAX _method=PUT
Route::post('/playlist/update/{id}', [VendorDashboardController::class, 'updatePlaylist']);

// FIX: nama route 'playlist.delete' harus konsisten dengan JS di view
// JS pakai: url:'/playlist/delete/'+hapusId → DELETE method
Route::delete('/playlist/delete/{id}', [VendorDashboardController::class, 'deletePlaylist'])
    ->name('playlist.delete');

// BARU: halaman invoice (AdminLTE style) — dibuka dari tombol hijau di tabel playlist
Route::get('/playlist/{id}/invoice', [VendorDashboardController::class, 'invoice'])
    ->name('playlist.invoice');