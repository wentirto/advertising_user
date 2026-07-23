<?php
namespace App\Http\Controllers;

class VendorDashboardController extends Controller
{
    public function dashboard()
    {
        $data = $this->apiGet();
        $playlists = $this->enrichPlaylists($data['playlists'] ?? [], $data['contents'] ?? [], $data['playlist_items'] ?? []);
        return view('vendor.dashboard', [ // Ganti view-nya ke folder vendor
            'playlists' => $playlists, 'vendors' => $data['vendors'] ?? [],
            'contents' => $data['contents'] ?? [], 'devices' => $data['devices'] ?? [],
        ]);
    }
}