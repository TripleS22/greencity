@extends('layouts.app')
@section('title', 'E-Money')
@section('breadcrumb', 'Stock > E-Money')

@push('styles')
<style>
.filter-bar{display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end;margin-bottom:16px;padding:14px 16px;background:#f8f9fa;border:1px solid var(--border-color);border-radius:8px;}
.filter-bar .filter-group{display:flex;flex-direction:column;gap:4px;flex:1;min-width:160px;}
.filter-bar .filter-group label{font-size:12px;font-weight:600;color:#555;text-transform:uppercase;letter-spacing:.5px;}
.filter-bar .filter-group input{padding:7px 12px;border:1px solid #ddd;border-radius:6px;font-size:13px;font-family:inherit;outline:none;}
.filter-bar .filter-actions{display:flex;gap:8px;align-items:flex-end;padding-bottom:1px;}
.cb-emoney, #select-all { cursor:pointer; width:15px; height:15px; accent-color:#3a7d34; }
th.th-cb, td.td-cb { width:36px; text-align:center; }
.bulk-toolbar { background:#f0f7ee; border:1px solid #c5dfc0; padding:10px; border-radius:8px; margin-bottom:14px; display:flex; align-items:center; justify-content:space-between; }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fas fa-credit-card"></i> E-Money</h1>
    <a href="{{ route('stocks.emoneys.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah</a>
</div>

<div class="filter-bar">
    <div class="filter-group">
        <label><i class="fas fa-search" style="margin-right:4px"></i>Cari Nomor Body / Kartu</label>
        <input type="text" id="filter-text" placeholder="Ketik nomor body atau nomor kartu..." autocomplete="off">
    </div>
    <div class="filter-actions">
        <button class="btn btn-secondary" id="btn-reset-filter"><i class="fas fa-undo"></i> Reset</button>
    </div>
</div>

{{-- Bulk Action Toolbar --}}
<div class="bulk-toolbar" id="bulk-toolbar" style="display:none;">
    <div style="font-size:13px; font-weight:600; color:#2e6429;">
        <i class="fas fa-check-square"></i> <span id="selected-count">0</span> E-Money dipilih
    </div>
    <button type="button" class="btn btn-info btn-sm" id="btn-bulk-out">
        <i class="fas fa-hand-holding-usd"></i> Bagikan Terpilih
    </button>
</div>

<form id="form-bulk-out" action="{{ route('stocks.emoneys.bulk_out') }}" method="POST" style="display:none;">
    @csrf
    <div id="bulk-inputs"></div>
</form>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="emoney-table" class="datatable">
                <thead>
                    <tr>
                        <th class="th-cb"><input type="checkbox" id="select-all" title="Pilih semua baris"></th>
                        <th style="width:40px">#</th>
                        <th>Status</th>
                        <th>Tanggal Diberikan</th>
                        <th>Nomor Body</th>
                        <th>Nomor Kartu</th>
                        <th>Masa Aktif</th>
                        <th>Keterangan</th>
                        <th style="width:120px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $i)
                    <tr>
                        <td class="td-cb">
                            @if($i->status === 'Tersedia')
                                <input type="checkbox" class="cb-emoney" value="{{ $i->id }}">
                            @else
                                <input type="checkbox" disabled title="Telah dibagikan">
                            @endif
                        </td>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            @if($i->status === 'Tersedia')
                                <span class="badge" style="background-color:#2ecc71; color:white; padding:4px 8px; border-radius:4px; font-size:11px;">Tersedia</span>
                            @else
                                <span class="badge" style="background-color:#e74c3c; color:white; padding:4px 8px; border-radius:4px; font-size:11px;">Telah Diberikan</span>
                            @endif
                        </td>
                        <td>{{ $i->tanggal_diberikan ? $i->tanggal_diberikan->format('d/m/Y') : '-' }}</td>
                        @php
                            $sortOrder = preg_replace_callback('/\d+/', function($m) {
                                return str_pad($m[0], 10, '0', STR_PAD_LEFT);
                            }, $i->nomor_body);
                        @endphp
                        <td data-order="{{ $sortOrder }}"><strong>{{ $i->nomor_body }}</strong></td>
                        <td>{{ $i->nomor_kartu }}</td>
                        <td>{{ $i->masa_aktif ? $i->masa_aktif->format('m/Y') : '-' }}</td>
                        <td>{{ $i->keterangan ?? '-' }}</td>
                        <td style="white-space:nowrap">
                            @if($i->status === 'Tersedia')
                                <a href="{{ route('stocks.emoneys.out', $i) }}" class="btn btn-info btn-sm" title="Bagikan E-Money"><i class="fas fa-hand-holding-usd"></i> Bagikan</a>
                            @endif
                            <a href="{{ route('stocks.emoneys.edit', $i) }}" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('stocks.emoneys.destroy', $i) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus e-money ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    // Ambil object DataTable yang sudah diinisialisasi oleh layout app.blade.php
    var table = $('#emoney-table').DataTable();

    $('#filter-text').on('keyup', function () {
        table.search(this.value).draw();
    });
    $('#btn-reset-filter').on('click', function () {
        $('#filter-text').val('');
        table.search('').draw();
    });

    // ── Bulk Action Logic ──
    function updateBulkToolbar() {
        var count = $('.cb-emoney:checked').length;
        $('#selected-count').text(count);
        if (count > 0) {
            $('#bulk-toolbar').show();
        } else {
            $('#bulk-toolbar').hide();
        }
    }

    $(document).on('change', '.cb-emoney', function() {
        updateBulkToolbar();
    });

    $('#select-all').on('change', function() {
        var isChecked = $(this).is(':checked');
        $('.cb-emoney:not(:disabled)').prop('checked', isChecked);
        updateBulkToolbar();
    });

    $('#btn-bulk-out').on('click', function() {
        var ids = $('.cb-emoney:checked').map(function() { return this.value; }).get();
        if (ids.length === 0) return;

        var $container = $('#bulk-inputs').empty();
        ids.forEach(function(id) {
            $container.append('<input type="hidden" name="ids[]" value="' + id + '">');
        });

        $('#form-bulk-out').submit();
    });
});
</script>
@endpush
