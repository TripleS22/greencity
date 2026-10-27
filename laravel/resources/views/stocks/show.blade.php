@extends('layouts.app')
@section('title', 'Detail Stock')
@section('breadcrumb', 'Stock > Detail')
@section('content')
<div class="page-header">
    <h1><i class="fas fa-boxes"></i> Detail & History: {{ $stock->item_name }}</h1>
    <a href="{{ route('stocks.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
</div>

<div style="display: flex; gap: 20px; flex-wrap: wrap;">
    <!-- Informasi Stock -->
    <div class="card" style="flex: 1; min-width: 300px;">
        <div class="card-header"><i class="fas fa-info-circle"></i> Informasi Stock</div>
        <div class="card-body">
            <table style="width: 100%; border-collapse: collapse;">
                <tr><td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Item Name</strong></td><td style="padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;">{{ $stock->item_name }}</td></tr>
                <tr><td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Unit / Tipe QTY</strong></td><td style="padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;">{{ $stock->unit_type ?? '-' }}</td></tr>
                <tr><td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Unit Price</strong></td><td style="padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;">Rp {{ number_format($stock->unit_price, 0, ',', '.') }}</td></tr>
                <tr><td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Location</strong></td><td style="padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;">{{ $stock->location->name ?? '-' }}</td></tr>
                <tr><td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Minimum Limit</strong></td><td style="padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;">{{ $stock->minimum_limit }}</td></tr>
                <tr><td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Total Stock In</strong></td><td style="padding: 8px 0; border-bottom: 1px solid #eee; text-align: right; color: green;">{{ $stock->stock_in }}</td></tr>
                <tr><td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Total Stock Out</strong></td><td style="padding: 8px 0; border-bottom: 1px solid #eee; text-align: right; color: red;">{{ $stock->stock_out }}</td></tr>
                <tr><td style="padding: 8px 0; border-bottom: 1px solid #eee; font-size: 1.1em;"><strong>Current Stock</strong></td><td style="padding: 8px 0; border-bottom: 1px solid #eee; text-align: right; font-size: 1.1em;"><strong class="{{ $stock->total_stock <= $stock->minimum_limit ? 'badge badge-urgent' : '' }}">{{ $stock->total_stock }}</strong></td></tr>
            </table>
        </div>
    </div>

    <!-- Riwayat Stock -->
    <div class="card" style="flex: 2; min-width: 300px;">
        <div class="card-header"><i class="fas fa-history"></i> Riwayat Pergerakan Stock</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="datatable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tanggal</th>
                            <th>Tipe</th>
                            <th>Jumlah</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($histories as $history)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($history->date)->format('d M Y') }}</td>
                            <td>
                                <span class="badge {{ $history->type == 'in' ? 'badge-closed' : 'badge-high' }}">
                                    {{ strtoupper($history->type) }}
                                </span>
                            </td>
                            <td><strong>{{ $history->quantity }} {{ $stock->unit_type ?? '' }}</strong></td>
                            <td>{{ $history->notes ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #999; padding: 30px;">Belum ada riwayat stock untuk item ini</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
