<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory, HasUuids;

    /**
     * Table reviews hanya memiliki kolom created_at.
     */
    public const UPDATED_AT = null;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'reviews';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'jenis',
        'laporan_id',
        'kasus_id',
        'pemeriksa_id',
        'keputusan',
        'catatan',
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
            'created_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke Laporan terkait.
     */
    public function laporan(): BelongsTo
    {
        return $this->belongsTo(Report::class, 'laporan_id');
    }

    /**
     * Relasi ke Kasus terkait.
     */
    public function kasus(): BelongsTo
    {
        return $this->belongsTo(Kasus::class, 'kasus_id');
    }

    /**
     * Relasi ke Pemeriksa (Admin RT/RW atau Warga).
     */
    public function pemeriksa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pemeriksa_id');
    }
}
