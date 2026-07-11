<?php

namespace App\Filament\Resources\Submissions\Tables;

use App\Services\SubmissionService;
use App\Services\ApprovalService;
use App\Services\PaymentService;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SubmissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->defaultSort('created_at', 'desc')

            ->columns([

                TextColumn::make('submission_number')
                    ->label('No Pengajuan')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('submission_date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Pengaju')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->searchable(),

                TextColumn::make('amount')
                    ->label('Nominal')
                    ->money('IDR')
                    ->sortable(),

                BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'draft',

                        'warning' => [
                            'submitted',
                            'waiting_spv',
                            'waiting_manager',
                            'waiting_director',
                            'waiting_finance',
                        ],

                        'success' => 'paid',

                        'danger' => 'rejected',
                    ]),

                BadgeColumn::make('current_approval')
                    ->label('Approval')
                    ->default('-')
                    ->colors([
                        'info' => 'SPV',
                        'warning' => 'Manager',
                        'primary' => 'Director',
                        'success' => 'Finance',
                    ]),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->since()
                    ->sortable(),

            ])

            ->filters([

                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'submitted' => 'Submitted',
                        'waiting_spv' => 'Waiting SPV',
                        'waiting_manager' => 'Waiting Manager',
                        'waiting_director' => 'Waiting Director',
                        'waiting_finance' => 'Waiting Finance',
                        'paid' => 'Paid',
                        'rejected' => 'Rejected',
                    ]),

                SelectFilter::make('category')
                    ->relationship('category', 'name'),

            ])

            ->recordActions([

                ViewAction::make(),

                EditAction::make()
                    ->visible(fn ($record) => $record->status === 'draft'),

                // ==========================
                // SUBMIT
                // ==========================

                Action::make('submit')

                    ->label('Submit')

                    ->icon('heroicon-o-paper-airplane')

                    ->color('success')

                    ->requiresConfirmation()

                    ->visible(fn ($record) =>

                        $record->isDraft()

                        && auth()->id() === $record->user_id

                    )

                    ->action(function ($record) {

                        try {

                            app(SubmissionService::class)
                                ->submit($record);

                            Notification::make()

                                ->title('Berhasil')

                                ->body('Pengajuan berhasil dikirim.')

                                ->success()

                                ->send();

                        } catch (\Throwable $e) {

                            Notification::make()

                                ->title('Gagal')

                                ->body($e->getMessage())

                                ->danger()

                                ->send();

                        }

                    }),

                // ==========================
                // APPROVE
                // ==========================

                Action::make('approve')

                    ->label('Approve')

                    ->icon('heroicon-o-check-circle')

                    ->color('success')

                    ->visible(function ($record) {

                        $approval = $record
                            ->approvals()
                            ->pending()
                            ->where('is_current', true)
                            ->first();

                        if (! $approval) {
                            return false;
                        }

                        return auth()->check()
                            && auth()->user()->hasRole($approval->role);

                    })

                    ->form([

                        Textarea::make('note')
                            ->label('Catatan Approval')
                            ->rows(3)
                            ->placeholder('Opsional'),

                    ])

                    ->requiresConfirmation()

                    ->action(function ($record, array $data) {

                        try {

                            app(ApprovalService::class)
                                ->approve(
                                    $record,
                                    auth()->user(),
                                    $data['note'] ?? null,
                                );

                            Notification::make()

                                ->title('Berhasil')

                                ->body('Approval berhasil diproses.')

                                ->success()

                                ->send();

                        } catch (\Throwable $e) {

                            Notification::make()

                                ->title('Gagal')

                                ->body($e->getMessage())

                                ->danger()

                                ->send();

                        }

                    }),

                // ==========================
                // REJECT
                // ==========================

                Action::make('reject')

                    ->label('Reject')

                    ->icon('heroicon-o-x-circle')

                    ->color('danger')

                    ->visible(function ($record) {

                        $approval = $record
                            ->approvals()
                            ->pending()
                            ->where('is_current', true)
                            ->first();

                        if (! $approval) {
                            return false;
                        }

                        return auth()->check()
                            && auth()->user()->hasRole($approval->role);

                    })

                    ->form([

                        Textarea::make('note')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->rows(3)

                    ])

                    ->requiresConfirmation()

                    ->modalHeading('Tolak Pengajuan')

                    ->modalDescription(
                        'Pengajuan akan dihentikan dan tidak dapat dilanjutkan.'
                    )

                    ->action(function ($record, array $data) {

                        try {

                            app(ApprovalService::class)
                                ->reject(
                                    $record,
                                    auth()->user(),
                                    $data['note']
                                );

                            Notification::make()

                                ->success()

                                ->title('Pengajuan berhasil ditolak.')

                                ->send();

                        } catch (\Throwable $e) {

                            Notification::make()

                                ->danger()

                                ->title('Gagal')

                                ->body($e->getMessage())

                                ->send();

                        }

                    }),


            ])

            ->toolbarActions([]);

    }
}