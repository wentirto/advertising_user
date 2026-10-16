<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Models\Vendor;
use App\Models\Device;

class VendorDashboardController extends Controller
{
    public function dashboard()
    {
        $playlists = Playlist::with(['device', 'vendor', 'items.content', 'invoice'])->get();
        $vendors   = Vendor::all();
        $devices   = Device::all();

        return view('vendor.dashboard', [
            'playlists' => $playlists,
            'vendors'   => $vendors,
            'devices'   => $devices,
        ]);
    }
}
