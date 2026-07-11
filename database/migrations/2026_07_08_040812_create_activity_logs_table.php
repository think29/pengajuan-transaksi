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
        Schema::create('activity_logs', function (Blueprint $table) {

            $table->id();

            // User
            $table->foreignId('user_id')
                ->constrained()
                ->restrictOnDelete();

            // Submission
            $table->foreignId('submission_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // Aktivitas
            $table->string('activity');

            // Deskripsi
            $table->text('description')->nullable();

            // IP Address
            $table->string('ip_address',45)->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Index
            |--------------------------------------------------------------------------
            */

            $table->index('activity');

            $table->index([
                'user_id',
                'created_at'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};