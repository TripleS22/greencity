<div class="dbr-table-wrapper">
    <table class="dbr-table">
        <thead>
            <tr>
                <th style="width: 35% !important;">Komponen</th>
                <th style="width: 10% !important; text-align: center !important;">Jumlah</th>
                <th style="width: 12% !important; text-align: center !important;">Satuan</th>
                <th style="width: 21% !important; text-align: right !important;">Harga Satuan</th>
                <th style="width: 22% !important; text-align: right !important;">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($getState() ?? [] as $item)
                <tr>
                    <td style="width: 35% !important; text-align: left !important; font-weight: 600;">
                        {{ $item['component'] ?? '-' }}
                    </td>
                    <td style="width: 10% !important; text-align: center !important;">
                        {{ $item['qty'] ?? 0 }}
                    </td>
                    <td style="width: 12% !important; text-align: center !important;">
                        {{ $item['unit'] ?? '-' }}
                    </td>
                    <td style="width: 21% !important; text-align: right !important; font-family: monospace;">
                        <span class="currency-label">IDR</span> {{ number_format((float) ($item['price_per_pcs'] ?? 0), 0, ',', '.') }}
                    </td>
                    <td style="width: 22% !important; text-align: right !important; font-family: monospace; font-weight: bold;" class="text-primary">
                        <span class="currency-label">IDR</span> {{ number_format((float) ($item['total'] ?? 0), 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="empty-state">
                        Tidak ada data suku cadang yang tersedia.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<style>
    .dbr-table-wrapper {
        width: 100%;
        overflow-x: auto;
        border-radius: 0.75rem;
        border: 1px solid #e5e7eb;
        margin-bottom: 1.5rem;
        background-color: #ffffff;
    }

    .dark .dbr-table-wrapper {
        border-color: #374151;
        background-color: #111827;
    }

    .dbr-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        min-width: 800px;
    }

    .dbr-table th {
        background-color: #f9fafb;
        padding: 1rem 1.25rem;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6b7280;
        border-bottom: 1px solid #e5e7eb;
    }

    .dark .dbr-table th {
        background-color: #1f2937;
        color: #9ca3af;
        border-bottom-color: #374151;
    }

    .dbr-table td {
        padding: 1rem 1.25rem;
        font-size: 0.875rem;
        color: #111827;
        border-bottom: 1px solid #f3f4f6;
        vertical-align: middle;
    }

    .dark .dbr-table td {
        color: #f3f4f6;
        border-bottom-color: #1f2937;
    }

    .dbr-table tr:last-child td {
        border-bottom: none;
    }

    .dbr-table tr:hover td {
        background-color: #f9fafb;
    }

    .dark .dbr-table tr:hover td {
        background-color: #1f2937;
    }

    .currency-label {
        font-size: 0.65rem;
        opacity: 0.6;
        margin-right: 2px;
    }

    .text-primary {
        color: #eab308; /* Tema warna gold/filament default */
    }

    .empty-state {
        padding: 3rem !important;
        text-align: center !important;
        font-style: italic;
        color: #9ca3af;
    }
</style>
