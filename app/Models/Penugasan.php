<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penugasan extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'penugasans';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'kasus_id',
        'petugas_id',
        'pemberi_tugas_id',
        'status',
        'instruksi',
        'ditugaskan_at',
        'diterima_at',
        'selesai_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ditugaskan_at' => 'datetime',
            'diterima_at' => 'datetime',
            'selesai_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke Kasus.
     */
    public function kasus(): BelongsTo
    {
        return $this->belongsTo(Kasus::class, 'kasus_id');
    }

    /**
     * Relasi ke Petugas pelaksana.
     */
    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    /**
     * Relasi ke Admin pemberi instruksi.
     */
    public function pemberiTugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pemberi_tugas_id');
    }

    /**
     * Relasi ke catatan progres petugas.
     */
    public function progres(): HasMany
    {
        return $this->hasMany(Progres::class, 'penugasan_id');
    }
}
