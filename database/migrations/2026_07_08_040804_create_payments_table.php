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
        Schema::create('payments', function (Blueprint $table) {

            $table->id();

            // Submission
            $table->foreignId('submission_id')
                ->constrained()
                ->cascadeOnDelete();

            // Finance
            $table->foreignId('finance_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Nominal Pembayaran
            $table->decimal('payment_amount',15,2);

            // Tanggal Bayar
            $table->date('payment_date')
                ->nullable()
                ->index();

            // Metode Pembayaran
            $table->string('payment_method')
                ->nullable();

            // Nomor Referensi
            $table->string('reference_number')
                ->nullable();

            // Status
            $table->enum('status',[
                'pending',
                'paid',
                'rejected'
            ])->default('pending');

            // Catatan
            $table->text('notes')->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Composite Index
            |--------------------------------------------------------------------------
            */

            $table->index([
                'finance_id',
                'status'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};