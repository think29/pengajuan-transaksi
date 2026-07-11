<?php

namespace App\Filament\Auth;

use Filament\Auth\Pages\Login as BaseLogin;

class Login extends BaseLogin
{
    public function getHeading(): string
    {
        return 'Sistem Pengajuan Transaksi Pengeluaran';
    }

    public function getSubHeading(): ?string
    {
        return 'Silakan masuk menggunakan akun Anda';
    }
}