<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relasi
            |--------------------------------------------------------------------------
            */

            $table->foreignId('category_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Budget
            |--------------------------------------------------------------------------
            */

            // Tahun Anggaran
            $table->year('year');

            // Total Anggaran
            $table->decimal('total_budget', 15, 2);

            // Anggaran yang sudah digunakan
            $table->decimal('used_budget', 15, 2)
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Laravel Default
            |--------------------------------------------------------------------------
            */

            $table->timestamps();
            $table->softDeletes();

            /*
            |--------------------------------------------------------------------------
            | Constraint
            |--------------------------------------------------------------------------
            */

            // Satu kategori hanya boleh memiliki satu budget per tahun
            $table->unique([
                'category_id',
                'year'
            ]);

            // Mempercepat pencarian budget berdasarkan tahun
            $table->index('year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};