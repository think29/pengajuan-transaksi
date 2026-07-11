<?php

namespace App\Filament\Widgets;

use App\Models\Budget;
use App\Models\Submission;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [

            Stat::make('Total Pengajuan', Submission::count())
                ->description('Seluruh pengajuan')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),

            Stat::make(
                'Menunggu Approval',
                Submission::whereNotIn('status', [
                    Submission::STATUS_DRAFT,
                    Submission::STATUS_PAID,
                    Submission::STATUS_REJECTED,
                ])->count()
            )
                ->description('Sedang diproses')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make(
                'Sudah Dibayar',
                Submission::where('status', Submission::STATUS_PAID)->count()
            )
                ->description('Selesai')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make(
                'Ditolak',
                Submission::where('status', Submission::STATUS_REJECTED)->count()
            )
                ->description('Rejected')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),

            Stat::make(
                'Total Budget',
                'Rp ' . number_format(Budget::sum('total_budget'), 0, ',', '.')
            )
                ->description('Budget tersedia')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('info'),

            Stat::make(
                'Budget Terpakai',
                'Rp ' . number_format(Budget::sum('used_budget'), 0, ',', '.')
            )
                ->description('Sudah digunakan')
                ->descriptionIcon('heroicon-m-wallet')
                ->color('gray'),

        ];
    }
}