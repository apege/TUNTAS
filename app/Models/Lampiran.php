<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lampiran extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'lampirans';

    /**
     * Indicates if the model should be timestamped.
     * Table lampirans only has created_at timestamp.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'pengunggah_id',
        'laporan_id',
        'progres_id',
        'storage_key',
        'nama_file',
        'mime_type',
        'ukuran_byte',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ukuran_byte' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke Pengunggah (User).
     */
    public function pengunggah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengunggah_id');
    }

    /**
     * Relasi ke Laporan.
     */
    public function laporan(): BelongsTo
    {
        return $this->belongsTo(Report::class, 'laporan_id');
    }
}
