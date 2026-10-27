<?php

namespace App\Filament\Resources\DBRS\Schemas;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DBRInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi Dasar')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('date')
                                    ->label('Tanggal')
                                    ->date(),
                                TextEntry::make('week')
                                    ->label('Minggu')
                                    ->numeric(),
                                TextEntry::make('project')
                                    ->label('Proyek'),
                                TextEntry::make('created_at')
                                    ->label('Dibuat Pada')
                                    ->dateTime()
                                    ->placeholder('-'),
                                TextEntry::make('updated_at')
                                    ->label('Diperbarui Pada')
                                    ->dateTime()
                                    ->placeholder('-'),
                                TextEntry::make('status_code')
                                    ->label('Status Code')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'Open' => 'success',
                                        'Closed' => 'gray',
                                        default => 'warning',
                                    }),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Informasi Unit')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('unit_code')
                                    ->label('Kode Unit'),
                                TextEntry::make('brand')
                                    ->label('Brand'),
                                TextEntry::make('model')
                                    ->label('Model'),
                                TextEntry::make('eqp_type')
                                    ->label('Tipe Equipment'),
                                TextEntry::make('location_unit')
                                    ->label('Lokasi Unit'),
                                TextEntry::make('smu_down')
                                    ->label('SMU Down'),
                                TextEntry::make('smu_ready')
                                    ->label('SMU Ready'),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Detail Breakdown & Aktivitas')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('problem_reported')
                                    ->label('Masalah Dilaporkan')
                                    ->columnSpanFull(),
                                TextEntry::make('activity')
                                    ->label('Aktivitas')
                                    ->columnSpanFull(),
                                TextEntry::make('remarks')
                                    ->label('Keterangan')
                                    ->columnSpanFull(),
                                
                                TextEntry::make('date_bd')
                                    ->label('Tanggal BD')
                                    ->date(),
                                TextEntry::make('time_start_bd')
                                    ->label('Waktu Mulai BD')
                                    ->time(),
                                TextEntry::make('category_bd')
                                    ->label('Kategori BD'),

                                TextEntry::make('time_start')
                                    ->label('Waktu Mulai Pengerjaan')
                                    ->time(),
                                TextEntry::make('time_finish')
                                    ->label('Waktu Selesai Pengerjaan')
                                    ->time(),
                                TextEntry::make('duration_db')
                                    ->label('Durasi BD'),

                                TextEntry::make('status_bd')
                                    ->label('Status BD')
                                    ->badge(),
                                TextEntry::make('unit_status')
                                    ->label('Status Unit')
                                    ->badge()
                                    ->color(fn (string $state): string => match (strtolower($state)) {
                                        'breakdown' => 'danger',
                                        'rfu' => 'success',
                                        'standby' => 'warning',
                                        default => 'gray',
                                    }),
                                TextEntry::make('mtc_type')
                                    ->label('Tipe Maintenance'),
                                
                                TextEntry::make('shift_down')
                                    ->label('Shift Down'),
                                TextEntry::make('count_mtc_type')
                                    ->label('Count MTC'),
                                TextEntry::make('task_down')
                                    ->label('Task Down'),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Personel')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('nama_driver')
                                    ->label('Nama Driver'),
                                TextEntry::make('nama_mechanic_satu')
                                    ->label('Mekanik 1'),
                                TextEntry::make('nama_mechanic_dua')
                                    ->label('Mekanik 2'),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Suku Cadang & Biaya')
                    ->columnSpanFull()
                    ->schema([
                        ViewEntry::make('spare_parts_list')
                            ->view('filament.infolists.dbr-spare-parts')
                            ->state(fn ($record) => $record->spare_parts)
                            ->columnSpanFull(),

                        Grid::make(3)
                            ->extraAttributes(['class' => 'mt-4 pt-4 border-t border-gray-100 dark:border-gray-800'])
                            ->schema([
                                TextEntry::make('sales_sparepart')
                                    ->label('Penjualan Sparepart')
                                    ->numeric(decimalPlaces: 0)
                                    ->prefix('IDR '),
                                TextEntry::make('sales_service')
                                    ->label('Penjualan Service')
                                    ->numeric(decimalPlaces: 0)
                                    ->prefix('IDR '),
                                TextEntry::make('total')
                                    ->label('Total Keseluruhan')
                                    ->numeric(decimalPlaces: 0)
                                    ->prefix('IDR ')
                                    ->weight(\Filament\Support\Enums\FontWeight::Bold),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }
}
