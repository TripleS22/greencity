<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Location;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class BarcodeController extends Controller
{
    /**
     * Generate barcode PDF for selected assets, grouped by location (A4).
     */
    public function generatePdf(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'Pilih minimal 1 asset untuk generate barcode.');
        }

        $assets = Asset::with(['location', 'brand', 'deviceModel'])
            ->whereIn('id', $ids)
            ->orderBy('location_id')
            ->orderBy('code_asset')
            ->get()
            ->groupBy(fn($a) => $a->location->name ?? 'Tanpa Lokasi');

        $pdf = Pdf::loadView('barcodes.pdf', compact('assets'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('barcode-labels-a4-' . now()->format('Ymd-His') . '.pdf');
    }

    /**
     * Generate barcode PDF khusus ukuran 58mm (thermal printer).
     * Satu label per baris memuat: QR code + info asset + barcode Code128.
     */
    public function generate58mm(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'Pilih minimal 1 asset untuk generate barcode 58mm.');
        }

        $assets = Asset::with(['location', 'brand', 'deviceModel'])
            ->whereIn('id', $ids)
            ->orderBy('location_id')
            ->orderBy('code_asset')
            ->get()
            ->groupBy(fn($a) => $a->location->name ?? 'Tanpa Lokasi');

        // Ukuran 58mm = 164.41pt  |  panjang auto (9999pt supaya muat semua label)
        $pdf = Pdf::loadView('barcodes.pdf58mm', compact('assets'))
            ->setPaper([0, 0, 164.41, 9999], 'portrait');

        // Hitung total label untuk ukuran halaman yang sesuai:
        // tiap label ±85pt tinggi, header lokasi ~28pt, gap antar label ~3pt
        $totalLabels = collect($assets)->sum(fn($g) => $g->count());
        $totalGroups = collect($assets)->count();
        $pageHeight   = ($totalLabels * 90) + ($totalGroups * 35) + 30;
        $pageHeight   = max($pageHeight, 200); // minimum 200pt

        $pdf = Pdf::loadView('barcodes.pdf58mm', compact('assets'))
            ->setPaper([0, 0, 164.41, $pageHeight], 'portrait');

        return $pdf->stream('barcode-labels-58mm-' . now()->format('Ymd-His') . '.pdf');
    }
}
