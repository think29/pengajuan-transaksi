<?php

namespace App\Filament\Resources\Submissions\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Support\Facades\Storage;

class SubmissionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                /*
                |--------------------------------------------------------------------------
                | Informasi Pengajuan
                |--------------------------------------------------------------------------
                */

                Section::make('Informasi Pengajuan')
                    ->columns(3)
                    ->schema([

                        TextEntry::make('submission_number')
                            ->label('No Pengajuan'),

                        TextEntry::make('submission_date')
                            ->label('Tanggal')
                            ->date(),

                        TextEntry::make('user.name')
                            ->label('Pengaju'),

                        TextEntry::make('category.name')
                            ->label('Kategori'),

                        TextEntry::make('amount')
                            ->label('Nominal')
                            ->money('IDR'),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state) => match ($state) {

                                'draft' => 'gray',

                                'waiting_spv' => 'warning',

                                'waiting_manager' => 'warning',

                                'waiting_director' => 'warning',

                                'waiting_finance' => 'warning',

                                'paid' => 'success',

                                'rejected' => 'danger',

                                default => 'gray',

                            }),

                        TextEntry::make('currentApproval.role')
                            ->label('Approval Saat Ini')
                            ->badge()
                            ->color(fn (?string $state) => match ($state) {

                                'SPV' => 'info',

                                'Manager' => 'warning',

                                'Director' => 'danger',

                                'Finance' => 'success',

                                default => 'gray',

                            })
                            ->placeholder('-'),

                    ]),

                /*
                |--------------------------------------------------------------------------
                | Budget
                |--------------------------------------------------------------------------
                */

                Section::make('Informasi Budget')
                    ->columns(2)
                    ->schema([

                        TextEntry::make('budget.year')
                            ->label('Tahun Budget'),

                        TextEntry::make('budget.total_budget')
                            ->label('Budget')
                            ->money('IDR'),

                        TextEntry::make('budget.used_budget')
                            ->label('Terpakai')
                            ->money('IDR'),

                        TextEntry::make('budget.remaining_budget')
                            ->label('Sisa Budget')
                            ->money('IDR')
                            ->color(fn ($state) => $state > 0 ? 'success' : 'danger'),

                    ]),

                /*
                |--------------------------------------------------------------------------
                | Deskripsi
                |--------------------------------------------------------------------------
                */

                Section::make('Deskripsi')
                    ->schema([

                        TextEntry::make('description')
                            ->placeholder('-')
                            ->columnSpanFull(),

                    ]),

                /*
                |--------------------------------------------------------------------------
                | Lampiran
                |--------------------------------------------------------------------------
                */

                Section::make('Lampiran')
                    ->schema([

                        TextEntry::make('attachment')
                            ->label('File Lampiran')
                            ->formatStateUsing(fn ($state) => $state
                                ? 'Download Lampiran'
                                : '-')
                            ->url(fn ($record) => $record->attachment
                                ? Storage::url($record->attachment)
                                : null)
                            ->openUrlInNewTab(),

                    ]),

                /*
                |--------------------------------------------------------------------------
                | Timeline Approval
                |--------------------------------------------------------------------------
                */

                Section::make('Timeline Approval')
                    ->description('Riwayat proses approval')
                    ->schema([

                        RepeatableEntry::make('approvals')
                            ->label('')
                            ->schema([

                                Grid::make(2)
                                    ->schema([

                                        TextEntry::make('step')
                                            ->label('Step')
                                            ->badge(),

                                        TextEntry::make('role')
                                            ->label('Role')
                                            ->badge(),

                                        TextEntry::make('approver.name')
                                            ->label('Approver')
                                            ->placeholder('-'),

                                        TextEntry::make('status')
                                            ->label('Status')
                                            ->badge()
                                            ->icon(fn ($state) => match ($state) {

                                                'approved' => 'heroicon-m-check-circle',

                                                'pending' => 'heroicon-m-clock',

                                                'rejected' => 'heroicon-m-x-circle',

                                                default => 'heroicon-m-minus-circle',

                                            })
                                            ->color(fn ($state) => match ($state) {

                                                'approved' => 'success',

                                                'pending' => 'warning',

                                                'rejected' => 'danger',

                                                default => 'gray',

                                            }),

                                        TextEntry::make('action_at')
                                            ->label('Tanggal')
                                            ->dateTime()
                                            ->placeholder('-'),

                                        TextEntry::make('notes')
                                            ->label('Catatan')
                                            ->placeholder('-')
                                            ->columnSpanFull(),

                                    ]),

                            ]),

                    ]),

            ]);
    }
}