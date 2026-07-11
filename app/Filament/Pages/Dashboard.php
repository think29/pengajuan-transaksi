<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getHeading(): string
    {
        return 'Dashboard';
    }

    public function getSubheading(): ?string
    {
        return 'Selamat datang kembali, ' . auth()->user()->name . '.';
    }
}