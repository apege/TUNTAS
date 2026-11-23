<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoris = [
            [
                'nama' => 'Sampah & Kebersihan Lingkungan',
                'warna_peta' => '#EF4444', // Merah
                'aktif' => true,
            ],
            [
                'nama' => 'Drainase & Saluran Air',
                'warna_peta' => '#3B82F6', // Biru
                'aktif' => true,
            ],
            [
                'nama' => 'Jalan Lingkungan & Trotoar',
                'warna_peta' => '#F59E0B', // Kuning/Amber
                'aktif' => true,
            ],
            [
                'nama' => 'Penerangan Jalan Umum',
                'warna_peta' => '#10B981', // Hijau
                'aktif' => true,
            ],
            [
                'nama' => 'Fasilitas Umum & Ruang Terbuka',
                'warna_peta' => '#8B5CF6', // Ungu
                'aktif' => true,
            ],
        ];

        foreach ($kategoris as $item) {
            Kategori::firstOrCreate(
                ['nama' => $item['nama']],
                array_merge($item, ['id' => (string) Str::uuid()])
            );
        }
    }
}
