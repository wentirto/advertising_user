<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Playlist;
use App\Models\Device;
use App\Models\Vendor;
use App\Models\Content;
use App\Models\PlaylistItem;
use App\Models\Invoice;

class PlaylistController extends Controller
{
    public function playlistIndex()
    {
        $playlists = Playlist::with(['device', 'vendor', 'items.content', 'invoice'])->get();
        $vendors   = Vendor::all();
        $devices   = Device::all();

        return view('vendor.playlist', [
            'playlists' => $playlists,
            'vendors'   => $vendors,
            'devices'   => $devices,
        ]);
    }

    public function permintaanIndex()
    {
        return view('vendor.permintaan');
    }

    public function getPlaylistDetail($id)
    {
        $playlist = Playlist::with(['device', 'vendor', 'items.content'])->find($id);
        if (!$playlist) {
            return response()->json(['status' => 'error'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $playlist]);
    }

    public function getPlaylistsByDevice(int $deviceId)
    {
        $playlists = Playlist::where('device_id', $deviceId)
            ->with(['items', 'invoice'])
            ->get()
            ->filter(function ($p) {
                $apiStatus = strtolower((string)($p->status ?? ''));
                $inv       = $p->invoice;
                $isPaid    = in_array($apiStatus, ['paid', 'approved', '1'])
                    || ($inv && in_array(strtolower($inv->status), ['paid', 'approved', '1']));
                return !$isPaid;
            })
            ->values()
            ->map(function ($p) {
                $starts = $p->items->pluck('start_time')->filter()->values();
                $ends   = $p->items->pluck('end_time')->filter()->values();
                $inv    = $p->invoice;

                return [
                    'id'             => $p->id,
                    'playlist_name'  => $p->playlist_name ?? '-',
                    'start_contract' => $p->start_contract,
                    'end_contract'   => $p->end_contract,
                    'start_time'     => $starts->min() ?? '00:00',
                    'end_time'       => $ends->max() ?? '00:00',
                    'contract_value' => $inv ? $inv->amount : ($p->contract_value ?? 0),
                    'status'         => 'unpaid',
                ];
            });

        return response()->json(['status' => 'success', 'playlists' => $playlists]);
    }

    public function storePlaylist(Request $request)
    {
        $request->validate([
            'playlist_name'  => 'required|string',
            'device_id'      => 'required',
            'start_contract' => 'required|date',
            'end_contract'   => 'required|date',
        ]);

        $device = Device::find($request->device_id);
        if (!$device) {
            return response()->json(['status' => 'error', 'message' => 'Device tidak ditemukan!'], 422);
        }

        $files = array_values(array_filter(
            (array)($request->file('file') ?? []),
            fn($f) => $f instanceof \Illuminate\Http\UploadedFile && $f->isValid()
        ));

        if (empty($files)) {
            return response()->json(['status' => 'error', 'message' => 'File konten wajib diisi!'], 422);
        }

        $durasiKonten = (array)$request->input('content_duration_seconds', []);
        $vendorId     = $device->id_vendor;
        $custId       = $request->cust_id ?: 1;
        $grandTotal   = 0;

        // Hitung grand total dulu
        foreach ($files as $i => $file) {
            $calc        = $this->hitungInvoiceSlot(
                $request->start_time, $request->end_time,
                $request->start_contract, $request->end_contract,
                (int)($durasiKonten[$i] ?? 0)
            );
            $grandTotal += $calc['sub_total'];
        }

        // Simpan playlist ke DB
        $playlist = Playlist::create([
            'playlist_name'  => $request->playlist_name,
            'device_id'      => $request->device_id,
            'vendor_id'      => $vendorId,
            'cust_id'        => $custId,
            'start_contract' => $request->start_contract,
            'end_contract'   => $request->end_contract,
            'contract_value' => $grandTotal,
            'status'         => 'unpaid',
        ]);

        // Simpan setiap file sebagai content + playlist_item
        foreach ($files as $i => $file) {
            $storedPath = $file->store('contents', 'public');

            $content = Content::create([
                'cust_id' => $custId,
                'title'   => $file->getClientOriginalName(),
                'type'    => str_starts_with($file->getMimeType(), 'video') ? 'video' : 'image',
                'format'  => $file->getClientOriginalExtension(),
                'file'    => $storedPath,
            ]);

            PlaylistItem::create([
                'playlist_id' => $playlist->id,
                'cust_id'     => $custId,
                'content_id'  => $content->id,
                'start_date'  => $request->start_contract,
                'end_date'    => $request->end_contract,
                'start_time'  => $request->start_time,
                'end_time'    => $request->end_time,
                'days'        => json_encode([]),
                'duration'    => (int)($durasiKonten[$i] ?? 15),
            ]);
        }

        // Simpan invoice
        Invoice::updateOrCreate(
            ['playlist_id' => $playlist->id],
            [
                'vendor_id'   => $vendorId,
                'customer_id' => $custId,
                'device_id'   => $request->device_id,
                'amount'      => $grandTotal,
                'status'      => 'unpaid',
            ]
        );

        return response()->json(['status' => 'success', 'message' => 'Playlist berhasil disimpan!', 'playlist_id' => $playlist->id], 201);
    }

    public function updatePlaylist(Request $request, $id)
    {
        $playlist = Playlist::findOrFail($id);
        $playlist->update($request->only([
            'playlist_name', 'device_id', 'start_contract',
            'end_contract', 'contract_value', 'status',
        ]));
        return response()->json(['status' => 'success']);
    }

    public function deletePlaylist($id)
    {
        Playlist::findOrFail($id)->delete();
        Invoice::where('playlist_id', $id)->delete();
        return response()->json(['status' => 'success']);
    }

    /**
     * Proxy file dari storage publik (untuk file yang sudah diupload lokal).
     */
    public function fileProxy(Request $request)
    {
        $path = $request->query('path');
        if (!$path) abort(404);

        // Jika masih ada request proxy ke URL eksternal (legacy), teruskan
        if (str_starts_with($path, 'http')) {
            $upstream = Http::withOptions(['stream' => true])->get($path);
            return response()->stream(function () use ($upstream) {
                $body = $upstream->toPsrResponse()->getBody();
                while (!$body->eof()) {
                    echo $body->read(65536);
                    flush();
                }
            }, $upstream->status(), ['Content-Type' => $upstream->header('Content-Type')]);
        }

        // Untuk file lokal, serve dari storage
        $fullPath = storage_path('app/public/' . ltrim($path, '/'));
        if (!file_exists($fullPath)) abort(404);

        return response()->file($fullPath);
    }
}
