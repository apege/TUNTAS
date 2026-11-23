<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wilayah extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wilayahs';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'parent_id',
        'tingkat',
        'nomor',
        'kelurahan',
    ];

    /**
     * Relasi ke Wilayah Induk (RW jika ini RT)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Relasi ke RT turunan jika ini RW
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Relasi ke Pengguna
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'wilayah_id');
    }

    /**
     * Relasi ke Laporan di wilayah ini.
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'wilayah_id');
    }

    /**
     * Relasi ke Kasus di wilayah ini (tingkat RW).
     */
    public function cases(): HasMany
    {
        return $this->hasMany(Kasus::class, 'wilayah_id');
    }

    /**
     * Relasi ke Pengumuman di wilayah ini.
     */
    public function pengumumans(): HasMany
    {
        return $this->hasMany(Pengumuman::class, 'wilayah_id');
    }
}
