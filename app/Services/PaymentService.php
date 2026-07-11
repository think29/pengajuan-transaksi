<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PaymentService
{
    public function __construct(
        protected BudgetService $budgetService
    ) {
    }

    /**
     * Membuat payment setelah seluruh approval selesai.
     */
    public function create(
        Submission $submission
    ): Payment {

        return Payment::create([

            'submission_id' => $submission->id,

            'payment_amount' => $submission->amount,

            'status' => Payment::STATUS_PENDING,

        ]);
    }

    /**
     * Menandai pembayaran selesai.
     */
    public function pay(
        Payment $payment,
        User $finance,
        ?string $referenceNumber = null,
        ?string $paymentMethod = null,
        ?string $notes = null,
    ): Payment {

        DB::transaction(function () use (
            $payment,
            $finance,
            $referenceNumber,
            $paymentMethod,
            $notes
        ) {

            if (! $payment->isPending()) {

                throw new RuntimeException(
                    'Payment sudah diproses.'
                );

            }

            $payment->markPaid(
                $finance,
                $referenceNumber,
                $paymentMethod,
                $notes
            );

            $submission = $payment->submission;

            $this->budgetService
                ->reserveBudget($submission);

            $submission->changeStatus(
                Submission::STATUS_PAID,
                null
            );

        });

        return $payment->refresh();
    }
}