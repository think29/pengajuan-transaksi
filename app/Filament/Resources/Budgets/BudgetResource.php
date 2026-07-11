<?php

namespace App\Filament\Resources\Budgets;

use App\Filament\Resources\Budgets\Pages\CreateBudget;
use App\Filament\Resources\Budgets\Pages\EditBudget;
use App\Filament\Resources\Budgets\Pages\ListBudgets;
use App\Filament\Resources\Budgets\Schemas\BudgetForm;
use App\Filament\Resources\Budgets\Tables\BudgetsTable;
use App\Models\Budget;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class BudgetResource extends Resource
{
    protected static ?string $model = Budget::class;

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'Budget';

    protected static ?string $modelLabel = 'Budget';

    protected static ?string $pluralModelLabel = 'Budget';

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 2;

    /*
    |--------------------------------------------------------------------------
    | Record
    |--------------------------------------------------------------------------
    */

    protected static ?string $recordTitleAttribute = 'year';

    public static function form(Schema $schema): Schema
    {
        return BudgetForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BudgetsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBudgets::route('/'),
            'create' => CreateBudget::route('/create'),
            'edit' => EditBudget::route('/{record}/edit'),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Global Search
    |--------------------------------------------------------------------------
    */

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'year',
            'category.name',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Navigation Badge
    |--------------------------------------------------------------------------
    */

    public static function getNavigationBadge(): ?string
    {
        return (string) Budget::count();
    }

    /*
    |--------------------------------------------------------------------------
    | Soft Delete
    |--------------------------------------------------------------------------
    */

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}