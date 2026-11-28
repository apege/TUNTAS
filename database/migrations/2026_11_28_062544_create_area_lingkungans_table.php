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
        Schema::create('area_lingkungans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('wilayah_id')
                ->comment('Relasi ke wilayah tingkat RT')
                ->constrained('wilayahs')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('nama', 80)->comment('Nama jalan, gang, blok, atau patokan area lingkungan');
            $table->enum('jenis', ['jalan', 'gang', 'blok', 'area'])->default('area');
            $table->boolean('aktif')->default(true);
            $table->timestamps();

            $table->unique(['wilayah_id', 'nama']);
            $table->index(['wilayah_id', 'aktif']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('area_lingkungans');
    }
};
