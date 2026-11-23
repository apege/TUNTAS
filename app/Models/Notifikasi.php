<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notifikasi extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'notifikasis';

    public const UPDATED_AT = null;

    protected $fillable = [
        'penerima_id',
        'laporan_id',
        'kasus_id',
        'pengumuman_id',
        'judul',
        'pesan',
        'dibaca_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'dibaca_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke penerima notifikasi (User).
     */
    public function penerima(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penerima_id');
    }

    /**
     * Relasi ke Laporan terkait (jika ada).
     */
    public function laporan(): BelongsTo
    {
        return $this->belongsTo(Report::class, 'laporan_id');
    }

    /**
     * Relasi ke Kasus terkait (jika ada).
     */
    public function kasus(): BelongsTo
    {
        return $this->belongsTo(Kasus::class, 'kasus_id');
    }

    /**
     * Relasi ke Pengumuman terkait (jika ada).
     */
    public function pengumuman(): BelongsTo
    {
        return $this->belongsTo(Pengumuman::class, 'pengumuman_id');
    }

    /**
     * Cek apakah notifikasi belum dibaca.
     */
    public function isUnread(): bool
    {
        return $this->dibaca_at === null;
    }

    /**
     * Tandai notifikasi sebagai telah dibaca.
     */
    public function markAsRead(): bool
    {
        if ($this->dibaca_at === null) {
            $this->dibaca_at = now();

            return $this->save();
        }

        return true;
    }
}
