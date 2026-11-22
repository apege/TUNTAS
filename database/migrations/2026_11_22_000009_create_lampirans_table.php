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
        Schema::create('lampirans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('pengunggah_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignUuid('laporan_id')
                ->nullable()
                ->comment('Foto kondisi awal laporan')
                ->constrained('reports')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignUuid('progres_id')
                ->nullable()
                ->comment('Foto dokumentasi progres')
                ->constrained('progres')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('storage_key')->comment('Path file di Laravel storage');
            $table->string('nama_file')->comment('Nama asli file');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('ukuran_byte');
            $table->timestamp('created_at')->useCurrent();

            $table->index('pengunggah_id');
            $table->index('laporan_id');
            $table->index('progres_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lampirans');
    }
};
