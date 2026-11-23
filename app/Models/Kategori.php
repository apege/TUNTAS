<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kategoris';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'warna_peta',
        'aktif',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'aktif' => 'boolean',
        ];
    }

    /**
     * Relasi ke Laporan dalam kategori ini.
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'kategori_id');
    }

    /**
     * Relasi ke Kasus dalam kategori ini.
     */
    public function cases(): HasMany
    {
        return $this->hasMany(Kasus::class, 'kategori_id');
    }
}
