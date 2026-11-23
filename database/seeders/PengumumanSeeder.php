<?php

namespace Database\Seeders;

use App\Models\Pengumuman;
use App\Models\User;
use App\Models\Wilayah;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PengumumanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRw = User::where('email', 'admin.rw@tuntas.id')->first();
        $adminRt01 = User::where('email', 'admin.rt01@tuntas.id')->first();
        $adminRt02 = User::where('email', 'admin.rt02@tuntas.id')->first();

        $rw05 = Wilayah::where('tingkat', 'rw')->where('nomor', '05')->first();
        $rt01 = Wilayah::where('tingkat', 'rt')->where('nomor', '01')->first();
        $rt02 = Wilayah::where('tingkat', 'rt')->where('nomor', '02')->first();

        if (! $adminRw || ! $rw05) {
            return;
        }

        $now = Carbon::now();

        $pengumumans = [
            [
                'pembuat_id' => $adminRw->id,
                'wilayah_id' => $rw05->id,
                'judul' => 'Peringatan Waspada DBD & Jadwal Fogging Wilayah RW 05',
                'isi' => 'Ditemukan indikasi jentik nyamuk di beberapa titik genangan air. Petugas kesehatan lingkungan akan melakukan fogging bertahap pada hari Sabtu pukul 08.00 WIB. Mohon warga menutup makanan dan mengamankan hewan peliharaan.',
                'jenis' => 'darurat',
                'status' => 'diterbitkan',
                'terbit_at' => $now->copy()->subHours(3),
            ],
            [
                'pembuat_id' => $adminRw->id,
                'wilayah_id' => $rw05->id,
                'judul' => 'Kerja Bakti Akbar Pembersihan Saluran Air Menghadapi Musim Hujan',
                'isi' => 'Seluruh warga RW 05 diimbau untuk berpartisipasi dalam kerja bakti serentak membersihkan drainase, selokan depan rumah, dan pemangkasan dahan pohon yang berpotensi tumbang pada hari Minggu mendatang.',
                'jenis' => 'kegiatan',
                'status' => 'diterbitkan',
                'terbit_at' => $now->copy()->subDays(2),
            ],
        ];

        if ($adminRt01 && $rt01) {
            $pengumumans[] = [
                'pembuat_id' => $adminRt01->id,
                'wilayah_id' => $rt01->id,
                'judul' => 'Jadwal Pengangkutan Sampah Terpilah Lingkungan RT 01',
                'isi' => 'Mulai pekan ini, armada pengangkut sampah daur ulang (anorganik) akan berkeliling setiap Selasa dan Jumat pagi. Mohon warga memisahkan sampah botol plastik, kardus, dan kaleng.',
                'jenis' => 'informasi',
                'status' => 'diterbitkan',
                'terbit_at' => $now->copy()->subDays(1),
            ];
        }

        if ($adminRt02 && $rt02) {
            $pengumumans[] = [
                'pembuat_id' => $adminRt02->id,
                'wilayah_id' => $rt02->id,
                'judul' => 'Pemasangan Lampu LED Penerangan Jalan Gang Melati RT 02 Selesai',
                'isi' => 'Pemasangan 8 titik lampu penerangan jalan umum (PJU) LED baru di sepanjang Gang Melati telah rampung. Warga dapat segera melaporkan melalui aplikasi TUNTAS jika terdapat kendala pencahayaan.',
                'jenis' => 'informasi',
                'status' => 'diterbitkan',
                'terbit_at' => $now->copy()->subDays(3),
            ];
        }

        foreach ($pengumumans as $item) {
            Pengumuman::firstOrCreate(
                ['judul' => $item['judul'], 'wilayah_id' => $item['wilayah_id']],
                array_merge($item, ['id' => (string) Str::uuid()])
            );
        }
    }
}
