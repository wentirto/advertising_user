<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Invoice;

class PlaylistController extends Controller
{
    public function playlistIndex()
    {
        $data = $this->apiGet();
        $playlists = $this->enrichPlaylists($data['playlists'] ?? [], $data['contents'] ?? [], $data['playlist_items'] ?? []);
        return view('vendor.playlist', [
            'playlists' => $playlists, 'vendors' => $data['vendors'] ?? [],
            'devices' => $data['devices'] ?? [],
        ]);
    }

    public function permintaanIndex()
    {
        return view('vendor.permintaan');
    }

    public function getPlaylistDetail($id)
    {
        $data = $this->apiGet();
        $playlist = collect($data['playlists'] ?? [])->firstWhere('id', (int)$id);
        if (!$playlist) return response()->json(['status' => 'error'], 404);

        $playlist['resolved_items'] = $this->resolvePlaylistContents((int)$id, $data['contents'] ?? [], $data['playlist_items'] ?? []);
        $playlist['device'] = collect($data['devices'] ?? [])->firstWhere('id', (int)($playlist['device_id'] ?? 0));
        return response()->json(['status' => 'success', 'data' => $playlist]);
    }

    public function getPlaylistsByDevice(int $deviceId)
    {
        $data = $this->apiGet();
        $localInvoices = Invoice::all()->keyBy('playlist_id');

        $result = collect($data['playlists'] ?? [])
            ->filter(function($p) use ($deviceId, $localInvoices) {
                if ((string)($p['device_id'] ?? '') !== (string)$deviceId) return false;
                $apiStatus = strtolower((string)($p['status'] ?? ''));
                $inv = $localInvoices->get($p['id'] ?? 0);
                $isPaid = in_array($apiStatus, ['paid', 'approved', '1']) || ($inv && in_array(strtolower($inv->status), ['paid', 'approved', '1']));
                return !$isPaid;
            })->values()->map(function ($p) use ($data, $localInvoices) {
                $items  = $this->resolvePlaylistContents((int)($p['id'] ?? 0), $data['contents'] ?? [], $data['playlist_items'] ?? []);
                $starts = collect($items)->pluck('start_time')->filter()->values();
                $ends   = collect($items)->pluck('end_time')->filter()->values();
                $inv = $localInvoices->get($p['id'] ?? 0);
                return [
                    'id' => $p['id'] ?? null, 'playlist_name' => $p['playlist_name'] ?? '-',
                    'start_contract' => $p['start_contract'] ?? null, 'end_contract' => $p['end_contract'] ?? null,
                    'start_time' => $starts->min() ?? '00:00', 'end_time' => $ends->max() ?? '00:00',
                    'contract_value' => $inv ? $inv->amount : ($p['contract_value'] ?? 0), 'status' => 'unpaid',
                ];
            });

        return response()->json(['status' => 'success', 'playlists' => $result]);
    }

    public function storePlaylist(Request $request)
    {
        $request->validate([
            'playlist_name' => 'required|string', 'device_id' => 'required',
            'start_contract' => 'required|date', 'end_contract' => 'required|date',
        ]);

        $apiData = $this->apiGet();
        $device = collect($apiData['devices'] ?? [])->firstWhere('id', (int)$request->device_id);
        $vendorId = $device['id_vendor'] ?? $device['vendor_id'] ?? null;
        $files = array_values(array_filter((array)($request->file('file') ?? []), fn($f) => $f instanceof \Illuminate\Http\UploadedFile && $f->isValid()));

        if (!$device || empty($files)) return response()->json(['status' => 'error', 'message' => 'Input tidak valid!'], 422);

        $durasiKonten = (array)$request->input('content_duration_seconds', []);
        $grandTotal = 0;
        foreach ($files as $i => $file) {
            $calc = $this->hitungInvoiceSlot($request->start_time, $request->end_time, $request->start_contract, $request->end_contract, (int)($durasiKonten[$i] ?? 0));
            $grandTotal += $calc['sub_total'];
        }

        $http = Http::withHeaders(['ngrok-skip-browser-warning' => 'true'])->asMultipart()
            ->attach('playlist_name', $request->playlist_name)->attach('device_id', $request->device_id)
            ->attach('vendor_id', (string)$vendorId)->attach('cust_id', $request->cust_id ?: '1')
            ->attach('start_contract', $request->start_contract)->attach('end_contract', $request->end_contract)
            ->attach('contract_value', (string)$grandTotal)->attach('status', 'unpaid')
            ->attach('start_time', $request->start_time)->attach('end_time', $request->end_time);

        foreach ($files as $i => $file) {
            $http->attach("start_date[$i]", $request->start_contract)->attach("end_date[$i]", $request->end_contract);
            $http->attach('file[]', fopen($file->getRealPath(), 'rb'), $file->getClientOriginalName());
        }

        $res = $http->post($this->apiStoreUrl);
        if ($res->failed()) return response()->json(['status' => 'error'], 500);

        if ($newId = $res->json('data.playlist.id') ?? $res->json('playlist_id')) {
            Invoice::updateOrCreate(['playlist_id' => $newId], [
                'vendor_id' => $vendorId, 'customer_id' => $request->cust_id ?: '1',
                'device_id' => $request->device_id, 'amount' => $grandTotal, 'status' => 'unpaid',
            ]);
        }
        return response()->json(['status' => 'success'], 201);
    }

    public function updatePlaylist(Request $request, $id)
    {
        Http::put($this->apiStoreUrl . '/' . $id, $request->only(['playlist_name', 'device_id', 'start_contract', 'end_contract', 'contract_value', 'status']));
        return response()->json(['status' => 'success']);
    }

    public function deletePlaylist($id)
    {
        Http::delete($this->apiStoreUrl . '/' . $id);
        Invoice::where('playlist_id', $id)->delete();
        return response()->json(['status' => 'success']);
    }

    public function fileProxy(Request $request)
    {
        $path = $request->query('path');
        if (!$path) abort(404);
        $upstream = Http::withOptions(['stream' => true])->get($path);
        return response()->stream(function () use ($upstream) {
            $body = $upstream->toPsrResponse()->getBody();
            while (!$body->eof()) { echo $body->read(65536); flush(); }
        }, $upstream->status(), ['Content-Type' => $upstream->header('Content-Type')]);
    }
}