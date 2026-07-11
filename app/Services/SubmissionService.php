<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Submission;
use App\Services\ActivityLogService;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

class SubmissionService
{

    protected ApprovalService $approvalService;

    public function __construct(
        ApprovalService $approvalService,
    ) {
        $this->approvalService = $approvalService;
    }

    public function create(array $data): Submission
    {
        return DB::transaction(function () use ($data) {

            $budget = Budget::find($data['budget_id']);

            if (! $budget) {
                throw new RuntimeException(
                    'Budget tidak ditemukan.'
                );
            }

            $submission = Submission::create([

                'submission_number' => Submission::generateSubmissionNumber(),

                'user_id' => auth()->id(),

                'category_id' => $data['category_id'],

                'budget_id' => $budget->id,

                'submission_date' => $data['submission_date'],

                'amount' => $data['amount'],

                'description' => $data['description'] ?? null,

                'attachment' => $data['attachment'] ?? null,

                'status' => Submission::STATUS_DRAFT,

                'current_approval' => null,

            ]);

            ActivityLogService::log(
                'CREATE_SUBMISSION',
                $submission,
                'Membuat pengajuan ' . $submission->submission_number
            );

            return $submission;

        });
    }

    public function update(
        Model $record,
        array $data
    ): Submission
    {
        if (! $record instanceof Submission) {
            throw new RuntimeException('Record bukan Submission.');
        }

        $submission = $record;

        DB::transaction(function () use (
            $submission,
            $data
        ) {

            if (! $submission->isDraft()) {
                throw new RuntimeException(
                    'Hanya draft yang boleh diubah.'
                );
            }

            if (empty($data['budget_id'])) {
                throw new RuntimeException(
                    'Budget belum ditemukan untuk kategori yang dipilih.'
                );
            }

            $submission->update([
                'category_id' => $data['category_id'],
                'budget_id' => $data['budget_id'],
                'submission_date' => $data['submission_date'],
                'amount' => $data['amount'],
                'description' => $data['description'] ?? null,
                'attachment' => $data['attachment'] ?? $submission->attachment,
            ]);

            ActivityLogService::log(
                'UPDATE_SUBMISSION',
                $submission,
                'Mengubah pengajuan ' . $submission->submission_number
            );

        });

        return $submission->refresh();
    }

    public function submit(
        Submission $submission
    ): Submission {

        if (! $submission->isDraft()) {

            throw new RuntimeException(
                'Hanya draft yang dapat disubmit.'
            );

        }

        $submission = $this->approvalService->submit($submission);

        ActivityLogService::log(
            'SUBMIT_SUBMISSION',
            $submission,
            'Submit pengajuan ' . $submission->submission_number
        );

        return $submission;

    }

    public function delete(Submission $submission): void
    {
        if (! $submission->isDraft()) {
            throw new RuntimeException(
                'Hanya draft yang boleh dihapus.'
            );
        }

        $submission->delete();

        ActivityLogService::log(
            'DELETE_DRAFT',
            $submission,
            'Menghapus draft ' . $submission->submission_number
        );
    }
}