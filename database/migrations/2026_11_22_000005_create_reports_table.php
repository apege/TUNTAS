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
        Schema::create('reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode', 50)->unique()->comment('Kode unik laporan, cth: LAP-202611-0001');
            $table->foreignUuid('pelapor_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignUuid('wilayah_id')
                ->comment('Harus wilayah bertingkat RT')
                ->constrained('wilayahs')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignUuid('kategori_id')
                ->constrained('kategoris')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignUuid('kasus_id')
                ->nullable()
                ->comment('Terisi jika digabungkan ke kasus')
                ->constrained('cases')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->string('judul');
            $table->text('uraian');
            $table->string('lokasi_detail');
            $table->string('blok', 50)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->enum('dampak', ['rendah', 'sedang', 'tinggi'])->default('sedang');
            $table->enum('urgensi', ['rendah', 'sedang', 'tinggi', 'mendesak'])->default('sedang');
            $table->enum('status_penerimaan', ['baru', 'diverifikasi', 'ditolak'])->default('baru');
            $table->timestamps();

            $table->index('pelapor_id');
            $table->index('wilayah_id');
            $table->index('kategori_id');
            $table->index('kasus_id');
            $table->index('status_penerimaan');
            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
