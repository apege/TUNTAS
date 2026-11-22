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
        Schema::create('notifikasis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('penerima_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
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
            $table->foreignUuid('pengumuman_id')
                ->nullable()
                ->constrained('pengumumans')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('judul');
            $table->text('pesan');
            $table->dateTime('dibaca_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('penerima_id');
            $table->index('laporan_id');
            $table->index('kasus_id');
            $table->index('pengumuman_id');
            $table->index('dibaca_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifikasis');
    }
};
