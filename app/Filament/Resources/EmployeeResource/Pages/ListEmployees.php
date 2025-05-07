<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use App\Models\Employee;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    public function getTabs(): array
    {
        return [
            'All' => Tab::make('All')->icon('heroicon-o-user-group'),
            'This Week' => Tab::make('This Week')->icon('heroicon-o-calendar-days')
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('date_hired', '>=', Carbon::now()->subWeek()))
            ->badge(Employee::query()->where('date_hired', '>=', Carbon::now()->subWeek())->count()),
            'This Month' => Tab::make('This Month')->icon('heroicon-o-calendar-days')
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('date_hired', '>=', Carbon::now()->subMonth()))
            ->badge(Employee::query()->where('date_hired', '>=', Carbon::now()->subMonth())->count()),
            'This Year' => Tab::make('This Year')->icon('heroicon-o-calendar')
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('date_hired', '>=', Carbon::now()->subYear()))
            ->badge(Employee::query()->where('date_hired', '>=', Carbon::now()->subYear())->count()),
        ];
    }
}
