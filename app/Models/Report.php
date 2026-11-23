<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'reports';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'kode',
        'pelapor_id',
        'wilayah_id',
        'kategori_id',
        'kasus_id',
        'judul',
        'uraian',
        'lokasi_detail',
        'blok',
        'latitude',
        'longitude',
        'dampak',
        'urgensi',
        'status_penerimaan',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    /**
     * Relasi ke Pelapor (User).
     */
    public function pelapor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pelapor_id');
    }

    /**
     * Relasi ke Wilayah (Tingkat RT).
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
     * Relasi ke Kasus Terkait.
     */
    public function kasus(): BelongsTo
    {
        return $this->belongsTo(Kasus::class, 'kasus_id');
    }

    /**
     * Relasi ke Lampiran Foto Laporan.
     */
    public function lampirans(): HasMany
    {
        return $this->hasMany(Lampiran::class, 'laporan_id');
    }

    /**
     * Scope laporan milik pelapor tertentu.
     */
    public function scopeMilikPelapor(Builder $query, string $userId): Builder
    {
        return $query->where('pelapor_id', $userId);
    }

    /**
     * Helper status label untuk kebutuhan visual antarmuka.
     */
    public function getStatusLabelAttribute(): string
    {
        if ($this->status_penerimaan === 'baru') {
            return 'Menunggu Verifikasi';
        }

        if ($this->status_penerimaan === 'ditolak') {
            return 'Ditolak';
        }

        if ($this->kasus) {
            return match ($this->kasus->status) {
                'diproses' => 'Sedang Ditangani',
                'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
                'selesai' => 'Selesai',
                'dibuka_kembali' => 'Ditangani Kembali',
                default => 'Diverifikasi',
            };
        }

        return 'Diverifikasi';
    }

    /**
     * Helper status badge styling.
     *
     * @return array{bg: string, text: string, dot: string}
     */
    public function getStatusBadgeAttribute(): array
    {
        if ($this->status_penerimaan === 'baru') {
            return [
                'bg' => 'bg-[#FEF3C7]',
                'text' => 'text-[#B45309]',
                'dot' => 'bg-[#D97706]',
            ];
        }

        if ($this->status_penerimaan === 'ditolak') {
            return [
                'bg' => 'bg-[#FEE2E2]',
                'text' => 'text-[#B91C1C]',
                'dot' => 'bg-[#DC2626]',
            ];
        }

        if ($this->kasus) {
            return match ($this->kasus->status) {
                'diproses' => [
                    'bg' => 'bg-[#E0E7FF]',
                    'text' => 'text-[#3730A3]',
                    'dot' => 'bg-[#4F46E5]',
                ],
                'menunggu_konfirmasi' => [
                    'bg' => 'bg-[#EDE9FE]',
                    'text' => 'text-[#5B21B6]',
                    'dot' => 'bg-[#7C3AED]',
                ],
                'selesai' => [
                    'bg' => 'bg-[#DCFCE7]',
                    'text' => 'text-[#166534]',
                    'dot' => 'bg-[#16A34A]',
                ],
                default => [
                    'bg' => 'bg-[#E0F2FE]',
                    'text' => 'text-[#075985]',
                    'dot' => 'bg-[#0284C7]',
                ],
            };
        }

        return [
            'bg' => 'bg-[#E0F2FE]',
            'text' => 'text-[#075985]',
            'dot' => 'bg-[#0284C7]',
        ];
    }
}
