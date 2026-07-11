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
        Schema::create('submissions', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Identitas Pengajuan
            |--------------------------------------------------------------------------
            */

            // Nomor Pengajuan (Auto Generate)
            $table->string('submission_number')->unique();

            // Staff Pengaju
            $table->foreignId('user_id')
                ->constrained()
                ->restrictOnDelete();

            // Kategori Pengajuan
            $table->foreignId('category_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('budget_id')
                ->constrained()
                ->restrictOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Data Pengajuan
            |--------------------------------------------------------------------------
            */

            // Tanggal Pengajuan
            $table->date('submission_date')->index();

            // Nilai Pengajuan
            $table->decimal('amount', 15, 2)->index();

            // Deskripsi Pengajuan
            $table->text('description');

            /*
            |--------------------------------------------------------------------------
            | Lampiran
            |--------------------------------------------------------------------------
            */

            // Lampiran utama (sesuai requirement soal)
            $table->string('attachment')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Workflow Approval
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'draft',
                'submitted',
                'waiting_spv',
                'waiting_manager',
                'waiting_director',
                'waiting_finance',
                'paid',
                'rejected',
            ])->default('draft');

            /*
             * Menentukan approver berikutnya.
             * Null berarti workflow telah selesai
             * (Paid atau Rejected).
             */
            $table->enum('current_approval', [
                'SPV',
                'Manager',
                'Director',
                'Finance',
            ])->nullable();

            /*
            |--------------------------------------------------------------------------
            | Laravel Default
            |--------------------------------------------------------------------------
            */

            $table->timestamps();
            $table->softDeletes();

            /*
            |--------------------------------------------------------------------------
            | Composite Index
            |--------------------------------------------------------------------------
            */

            // Dashboard Approval
            $table->index([
                'status',
                'current_approval',
            ]);

            // Dashboard Staff
            $table->index([
                'user_id',
                'status',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};