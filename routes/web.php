<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VendorDashboardController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\InvoiceController;

// Bikin / langsung nembak ke Dashboard Vendor
Route::get('/', function () {
    return redirect()->route('vendor.dashboard');
});

// ================= GRUP VENDOR (UI/UX) =================
Route::prefix('vendor')->name('vendor.')->group(function () {
    Route::get('/dashboard', [VendorDashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/playlist', [PlaylistController::class, 'playlistIndex'])->name('playlist');
    Route::get('/permintaan', [PlaylistController::class, 'permintaanIndex'])->name('permintaan');
    Route::get('/invoice/{id}', [InvoiceController::class, 'invoice'])->name('invoice');
});

// ================= GRUP USER =================
Route::prefix('user')->name('user.')->group(function () {
    // Controller untuk user nanti masuk sini
});

// ================= API & AJAX LOGIC =================
// (Sengaja gak dikasih prefix 'vendor' biar Javascript lu yang lama tetep jalan)
Route::get('/get-devices/{vendor_id}', [DeviceController::class, 'getDevicesByVendor']);
Route::get('/get-device/{id}', [DeviceController::class, 'getDevice']);
Route::get('/get-locations', [DeviceController::class, 'getAvailableLocations']);
Route::get('/get-device-schedule/{id}', [DeviceController::class, 'getDeviceSchedule']);

Route::get('/get-playlists-by-device/{id}', [PlaylistController::class, 'getPlaylistsByDevice']);
Route::get('/file-proxy', [PlaylistController::class, 'fileProxy'])->name('file.proxy');
Route::get('/api/playlist/{id}', [PlaylistController::class, 'getPlaylistDetail']);

Route::post('/playlist/store', [PlaylistController::class, 'storePlaylist'])->name('vendor.playlist.store');
Route::put('/playlist/update/{id}', [PlaylistController::class, 'updatePlaylist']);
Route::post('/playlist/update/{id}', [PlaylistController::class, 'updatePlaylist']); // Fallback buat method spoofing
Route::delete('/playlist/delete/{id}', [PlaylistController::class, 'deletePlaylist']);