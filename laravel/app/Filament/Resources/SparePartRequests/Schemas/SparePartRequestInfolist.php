<?php

namespace App\Filament\Resources\SparePartRequests\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SparePartRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('mechanicAnalysis.unit_code')
                    ->label('Unit Code'),
                TextEntry::make('inventory.item_sparepart')
                    ->label('Spare Part'),
                TextEntry::make('quantity')
                    ->numeric(),
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
                    ->placeholder('No notes from admin yet.')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
