<?php

namespace App\Filament\Resources\Submissions\Pages;

use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\Submissions\SubmissionResource;
use App\Models\Submission;
use App\Services\SubmissionService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditSubmission extends EditRecord
{
    protected static string $resource = SubmissionResource::class;

    protected function authorizeAccess(): void
    {
        abort_unless(
            $this->record->status === 'draft',
            403
        );
    }

    protected function handleRecordUpdate(
        Model $record,
        array $data
    ): Model {

        return app(SubmissionService::class)
            ->update($record, $data);

    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Draft berhasil diperbarui.');
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

}