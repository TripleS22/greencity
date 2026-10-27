<?php

namespace App\Filament\Resources\MechanicReports\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Hugomyb\FilamentMediaAction\Actions\MediaAction;

class MechanicReportInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Mechanic Report Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('unit_code')
                            ->weight('bold'),
                        TextEntry::make('activity'),
                        TextEntry::make('time_start')
                            ->time()
                            ->color('success'),
                        TextEntry::make('time_finish')
                            ->time()
                            ->color('info'),
                        TextEntry::make('documentation')
                            ->label('Documentation')
                            ->formatStateUsing(fn ($record) => $record->documentation 
                                ? count($record->documentation) . ' file(s) available'
                                : 'No documentation'),
                        
                        \Filament\Schemas\Components\Actions::make(function ($record) {
                            if (!$record->documentation) {
                                return [];
                            }
                            
                            $actions = [];
                            foreach ($record->documentation as $index => $path) {
                                $actions[] = MediaAction::make("file_" . $index)
                                    ->label('File ' . ($index + 1))
                                    ->icon('heroicon-o-eye')
                                    ->media(\Storage::disk('public')->url($path))
                                    ->modalHeading('Documentation Viewer - File ' . ($index + 1));
                            }
                            
                            return $actions;
                        })->columnSpanFull(),
                    ]),

                Section::make('Admin Feedback')
                    ->columns(1)
                    ->schema([
                        TextEntry::make('status_approval')
                            ->badge()
                            ->color(fn ($state) => match ($state) {
                                'Approved' => 'success',
                                'Rejected' => 'danger',
                                'Pending' => 'warning',
                                default => 'gray',
                            }),
                        TextEntry::make('admin_response')
                            ->label('Respon Admin')
                            ->placeholder('Belum ada respon dari admin.'),
                        TextEntry::make('sales_service')
                            ->label('Total Service (Jasa)')
                            ->money('IDR')
                            ->visible(fn ($record) => $record->status_approval === 'Approved'),
                    ]),

                Section::make('Timestamps')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextEntry::make('created_at')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->dateTime()
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
