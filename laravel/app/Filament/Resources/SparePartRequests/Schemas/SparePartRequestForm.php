<?php

namespace App\Filament\Resources\SparePartRequests\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SparePartRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('mechanic_analysis_id')
                    ->relationship('mechanicAnalysis', 'unit_code')
                    ->label('Unit Code')
                    ->required(),
                \Filament\Forms\Components\Select::make('inventory_id')
                    ->relationship('inventory', 'item_sparepart')
                    ->label('Spare Part')
                    ->required(),
                TextInput::make('quantity')
                    ->required()
                    ->numeric(),
                \Filament\Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'procurement' => 'Scheduled for Procurement',
                    ])
                    ->required()
                    ->default('pending'),
                Textarea::make('admin_note')
                    ->label('Admin Note')
                    ->columnSpanFull(),
            ]);
    }
}
