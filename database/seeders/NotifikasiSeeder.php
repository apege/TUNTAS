<?php

namespace Database\Seeders;

use App\Models\Notifikasi;
use App\Models\Pengumuman;
use App\Models\Report;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NotifikasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wargaSiti = User::where('email', 'warga@tuntas.id')->first();
        if (! $wargaSiti) {
            return;
        }

        $now = Carbon::now();
        $reports = Report::where('pelapor_id', $wargaSiti->id)->latest('created_at')->get();
        $pengumumans = Pengumuman::where('status', 'diterbitkan')->latest('terbit_at')->get();

        $report1 = $reports->first();
        $report2 = $reports->skip(1)->first();
        $pengumuman1 = $pengumumans->first();

        $notifications = [
            [
                'id' => (string) Str::uuid(),
                'penerima_id' => $wargaSiti->id,
                'laporan_id' => $report1?->id,
                'kasus_id' => $report1?->kasus_id,
                'pengumuman_id' => null,
                'judul' => 'Laporan Anda Sedang Ditindaklanjuti',
                'pesan' => 'Petugas telah ditugaskan untuk menindaklanjuti laporan "'.$report1?->judul.'".',
                'dibaca_at' => null,
                'created_at' => $now->copy()->subMinutes(25),
            ],
            [
                'id' => (string) Str::uuid(),
                'penerima_id' => $wargaSiti->id,
                'laporan_id' => null,
                'kasus_id' => null,
                'pengumuman_id' => $pengumuman1?->id,
                'judul' => 'Pengumuman Baru di Wilayah Anda',
                'pesan' => 'Ketua RT menerbitkan pengumuman baru: "'.$pengumuman1?->judul.'".',
                'dibaca_at' => null,
                'created_at' => $now->copy()->subHours(2),
            ],
            [
                'id' => (string) Str::uuid(),
                'penerima_id' => $wargaSiti->id,
                'laporan_id' => $report2?->id,
                'kasus_id' => $report2?->kasus_id,
                'pengumuman_id' => null,
                'judul' => 'Laporan Berhasil Diverifikasi',
                'pesan' => 'Laporan "'.$report2?->judul.'" telah diperiksa dan diverifikasi oleh Admin RT.',
                'dibaca_at' => $now->copy()->subDay(),
                'created_at' => $now->copy()->subDays(1),
            ],
        ];

        foreach ($notifications as $data) {
            Notifikasi::firstOrCreate(
                ['penerima_id' => $data['penerima_id'], 'judul' => $data['judul']],
                $data
            );
        }
    }
}
