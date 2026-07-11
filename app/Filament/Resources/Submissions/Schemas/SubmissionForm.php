<?php

namespace App\Filament\Resources\Submissions\Schemas;

use App\Models\Budget;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SubmissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()

                    ->afterStateUpdated(function ($set, $state) {

                        $budget = Budget::query()
                            ->where('category_id', $state)
                            ->where('year', now()->year)
                            ->first();

                        $set(
                            'budget_id',
                            $budget?->id
                        );

                    }),

                Hidden::make('budget_id')
                    ->dehydrated(),

                DatePicker::make('submission_date')
                    ->label('Tanggal Pengajuan')
                    ->default(now())
                    ->native(false)
                    ->required(),

                TextInput::make('amount')
                    ->label('Nominal')
                    ->numeric()
                    ->prefix('Rp')
                    ->minValue(1)
                    ->required(),

                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(4)
                    ->columnSpanFull(),

                FileUpload::make('attachment')
                    ->label('Lampiran')
                    ->disk('public')
                    ->directory('submissions')
                    ->acceptedFileTypes([
                        'application/pdf',
                        'image/jpeg',
                        'image/png',
                    ])
                    ->maxSize(5120)
                    ->downloadable()
                    ->openable()
                    ->previewable()
                    ->columnSpanFull(),

            ]);
    }
}