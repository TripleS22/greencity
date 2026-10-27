<?php

namespace App\Filament\Resources\MechanicAnalyses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Population;

class MechanicAnalysesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('tanggal')
                    ->date()
                    ->sortable(),
                TextColumn::make('unit_code')
                    ->searchable(),
                TextColumn::make('nama_mechanic_satu')
                    ->searchable(),
                TextColumn::make('nama_mechanic_dua')
                    ->searchable(),
                TextColumn::make('location_unit')
                    ->label('Location')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('unit_driver_problem')
                    ->label('Problem Driver')
                    ->searchable(),
                TextColumn::make('status_analysis')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'Completed' => 'success',
                        'In Progress' => 'info',
                        'Pending' => 'warning',
                        default => 'gray',
                    })
                    ->searchable(),
                IconColumn::make('is_approved')
                    ->label('Approval')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                TextColumn::make('spare_part_requests_pending_count')
                    ->counts([
                        'sparePartRequests' => fn ($query) => $query->where('status', 'pending'),
                    ])
                    ->label('Pending Parts')
                    ->badge()
                    ->color('warning')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('setujui')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn ($record) => $record->status_analysis === 'Completed' && !$record->is_approved)
                    ->fillForm(fn ($record) => [
                        'spare_parts_review' => $record->sparePartRequests->map(fn ($req) => [
                            'id' => $req->id,
                            'inventory_id' => $req->inventory_id,
                            'quantity' => $req->quantity,
                            'status' => $req->status,
                            'admin_note' => $req->admin_note,
                        ])->toArray(),
                    ])
                    ->form(fn ($record) => [
                        Repeater::make('spare_parts_review')
                            ->label('Review Spare Parts')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        Placeholder::make('item_name')
                                            ->label('Item')
                                            ->content(fn ($get) => \App\Models\Inventory::find($get('inventory_id'))?->item_sparepart ?? '-'),
                                        Placeholder::make('quantity_display')
                                            ->label('Quantity')
                                            ->content(fn ($get) => $get('quantity')),
                                        Select::make('status')
                                            ->options([
                                                'pending' => 'Pending',
                                                'approved' => 'Approved',
                                                'rejected' => 'Rejected',
                                                'procurement' => 'Procurement',
                                            ])
                                            ->required(),
                                    ]),
                                Textarea::make('admin_note')
                                    ->label('Admin Note')
                                    ->rows(1)
                                    ->columnSpanFull(),
                            ])
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false),
                    ])
                    ->action(function ($record, array $data) {
                        foreach ($data['spare_parts_review'] as $partData) {
                            $sparePartRequest = \App\Models\SparePartRequest::find($partData['id']);
                            if ($sparePartRequest) {
                                $sparePartRequest->update([
                                    'status' => $partData['status'],
                                    'admin_note' => $partData['admin_note'],
                                ]);
                            }
                        }

                        $record->update(['is_approved' => true]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Analisis Disetujui')
                            ->success()
                            ->send();
                    }),
                Action::make('start_repair')
                    ->label('Start Repair')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->visible(fn ($record) => $record->status_analysis === 'Completed' && $record->is_approved && !$record->mechanicReport)
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->mechanicReport()->create([
                            'unit_code' => $record->unit_code,
                            'activity' => "Perbaikan berdasarkan analisis: " . substr($record->hasil_analisis, 0, 50),
                            'time_start' => now()->toTimeString(),
                            'status_approval' => 'Pending',
                        ]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Perbaikan Dimulai')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('print_spk')
                        ->label('Cetak SPK')
                        ->icon('heroicon-o-printer')
                        ->action(function (Collection $records) {
                            $data = [];
                            $i = 1;

                            foreach ($records as $record) {
                                // Check if SPK already exists for this analysis
                                $existingSpk = \App\Models\Spk::where('mechanic_analysis_id', $record->id)->first();

                                if ($existingSpk) {
                                    $data[] = $existingSpk->data;
                                } else {
                                    $population = Population::where('unit_code', $record->unit_code)->first();
                                    
                                    // Generate SPK Number: [LocationCode][UnitNumeric][Date][Index]
                                    $locCode = match($record->location_unit) {
                                        'Sukamulya' => '001',
                                        'Sadang' => '002',
                                        default => '000'
                                    };

                                    $unitNumeric = preg_replace('/[^0-9]/', '', $record->unit_code);
                                    $spkDate = \Carbon\Carbon::parse($record->tanggal)->format('dmY');
                                    $batchIndex = str_pad($i++, 2, '0', STR_PAD_LEFT);
                                    
                                    $spkNumber = "{$locCode}{$unitNumeric}{$spkDate}{$batchIndex}";

                                    $recordData = [
                                        'spk_number' => $spkNumber,
                                        'unit_code' => $record->unit_code,
                                        'location_unit' => $record->location_unit ?: '-',
                                        'plat_no' => $population->unit_code ?? '-',
                                        'smu_down' => $record->smu_down,
                                        'tanggal' => $record->tanggal,
                                        'nama_driver' => $record->nama_driver,
                                        'unit_driver_problem' => $record->unit_driver_problem,
                                        'nama_mechanic_satu' => $record->nama_mechanic_satu,
                                        'nama_mechanic_dua' => $record->nama_mechanic_dua,
                                        'hasil_analisis' => $record->hasil_analisis,
                                        'saran_perbaikan' => $record->saran_perbaikan,
                                        'parts' => $record->sparePartRequests->map(fn($part) => [
                                            'name' => $part->inventory?->item_sparepart ?? 'Unknown Part',
                                            'qty' => $part->quantity,
                                            'status' => $part->status,
                                        ])->toArray(),
                                    ];

                                    // Save to history
                                    \App\Models\Spk::create([
                                        'spk_number' => $spkNumber,
                                        'mechanic_analysis_id' => $record->id,
                                        'unit_code' => $record->unit_code,
                                        'data' => $recordData,
                                    ]);

                                    $data[] = $recordData;
                                }
                            }

                            $pdf = Pdf::loadView('pdf.spk', ['records' => $data]);
                            
                            return response()->streamDownload(function () use ($pdf) {
                                echo $pdf->stream();
                            }, 'SPK-' . now()->format('YmdHis') . '.pdf');
                        })
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
