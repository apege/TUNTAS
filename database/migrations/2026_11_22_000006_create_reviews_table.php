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
        Schema::create('reviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('jenis', ['verifikasi_rt', 'verifikasi_rw', 'konfirmasi_warga']);
            $table->foreignUuid('laporan_id')
                ->nullable()
                ->constrained('reports')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignUuid('kasus_id')
                ->nullable()
                ->constrained('cases')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignUuid('pemeriksa_id')
                ->comment('Admin RT/RW atau warga pelapor')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->enum('keputusan', ['diterima', 'ditolak', 'perlu_perbaikan', 'tuntas', 'belum_tuntas']);
            $table->text('catatan')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('laporan_id');
            $table->index('kasus_id');
            $table->index('pemeriksa_id');
            $table->index('jenis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
