<?php

namespace App\Filament\Resources\MechanicReports\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MechanicReportsTable
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
                TextColumn::make('unit_code')
                    ->searchable(),
                TextColumn::make('activity')
                    ->searchable(),
                TextColumn::make('time_start')
                    ->time()
                    ->sortable(),
                TextColumn::make('time_finish')
                    ->time()
                    ->sortable(),
                ImageColumn::make('documentation')
                    ->label('Documentation')
                    ->disk('public')
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->toggleable(),
                TextColumn::make('mechanicAnalysis.hasil_analisis')
                    ->label('Analysis Findings')
                    ->limit(50)
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('status_approval')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'Approved' => 'success',
                        'Rejected' => 'danger',
                        'Pending' => 'warning',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('duration')
                    ->label('Duration')
                    ->getStateUsing(fn ($record) => $record->duration)
                    ->badge()
                    ->color('info'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Setujui')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn ($record) => $record->time_finish && $record->status_approval !== 'Approved')
                    ->form([
                        Select::make('status_approval')
                            ->label('Status Approval')
                            ->options([
                                'Approved' => 'Approved',
                                'Rejected' => 'Rejected',
                            ])
                            ->required()
                            ->live(),
                        Repeater::make('service_charges')
                            ->label('Rincian Biaya Jasa per Sparepart')
                            ->schema([
                                TextInput::make('item_name')
                                    ->label('Sparepart')
                                    ->disabled()
                                    ->dehydrated(false),
                                TextInput::make('quantity')
                                    ->label('Qty')
                                    ->disabled()
                                    ->dehydrated(true), 
                                TextInput::make('service_charge')
                                    ->label('Biaya Jasa (Satuan)')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required()
                                    ->default(0),
                                Hidden::make('spare_part_request_id'),
                            ])
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->columns(3)
                            ->default(fn ($record) => ($record->mechanicAnalysis?->sparePartRequests ?? collect())->where('status', 'approved')->map(fn ($part) => [
                                'item_name' => $part->inventory?->item_sparepart ?? 'Unknown',
                                'quantity' => $part->quantity,
                                'service_charge' => $part->inventory?->service_charge ?? 0,
                                'spare_part_request_id' => $part->id,
                            ])->toArray())
                            ->visible(fn ($get) => $get('status_approval') === 'Approved'),
                        Textarea::make('admin_response')
                            ->label('Respon Admin')
                            ->placeholder('Berikan alasan atau instruksi tambahan...')
                            ->required(fn ($get) => $get('status_approval') === 'Rejected')
                            ->helperText(fn ($get) => $get('status_approval') === 'Rejected' ? 'Wajib diisi jika ditolak' : null),
                    ])
                    ->action(function ($record, array $data) {
                        if ($data['status_approval'] === 'Approved') {
                             $totalServiceCharge = 0;
                             
                             // Calculate total from repeater
                             if (!empty($data['service_charges'])) {
                                 foreach ($data['service_charges'] as $item) {
                                     $qty = (int) $item['quantity'];
                                     $charge = (int) $item['service_charge'];
                                     $totalServiceCharge += ($charge * $qty);
                                 }
                             }
                             
                             $record->update([
                                 'status_approval' => 'Approved',
                                 'sales_service' => $totalServiceCharge,
                                 'admin_response' => $data['admin_response'] ?? null,
                             ]);
                             
                             // Update DBR
                             $dbr = \App\Models\DBR::where('mechanic_analysis_id', $record->mechanic_analysis_id)->first();
                             if ($dbr) {
                                 // Re-build DBR parts with new service charges
                                 $dbrParts = [];
                                 $totalSalesSparepart = 0;
                                 
                                 if (!empty($data['service_charges'])) {
                                     foreach ($data['service_charges'] as $item) {
                                         $req = \App\Models\SparePartRequest::find($item['spare_part_request_id']);
                                         if ($req && $req->inventory) {
                                             $sellingPrice = $req->inventory->selling_price ?? 0;
                                             $qty = $req->quantity;
                                             $srvCharge = (int) $item['service_charge'];
                                             $amount = $sellingPrice * $qty;
                                             
                                             $dbrParts[] = [
                                                 'component' => $req->inventory->item_sparepart,
                                                 'qty' => $qty,
                                                 'unit' => $req->inventory->unit,
                                                 'purchase_price' => $req->inventory->price_per_unit ?? 0,
                                                 'price_per_pcs' => $sellingPrice,
                                                 'amount' => $amount,
                                                 'service_charge' => $srvCharge * $qty,
                                                 'total' => $amount + ($srvCharge * $qty),
                                             ];
                                             $totalSalesSparepart += $amount;
                                         }
                                     }
                                 }
                                 $dbr->spare_parts = $dbrParts;
                                 $dbr->sales_service = $totalServiceCharge;
                                 $dbr->sales_sparepart = $totalSalesSparepart;
                                 
                                 // Compat fields (first item)
                                 if (count($dbrParts) === 1) {
                                     $first = $dbrParts[0];
                                     $dbr->component = $first['component'];
                                     $dbr->qty = $first['qty'];
                                     $dbr->price_per_pcs = $first['price_per_pcs'];
                                     $dbr->amount = $first['amount'];
                                     $dbr->service_charge = $first['service_charge'];
                                     $dbr->total = $first['total'];
                                 } elseif (count($dbrParts) > 1) {
                                     $dbr->component = 'Multiple Items (' . count($dbrParts) . ')';
                                 }
                                 
                                 $dbr->save();
                             }
                        } else {
                            $record->update([
                                'status_approval' => 'Rejected',
                                'admin_response' => $data['admin_response'],
                            ]);
                        }

                        Notification::make()
                            ->title('Laporan Diperbarui')
                            ->body('Status approval mechanic report telah diperbarui.')
                            ->success()
                            ->send();
                    }),
                Action::make('finish_repair')
                    ->label('Finish Repair')
                    ->icon('heroicon-o-stop')
                    ->color('danger')
                    ->visible(fn ($record) => $record->time_start && !$record->time_finish)
                    ->form([
                        TextInput::make('smu_ready')
                            ->label('SMU Ready (Km Akhir)')
                            ->numeric()
                            ->required()
                            ->hint(fn ($record) => "SMU KM Awal: " . ($record->mechanicAnalysis?->smu_down ?? '0')),
                        FileUpload::make('documentation')
                            ->label('Documentation (Images/PDF)')
                            ->disk('public')
                            ->directory('mechanic-reports')
                            ->multiple()
                            ->reorderable()
                            ->required(),
                        Textarea::make('activity')
                            ->label('Final Activity Description')
                            ->default(fn ($record) => $record->activity)
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'time_finish' => now()->toTimeString(),
                            'smu_ready' => $data['smu_ready'],
                            'documentation' => $data['documentation'],
                            'activity' => $data['activity'],
                        ]);

                        Notification::make()
                            ->title('Perbaikan Selesai')
                            ->success()
                            ->send();
                    }),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
