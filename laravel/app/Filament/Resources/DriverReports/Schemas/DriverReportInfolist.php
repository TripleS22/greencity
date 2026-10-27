<?php

namespace App\Filament\Resources\DriverReports\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DriverReportInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('tanggal')
                    ->date(),
                TextEntry::make('unit_code'),
                TextEntry::make('nama_driver'),
                TextEntry::make('unit_driver_problem'),
            ]);
    }
}
