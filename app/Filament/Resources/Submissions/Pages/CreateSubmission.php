<?php

namespace App\Filament\Resources\Submissions\Pages;

use App\Filament\Resources\Submissions\SubmissionResource;
use App\Models\Submission;
use App\Services\SubmissionService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateSubmission extends CreateRecord
{
    protected static string $resource = SubmissionResource::class;

    protected function handleRecordCreation(array $data): Submission
    {
        return app(SubmissionService::class)
            ->create($data);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Draft berhasil dibuat.');
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}