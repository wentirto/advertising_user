<?php
namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected string $apiBase = 'https://snowsuit-envision-growing.ngrok-free.dev/api/vendors';
    protected string $apiStoreUrl = 'https://snowsuit-envision-growing.ngrok-free.dev/api/playlist/store';
    protected string $storageDomain = 'https://snowsuit-envision-growing.ngrok-free.dev';

    protected function apiGet(): array
    {
        try {
            $response = Http::withHeaders(['ngrok-skip-browser-warning' => 'true', 'Accept' => 'application/json'])
                ->timeout(30)->get($this->apiBase);
            if ($response->failed()) return [];
            $json = $response->json();
            return is_array($json) && isset($json['data']) ? $json['data'] : [];
        } catch (\Exception $e) { return []; }
    }

    protected function buildFileUrl(string $filePath): string
    {
        $filePath = ltrim($filePath, '/');
        if (str_starts_with($filePath, 'http')) return $filePath;
        $fullUrl = rtrim($this->storageDomain, '/') . (str_starts_with($filePath, 'storage/') ? '/' : '/storage/') . $filePath;
        return route('file.proxy', ['path' => $fullUrl]);
    }

    protected function resolvePlaylistContents(int $playlistId, array $contents, array $playlistItems): array
    {
        $contentsById = collect($contents)->keyBy('id');
        return collect($playlistItems)->filter(fn($item) => (int)($item['playlist_id'] ?? 0) === $playlistId)->values()
            ->map(function ($item) use ($contentsById) {
                $content = $contentsById->get((int)($item['content_id'] ?? 0));
                $days = is_string($item['days'] ?? []) ? json_decode($item['days'], true) : ($item['days'] ?? []);
                return array_merge($item, [
                    'content' => $content, 'content_id' => (int)($item['content_id'] ?? 0),
                    'file_url' => ($content && !empty($content['file'])) ? $this->buildFileUrl($content['file']) : null,
                    'file_type' => $content['type'] ?? null, 'days' => is_array($days) ? $days : [],
                ]);
            })->all();
    }

    protected function enrichPlaylists(array $playlists, array $contents, array $playlistItems): array
    {
        return array_map(function ($playlist) use ($contents, $playlistItems) {
            $playlist['resolved_items'] = $this->resolvePlaylistContents((int)($playlist['id'] ?? 0), $contents, $playlistItems);
            return $playlist;
        }, $playlists);
    }

    protected function hitungInvoiceSlot(?string $startTime, ?string $endTime, ?string $startContract, ?string $endContract, int $durasiKontenDetik): array
    {
        $toMinutes = fn(?string $t) => $t ? (int)(explode(':', $t)[0]) * 60 + (int)(explode(':', $t)[1] ?? 0) : 0;
        $diffMin = $toMinutes($endTime) - $toMinutes($startTime);
        if ($diffMin <= 0) $diffMin += 1440;

        $jumlahHari = 1;
        if ($startContract && $endContract) {
            try { $jumlahHari = max(1, (int) Carbon::parse($startContract)->startOfDay()->diffInDays(Carbon::parse($endContract)->startOfDay()) + 1); } 
            catch (\Exception $e) { $jumlahHari = 1; }
        }

        $totalSlotDetik = $diffMin * 60 * $jumlahHari;
        $durasiFix = $durasiKontenDetik > 0 ? $durasiKontenDetik : 15;
        $blok = ceil($durasiFix / 15);
        $hargaSatuKaliTayang = $blok * 50000;
        $jumlahPutar = floor($totalSlotDetik / $durasiFix);
        $subTotal = $hargaSatuKaliTayang * $jumlahPutar;

        return [
            'total_slot_detik' => $totalSlotDetik, 'jumlah_hari' => $jumlahHari,
            'satuan_detik' => 15, 'harga_satuan' => 50000, 'jumlah_blok' => $blok,
            'jumlah_putar' => $jumlahPutar, 'durasi_tayang_detik' => $totalSlotDetik,
            'jumlah_tayang_per_hari' => $jumlahHari > 0 ? ($jumlahPutar / $jumlahHari) : 0,
            'sub_total' => round($subTotal, 2),
        ];
    }
}