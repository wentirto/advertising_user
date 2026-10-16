<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Playlist;

class DeviceController extends Controller
{
    public function getDevice($id)
    {
        $device = Device::findOrFail($id);
        return response()->json($device);
    }

    public function getDevicesByVendor($vendor_id)
    {
        $devices = Device::where('id_vendor', $vendor_id)->get();
        return response()->json($devices);
    }

    public function getAvailableLocations()
    {
        $devices = Device::all()->map(function ($d) {
            // Ambil gambar dari beberapa kemungkinan kolom
            $image = $d->image ?? $d->thumbnail ?? $d->device_image ?? null;

            // Coba ambil array gambar dari string (jika DB-nya nemu kasih URL+ JSON campur)
            if (is_string($image) && strpos($image, '[') !== false) {
                $jsonString = substr($image, strpos($image, '['));
                $jsonString = rtrim(trim($jsonString), ']');
                $images = json_decode($jsonString, true);
                if (is_array($images) && count($images) > 0) {
                    $image = array_shift($images);
                } else {
                    $image = null;
                }
            }

            if ($image) {
                // Kalau belum full URL, buat dari asset storage
                if (!str_starts_with($image, 'http')) {
                    $image = asset('storage/' . $image);
                }
            }

            return [
                'id'          => $d->id,
                'device_name' => $d->device_name,
                'provinsi'    => $d->provinsi ?? '-',
                'kota'        => $d->kota ?? '-',
                'r_width'     => $d->r_width,
                'r_height'    => $d->r_height,
                'thumbnail'   => $image,
            ];
        });

        return response()->json($devices);
    }

    public function getDeviceSchedule($deviceId)
    {
        $device = Device::find($deviceId);
        if (!$device) {
            return response()->json(['status' => 'error'], 404);
        }

        // Ambil semua playlist untuk device ini, beserta items-nya
        $playlists = Playlist::where('device_id', $deviceId)
            ->with('items')
            ->get();

        $bookings = $playlists->map(function ($p) {
            $starts = $p->items->pluck('start_time')->filter()->values();
            $ends   = $p->items->pluck('end_time')->filter()->values();

            return [
                'playlist_id'     => $p->id,
                'playlist_name'   => $p->playlist_name ?? '-',
                'start_contract'  => $p->start_contract,
                'end_contract'    => $p->end_contract,
                'start_time'      => $starts->min() ?? '00:00',
                'end_time'        => $ends->max() ?? '24:00',
            ];
        })->filter(fn($b) => !empty($b['start_contract']) && !empty($b['end_contract']))->values();

        return response()->json([
            'status'   => 'success',
            'device'   => $device,
            'bookings' => $bookings,
        ]);
    }
}
