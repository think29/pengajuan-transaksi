<?php

namespace App\Filament\Resources\ActivityLogs\Schemas;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ActivityLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Informasi Aktivitas')

                    ->schema([

                        Grid::make(2)
                            ->schema([

                                TextEntry::make('created_at')
                                    ->label('Tanggal')
                                    ->dateTime('d M Y H:i'),

                                TextEntry::make('user.name')
                                    ->label('User'),

                                TextEntry::make('activity')
                                    ->label('Aktivitas')
                                    ->badge(),

                                TextEntry::make('submission.submission_number')
                                    ->label('No Pengajuan')
                                    ->placeholder('-'),

                                TextEntry::make('ip_address')
                                    ->label('IP Address'),

                            ]),

                    ]),

                Section::make('Deskripsi')

                    ->schema([

                        TextEntry::make('description')
                            ->markdown()

                    ])

            ]);
    }
}