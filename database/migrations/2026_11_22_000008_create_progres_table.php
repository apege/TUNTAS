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
        Schema::create('progres', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('penugasan_id')
                ->constrained('penugasans')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignUuid('pencatat_id')
                ->comment('Petugas pencatat progres')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->text('catatan');
            $table->enum('jenis', ['pembaruan', 'kendala', 'penyelesaian'])->default('pembaruan');
            $table->timestamp('created_at')->useCurrent();

            $table->index('penugasan_id');
            $table->index('pencatat_id');
            $table->index('jenis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progres');
    }
};
