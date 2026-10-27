<?php

namespace App\Filament\Resources\MechanicAnalyses\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MechanicAnalysisInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Report Information')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('unit_code')
                            ->weight('bold'),
                        TextEntry::make('tanggal')
                            ->date(),
                        TextEntry::make('unit_driver_problem')
                            ->label('Masalah yang Dilaporkan')
                            ->columnSpanFull(),
                    ]),

                Section::make('Analysis & Results')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('nama_mechanic_satu')
                            ->label('Mechanic Utama'),
                        TextEntry::make('nama_mechanic_dua')
                            ->label('Mechanic Pendamping'),
                        TextEntry::make('status_analysis')
                            ->badge()
                            ->color(fn ($state) => match ($state) {
                                'Completed' => 'success',
                                'In Progress' => 'info',
                                'Pending' => 'warning',
                                default => 'gray',
                            }),
                        TextEntry::make('hasil_analisis')
                            ->label('Hasil Analisis')
                            ->columnSpanFull()
                            ->placeholder('Belum ada hasil analisis.'),
                        TextEntry::make('saran_perbaikan')
                            ->label('Saran Perbaikan')
                            ->columnSpanFull()
                            ->placeholder('Belum ada saran perbaikan.'),
                    ]),

                Section::make('Spare Parts Needed')
                    ->schema([
                        RepeatableEntry::make('sparePartRequests')
                            ->hiddenLabel()
                            ->schema([
                                TextEntry::make('inventory.item_sparepart')
                                    ->label('Item'),
                                TextEntry::make('quantity')
                                    ->label('Quantity')
                                    ->suffix(' units'),
                                TextEntry::make('status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        'procurement' => 'info',
                                        'pending' => 'warning',
                                        default => 'gray',
                                    }),
                                TextEntry::make('admin_note')
                                    ->label('Admin Note')
                                    ->columnSpanFull()
                                    ->placeholder('No notes from admin yet.'),
                            ])
                            ->columns(3)
                            ->placeholder('Tidak ada spare part yang diganti.'),
                    ]),

                Section::make('Work Report')
                    ->description('Laporan perbaikan yang diajukan setelah analisis.')
                    ->schema([
                        TextEntry::make('mechanicReport.id')
                            ->label('Report Reference')
                            ->formatStateUsing(fn ($state) => $state ? "Report #{$state}" : 'Belum dilaporkan')
                            ->color(fn ($state) => $state ? 'success' : 'warning'),
                        TextEntry::make('mechanicReport.status_approval')
                            ->label('Approval Status')
                            ->badge()
                            ->color(fn ($state) => match ($state) {
                                'Approved' => 'success',
                                'Rejected' => 'danger',
                                'Pending' => 'warning',
                                default => 'gray',
                            })
                            ->placeholder('Belum ada laporan.'),
                    ])
                    ->columns(2),

                Section::make('System Information')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextEntry::make('created_at')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->dateTime(),
                    ]),
            ]);
    }
}
