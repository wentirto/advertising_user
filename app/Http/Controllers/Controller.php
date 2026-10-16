<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Carbon\Carbon;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Hitung pricing invoice berdasarkan slot waktu tayang.
     */
    protected function hitungInvoiceSlot(
        ?string $startTime,
        ?string $endTime,
        ?string $startContract,
        ?string $endContract,
        int $durasiKontenDetik
    ): array {
        $toMinutes = fn(?string $t) => $t
            ? (int)(explode(':', $t)[0]) * 60 + (int)(explode(':', $t)[1] ?? 0)
            : 0;

        $diffMin = $toMinutes($endTime) - $toMinutes($startTime);
        if ($diffMin <= 0) $diffMin += 1440;

        $jumlahHari = 1;
        if ($startContract && $endContract) {
            try {
                $jumlahHari = max(
                    1,
                    (int) Carbon::parse($startContract)->startOfDay()
                        ->diffInDays(Carbon::parse($endContract)->startOfDay()) + 1
                );
            } catch (\Exception $e) {
                $jumlahHari = 1;
            }
        }

        $totalSlotDetik = $diffMin * 60 * $jumlahHari;

        // Harga per tayang = blok 15 detik x 50.000.
        // Durasi konten dibulatkan ke atas 15 detik minimal (biar detik < 15
        // nggak dihitung sangat kecil sehingga jumlah putaran meledak).
        // Jumlah putaran = slot total dibagi durasi konten, minimum 1 kali
        // (biar konten lebih panjang dari slot nggak langsung 0).
        $durasiFix         = max($durasiKontenDetik, 15);
        $blok              = ceil($durasiFix / 15);
        $hargaSatuKaliTayang = $blok * 50000;
        $jumlahPutar       = max(1, floor($totalSlotDetik / $durasiFix));
        $subTotal          = $hargaSatuKaliTayang * $jumlahPutar;

        return [
            'total_slot_detik'       => $totalSlotDetik,
            'jumlah_hari'            => $jumlahHari,
            'satuan_detik'           => 15,
            'harga_satuan'           => 50000,
            'jumlah_blok'            => $blok,
            'jumlah_putar'           => $jumlahPutar,
            'durasi_tayang_detik'    => $totalSlotDetik,
            'jumlah_tayang_per_hari' => $jumlahHari > 0 ? ($jumlahPutar / $jumlahHari) : 0,
            'sub_total'              => round($subTotal, 2),
        ];
    }
}
