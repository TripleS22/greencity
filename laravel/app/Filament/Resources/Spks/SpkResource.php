<?php

namespace App\Filament\Resources\Spks;

use App\Filament\Resources\Spks\Pages\ManageSpks;
use App\Models\Spk;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;

class SpkResource extends Resource
{
    protected static ?string $model = Spk::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';
    
    protected static string|\UnitEnum|null $navigationGroup = 'Mechanic';
    
    protected static ?string $navigationLabel = 'History SPK';
    
    protected static ?string $modelLabel = 'SPK';

    protected static ?string $recordTitleAttribute = 'spk_number';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('spk_number')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('spk_number')
            ->columns([
                TextColumn::make('spk_number')
                    ->label('Nomor SPK')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('unit_code')
                    ->label('Unit')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Waktu Dibuat')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('print')
                    ->label('Print Ulang')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->action(function (Spk $record) {
                        $pdf = Pdf::loadView('pdf.spk', ['records' => [$record->data]]);
                        
                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->stream();
                        }, "REPRINT-{$record->spk_number}.pdf");
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSpks::route('/'),
        ];
    }
}
