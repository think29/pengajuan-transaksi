<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')

            ->columns([

                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('submission.submission_number')
                    ->label('No Pengajuan')
                    ->searchable()
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('activity')
                    ->label('Aktivitas')
                    ->badge()
                    ->sortable()
                    ->searchable()
                    ->color(fn (string $state): string => match ($state) {

                        'CREATE' => 'info',
                        'UPDATE' => 'warning',
                        'DELETE' => 'danger',

                        'SUBMIT_APPROVAL' => 'primary',

                        'APPROVE' => 'success',
                        'REJECT' => 'danger',

                        'NEXT_APPROVER' => 'warning',

                        'FINISH_APPROVAL' => 'success',

                        'PAYMENT' => 'success',

                        default => 'gray',
                    }),

                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(60)
                    ->wrap()
                    ->searchable(),

                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->toggleable(isToggledHiddenByDefault: true),

            ])

            ->filters([
                //
            ])

            ->recordActions([
                ViewAction::make(),
            ])

            ->toolbarActions([
                //
            ]);
    }
}