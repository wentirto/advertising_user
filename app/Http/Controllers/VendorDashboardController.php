<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Invoice; // Cuma pakai Invoice, ContentPricing udah dibuang

class VendorDashboardController extends Controller
{
    private string $apiBase = 'https://snowsuit-envision-growing.ngrok-free.dev/api/vendors';
    private string $apiStoreUrl = 'https://snowsuit-envision-growing.ngrok-free.dev/api/playlist/store';
    private string $storageDomain = 'https://snowsuit-envision-growing.ngrok-free.dev';

    private function apiGet(): array
    {
        try {
            $response = Http::withHeaders([
                'ngrok-skip-browser-warning' => 'true',
                'Accept'                     => 'application/json',
            ])->timeout(30)->get($this->apiBase);

            if ($response->failed()) {
                Log::error('apiGet failed', ['status' => $response->status(), 'body' => $response->body()]);
                return [];
            }
            $json = $response->json();
            return is_array($json) && isset($json['data']) ? $json['data'] : [];
        } catch (\Exception $e) {
            Log::error('apiGet exception', ['error' => $e->getMessage()]);
            return [];
        }
    }

    private function buildFileUrl(string $filePath): string
    {
        $filePath = ltrim($filePath, '/');
        if (str_starts_with($filePath, 'http')) {
            $fullUrl = $filePath;
        } elseif (str_starts_with($filePath, 'storage/')) {
            $fullUrl = rtrim($this->storageDomain, '/') . '/' . $filePath;
        } else {
            $fullUrl = rtrim($this->storageDomain, '/') . '/storage/' . $filePath;
        }
        return route('file.proxy', ['path' => $fullUrl]);
    }

    public function fileProxy(Request $request)
    {
        $path = $request->query('path');
        if (!$path || !str_starts_with($path, $this->storageDomain)) {
            abort(404, 'Path file tidak valid');
        }

        $upstreamHeaders = ['ngrok-skip-browser-warning' => 'true'];
        if ($request->hasHeader('Range')) $upstreamHeaders['Range'] = $request->header('Range');

        try {
            $upstream = Http::withHeaders($upstreamHeaders)
                ->withOptions(['stream' => true, 'connect_timeout' => 30, 'timeout' => 0])
                ->get($path);
        } catch (\Exception $e) {
            abort(502, 'Gagal ambil file dari server vendor');
        }

        $status = $upstream->status();
        if ($status >= 400) abort($status, 'File tidak ditemukan di server vendor');

        $respHeaders = [
            'Content-Type'        => $upstream->header('Content-Type') ?: 'application/octet-stream',
            'Accept-Ranges'       => $upstream->header('Accept-Ranges') ?: 'bytes',
            'Cache-Control'       => 'public, max-age=86400',
            'X-Accel-Buffering'   => 'no',
        ];
        if ($cl = $upstream->header('Content-Length')) $respHeaders['Content-Length'] = $cl;
        if ($cr = $upstream->header('Content-Range'))  $respHeaders['Content-Range']  = $cr;

        $body = $upstream->toPsrResponse()->getBody();
        return response()->stream(function () use ($body) {
            set_time_limit(0);
            while (!$body->eof()) {
                echo $body->read(65536);
                if (ob_get_level() > 0) ob_flush();
                flush();
            }
            $body->close();
        }, $status, $respHeaders);
    }

    private function resolvePlaylistContents(int $playlistId, array $contents, array $playlistItems): array
    {
        $contentsById = collect($contents)->keyBy('id');
        $items = collect($playlistItems)->filter(fn($item) => (int)($item['playlist_id'] ?? 0) === $playlistId)->values();

        return $items->map(function ($item) use ($contentsById) {
            $contentId = (int)($item['content_id'] ?? 0);
            $content   = $contentsById->get($contentId);
            $fileUrl = ($content && !empty($content['file'])) ? $this->buildFileUrl($content['file']) : null;
            $days = is_string($item['days'] ?? []) ? json_decode($item['days'], true) : ($item['days'] ?? []);

            return array_merge($item, [
                'content'    => $content,
                'content_id' => $contentId,
                'file_url'   => $fileUrl,
                'file_type'  => $content['type'] ?? null,
                'days'       => is_array($days) ? $days : [],
            ]);
        })->all();
    }

    private function enrichPlaylists(array $playlists, array $contents, array $playlistItems): array
    {
        return array_map(function ($playlist) use ($contents, $playlistItems) {
            $playlist['resolved_items'] = $this->resolvePlaylistContents((int)($playlist['id'] ?? 0), $contents, $playlistItems);
            return $playlist;
        }, $playlists);
    }

    private function snapSatuan(int $val): int
    {
        if ($val < 1) return 15;
        $snapped = (int) round($val / 15) * 15;
        return $snapped < 15 ? 15 : $snapped;
    }

   // ================================================================
    // HELPER: Hitung invoice berdasarkan slot waktu
    // ================================================================
    private function hitungInvoiceSlot(
        ?string $startTime,
        ?string $endTime,
        ?string $startContract,
        ?string $endContract,
        int $durasiKontenDetik,
        int $satuanDetik,
        float $hargaSatuan 
    ): array {
        $toMinutes = function (?string $t): int {
            if (!$t) return 0;
            $parts = explode(':', $t);
            return (int)($parts[0] ?? 0) * 60 + (int)($parts[1] ?? 0);
        };

        $diffMin = $toMinutes($endTime) - $toMinutes($startTime);
        if ($diffMin <= 0) $diffMin += 1440;

        $jumlahHari = 1;
        if ($startContract && $endContract) {
            try {
                $d1 = Carbon::parse($startContract)->startOfDay();
                $d2 = Carbon::parse($endContract)->startOfDay();
                $jumlahHari = max(1, (int) $d1->diffInDays($d2) + 1);
            } catch (\Exception $e) {
                $jumlahHari = 1;
            }
        }

        $totalSlotDetik = $diffMin * 60 * $jumlahHari;
        $satuanFix   = $this->snapSatuan($satuanDetik);
        
        // PERUBAHAN HARGA: Dari 45000 jadi 50000
        $hargaSatuan = 50000; 

        $jumlahBlok  = (int) ceil($totalSlotDetik / $satuanFix);
        $subTotal    = $jumlahBlok * $hargaSatuan; 

        $jumlahPutar = ($durasiKontenDetik > 0) ? round($totalSlotDetik / $durasiKontenDetik, 2) : 0;

        return [
            'total_slot_detik'       => $totalSlotDetik,
            'jumlah_hari'            => $jumlahHari,
            'satuan_detik'           => $satuanFix,
            'harga_satuan'           => $hargaSatuan,
            'jumlah_blok'            => $jumlahBlok,
            'jumlah_putar'           => $jumlahPutar,
            'durasi_tayang_detik'    => $totalSlotDetik,
            'jumlah_tayang_per_hari' => $jumlahPutar / max(1, $jumlahHari),
            'sub_total'              => round($subTotal, 2),
        ];
    }
    public function dashboard()
    {
        $data = $this->apiGet();
        $playlists = $this->enrichPlaylists($data['playlists'] ?? [], $data['contents'] ?? [], $data['playlist_items'] ?? []);
        return view('dashboard', [
            'playlists'      => $playlists,
            'vendors'        => $data['vendors']  ?? [],
            'contents'       => $data['contents'] ?? [],
            'devices'        => $data['devices']  ?? [],
            'playlist_items' => $data['playlist_items'] ?? [],
        ]);
    }
    
    public function permintaanIndex()
    {
        return view('permintaan');
    }

    public function playlistIndex()
    {
        $data = $this->apiGet();
        $playlists = $this->enrichPlaylists($data['playlists'] ?? [], $data['contents'] ?? [], $data['playlist_items'] ?? []);
        return view('playlist', [
            'playlists'      => $playlists,
            'vendors'        => $data['vendors']  ?? [],
            'contents'       => $data['contents'] ?? [],
            'devices'        => $data['devices']  ?? [],
            'play' => $data['playlist_items'] ?? [],
        ]);
    }
    
    public function getPlaylistDetail($id)
    {
        $data = $this->apiGet();
        if (empty($data)) return response()->json(['status' => 'error', 'message' => 'Gagal ambil data'], 500);

        $playlist = collect($data['playlists'] ?? [])->firstWhere('id', (int)$id);
        if (!$playlist) return response()->json(['status' => 'error', 'message' => 'Playlist tidak ditemukan'], 404);

        $playlist['resolved_items'] = $this->resolvePlaylistContents((int)$id, $data['contents'] ?? [], $data['playlist_items'] ?? []);
        $playlist['device'] = collect($data['devices'] ?? [])->firstWhere('id', (int)($playlist['device_id'] ?? 0));
        $playlist['vendor'] = collect($data['vendors'] ?? [])->firstWhere('id', (int)($playlist['vendor_id'] ?? $playlist['cust_id'] ?? 0));

        return response()->json(['status' => 'success', 'data' => $playlist]);
    }
    // ================================================================
    // GET DEVICE SCHEDULE (Ambil Jadwal Device buat Step 2)
    // ================================================================
    public function getDeviceSchedule($deviceId)
    {
        try {
            $data = $this->apiGet();
            
            if (empty($data)) {
                return response()->json([
                    'status' => 'error', 
                    'message' => 'API vendor kosong atau offline'
                ], 500);
            }

            $device = collect($data['devices'] ?? [])->firstWhere('id', (int)$deviceId);
            
            if (!$device) {
                return response()->json([
                    'status' => 'error', 
                    'message' => 'Device tidak ditemukan'
                ], 404);
            }

            // Cari playlist yang nyangkut di device ini
            $devicePlaylists = collect($data['playlists'] ?? [])
                ->filter(fn($p) => (int)($p['device_id'] ?? 0) === (int)$deviceId)
                ->values();

            // Ekstrak jadwal dari playlist yang ada
            $bookings = $devicePlaylists->map(function ($p) use ($data) {
                $items = $this->resolvePlaylistContents((int)($p['id'] ?? 0), $data['contents'] ?? [], $data['playlist_items'] ?? []);
                
                $starts = collect($items)->pluck('start_time')->filter()->values();
                $ends   = collect($items)->pluck('end_time')->filter()->values();
                
                return [
                    'playlist_id'    => $p['id'] ?? null,
                    'playlist_name'  => $p['playlist_name'] ?? 'Tanpa Nama',
                    'start_contract' => $p['start_contract'] ?? null,
                    'end_contract'   => $p['end_contract']   ?? null,
                    'start_time'     => $starts->min() ?? '00:00',
                    'end_time'       => $ends->max()   ?? '24:00',
                ];
            })->filter(fn($b) => !empty($b['start_contract']) && !empty($b['end_contract']))->values();

            return response()->json([
                'status' => 'success', 
                'device' => $device, 
                'bookings' => $bookings
            ]);

        } catch (\Exception $e) {
            // Kalau ada error PHP, paksa balikin jadi JSON biar keliatan di console
            return response()->json([
                'status' => 'error', 
                'message' => 'Server Error: ' . $e->getMessage() . ' (Line: ' . $e->getLine() . ')'
            ], 500);
        }
    }
    public function getDevice($id)
    {
        $data = $this->apiGet();
        $device = collect($data['devices'] ?? [])->firstWhere('id', (int)$id);
        if (!$device) return response()->json(null, 404);
        $vendor = collect($data['vendors'] ?? [])->firstWhere('id', (int)($device['id_vendor'] ?? 0));

        return response()->json([
            'provinsi'       => $device['provinsi'] ?? '-',
            'kota'           => $device['kota'] ?? '-',
            'ip_address'     => $device['ip_address'] ?? '-',
            'url'            => $device['url'] ?? '-',
            'r_width'        => $device['r_width'] ?? '',
            'r_height'       => $device['r_height'] ?? '',
            'vendor'         => $vendor['nama'] ?? '-',
            'nego_value'     => $device['nego_value'] ?? null,
            'price'          => $device['price'] ?? null,
            'contract_value' => $device['contract_value'] ?? null,
        ]);
    }

    public function getDevicesByVendor($vendor_id)
    {
        $data = $this->apiGet();
        $devices = collect($data['devices'] ?? [])->filter(fn($d) => (int)($d['id_vendor'] ?? 0) === (int)$vendor_id)->values();
        return response()->json($devices);
    }

   // ================================================================
    // GET AVAILABLE LOCATIONS
    // ================================================================
    public function getAvailableLocations()
    {
        $data = $this->apiGet();

        // Balikin array kosong kalau API temen lu lagi down/kosong
        if (empty($data)) {
            return response()->json([]);
        }

        $devices = collect($data['devices'] ?? [])->map(function ($d) {
            
            $thumbnail = null;
            if (!empty($d['image'])) {
                $imgData = $d['image'];
                
                // Looping decode buat ngakalin JSON temen lu yang ke-encode berkali-kali
                while (is_string($imgData)) {
                    $decoded = json_decode($imgData, true);
                    if (json_last_error() === JSON_ERROR_NONE && !is_numeric($decoded)) {
                        $imgData = $decoded;
                    } else {
                        $trimmed = trim($imgData, "'\"");
                        if ($trimmed !== $imgData) {
                            $imgData = $trimmed;
                        } else {
                            break;
                        }
                    }
                }

                if (is_array($imgData) && count($imgData) > 0) {
                    $thumbnail = $imgData[0]; // Ambil URL gambar pertama
                } elseif (is_string($imgData)) {
                    $thumbnail = $imgData; // Kalau isinya cuma string 1 link
                }
                
                // PERBAIKAN: Bungkus pakai buildFileUrl biar ngambil langsung dari server temen lu
                if ($thumbnail) {
                    $thumbnail = $this->buildFileUrl($thumbnail);
                }
            }

            return [
                'id'          => $d['id'] ?? null,
                'device_name' => $d['device_name'] ?? 'Tanpa Nama',
                'provinsi'    => $d['provinsi'] ?? '-',
                'kota'        => $d['kota'] ?? '-',
                'r_width'     => $d['r_width']  ?? null,
                'r_height'    => $d['r_height'] ?? null,
                'thumbnail'   => $thumbnail, 
            ];
        })->filter(function ($d) {
            return !is_null($d['id']);
        })->values();

        return response()->json($devices);
    }
    public function getPlaylistsByDevice(int $deviceId)
    {
        $data = $this->apiGet();
        $device = collect($data['devices'] ?? [])->first(fn($d) => (string)($d['id'] ?? '') === (string)$deviceId);
        if (!$device) return response()->json(['status' => 'error'], 404);

        $isPaid = fn(mixed $status): bool => in_array(strtolower((string)($status ?? '')), ['paid', 'approved'], true);
        $result = collect($data['playlists'] ?? [])
            ->filter(fn($p) => (string)($p['device_id'] ?? '') === (string)$deviceId && !$isPaid($p['status'] ?? null))
            ->values()
            ->map(function ($p) use ($data) {
                $items  = $this->resolvePlaylistContents((int)($p['id'] ?? 0), $data['contents'] ?? [], $data['playlist_items'] ?? []);
                return [
                    'id'             => $p['id'] ?? null,
                    'playlist_name'  => $p['playlist_name'] ?? '-',
                    'start_contract' => $p['start_contract'] ?? null,
                    'end_contract'   => $p['end_contract'] ?? null,
                    'contract_value' => $p['contract_value'] ?? 0,
                    'status'         => 'unpaid',
                ];
            });

        return response()->json(['status' => 'success', 'playlists' => $result]);
    }

    public function storePlaylist(Request $request)
    {
        $request->validate([
            'playlist_name'  => 'required|string|max:255',
            'device_id'      => 'required',
            'start_contract' => 'required|date',
            'end_contract'   => 'required|date|after_or_equal:start_contract',
        ]);

        $apiData = $this->apiGet();
        $devices = $apiData['devices'] ?? [];
        $device = collect($devices)->first(fn($d) => (string)($d['id'] ?? '') === (string)$request->device_id);

        if (!$device) return response()->json(['status' => 'error', 'message' => 'Device tidak ditemukan!'], 422);

        $vendorId = $device['id_vendor'] ?? $device['vendor_id'] ?? null;
        $files = $request->file('file') ?? [];
        if (!is_array($files)) $files = [$files];
        $validFiles = array_values(array_filter($files, fn($f) => $f instanceof \Illuminate\Http\UploadedFile && $f->isValid()));

        if (empty($validFiles)) return response()->json(['status' => 'error', 'message' => 'Minimal upload 1 file!'], 422);

        $startTime     = $request->input('start_time', '00:00');
        $endTime       = $request->input('end_time', '00:00');
        $startContract = $request->start_contract;
        $endContract   = $request->end_contract;
        $durasiKonten  = (array)$request->input('content_duration_seconds', []);
        $priceUnits    = (array)$request->input('price_unit', []);
        $custId        = (string)($request->cust_id ?: $vendorId ?: '1');

        // PERBAIKAN 2: Lempar harga asli device ke fungsi hitung
        $devicePrice = (float)($device['price'] ?? 0); 
        
        $grandTotal = 0;
        $pricingPerIndex = [];
        foreach ($validFiles as $i => $file) {
            $calc = $this->hitungInvoiceSlot(
                $startTime, $endTime, $startContract, $endContract,
                (int)($durasiKonten[$i] ?? 0),
                (int)($priceUnits[$i] ?? 15),
                $devicePrice 
            );
            $pricingPerIndex[$i] = $calc;
        }

        $grandTotal = !empty($pricingPerIndex) ? $pricingPerIndex[0]['sub_total'] : 0;
        $contractValue = $grandTotal > 0 ? $grandTotal : (float)$request->input('contract_value', 0);

        $http = Http::withHeaders(['ngrok-skip-browser-warning' => 'true', 'Accept' => 'application/json'])->asMultipart()
            ->attach('playlist_name', $request->playlist_name)->attach('cust_id', $custId)
            ->attach('vendor_id', (string)$vendorId)->attach('device_id', (string)$request->device_id)
            ->attach('start_contract', $request->start_contract)->attach('end_contract', $request->end_contract)
            ->attach('contract_value', (string)$contractValue)->attach('status', 'unpaid')
            ->attach('start_time', $startTime)->attach('end_time', $endTime);

        foreach ($validFiles as $i => $file) {
            $http->attach("start_date[{$i}]", $request->start_contract)->attach("end_date[{$i}]", $request->end_contract);
            $http->attach('file[]', fopen($file->getRealPath(), 'rb'), $file->getClientOriginalName());
        }

        $response = $http->post($this->apiStoreUrl);
        if ($response->failed()) return response()->json(['status' => 'error', 'message' => 'API error!'], 500);

        $responseData = $response->json();
        $newPlaylistId = $responseData['data']['playlist']['id'] ?? $responseData['playlist_id'] ?? null;
        $createdContents = $responseData['data']['contents'] ?? $responseData['contents'] ?? [];

        // PERBAIKAN 3: Simpan cuma ke Invoice lokal (sesuai db MySQL temen lu)
        if (isset($newPlaylistId)) {
            try {
                Invoice::updateOrCreate(
                    ['playlist_id' => $newPlaylistId],
                    [
                        'vendor_id'   => $vendorId,
                        'customer_id' => $custId,
                        'device_id'   => $request->device_id,
                        'content_id'  => $createdContents[0]['id'] ?? null,
                        'amount'      => $contractValue,
                        'status'      => 'unpaid',
                    ]
                );
            } catch (\Exception $e) {
                Log::warning('Gagal simpan invoice lokal', ['error' => $e->getMessage()]);
            }
        }

        return response()->json(['status' => 'success', 'data' => $responseData], 201);
    }

    public function updatePlaylist(Request $request, $id)
    {
        $payload = $request->only(['playlist_name', 'device_id', 'start_contract', 'end_contract', 'contract_value', 'status']);
        $response = Http::withHeaders(['ngrok-skip-browser-warning' => 'true'])->put($this->apiStoreUrl . '/' . $id, $payload);
        if ($response->failed()) return response()->json(['status' => 'error'], 500);
        return response()->json(['status' => 'success']);
    }

    public function deletePlaylist($id)
    {
        $response = Http::withHeaders(['ngrok-skip-browser-warning' => 'true'])->delete($this->apiStoreUrl . '/' . $id);
        if ($response->failed()) return response()->json(['status' => 'error'], 500);

        // PERBAIKAN 4: Ganti hapus dr tabel invoice lokal (bukan ContentPricing)
        Invoice::where('playlist_id', $id)->delete();
        return response()->json(['status' => 'success'], 200);
    }

    public function invoice($id)
    {
        $data = $this->apiGet();
        if (empty($data)) abort(500, 'Gagal ambil data API');

        $playlist = collect($data['playlists'] ?? [])->firstWhere('id', (int)$id);
        if (!$playlist) abort(404, 'Playlist tidak ditemukan');

        $resolvedItems = $this->resolvePlaylistContents((int)$id, $data['contents'] ?? [], $data['playlist_items'] ?? []);
        $device   = collect($data['devices'] ?? [])->firstWhere('id', (int)($playlist['device_id'] ?? 0));
        $vendor   = collect($data['vendors'] ?? [])->firstWhere('id', (int)($playlist['vendor_id'] ?? 0));
        $customer = collect($data['customers'] ?? [])->firstWhere('id', (int)($playlist['cust_id'] ?? 0));

        // PERBAIKAN 5: Hitung Subtotal On-the-fly karena udah gak pake ContentPricing DB
        $devicePrice = (float)($device['price'] ?? 0);
        $items = collect($resolvedItems)->values()->map(function ($item) use ($playlist, $devicePrice) {
            $calc = $this->hitungInvoiceSlot(
                $item['start_time'] ?? null,
                $item['end_time'] ?? null,
                $item['start_date'] ?? $playlist['start_contract'] ?? null,
                $item['end_date'] ?? $playlist['end_contract'] ?? null,
                15, 15, $devicePrice
            );
            // Bikin object buatan supaya file blade invoice lu gak usah diotak-atik lagi
            $item['pricing'] = (object) ['sub_total' => $calc['sub_total']];
            return $item;
        });

        // Ambil Data Invoice dr tabel lokal
        $invoiceData = Invoice::where('playlist_id', $id)->first();
        if ($invoiceData && $invoiceData->amount > 0) {
            $grandTotal = $invoiceData->amount;
        } else {
            // Backup kalau database invoice lokal kosong
            $grandTotal = $items->sum(fn($i) => $i['pricing']->sub_total);
            if ($grandTotal <= 0) $grandTotal = (float)($playlist['contract_value'] ?? 0);
        }

        $statusRaw = $invoiceData ? $invoiceData->status : strtolower((string)($playlist['status'] ?? ''));
        $isPaid    = in_array($statusRaw, ['paid', 'approved', '1'], true);
        
        return view('invoice', [
            'playlist'     => $playlist,
            'items'        => $items,
            'grand_total'  => $grandTotal,
            'invoice_no'   => 'INV-' . str_pad((string)($invoiceData ? $invoiceData->id : $id), 5, '0', STR_PAD_LEFT),
            'status'       => $isPaid ? 'PAID' : 'UNPAID',
            'is_paid'      => $isPaid,
            'vendor'       => $vendor,
            'customer'     => $customer,
            'device'       => $device,
        ]);
    }
}