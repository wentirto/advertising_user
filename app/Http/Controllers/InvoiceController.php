<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Models\Invoice;

class InvoiceController extends Controller
{
    public function invoice($id)
    {
        $playlist = Playlist::with(['device', 'vendor', 'customer', 'items.content', 'invoice'])
            ->find($id);

        if (!$playlist) {
            abort(404, 'Playlist tidak ditemukan');
        }

        // Map setiap item dengan kalkulasi harga
        $items = $playlist->items->values()->map(function ($item) use ($playlist) {
            $calc = $this->hitungInvoiceSlot(
                $item->start_time ?? null,
                $item->end_time ?? null,
                $item->start_date ?? optional($playlist->start_contract)->toDateString(),
                $item->end_date ?? optional($playlist->end_contract)->toDateString(),
                (int)($item->duration ?? 15)
            );
            $item->pricing  = (object)['sub_total' => $calc['sub_total']];
            return $item;
        });

        // Grand total: prioritas dari invoice lokal, fallback hitung dari items
        $invoiceData = $playlist->invoice;
        if ($invoiceData && $invoiceData->amount > 0) {
            $grandTotal = $invoiceData->amount;
        } else {
            $grandTotal = $items->sum(fn($i) => $i->pricing->sub_total);
            if ($grandTotal <= 0) {
                $grandTotal = (float)($playlist->contract_value ?? 0);
            }
        }

        $statusRaw = $invoiceData ? $invoiceData->status : strtolower((string)($playlist->status ?? ''));
        $isPaid    = in_array($statusRaw, ['paid', 'approved', '1'], true);

        return view('vendor.invoice', [
            'playlist'   => $playlist,
            'items'      => $items,
            'grand_total'=> $grandTotal,
            'invoice_no' => 'INV-' . str_pad((string)($invoiceData ? $invoiceData->id : $id), 5, '0', STR_PAD_LEFT),
            'status'     => $isPaid ? 'PAID' : 'UNPAID',
            'is_paid'    => $isPaid,
            'vendor'     => $playlist->vendor,
            'customer'   => $playlist->customer,
            'device'     => $playlist->device,
        ]);
    }
}
