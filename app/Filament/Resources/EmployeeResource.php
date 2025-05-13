<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\City;
use App\Models\Employee;
use App\Models\State;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\Section as ComponentsSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static? string $navigationGroup = 'Employee Management';
    protected static ?string $recordTitleAttribute = 'first_name';
    protected static ?string $tenantOwnershipRelationshipName = 'team';


    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->first_name . ' ' . $record->last_name;
    }
    public static function getGloballySearchableAttributes(): array
    {
        return [
            'first_name',
            'middle_name',
            'last_name',
            'country.name',
            'state.name',
            'city.name',
        ];
    }
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Country' => $record->country->name,
            'State' => $record->state->name,
            'City' => $record->city->name,
            
        ];
    }
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()
            ->with(['country', 'state', 'city']);
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 10 ? 'success' : 'primary';
    }
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
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('middle_name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('zip_code')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('date_of_birth')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Columns\TextColumn::make('department.name')
                    ->numeric()
                    ->label('Department')
                    
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
                SelectFilter::make('department_id')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Filter By Department')
                    ->indicator('Department'),
            
                Filter::make('date_hired')
                    ->form([
                        DatePicker::make('date_hired_from')->label('Date Hired From'),
                        DatePicker::make('date_hired_until')->label('Date Hired Until')
                            ->default(now()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_hired_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date_hired', '>=', $date)
                            )
                            ->when(
                                $data['date_hired_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date_hired', '<=', $date)
                            );
                    })
                    ->label('Date Hired')
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
            
                        if ($data['date_hired_from'] ?? null) {
                            $indicators[] = Indicator::make('Hired from ' . Carbon::parse($data['date_hired_from'])->toFormattedDateString())
                                ->removeField('date_hired_from');
                        }
            
                        if ($data['date_hired_until'] ?? null) {
                            $indicators[] = Indicator::make('Hired until ' . Carbon::parse($data['date_hired_until'])->toFormattedDateString())
                                ->removeField('date_hired_until');
                        }
            
                        return $indicators;
                    }),
            
                Filter::make('date_of_birth')
                    ->form([
                        DatePicker::make('dob_from')->label('Date of Birth From'),
                        DatePicker::make('dob_until')->label('Date of Birth Until')
                            ->default(now()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dob_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date_of_birth', '>=', $date)
                            )
                            ->when(
                                $data['dob_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date_of_birth', '<=', $date)
                            );
                    })
                    ->label('Date of Birth')
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
            
                        if ($data['dob_from'] ?? null) {
                            $indicators[] = Indicator::make('Born from ' . Carbon::parse($data['dob_from'])->toFormattedDateString())
                                ->removeField('dob_from');
                        }
            
                        if ($data['dob_until'] ?? null) {
                            $indicators[] = Indicator::make('Born until ' . Carbon::parse($data['dob_until'])->toFormattedDateString())
                                ->removeField('dob_until');
                        }
            
                        return $indicators;
                    }),
                ],layout: FiltersLayout::AboveContent)->filtersFormColumns(3)
            
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                ->successNotification(
                    Notification::make()
                    ->success()
                    ->title('Employee deleted successfully.')
                    ->body('The employee has been deleted.')
                ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            ComponentsSection::make('User Name')->schema([
                TextEntry::make('first_name')->label('First Name'),
                TextEntry::make('middle_name')->label('Middle Name'),
                TextEntry::make('last_name')->label('Last Name'),
            ])->columns(3),
            ComponentsSection::make('User Address Details')->schema([
                TextEntry::make('country.name')->label('Country'),
                TextEntry::make('state.name')->label('State'),
                TextEntry::make('city.name')->label('City'),
                TextEntry::make('department.name')->label('Department'),
            ])->columns(2),
            ComponentsSection::make('User Contact Details')->schema([
                TextEntry::make('email')->label('Email'),
                TextEntry::make('address')->label('Address'),
                TextEntry::make('zip_code')->label('Zip Code'),
            ])->columns(3),
            ComponentsSection::make('Dates')->schema([
                TextEntry::make('date_of_birth')->label('Date of Birth'),
                TextEntry::make('date_hired')->label('Date Hired'),
                TextEntry::make('created_at')->label('Created At'),
                TextEntry::make('updated_at')->label('Updated At'),
            ])->columns(2),
           
        ])->columns(2);
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
            // 'view' => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }

}
