<?php

namespace App\Imports;

use App\Models\Inventory;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class InventoryImport implements ToCollection, WithHeadingRow
{
    protected array $mapping;
    protected int $imported = 0;
    protected int $skipped = 0;
    protected array $errors = [];

    /**
     * @param array $mapping ['db_column' => 'excel_column_header']
     */
    public function __construct(array $mapping)
    {
        $this->mapping = $mapping;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            try {
                $data = [];

                foreach ($this->mapping as $dbColumn => $excelColumn) {
                    if (empty($excelColumn) || $excelColumn === '__skip__') {
                        continue;
                    }
                    // Normalize the excel column key (maatwebsite lowercases + underscores headers)
                    $normalizedKey = $this->normalizeHeader($excelColumn);
                    $value = $row[$normalizedKey] ?? null;
                    $data[$dbColumn] = $value;
                }

                // item_sparepart is required
                if (empty($data['item_sparepart'])) {
                    $this->skipped++;
                    continue;
                }

                // Pastikan semua field numerik selalu punya nilai (default 0, tidak boleh null)
                $numericFields = [
                    'pcs_ltl', 'price_per_unit', 'selling_price', 'service_charge',
                    'margin', 'begins', 'in', 'return_in', 'out', 'return_out',
                ];
                foreach ($numericFields as $field) {
                    $val = $data[$field] ?? null;
                    $data[$field] = (is_numeric($val) && $val !== '') ? (float) $val : 0;
                }

                Inventory::create($data);
                $this->imported++;
            } catch (\Exception $e) {
                $this->skipped++;
                $this->errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
            }
        }
    }

    public function getImported(): int
    {
        return $this->imported;
    }

    public function getSkipped(): int
    {
        return $this->skipped;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Normalize header to match maatwebsite/excel's internal key format.
     * They lowercase and replace spaces/special chars with underscores.
     */
    private function normalizeHeader(string $header): string
    {
        $header = mb_strtolower(trim($header));
        $header = preg_replace('/\s+/', '_', $header);
        $header = preg_replace('/[^a-z0-9_]/', '', $header);
        return $header;
    }
}
