<?php

namespace Database\Seeders;

use App\Models\AreaLingkungan;
use App\Models\Wilayah;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AreaLingkunganSeeder extends Seeder
{
    /**
     * Isi data area lingkungan per RT (mencakup jalan utama, gang, hingga titik patokan warga).
     */
    public function run(): void
    {
        $rw05 = Wilayah::where('tingkat', 'rw')->where('nomor', '05')->first();
        if (! $rw05) {
            return;
        }

        $rtList = Wilayah::where('parent_id', $rw05->id)
            ->where('tingkat', 'rt')
            ->get()
            ->keyBy('nomor');

        $mappingArea = [
            '01' => [
                ['nama' => 'Jl. Mawar (Jalan Utama)', 'jenis' => 'jalan'],
                ['nama' => 'Gang 1 / Sekitar Musala', 'jenis' => 'gang'],
                ['nama' => 'Gang 2 / Sisi Timur', 'jenis' => 'gang'],
                ['nama' => 'Sekitar Pos Ronda RT 01', 'jenis' => 'area'],
                ['nama' => 'Tikungan Mawar Barat', 'jenis' => 'area'],
            ],
            '02' => [
                ['nama' => 'Jl. Melati (Jalan Utama)', 'jenis' => 'jalan'],
                ['nama' => 'Gang Pos Ronda', 'jenis' => 'gang'],
                ['nama' => 'Area TPS / Lapangan', 'jenis' => 'area'],
                ['nama' => 'Blok B-2', 'jenis' => 'blok'],
                ['nama' => 'Blok B-3', 'jenis' => 'blok'],
            ],
            '03' => [
                ['nama' => 'Jl. Cempaka Utama', 'jenis' => 'jalan'],
                ['nama' => 'Gang 1 / Pinggir Kali', 'jenis' => 'gang'],
                ['nama' => 'Area Jembatan RT 03', 'jenis' => 'area'],
                ['nama' => 'Sekitar Balai Warga', 'jenis' => 'area'],
            ],
        ];

        foreach ($mappingArea as $nomorRt => $areas) {
            $rt = $rtList->get($nomorRt);
            if (! $rt) {
                continue;
            }

            foreach ($areas as $area) {
                AreaLingkungan::firstOrCreate(
                    [
                        'wilayah_id' => $rt->id,
                        'nama' => $area['nama'],
                    ],
                    [
                        'id' => (string) Str::uuid(),
                        'jenis' => $area['jenis'],
                        'aktif' => true,
                    ]
                );
            }
        }
    }
}
