<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CountryResource\RelationManagers\EmployeesRelationManager;
use App\Filament\Resources\StateResource\Pages;
use App\Filament\Resources\StateResource\RelationManagers;
use App\Filament\Resources\StateResource\RelationManagers\CitiesRelationManager;
use App\Models\State;
use Filament\Forms;
use Filament\Forms\Components\Section as ComponentsSection;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StateResource extends Resource
{
    protected static ?string $model = State::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static?string $navigationLabel = 'State';

    protected static? string $modelLabel = 'States';
   protected static ?string $tenantOwnershipRelationshipName = 'team';
    protected static? string $navigationGroup = 'System Management';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                ComponentsSection::make('State Details')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('country_id')
                ->label('Country')
                ->required()
                ->searchable()
                ->preload()
                ->relationship('country', 'name'),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('country.name')
                ->sortable()
                    ->searchable(isIndividual: true)
                    ->label('Country'),
                Tables\Columns\TextColumn::make('name')
                ->sortable()
                    ->searchable()
                    ->label('State'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->defaultSort('country.name', 'asc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    public static function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            // ...
           Section::make('State Details')
           ->schema([
            TextEntry::make('country.name')
            ->label('Country'),
            TextEntry::make('name')
            ->label('State'),
           ])->columns(2),
        ]);
}

    public static function getRelations(): array
    {
        return [
            //
            EmployeesRelationManager::class,
            CitiesRelationManager::class

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStates::route('/'),
            'create' => Pages\CreateState::route('/create'),
            'edit' => Pages\EditState::route('/{record}/edit'),
        ];
    }
}
