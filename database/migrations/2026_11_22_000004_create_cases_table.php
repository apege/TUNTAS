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
        Schema::create('cases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode', 50)->unique()->comment('Kode unik kasus, cth: KAS-202611-0001');
            $table->foreignUuid('wilayah_id')
                ->comment('Harus wilayah bertingkat RW')
                ->constrained('wilayahs')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignUuid('kategori_id')
                ->constrained('kategoris')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignUuid('koordinator_id')
                ->comment('Admin penanggung jawab')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('judul');
            $table->text('deskripsi');
            $table->enum('prioritas', ['rendah', 'sedang', 'tinggi', 'mendesak'])->default('sedang');
            $table->enum('status', ['menunggu', 'diproses', 'menunggu_konfirmasi', 'selesai', 'dibuka_kembali'])->default('menunggu');
            $table->boolean('perlu_verifikasi_rw')->default(false);
            $table->dateTime('target_selesai')->nullable();
            $table->dateTime('selesai_at')->nullable();
            $table->timestamps();

            $table->index('wilayah_id');
            $table->index('kategori_id');
            $table->index('koordinator_id');
            $table->index('status');
            $table->index('prioritas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cases');
    }
};
