<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VendorDashboardController extends Controller
{
    // URL API temen — GET semua data
    private string $apiBase = 'https://snowsuit-envision-growing.ngrok-free.dev/api/vendors';

    // URL simpan playlist — ini mengarah ke PlaylistController@saveplaylist
    // (bukan VendorController@store yang tidak handle file)
    private string $apiStoreUrl = 'https://snowsuit-envision-growing.ngrok-free.dev/api/playlist/store';

    // Base URL domain temen untuk build storage URL file
    private string $storageDomain = 'https://snowsuit-envision-growing.ngrok-free.dev';

    // ================================================================
    // HELPER: Ambil semua data dari API temen
    // ================================================================
    private function apiGet(): array
    {
        try {
            $response = Http::withHeaders([
                'ngrok-skip-browser-warning' => 'true',
                'Accept'                     => 'application/json',
            ])->timeout(30)->get($this->apiBase);

            if ($response->failed()) {
                Log::error('apiGet failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return [];
            }

            return $response->json()['data'] ?? [];

        } catch (\Exception $e) {
            Log::error('apiGet exception', ['error' => $e->getMessage()]);
            return [];
        }
    }

    // ================================================================
    // HELPER: Build URL file yang bisa diakses browser
    //
    // Field 'file' di tabel contents dari API berisi path relatif,
    // misal: "uploads/1/led-showroom-kantor/promo/foto.jpg"
    // Perlu jadi: "https://[domain-temen]/storage/uploads/1/.../foto.jpg"
    // ================================================================
    private function buildFileUrl(string $filePath): string
    {
        $filePath = ltrim($filePath, '/');

        // Kalau sudah URL penuh, pakai langsung
        if (str_starts_with($filePath, 'http')) {
            return $filePath;
        }

        // Kalau path sudah mulai dengan 'storage/', jangan double
        if (str_starts_with($filePath, 'storage/')) {
            return rtrim($this->storageDomain, '/') . '/' . $filePath;
        }

        return rtrim($this->storageDomain, '/') . '/storage/' . $filePath;
    }

    // ================================================================
    // HELPER: Gabungkan playlist_items + contents untuk satu playlist
    //
    // ROOT CAUSE FIX untuk preview:
    // Tabel 'contents' TIDAK punya playlist_id.
    // Yang punya playlist_id adalah 'playlist_items'.
    // Alur: playlist → playlist_items (by playlist_id) → contents (by content_id)
    //
    // Setiap item yang dikembalikan punya:
    //   - file_url   : URL lengkap yang bisa diakses browser
    //   - file_type  : 'image' atau 'video'
    //   - content    : data lengkap dari tabel contents
    //   - days       : array (sudah di-decode dari JSON string)
    //   - start_date, end_date, start_time, end_time, priority_mode
    // ================================================================
    private function resolvePlaylistContents(int $playlistId, array $contents, array $playlistItems): array
    {
        // Index contents by id supaya lookup O(1)
        $contentsById = collect($contents)->keyBy('id');

        // Filter playlist_items yang milik playlist ini
        $items = collect($playlistItems)
            ->filter(fn($item) => (int)($item['playlist_id'] ?? 0) === $playlistId)
            ->values();

        return $items->map(function ($item) use ($contentsById) {
            $contentId = (int)($item['content_id'] ?? 0);
            $content   = $contentsById->get($contentId);

            // Build URL lengkap
            $fileUrl = null;
            if ($content && !empty($content['file'])) {
                $fileUrl = $this->buildFileUrl($content['file']);
            }

            // Decode days dari JSON string jika perlu
            $days = $item['days'] ?? [];
            if (is_string($days)) {
                $decoded = json_decode($days, true);
                $days = is_array($decoded) ? $decoded : [];
            }

            return array_merge($item, [
                'content'   => $content,
                'file_url'  => $fileUrl,
                'file_type' => $content['type'] ?? null,   // 'image' atau 'video'
                'days'      => $days,
            ]);
        })->all();
    }

    // ================================================================
    // HELPER: Tambahkan resolved_items ke setiap playlist
    // ================================================================
    private function enrichPlaylists(array $playlists, array $contents, array $playlistItems): array
    {
        return array_map(function ($playlist) use ($contents, $playlistItems) {
            $playlist['resolved_items'] = $this->resolvePlaylistContents(
                (int)($playlist['id'] ?? 0),
                $contents,
                $playlistItems
            );
            return $playlist;
        }, $playlists);
    }

    // ================================================================
    // DASHBOARD — halaman utama (preview max 5 playlist)
    // ================================================================
    public function dashboard()
    {
        $data          = $this->apiGet();
        $playlists     = $data['playlists']      ?? [];
        $contents      = $data['contents']       ?? [];
        $playlistItems = $data['playlist_items'] ?? [];

        $playlists = $this->enrichPlaylists($playlists, $contents, $playlistItems);

        return view('dashboard', [
            'playlists'      => $playlists,
            'vendors'        => $data['vendors']  ?? [],
            'contents'       => $contents,
            'devices'        => $data['devices']  ?? [],
            'playlist_items' => $playlistItems,
        ]);
    }

    // ================================================================
    // PLAYLIST INDEX — halaman /playlist (full table)
    // ================================================================
    public function playlistIndex()
    {
        $data          = $this->apiGet();
        $playlists     = $data['playlists']      ?? [];
        $contents      = $data['contents']       ?? [];
        $playlistItems = $data['playlist_items'] ?? [];

        $playlists = $this->enrichPlaylists($playlists, $contents, $playlistItems);

        return view('playlist', [
            'playlists'      => $playlists,
            'vendors'        => $data['vendors']  ?? [],
            'contents'       => $contents,
            'devices'        => $data['devices']  ?? [],
            'playlist_items' => $playlistItems,
        ]);
    }

    // ================================================================
    // GET SINGLE PLAYLIST DETAIL — untuk AJAX preview di JS
    // Dipanggil dari: fetch('/api/playlist/' + id)
    // ================================================================
    public function getPlaylistDetail($id)
    {
        $data = $this->apiGet();

        if (empty($data)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal ambil data dari API',
            ], 500);
        }

        $playlists     = $data['playlists']      ?? [];
        $contents      = $data['contents']       ?? [];
        $playlistItems = $data['playlist_items'] ?? [];
        $devices       = $data['devices']        ?? [];
        $vendors       = $data['vendors']        ?? [];

        $playlist = collect($playlists)->firstWhere('id', (int)$id);

        if (!$playlist) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Playlist tidak ditemukan',
            ], 404);
        }

        // Gabungkan konten
        $playlist['resolved_items'] = $this->resolvePlaylistContents(
            (int)$id,
            $contents,
            $playlistItems
        );

        // Tambahkan data device dan vendor untuk info lengkap
        $playlist['device'] = collect($devices)->firstWhere('id', (int)($playlist['device_id'] ?? 0));
        $playlist['vendor'] = collect($vendors)->firstWhere('id', (int)($playlist['vendor_id'] ?? $playlist['cust_id'] ?? 0));

        return response()->json([
            'status' => 'success',
            'data'   => $playlist,
        ]);
    }

    // ================================================================
    // GET DEVICE BY ID — untuk auto-fill form
    // ================================================================
    public function getDevice($id)
    {
        $data = $this->apiGet();

        if (empty($data)) {
            return response()->json(null, 500);
        }

        $device = collect($data['devices'] ?? [])->firstWhere('id', (int)$id);

        if (!$device) {
            return response()->json(null, 404);
        }

        $vendor = collect($data['vendors'] ?? [])->firstWhere('id', (int)($device['id_vendor'] ?? 0));

        return response()->json([
            'provinsi'       => $device['provinsi']       ?? '-',
            'kota'           => $device['kota']           ?? '-',
            'ip_address'     => $device['ip_address']     ?? '-',
            'url'            => $device['url']            ?? '-',
            'r_width'        => $device['r_width']        ?? '',
            'r_height'       => $device['r_height']       ?? '',
            'vendor'         => $vendor['nama']           ?? '-',
            'nego_value'     => $device['nego_value']     ?? null,
            'price'          => $device['price']          ?? null,
            'contract_value' => $device['contract_value'] ?? null,
        ]);
    }

    // ================================================================
    // GET DEVICES BY VENDOR
    // ================================================================
    public function getDevicesByVendor($vendor_id)
    {
        $data = $this->apiGet();

        if (empty($data)) {
            return response()->json([], 500);
        }

        $devices = collect($data['devices'] ?? [])
            ->filter(fn($d) => (int)($d['id_vendor'] ?? 0) === (int)$vendor_id)
            ->values();

        return response()->json($devices);
    }

    // ================================================================
    // STORE PLAYLIST
    //
    // TARGET ENDPOINT: POST /playlist/store → PlaylistController@saveplaylist
    //
    // saveplaylist() temen baca:
    //   - $request->file('file')           → array file (UploadedFile[])
    //   - $request->playlist_name          → string
    //   - $request->cust_id                → required! (validated)
    //   - $request->vendor_id, device_id   → string
    //   - $request->start_date[$index]     → indexed array (BUKAN flat[])
    //   - $request->end_date[$index]       → indexed array
    //   - $request->start_time[$index]     → indexed array
    //   - $request->end_time[$index]       → indexed array
    //   - $request->days[$index]           → indexed array of arrays
    //   - isset($request->priority_mode[$index]) → cek keberadaan key, bukan nilai
    //
    // FIX SUMMARY:
    //   1. file[]: kirim dengan Content-Type eksplisit agar dikenali UploadedFile
    //   2. cust_id: wajib ada, fallback ke vendor_id atau '1'
    //   3. start_date, end_date, dll: indexed "start_date[0]", "start_date[1]", ...
    //      (BUKAN flat "start_date[]") agar $request->start_date[$index] bekerja
    //   4. priority_mode: kirim key HANYA kalau aktif ('1')
    //      (saveplaylist temen pakai isset() bukan nilai — kirim '0' tetap true)
    // ================================================================
    public function storePlaylist(Request $request)
    {
        $request->validate([
            'playlist_name'  => 'required|string|max:255',
            'vendor_id'      => 'required',
            'device_id'      => 'required',
            'start_contract' => 'required|date',
            'end_contract'   => 'required|date|after_or_equal:start_contract',
            'contract_value' => 'required|numeric|min:0',
        ], [
            'playlist_name.required'      => 'Nama playlist wajib diisi!',
            'vendor_id.required'          => 'Vendor wajib dipilih!',
            'device_id.required'          => 'Device wajib dipilih!',
            'start_contract.required'     => 'Start contract wajib diisi!',
            'end_contract.required'       => 'End contract wajib diisi!',
            'end_contract.after_or_equal' => 'End contract harus setelah start contract!',
            'contract_value.required'     => 'Contract value wajib diisi!',
            'contract_value.numeric'      => 'Contract value harus berupa angka!',
        ]);

        // ── Kumpulkan file valid ──
        $files = $request->file('file') ?? [];
        if (!is_array($files)) $files = [$files];

        $validFiles = array_values(array_filter(
            $files,
            fn($f) => $f instanceof \Illuminate\Http\UploadedFile && $f->isValid()
        ));

        if (empty($validFiles)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Minimal upload 1 file konten!',
            ], 422);
        }

        // ── Data per-konten ──
        $startDates    = (array)$request->input('start_date',    []);
        $endDates      = (array)$request->input('end_date',      []);
        $startTimes    = (array)$request->input('start_time',    []);
        $endTimes      = (array)$request->input('end_time',      []);
        $days          = (array)$request->input('days',          []);
        $priorityModes = (array)$request->input('priority_mode', []);

        // FIX 2: cust_id wajib ada untuk validasi di saveplaylist temen
        $custId = (string)($request->cust_id ?: $request->vendor_id ?: '1');

        // ── Build multipart HTTP request ──
        $http = Http::withHeaders([
            'ngrok-skip-browser-warning' => 'true',
            'Accept'                     => 'application/json',
        ])->asMultipart();

        // ── Field utama playlist ──
        $http = $http
            ->attach('playlist_name',  $request->playlist_name)
            ->attach('cust_id',        $custId)
            ->attach('vendor_id',      (string)$request->vendor_id)
            ->attach('device_id',      (string)$request->device_id)
            ->attach('start_contract', $request->start_contract)
            ->attach('end_contract',   $request->end_contract)
            ->attach('contract_value', (string)$request->contract_value)
            ->attach('location',       $request->input('location', ''))
            ->attach('ip_address',     $request->ip() ?? '');

        // Status dan nego_value (opsional, dari flow nego harga)
        if ($request->filled('status')) {
            $http = $http->attach('status', (string)$request->status);
        }
        if ($request->filled('nego_value')) {
            $http = $http->attach('nego_value', (string)$request->nego_value);
        }

        // FIX 3: indexed arrays — "start_date[0]", "start_date[1]", ...
        // saveplaylist temen baca: $request->start_date[$index]
        // Kalau dikirim sebagai "start_date[]" (flat), $request->start_date[0]
        // mungkin tidak ada karena PHP parse "start_date[]" jadi array numeric
        // tapi urutan bisa tidak terjamin di multipart. Indexed lebih aman.
        foreach ($startDates as $i => $val) {
            $http = $http->attach("start_date[{$i}]", (string)($val ?? ''));
        }
        foreach ($endDates as $i => $val) {
            $http = $http->attach("end_date[{$i}]", (string)($val ?? ''));
        }
        foreach ($startTimes as $i => $val) {
            $http = $http->attach("start_time[{$i}]", (string)($val ?? ''));
        }
        foreach ($endTimes as $i => $val) {
            $http = $http->attach("end_time[{$i}]", (string)($val ?? ''));
        }

        // Days: days[i][] → temen baca $request->days[$index] sebagai array
        foreach ($days as $i => $dayArr) {
            foreach ((array)$dayArr as $day) {
                $http = $http->attach("days[{$i}][]", (string)$day);
            }
        }

        // FIX 4: priority_mode — kirim key HANYA kalau aktif
        // saveplaylist temen: $priority = isset($request->priority_mode[$index]) ? 1 : 0;
        // Kalau lo kirim '0', isset() tetap TRUE → terbaca sebagai 1 (SALAH!)
        // Solusi: kalau tidak aktif, jangan attach sama sekali
        foreach ($priorityModes as $i => $val) {
            if ((string)$val === '1') {
                $http = $http->attach("priority_mode[{$i}]", '1');
            }
            // Kalau '0': SKIP — isset() = false di sisi temen → priority = 0
        }

        // FIX 1: file[] dengan MIME type eksplisit
        // Tanpa Content-Type header, Laravel sisi temen tidak bisa kenali file
        // sebagai UploadedFile yang valid → $request->file('file') = null
        foreach ($validFiles as $index => $file) {
            $http = $http->attach(
                'file[]',
                fopen($file->getRealPath(), 'rb'),
                $file->getClientOriginalName(),
                ['Content-Type' => $file->getMimeType() ?: 'application/octet-stream']
            );
        }

        // ── Kirim ke API temen ──
        try {
            $response = $http->post($this->apiStoreUrl);
        } catch (\Exception $e) {
            Log::error('storePlaylist exception', [
                'error'      => $e->getMessage(),
                'file_count' => count($validFiles),
            ]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Koneksi ke API gagal: ' . $e->getMessage(),
            ], 500);
        }

        if ($response->failed()) {
            Log::error('storePlaylist API error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return response()->json([
                'status'  => 'error',
                'message' => 'API error! Status: ' . $response->status(),
                'debug'   => $response->body(),
            ], 500);
        }

        $responseData = $response->json();

        Log::info('storePlaylist sukses', [
            'playlist_name' => $request->playlist_name,
            'file_count'    => count($validFiles),
            'cust_id'       => $custId,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => $responseData['message'] ?? 'Playlist berhasil disimpan!',
            'data'    => $responseData,
        ], 201);
    }

    // ================================================================
    // UPDATE PLAYLIST
    // ================================================================
    public function updatePlaylist(Request $request, $id)
    {
        $request->validate([
            'playlist_name'  => 'required|string|max:255',
            'vendor_id'      => 'required',
            'device_id'      => 'required',
            'start_contract' => 'required|date',
            'end_contract'   => 'required|date|after_or_equal:start_contract',
            'contract_value' => 'required|numeric|min:0',
        ]);

        $payload = [
            'playlist_name'  => $request->playlist_name,
            'cust_id'        => $request->cust_id ?: $request->vendor_id,
            'device_id'      => $request->device_id,
            'start_contract' => $request->start_contract,
            'end_contract'   => $request->end_contract,
            'contract_value' => $request->contract_value,
            'updated_at'     => now()->toDateTimeString(),
        ];

        if ($request->filled('status')) {
            $payload['status'] = $request->status;
        }

        try {
            $response = Http::withHeaders([
                'ngrok-skip-browser-warning' => 'true',
                'Accept'                     => 'application/json',
            ])->put($this->apiStoreUrl . '/' . $id, $payload);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Koneksi ke API gagal: ' . $e->getMessage(),
            ], 500);
        }

        if ($response->failed()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal update playlist! Status: ' . $response->status(),
                'debug'   => $response->body(),
            ], 500);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Playlist berhasil diupdate!',
        ]);
    }

    // ================================================================
    // DELETE PLAYLIST
    // ================================================================
    public function deletePlaylist($id)
    {
        try {
            $response = Http::withHeaders([
                'ngrok-skip-browser-warning' => 'true',
                'Accept'                     => 'application/json',
            ])->delete($this->apiStoreUrl . '/' . $id);
        } catch (\Exception $e) {
            Log::error('deletePlaylist exception', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Koneksi ke API gagal: ' . $e->getMessage(),
            ], 500);
        }

        if ($response->failed()) {
            Log::error('deletePlaylist API error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menghapus playlist! Status: ' . $response->status(),
                'debug'   => $response->body(),
            ], 500);
        }

        return response()->json([
            'status'  => 'success',
            'message' => $response->json()['message'] ?? 'Playlist berhasil dihapus!',
        ], 200);
    }
}