<?php

namespace App\Filament\Resources\Budgets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BudgetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('year')
                    ->label('Tahun')
                    ->sortable(),

                TextColumn::make('total_budget')
                    ->label('Total Budget')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('used_budget')
                    ->label('Terpakai')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('remaining_budget')
                    ->label('Sisa Budget')
                    ->money('IDR')
                    ->state(function ($record) {
                        return $record->total_budget - $record->used_budget;
                    }),

                TextColumn::make('progress')
                    ->label('Progress')
                    ->badge()
                    ->color(function ($record) {

                        if ($record->total_budget == 0) {
                            return 'gray';
                        }

                        $percent = ($record->used_budget / $record->total_budget) * 100;

                        if ($percent >= 90) {
                            return 'danger';
                        }

                        if ($percent >= 70) {
                            return 'warning';
                        }

                        return 'success';
                    })
                    ->state(function ($record) {

                        if ($record->total_budget == 0) {
                            return '0%';
                        }

                        return number_format(
                            ($record->used_budget / $record->total_budget) * 100,
                            1
                        ) . '%';
                    }),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable(),

            ])

            ->defaultSort('year', 'desc')

            ->recordActions([
                EditAction::make(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}