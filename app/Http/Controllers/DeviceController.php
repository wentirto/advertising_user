<?php
namespace App\Http\Controllers;

class DeviceController extends Controller
{
    public function getDevice($id)
    {
        $data = $this->apiGet();
        return response()->json(collect($data['devices'] ?? [])->firstWhere('id', (int)$id));
    }

    public function getDevicesByVendor($vendor_id)
    {
        $data = $this->apiGet();
        return response()->json(collect($data['devices'] ?? [])->filter(fn($d) => (int)($d['id_vendor'] ?? 0) === (int)$vendor_id)->values());
    }

    public function getAvailableLocations()
    {
        $data = $this->apiGet();
        if (empty($data)) return response()->json([]);

        $devices = collect($data['devices'] ?? [])->map(function ($d) {
            $thumbnail = null;
            if (!empty($d['image'])) {
                $imgData = $d['image'];
                while (is_string($imgData)) {
                    $decoded = json_decode($imgData, true);
                    if (json_last_error() === JSON_ERROR_NONE && !is_numeric($decoded)) $imgData = $decoded;
                    else { $trimmed = trim($imgData, "'\""); if ($trimmed !== $imgData) $imgData = $trimmed; else break; }
                }
                if (is_array($imgData) && count($imgData) > 0) $thumbnail = $imgData[0];
                elseif (is_string($imgData)) $thumbnail = $imgData;
                if ($thumbnail) $thumbnail = $this->buildFileUrl($thumbnail);
            }
            return [
                'id' => $d['id'] ?? null, 'device_name' => $d['device_name'] ?? '-',
                'provinsi' => $d['provinsi'] ?? '-', 'kota' => $d['kota'] ?? '-',
                'r_width' => $d['r_width'] ?? null, 'r_height' => $d['r_height'] ?? null,
                'thumbnail' => $thumbnail, 
            ];
        })->filter(fn($d) => !is_null($d['id']))->values();

        return response()->json($devices);
    }

    public function getDeviceSchedule($deviceId)
    {
        $data = $this->apiGet();
        $device = collect($data['devices'] ?? [])->firstWhere('id', (int)$deviceId);
        if (!$device) return response()->json(['status' => 'error'], 404);

        $devicePlaylists = collect($data['playlists'] ?? [])->filter(fn($p) => (int)($p['device_id'] ?? 0) === (int)$deviceId)->values();
        $bookings = $devicePlaylists->map(function ($p) use ($data) {
            $items = $this->resolvePlaylistContents((int)($p['id'] ?? 0), $data['contents'] ?? [], $data['playlist_items'] ?? []);
            $starts = collect($items)->pluck('start_time')->filter()->values();
            $ends   = collect($items)->pluck('end_time')->filter()->values();
            return [
                'playlist_id' => $p['id'] ?? null, 'playlist_name' => $p['playlist_name'] ?? '-',
                'start_contract' => $p['start_contract'] ?? null, 'end_contract' => $p['end_contract'] ?? null,
                'start_time' => $starts->min() ?? '00:00', 'end_time' => $ends->max() ?? '24:00',
            ];
        })->filter(fn($b) => !empty($b['start_contract']) && !empty($b['end_contract']))->values();

        return response()->json(['status' => 'success', 'device' => $device, 'bookings' => $bookings]);
    }
}