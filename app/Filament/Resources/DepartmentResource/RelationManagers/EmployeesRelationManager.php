<?php

namespace App\Filament\Resources\DepartmentResource\RelationManagers;

use App\Models\City;
use App\Models\State;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Collection;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeesRelationManager extends RelationManager
{
    protected static string $relationship = 'employees';

    public function form(Form $form): Form
    {
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
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('first_name')
            ->columns([
                Tables\Columns\TextColumn::make('first_name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
