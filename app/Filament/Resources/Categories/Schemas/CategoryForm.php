<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Informasi Kategori')
                    ->description('Data master kategori pengajuan')
                    ->schema([

                        TextInput::make('name')
                            ->label('Nama Kategori')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder('Contoh: Pembelian Barang'),

                        Toggle::make('is_po_product')
                            ->label('Menggunakan Purchase Order')
                            ->helperText('Aktifkan jika kategori ini memerlukan proses Purchase Order')
                            ->default(false),

                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(4)
                            ->maxLength(1000)
                            ->placeholder('Deskripsi kategori...')
                            ->columnSpanFull(),

                    ])
                    ->columns(2),

            ]);
    }
}