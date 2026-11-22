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
        Schema::create('penugasans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('kasus_id')
                ->constrained('cases')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignUuid('petugas_id')
                ->comment('Petugas pelaksana')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignUuid('pemberi_tugas_id')
                ->comment('Admin pemberi instruksi')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->enum('status', ['ditugaskan', 'diterima', 'dikerjakan', 'selesai', 'dibatalkan'])->default('ditugaskan');
            $table->text('instruksi')->nullable();
            $table->dateTime('ditugaskan_at')->useCurrent();
            $table->dateTime('diterima_at')->nullable();
            $table->dateTime('selesai_at')->nullable();
            $table->timestamps();

            $table->index('kasus_id');
            $table->index('petugas_id');
            $table->index('pemberi_tugas_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penugasans');
    }
};
