<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kasus extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cases';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'kode',
        'wilayah_id',
        'kategori_id',
        'koordinator_id',
        'judul',
        'deskripsi',
        'prioritas',
        'status',
        'perlu_verifikasi_rw',
        'target_selesai',
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
            'perlu_verifikasi_rw' => 'boolean',
            'target_selesai' => 'datetime',
            'selesai_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke Wilayah (Tingkat RW).
     */
    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id');
    }

    /**
     * Relasi ke Kategori.
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    /**
     * Relasi ke Koordinator (Admin penanggung jawab).
     */
    public function koordinator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'koordinator_id');
    }

    /**
     * Relasi ke Laporan-laporan warga yang digabungkan ke kasus ini.
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'kasus_id');
    }
}
