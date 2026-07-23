<?php
namespace App\Http\Controllers;

use App\Models\Invoice;

class InvoiceController extends Controller
{
    public function invoice($id)
    {
        $data = $this->apiGet();
        $playlist = collect($data['playlists'] ?? [])->firstWhere('id', (int)$id);
        if (!$playlist) abort(404, 'Playlist tidak ditemukan');

        $resolvedItems = $this->resolvePlaylistContents((int)$id, $data['contents'] ?? [], $data['playlist_items'] ?? []);
        $device   = collect($data['devices'] ?? [])->firstWhere('id', (int)($playlist['device_id'] ?? 0));
        
        $items = collect($resolvedItems)->values()->map(function ($item) use ($playlist) {
            $calc = $this->hitungInvoiceSlot(
                $item['start_time'] ?? null, $item['end_time'] ?? null,
                $item['start_date'] ?? $playlist['start_contract'] ?? null,
                $item['end_date'] ?? $playlist['end_contract'] ?? null, 15
            );
            $item['pricing'] = (object) ['sub_total' => $calc['sub_total']];
            return $item;
        });

        $invoiceData = Invoice::where('playlist_id', $id)->first();
        if ($invoiceData && $invoiceData->amount > 0) {
            $grandTotal = $invoiceData->amount;
        } else {
            $grandTotal = $items->sum(fn($i) => $i['pricing']->sub_total);
            if ($grandTotal <= 0) $grandTotal = (float)($playlist['contract_value'] ?? 0);
        }

        $statusRaw = $invoiceData ? $invoiceData->status : strtolower((string)($playlist['status'] ?? ''));
        $isPaid    = in_array($statusRaw, ['paid', 'approved', '1'], true);
        
        return view('vendor.invoice', [ // Ganti view-nya ke folder vendor
            'playlist' => $playlist, 'items' => $items, 'grand_total' => $grandTotal,
            'invoice_no' => 'INV-' . str_pad((string)($invoiceData ? $invoiceData->id : $id), 5, '0', STR_PAD_LEFT),
            'status' => $isPaid ? 'PAID' : 'UNPAID', 'is_paid' => $isPaid,
            'vendor' => collect($data['vendors'] ?? [])->firstWhere('id', (int)($playlist['vendor_id'] ?? 0)),
            'customer' => collect($data['customers'] ?? [])->firstWhere('id', (int)($playlist['cust_id'] ?? 0)),
            'device' => $device,
        ]);
    }
}