<?php

namespace App\Filament\Resources\ResetStockHistories;

use App\Filament\Resources\ResetStockHistories\Pages\ManageResetStockHistories;
use App\Models\ResetStockHistory;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Exports\InventoriesExport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Actions\Action;

class ResetStockHistoryResource extends Resource
{
    protected static ?string $model = ResetStockHistory::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static string|\UnitEnum|null $navigationGroup = 'Inventory';

    protected static ?string $navigationLabel = 'History Reset Stok';

    protected static ?string $modelLabel = 'History Reset Stok';

    protected static ?string $recordTitleAttribute = 'filename';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('filename')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('filename'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('filename')
            ->columns([
                TextColumn::make('filename')
                    ->label('Nama File')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('reset_by')
                    ->label('Oleh')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Tanggal Reset')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('download')
                    ->label('Download Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function (ResetStockHistory $record) {
                        return Excel::download(
                            new InventoriesExport(collect($record->data_snapshot)), 
                            $record->filename
                        );
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
            'index' => ManageResetStockHistories::route('/'),
        ];
    }
}
