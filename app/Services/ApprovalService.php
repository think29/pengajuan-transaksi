<?php

namespace App\Services;

use App\Models\Approval;
use App\Models\Submission;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Support\Facades\DB;
use RuntimeException;


class ApprovalService
{

    protected BudgetService $budgetService;

    protected PaymentService $paymentService;

    public function __construct(
        BudgetService $budgetService,
        PaymentService $paymentService,
    ) {
        $this->budgetService = $budgetService;
        $this->paymentService = $paymentService;
    }

    public function submit(Submission $submission): Submission
    {
        DB::transaction(function () use ($submission) {

            // Pastikan budget masih tersedia
            $this->budgetService->ensureBudgetAvailable($submission);

            // Tidak boleh submit dua kali
            if (! $submission->isDraft()) {
                throw new RuntimeException(
                    'Pengajuan sudah pernah disubmit.'
                );
            }

            // Tentukan workflow approval
            $workflow = $this->determineWorkflow($submission);

            // Buat seluruh approval step
            $this->createApprovalSteps(
                $submission,
                $workflow
            );

            // Tentukan status pertama
            $status = match ($workflow[0]) {

                Approval::ROLE_SPV =>
                    Submission::STATUS_WAITING_SPV,

                Approval::ROLE_MANAGER =>
                    Submission::STATUS_WAITING_MANAGER,

                Approval::ROLE_DIRECTOR =>
                    Submission::STATUS_WAITING_DIRECTOR,

                Approval::ROLE_FINANCE =>
                    Submission::STATUS_WAITING_FINANCE,

                default =>
                    Submission::STATUS_SUBMITTED,
            };

            $submission->changeStatus(
                $status,
                $workflow[0]
            );

        });

        $submission = $submission->refresh();

        ActivityLogService::log(
            'SUBMIT_APPROVAL',
            $submission,
            'Pengajuan dikirim ke workflow approval'
        );

        return $submission;
    }

    public function approve(
    Submission $submission,
    User $user,
    ?string $note = null,
    ): Submission {

        DB::transaction(function () use (
            $submission,
            $user,
            $note
        ) {

            $approval = $this->getCurrentApproval(
                $submission
            );

            if (! $approval) {

                throw new RuntimeException(
                    'Approval tidak ditemukan.'
                );

            }

            if (! $approval->canApprove($user)) {

                throw new RuntimeException(
                    'Anda tidak berhak melakukan approval.'
                );

            }

            // Approve current step
            $approval->approve($note);

            ActivityLogService::log(
                'APPROVE',
                $submission,
                sprintf(
                    '%s menyetujui sebagai %s%s',
                    $user->name,
                    $approval->role,
                    $note ? ' | Catatan: '.$note : ''
                )
            );

            // Jika Finance
            if ($approval->isFinance()) {

                $this->finishSubmission(
                    $submission
                );

                return;

            }

            // Aktifkan approval berikutnya
            $this->activateNextApproval(
                $submission
            );

        });

        return $submission->refresh();
    }

    public function reject(
        Submission $submission,
        User $user,
        ?string $note = null,
    ): Submission {

        DB::transaction(function () use (
            $submission,
            $user,
            $note
        ) {

            $approval = $this->getCurrentApproval(
                $submission
            );

            if (! $approval) {
                throw new RuntimeException(
                    'Approval tidak ditemukan.'
                );
            }

            if (! $approval->canApprove($user)) {

                throw new RuntimeException(
                    'Anda tidak berhak melakukan approval.'
                );

            }

            $approval->reject($note);

            ActivityLogService::log(
                'REJECT',
                $submission,
                sprintf(
                    '%s menolak sebagai %s%s',
                    $user->name,
                    $approval->role,
                    $note ? ' | Catatan: '.$note : ''
                )
            );

            $this->rejectSubmission($submission);

        });

        return $submission->refresh();
    }

    protected function determineWorkflow(
    Submission $submission
    ): array {

        /*
        |--------------------------------------------------------------------------
        | PO Produk
        |--------------------------------------------------------------------------
        */

        if ($submission->category->is_po_product) {

            return [
                Approval::ROLE_DIRECTOR,
                Approval::ROLE_FINANCE,
            ];

        }

        /*
        |--------------------------------------------------------------------------
        | PO Non Produk
        |--------------------------------------------------------------------------
        */

        $workflow = [
            Approval::ROLE_SPV,
        ];

        if ($submission->amount > 5000000) {

            $workflow[] = Approval::ROLE_MANAGER;

        }

        if ($submission->amount > 10000000) {

            $workflow[] = Approval::ROLE_DIRECTOR;

        }

        // Finance selalu terakhir
        $workflow[] = Approval::ROLE_FINANCE;

        return $workflow;
    }

    protected function createApprovalSteps(
    Submission $submission,
    array $workflow
    ): void {

        foreach ($workflow as $index => $role) {

            $approver = User::byRole($role);

            if (! $approver) {
                throw new RuntimeException(
                    "User dengan role {$role} tidak ditemukan."
                );
            }

            Approval::create([

                'submission_id' => $submission->id,

                'approver_id' => $approver->id,

                'step' => $index + 1,

                'role' => $role,

                'status' => Approval::STATUS_PENDING,

                'is_current' => $index === 0,

            ]);

        }
    }

    protected function getCurrentApproval(
    Submission $submission
    ): ?Approval {

        return $submission
            ->approvals()
            ->where('is_current', true)
            ->first();

    }

    protected function activateNextApproval(
    Submission $submission
    ): void {

        $current = $submission
            ->approvals()
            ->approved()
            ->latest('step')
            ->first();

        $next = $submission
            ->approvals()
            ->pending()
            ->where('step', '>', $current->step)
            ->orderBy('step')
            ->first();

        if (! $next) {

            $this->finishSubmission(
                $submission
            );

            return;

        }

        $next->activate();

        $status = match ($next->role) {

            Approval::ROLE_SPV =>
                Submission::STATUS_WAITING_SPV,

            Approval::ROLE_MANAGER =>
                Submission::STATUS_WAITING_MANAGER,

            Approval::ROLE_DIRECTOR =>
                Submission::STATUS_WAITING_DIRECTOR,

            Approval::ROLE_FINANCE =>
                Submission::STATUS_WAITING_FINANCE,

            default =>
                Submission::STATUS_SUBMITTED,
        };

        $submission->changeStatus(
            $status,
            $next->role
        );

        ActivityLogService::log(
            'NEXT_APPROVER',
            $submission,
            sprintf(
                'Pengajuan diteruskan ke %s',
                $next->role
            )
        );

    }

    protected function finishSubmission(
    Submission $submission
    ): void {
        
        // Buat payment
        $payment = $this->paymentService
            ->create($submission);

        // Langsung proses pembayaran
        $this->paymentService->pay(
            $payment,
            auth()->user(),
            'AUTO-' . now()->format('YmdHis'),
            'Transfer',
            'Pembayaran otomatis setelah approval Finance.'
        );

        ActivityLogService::log(
            'FINISH_APPROVAL',
            $submission,
            'Seluruh approval selesai'
        );

    }

    protected function rejectSubmission(
    Submission $submission
    ): void {

        $submission->changeStatus(
            Submission::STATUS_REJECTED,
            null
        );

        $submission
            ->approvals()
            ->pending()
            ->update([
                'is_current' => false,
            ]);

    }

}