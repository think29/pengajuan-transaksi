<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Submission;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BudgetService
{
    /**
     * Mengambil budget dari submission.
     */
    public function getBudget(Submission $submission): ?Budget
    {
        return $submission->budget;
    }

    /**
     * Mengecek apakah budget tersedia.
     */
    public function hasAvailableBudget(Submission $submission): bool
    {
        $budget = $this->getBudget($submission);

        if (! $budget) {
            return false;
        }

        return $budget->remaining_budget >= $submission->amount;
    }

    /**
     * Memastikan budget masih tersedia.
     *
     * @throws RuntimeException
     */
    public function ensureBudgetAvailable(
        Submission $submission
    ): void {
        if (! $this->hasAvailableBudget($submission)) {
            throw new RuntimeException(
                'Budget tidak mencukupi.'
            );
        }
    }

    /**
     * Mengambil sisa budget.
     */
    public function getRemainingBudget(
        Submission $submission
    ): float {
        return $this->getBudget($submission)?->remaining_budget ?? 0;
    }

    /**
     * Menambah budget terpakai.
     */
    public function reserveBudget(
        Submission $submission
    ): Budget {

        $budget = $this->getBudget($submission);

        if (! $budget) {
            throw new RuntimeException(
                'Budget tidak ditemukan.'
            );
        }

        $this->ensureBudgetAvailable($submission);

        DB::transaction(function () use ($budget, $submission) {

            $budget->increment(
                'used_budget',
                $submission->amount
            );

        });

        return $budget->refresh();
    }

    /**
     * Mengurangi budget terpakai.
     */
    public function releaseBudget(
        Submission $submission
    ): Budget {

        $budget = $this->getBudget($submission);

        if (! $budget) {
            throw new RuntimeException(
                'Budget tidak ditemukan.'
            );
        }

        DB::transaction(function () use ($budget, $submission) {

            $budget->update([
                'used_budget' => max(
                    0,
                    $budget->used_budget - $submission->amount
                ),
            ]);

        });

        return $budget->refresh();
    }

    /**
     * Persentase penggunaan budget.
     */
    public function getUsagePercentage(
        Submission $submission
    ): float {

        $budget = $this->getBudget($submission);

        if (! $budget?->total_budget) {
            return 0;
        }

        return round(
            ($budget->used_budget / $budget->total_budget) * 100,
            2
        );
    }
}