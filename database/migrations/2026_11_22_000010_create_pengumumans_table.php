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
        Schema::create('pengumumans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('pembuat_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignUuid('wilayah_id')
                ->comment('RW = seluruh RW, RT = khusus RT tersebut')
                ->constrained('wilayahs')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('judul');
            $table->text('isi');
            $table->enum('jenis', ['informasi', 'imbauan', 'kegiatan', 'darurat'])->default('informasi');
            $table->enum('status', ['draft', 'diterbitkan', 'diarsipkan'])->default('draft');
            $table->dateTime('terbit_at')->nullable();
            $table->timestamps();

            $table->index('pembuat_id');
            $table->index('wilayah_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengumumans');
    }
};
