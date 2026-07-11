<?php

namespace App\Filament\Resources\Budgets\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BudgetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Budget')
                    ->schema([

                        Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('year')
                            ->label('Tahun')
                            ->numeric()
                            ->minValue(date('Y'))
                            ->maxValue(date('Y') + 10)
                            ->required(),

                        TextInput::make('total_budget')
                            ->label('Total Budget')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),

                        TextInput::make('used_budget')
                            ->label('Budget Terpakai')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->disabled()
                            ->dehydrated(),

                    ])
                    ->columns(2),
            ]);
    }
}