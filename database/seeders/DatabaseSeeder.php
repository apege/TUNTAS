<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat RW 05 Kelurahan Sukamaju
        $rw05 = Wilayah::firstOrCreate(
            ['tingkat' => 'rw', 'nomor' => '05', 'kelurahan' => 'Sukamaju'],
            ['id' => (string) Str::uuid()]
        );

        // 2. Buat RT 01 di bawah RW 05
        $rt01 = Wilayah::firstOrCreate(
            ['parent_id' => $rw05->id, 'tingkat' => 'rt', 'nomor' => '01'],
            ['id' => (string) Str::uuid(), 'kelurahan' => null]
        );

        // 3. User Seeders
        $defaultPassword = Hash::make('password');

        User::firstOrCreate(
            ['email' => 'admin.rt@tuntas.id'],
            [
                'id' => (string) Str::uuid(),
                'wilayah_id' => $rt01->id,
                'nama' => 'Bambang Sudarsono (Ketua RT 01)',
                'password' => $defaultPassword,
                'peran' => 'admin_rt',
                'telepon' => '081234567891',
                'alamat' => 'Jl. Mawar No. 12 RT 01 RW 05',
                'notifikasi_aktif' => true,
                'aktif' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin.rw@tuntas.id'],
            [
                'id' => (string) Str::uuid(),
                'wilayah_id' => $rw05->id,
                'nama' => 'Hendro Prasetyo (Ketua RW 05)',
                'password' => $defaultPassword,
                'peran' => 'admin_rw',
                'telepon' => '081234567892',
                'alamat' => 'Jl. Melati No. 01 RT 03 RW 05',
                'notifikasi_aktif' => true,
                'aktif' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'warga@tuntas.id'],
            [
                'id' => (string) Str::uuid(),
                'wilayah_id' => $rt01->id,
                'nama' => 'Siti Aminah',
                'password' => $defaultPassword,
                'peran' => 'warga',
                'telepon' => '081234567893',
                'alamat' => 'Jl. Kenanga No. 8 RT 01 RW 05',
                'notifikasi_aktif' => true,
                'aktif' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'petugas@tuntas.id'],
            [
                'id' => (string) Str::uuid(),
                'wilayah_id' => $rw05->id,
                'nama' => 'Dedi Iskandar',
                'password' => $defaultPassword,
                'peran' => 'petugas',
                'telepon' => '081234567894',
                'alamat' => 'Jl. Anggrek No. 15 RT 02 RW 05',
                'notifikasi_aktif' => true,
                'aktif' => true,
            ]
        );
    }
}
