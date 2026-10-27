<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public function filtersForm(Schema $form): Schema
    {
        return $form
            ->components([
                Section::make()
                    ->schema([
                        DatePicker::make('startDate')
                            ->label('Dari Tanggal')
                            ->default(now()->startOfMonth()),
                        DatePicker::make('endDate')
                            ->label('Sampai Tanggal')
                            ->default(now()->endOfMonth()),
                    ])
                    ->columns(2),
            ]);
    }

    public function getColumns(): int | array
    {
        return 3;
    }
}
