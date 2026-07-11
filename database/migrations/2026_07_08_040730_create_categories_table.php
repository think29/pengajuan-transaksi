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
        Schema::create('categories', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Category Information
            |--------------------------------------------------------------------------
            */

            // Nama Kategori
            $table->string('name', 100)
                ->unique();

            // Penanda kategori PO Produk
            $table->boolean('is_po_product')
                ->default(false)
                ->index();

            // Deskripsi
            $table->text('description')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Laravel Default
            |--------------------------------------------------------------------------
            */

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};