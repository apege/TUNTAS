<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasUuids, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'wilayah_id',
        'nama',
        'email',
        'password',
        'peran',
        'telepon',
        'alamat',
        'notifikasi_aktif',
        'aktif',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'notifikasi_aktif' => 'boolean',
            'aktif' => 'boolean',
        ];
    }

    /**
     * Relasi ke Wilayah
     */
    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id');
    }

    /**
     * Relasi ke Laporan yang dibuat pengguna.
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'pelapor_id');
    }

    /**
     * Relasi ke Pengumuman yang diterbitkan pengguna.
     */
    public function pengumumans(): HasMany
    {
        return $this->hasMany(Pengumuman::class, 'pembuat_id');
    }

    /**
     * Relasi ke Kasus yang dikoordinasikan.
     */
    public function cases(): HasMany
    {
        return $this->hasMany(Kasus::class, 'koordinator_id');
    }

    /**
     * Relasi ke Notifikasi yang diterima pengguna.
     */
    public function notifikasis(): HasMany
    {
        return $this->hasMany(Notifikasi::class, 'penerima_id');
    }
}
