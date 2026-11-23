<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengumuman extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pengumumans';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'pembuat_id',
        'wilayah_id',
        'judul',
        'isi',
        'jenis',
        'status',
        'terbit_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'terbit_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke Pembuat Pengumuman (User).
     */
    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pembuat_id');
    }

    /**
     * Relasi ke Wilayah (RW atau RT).
     */
    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id');
    }

    /**
     * Scope pengumuman yang sudah diterbitkan.
     */
    public function scopeDiterbitkan(Builder $query): Builder
    {
        return $query->where('status', 'diterbitkan');
    }

    /**
     * Scope pengumuman relevan untuk wilayah tertentu (RT dan RW induk).
     *
     * @param  list<string>|string  $wilayahIds
     */
    public function scopeUntukWilayah(Builder $query, array|string $wilayahIds): Builder
    {
        $ids = is_array($wilayahIds) ? $wilayahIds : [$wilayahIds];

        return $query->whereIn('wilayah_id', $ids);
    }

    /**
     * Helper jenis badge styling.
     *
     * @return array{label: string, bg: string, text: string}
     */
    public function getJenisBadgeAttribute(): array
    {
        return match ($this->jenis) {
            'darurat' => [
                'label' => 'Darurat',
                'bg' => 'bg-[#FEE2E2]',
                'text' => 'text-[#DC2626]',
            ],
            'imbauan' => [
                'label' => 'Imbauan',
                'bg' => 'bg-[#FEF3C7]',
                'text' => 'text-[#D97706]',
            ],
            'kegiatan' => [
                'label' => 'Kegiatan',
                'bg' => 'bg-[#E0E7FF]',
                'text' => 'text-[#4F46E5]',
            ],
            default => [
                'label' => 'Informasi',
                'bg' => 'bg-[#E8F1EC]',
                'text' => 'text-[#245640]',
            ],
        };
    }
}
