<?php

namespace App\Http\Controllers;

use App\Imports\InventoryImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class InventoryImportController extends Controller
{
    /**
     * Handle file upload: store file, read headers, store in session, redirect to mapping.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ], [
            'excel_file.required' => 'Pilih file Excel terlebih dahulu.',
            'excel_file.mimes'    => 'File harus berformat .xlsx, .xls, atau .csv.',
            'excel_file.max'      => 'Ukuran file maksimum 10MB.',
        ]);

        // Store file
        $path = $request->file('excel_file')->store('imports/inventory', 'local');
        $fullPath = Storage::disk('local')->path($path);

        try {
            $spreadsheet = IOFactory::load($fullPath);
            $sheet = $spreadsheet->getActiveSheet();
            $highestColumn = $sheet->getHighestColumn();
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

            // Read headers
            $headers = [];
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $cellValue = $sheet->getCellByColumnAndRow($col, 1)->getValue();
                if ($cellValue !== null && $cellValue !== '') {
                    $headers[] = (string) $cellValue;
                }
            }

            // Preview rows (2-4)
            $preview = [];
            $highestRow = min($sheet->getHighestRow(), 4);
            for ($row = 2; $row <= $highestRow; $row++) {
                $rowData = [];
                for ($col = 1; $col <= count($headers); $col++) {
                    $rowData[] = (string) ($sheet->getCellByColumnAndRow($col, $row)->getValue() ?? '');
                }
                $preview[] = $rowData;
            }

            // Store to session
            session([
                'inv_import_path'    => $path,
                'inv_import_headers' => $headers,
                'inv_import_preview' => $preview,
            ]);

            return redirect()->route('filament.admin.resources.inventories.import');

        } catch (\Exception $e) {
            Storage::disk('local')->delete($path);
            return back()->withErrors(['excel_file' => 'Gagal membaca file: ' . $e->getMessage()]);
        }
    }

    /**
     * Process the import with user-defined mapping.
     */
    public function import(Request $request)
    {
        $path = session('inv_import_path');
        if (!$path) {
            return redirect()->route('filament.admin.resources.inventories.import')
                ->withErrors(['general' => 'Session expired. Upload ulang file.']);
        }

        $mapping = $request->input('mapping', []);
        $fullPath = Storage::disk('local')->path($path);

        if (!file_exists($fullPath)) {
            return redirect()->route('filament.admin.resources.inventories.import')
                ->withErrors(['general' => 'File tidak ditemukan. Upload ulang.']);
        }

        $importObj = new InventoryImport($mapping);

        try {
            Excel::import($importObj, $fullPath);
            Storage::disk('local')->delete($path);
            session()->forget(['inv_import_path', 'inv_import_headers', 'inv_import_preview']);

            session([
                'inv_import_result' => [
                    'imported' => $importObj->getImported(),
                    'skipped'  => $importObj->getSkipped(),
                    'errors'   => $importObj->getErrors(),
                ],
            ]);

            return redirect()->route('filament.admin.resources.inventories.import');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['general' => 'Import gagal: ' . $e->getMessage()]);
        }
    }

    /**
     * Clear import session.
     */
    public function clear()
    {
        session()->forget(['inv_import_path', 'inv_import_headers', 'inv_import_preview', 'inv_import_result']);
        return redirect()->route('filament.admin.resources.inventories.import');
    }
}
