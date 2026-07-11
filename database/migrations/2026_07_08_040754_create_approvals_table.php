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
        Schema::create('approvals', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relationship
            |--------------------------------------------------------------------------
            */

            // Submission
            $table->foreignId('submission_id')
                ->constrained()
                ->cascadeOnDelete();

            // User yang melakukan approval
            $table->foreignId('approver_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Approval Information
            |--------------------------------------------------------------------------
            */

            // Urutan approval
            $table->unsignedTinyInteger('step');

            // Role approver
            $table->enum('role', [
                'SPV',
                'Manager',
                'Director',
                'Finance',
            ]);

            // Status approval
            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
            ])->default('pending');

            // Approval yang sedang aktif
            $table->boolean('is_current')->default(false);

            // Catatan approver
            $table->text('notes')->nullable();

            // Waktu approval
            $table->timestamp('action_at')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Laravel Default
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

           /*
            |--------------------------------------------------------------------------
            | Index
            |--------------------------------------------------------------------------
            */

            $table->index([
                'submission_id',
                'step',
            ]);

            $table->index([
                'submission_id',
                'status',
            ]);

            $table->index([
                'submission_id',
                'is_current',
            ]);

            $table->index([
                'approver_id',
                'status',
            ]);

            $table->index([
                'role',
                'status',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};