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
        Schema::create('wilayahs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('parent_id')
                ->nullable()
                ->comment('NULL jika RW, terisi ID RW jika RT')
                ->constrained('wilayahs')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->enum('tingkat', ['rw', 'rt']);
            $table->string('nomor', 10)->comment('Nomor RW / RT');
            $table->string('kelurahan', 100)->nullable()->comment('Hanya diisi pada tingkat RW');
            $table->timestamps();

            $table->index('parent_id');
            $table->index('tingkat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wilayahs');
    }
};
