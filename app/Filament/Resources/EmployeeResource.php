<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\City;
use App\Models\Employee;
use App\Models\State;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static? string $navigationGroup = 'Employee Management';

    public static function form(Form $form): Form
   
    {
        'required|max:255';
        return $form
            ->schema([
               Section::make('User Name')
               ->description('Put the user details here')
               ->schema([
                Forms\Components\TextInput::make('first_name')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('middle_name')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('last_name')
                ->required()
                ->maxLength(255),
               ])->columns(3),
              
               Section::make('Select Address')
               ->description('Put the user address here')
               ->schema([
                Forms\Components\Select::make('country_id')
                ->required()
                ->label('Country')
                ->relationship('country', 'name')
                ->searchable()
                ->preload()
                ->afterStateUpdated(function(Set $set){
                    $set('state_id',null);
                    $set('city_id',null);
                })
                ->live(),
            Forms\Components\Select::make('state_id')
                ->options(fn(Get $get): Collection =>State::query()
                ->where('country_id',$get('country_id'))
                ->get()
            
                ->pluck('name', 'id')

                )
                ->afterStateUpdated(fn(Set $set)=>$set('city_id',null))
                ->required()
                ->label('State')
                ->searchable()
                ->preload()
                ->live()
                ,
                Forms\Components\Select::make('city_id')
                ->required()
                ->label('City')
                ->searchable()
                ->preload()
                ->options(fn(Get $get): Collection =>City::query()
                ->where('state_id',$get('state_id'))
                ->get()
                ->pluck('name', 'id')
            )
            ->live(),
            Forms\Components\Select::make('department_id')
                ->required()
                ->label('Department')
                ->searchable()
                ->preload()
                ->relationship('department', 'name'),
               ])->columns(2),
                Section::make('User Address Details')
                ->description('Put the user contact details here')
                ->schema([
                    Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('address')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('zip_code')
                    ->required()
                    ->maxLength(255),
                ])->columns(3),
                Section::make('Dates')
                ->description('Put the user dates here')
                ->schema([
                    Forms\Components\DatePicker::make('date_of_birth')
                    ->native(false)
                    ->required()
                    ->displayFormat('d/m/Y'),
                Forms\Components\DatePicker::make('date_hired')
                    ->required()
                    ->native(false)
                    
                    ->displayFormat('d/m/Y')
                    ,
                ])->columns(2),
                
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('middle_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('zip_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_of_birth')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_hired')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('country.name')
                    
                    ->sortable(),
                Tables\Columns\TextColumn::make('state.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('department_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'view' => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
