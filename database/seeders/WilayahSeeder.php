<?php

namespace Database\Seeders;

use App\Models\Wilayah;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WilayahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. RW 05 Sukamaju
        $rw05 = Wilayah::firstOrCreate(
            [
                'tingkat' => 'rw',
                'nomor' => '05',
                'kelurahan' => 'Sukamaju',
            ],
            [
                'id' => (string) Str::uuid(),
                'parent_id' => null,
            ]
        );

        // RT-RT di bawah RW 05
        Wilayah::firstOrCreate(
            [
                'parent_id' => $rw05->id,
                'tingkat' => 'rt',
                'nomor' => '01',
            ],
            [
                'id' => (string) Str::uuid(),
                'kelurahan' => null,
            ]
        );

        Wilayah::firstOrCreate(
            [
                'parent_id' => $rw05->id,
                'tingkat' => 'rt',
                'nomor' => '02',
            ],
            [
                'id' => (string) Str::uuid(),
                'kelurahan' => null,
            ]
        );

        Wilayah::firstOrCreate(
            [
                'parent_id' => $rw05->id,
                'tingkat' => 'rt',
                'nomor' => '03',
            ],
            [
                'id' => (string) Str::uuid(),
                'kelurahan' => null,
            ]
        );

        // 2. RW 06 Sukamaju (opsional untuk data pembanding / multi-RW)
        $rw06 = Wilayah::firstOrCreate(
            [
                'tingkat' => 'rw',
                'nomor' => '06',
                'kelurahan' => 'Sukamaju',
            ],
            [
                'id' => (string) Str::uuid(),
                'parent_id' => null,
            ]
        );

        Wilayah::firstOrCreate(
            [
                'parent_id' => $rw06->id,
                'tingkat' => 'rt',
                'nomor' => '01',
            ],
            [
                'id' => (string) Str::uuid(),
                'kelurahan' => null,
            ]
        );
    }
}
