<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan wilayah dasar sudah ada
        $rw05 = Wilayah::where('tingkat', 'rw')->where('nomor', '05')->first();
        if (! $rw05) {
            $this->call(WilayahSeeder::class);
            $rw05 = Wilayah::where('tingkat', 'rw')->where('nomor', '05')->first();
        }

        $rt01 = Wilayah::where('parent_id', $rw05->id)->where('tingkat', 'rt')->where('nomor', '01')->first();
        $rt02 = Wilayah::where('parent_id', $rw05->id)->where('tingkat', 'rt')->where('nomor', '02')->first();

        $defaultPassword = Hash::make('password');

        $users = [
            // -------------------------------------------------------------
            // 1. ROLE: ADMIN RW (Wilayah harus bertingkat RW)
            // -------------------------------------------------------------
            [
                'email' => 'admin.rw@tuntas.id',
                'wilayah_id' => $rw05->id,
                'nama' => 'Hendro Prasetyo, S.Sos (Ketua RW 05)',
                'password' => $defaultPassword,
                'peran' => 'admin_rw',
                'telepon' => '081234567890',
                'alamat' => 'Jl. Bougenville No. 01, RW 05, Sukamaju',
                'notifikasi_aktif' => true,
                'aktif' => true,
            ],

            // -------------------------------------------------------------
            // 2. ROLE: ADMIN RT (Wilayah harus bertingkat RT)
            // -------------------------------------------------------------
            [
                'email' => 'admin.rt01@tuntas.id',
                'wilayah_id' => $rt01->id,
                'nama' => 'Bambang Sudarsono (Ketua RT 01)',
                'password' => $defaultPassword,
                'peran' => 'admin_rt',
                'telepon' => '081234567891',
                'alamat' => 'Jl. Mawar No. 12, RT 01 / RW 05, Sukamaju',
                'notifikasi_aktif' => true,
                'aktif' => true,
            ],
            [
                'email' => 'admin.rt02@tuntas.id',
                'wilayah_id' => $rt02->id,
                'nama' => 'Supriyadi (Ketua RT 02)',
                'password' => $defaultPassword,
                'peran' => 'admin_rt',
                'telepon' => '081234567892',
                'alamat' => 'Jl. Melati No. 05, RT 02 / RW 05, Sukamaju',
                'notifikasi_aktif' => true,
                'aktif' => true,
            ],

            // -------------------------------------------------------------
            // 3. ROLE: PETUGAS (Wilayah bertingkat RW - bertugas lintas RT)
            // -------------------------------------------------------------
            [
                'email' => 'petugas@tuntas.id',
                'wilayah_id' => $rw05->id,
                'nama' => 'Dedi Iskandar (Koordinator Lapangan)',
                'password' => $defaultPassword,
                'peran' => 'petugas',
                'telepon' => '081234567893',
                'alamat' => 'Jl. Anggrek No. 15, RT 02 / RW 05, Sukamaju',
                'notifikasi_aktif' => true,
                'aktif' => true,
            ],
            [
                'email' => 'petugas2@tuntas.id',
                'wilayah_id' => $rw05->id,
                'nama' => 'Rahmat Hidayat (Teknisi Lingkungan)',
                'password' => $defaultPassword,
                'peran' => 'petugas',
                'telepon' => '081234567894',
                'alamat' => 'Jl. Cempaka No. 07, RT 03 / RW 05, Sukamaju',
                'notifikasi_aktif' => true,
                'aktif' => true,
            ],

            // -------------------------------------------------------------
            // 4. ROLE: WARGA (Wilayah harus bertingkat RT)
            // -------------------------------------------------------------
            [
                'email' => 'warga@tuntas.id',
                'wilayah_id' => $rt01->id,
                'nama' => 'Siti Aminah',
                'password' => $defaultPassword,
                'peran' => 'warga',
                'telepon' => '081234567895',
                'alamat' => 'Jl. Mawar No. 08, RT 01 / RW 05, Sukamaju',
                'notifikasi_aktif' => true,
                'aktif' => true,
            ],
            [
                'email' => 'warga2@tuntas.id',
                'wilayah_id' => $rt02->id,
                'nama' => 'Budi Santoso',
                'password' => $defaultPassword,
                'peran' => 'warga',
                'telepon' => '081234567896',
                'alamat' => 'Jl. Melati No. 19, RT 02 / RW 05, Sukamaju',
                'notifikasi_aktif' => true,
                'aktif' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                array_merge($userData, ['id' => (string) Str::uuid()])
            );
        }
    }
}
