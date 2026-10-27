<?php

namespace App\Filament\Resources\PeriodicServices\Tables;

use App\Models\DBR;
use App\Models\Project;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class PeriodicServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('unit_code')
                    ->label('Unit Code')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('population.project')
                    ->label('Project')
                    ->searchable(),

                TextColumn::make('average_daily_ritase')
                    ->label('Ritase/Hari')
                    ->numeric()
                    ->alignCenter(),

                // 1. SERVIS TERAKHIR: Diambil dari laporan DBR PALING BARU (Karena DBR dianggap laporan aktivitas servis/perawatan)
                TextColumn::make('last_service_info')
                    ->label('Servis Terakhir (Data DBR)')
                    ->getStateUsing(function ($record) {
                        $latestDbr = DBR::where('unit_code', $record->unit_code)->latest('date')->first();
                        
                        if ($latestDbr) {
                            return $latestDbr->date->format('d/m/Y');
                        }

                        // Fallback jika belum ada DBR sama sekali
                        return $record->last_service_date ? $record->last_service_date->format('d/m/Y') : '-';
                    })
                    ->description(function ($record) {
                        $latestDbr = DBR::where('unit_code', $record->unit_code)->latest('date')->first();
                        $smu = $latestDbr ? $latestDbr->smu_ready : ($record->last_service_smu ?? 0);
                        return "SMU: " . number_format($smu, 0);
                    })
                    ->color('success'),

                // 2. POSISI SMU: Hasil hitungan otomatis per hari dari data DBR terakhir tersebut
                TextColumn::make('estimated_today_smu')
                    ->label('Posisi SMU (Estimasi Hari Ini)')
                    ->getStateUsing(function ($record) {
                        $latestDbr = DBR::where('unit_code', $record->unit_code)->latest('date')->first();
                        
                        $baseSmu = $latestDbr ? $latestDbr->smu_ready : ($record->last_service_smu ?? 0);
                        $baseDate = $latestDbr ? Carbon::parse($latestDbr->date) : ($record->last_service_date ? Carbon::parse($record->last_service_date) : Carbon::today());
                        
                        $daysDiff = $baseDate->startOfDay()->diffInDays(Carbon::today());

                        $projectPop = $record->population?->project;
                        $project = Project::where('name', $projectPop)->first();
                        $addedSmu = 0;
                        if ($project && $record->average_daily_ritase > 0) {
                            $dailyKm = $record->average_daily_ritase * 2 * $project->distance_km;
                            $addedSmu = $daysDiff * $dailyKm;
                        }

                        return $baseSmu + $addedSmu;
                    })
                    ->numeric()
                    ->color('info')
                    ->weight('bold'),

                // 3. TERAKUMULASI: Berapa KM yang sudah dilewati sejak laporan terakhir tadi
                TextColumn::make('realtime_km')
                    ->label('KM Terakumulasi (Berjalan)')
                    ->getStateUsing(function ($record) {
                        $latestDbr = DBR::where('unit_code', $record->unit_code)->latest('date')->first();
                        
                        $baseDate = $latestDbr ? Carbon::parse($latestDbr->date) : ($record->last_service_date ? Carbon::parse($record->last_service_date) : Carbon::today());
                        $daysDiff = $baseDate->startOfDay()->diffInDays(Carbon::today());

                        $projectPop = $record->population?->project;
                        $project = Project::where('name', $projectPop)->first();
                        $addedSmu = 0;
                        if ($project && $record->average_daily_ritase > 0) {
                            $dailyKm = $record->average_daily_ritase * 2 * $project->distance_km;
                            $addedSmu = $daysDiff * $dailyKm;
                        }

                        return $addedSmu;
                    })
                    ->numeric()
                    ->suffix(' KM')
                    ->color(function ($state, $record) {
                        $interval = $record->service_interval_km ?? 8000;
                        if ($state >= $interval) return 'danger';
                        if ($interval - $state <= 1000) return 'warning';
                        return 'success';
                    })
                    ->weight('bold'),

                TextColumn::make('prediksi_tanggal')
                    ->label('Estimasi Servis Berikutnya')
                    ->getStateUsing(function ($record) {
                        $projectPop = $record->population?->project;
                        $project = Project::where('name', $projectPop)->first();
                        if (!$project || $record->average_daily_ritase <= 0) return 'Isi Ritase & Jarak';

                        $latestDbr = DBR::where('unit_code', $record->unit_code)->latest('date')->first();
                        $baseSmu = $latestDbr ? $latestDbr->smu_ready : ($record->last_service_smu ?? 0);
                        $baseDate = $latestDbr ? Carbon::parse($latestDbr->date) : ($record->last_service_date ? Carbon::parse($record->last_service_date) : Carbon::today());
                        
                        $daysSince = $baseDate->startOfDay()->diffInDays(Carbon::today());
                        $dailyKm = $record->average_daily_ritase * 2 * $project->distance_km;
                        $smuNow = $baseSmu + ($daysSince * $dailyKm);

                        $interval = $record->service_interval_km ?? 8000;
                        $targetSmu = $baseSmu + $interval;
                        
                        $remaining = $targetSmu - $smuNow;
                        if ($remaining <= 0) return 'WAKTUNYA SERVIS!';

                        $daysToService = ceil($remaining / $dailyKm);
                        return Carbon::today()->addDays($daysToService)->format('d M Y');
                    })
                    ->description(function($record) {
                        $projectPop = $record->population?->project;
                        $project = Project::where('name', $projectPop)->first();
                        if ($project && $record->average_daily_ritase > 0) {
                            $dailyKm = $record->average_daily_ritase * 2 * $project->distance_km;
                            return "Laju: " . number_format($dailyKm, 1) . " KM/hari";
                        }
                        return null;
                    }),
            ])
            ->filters([])
            ->actions([
                EditAction::make()->label('Update Manual'),
                DeleteAction::make(),
            ]);
    }
}
