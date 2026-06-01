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

// ================= PLAYLIST =================
Route::get('/playlist', [VendorDashboardController::class, 'playlistIndex'])
    ->name('playlist.index');

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