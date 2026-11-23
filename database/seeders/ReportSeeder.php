<?php

namespace Database\Seeders;

use App\Models\Kasus;
use App\Models\Kategori;
use App\Models\Report;
use App\Models\User;
use App\Models\Wilayah;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wargaSiti = User::where('email', 'warga@tuntas.id')->first();
        $adminRw = User::where('email', 'admin.rw@tuntas.id')->first();
        $rw05 = Wilayah::where('tingkat', 'rw')->where('nomor', '05')->first();

        if (! $wargaSiti || ! $adminRw || ! $rw05) {
            return;
        }

        $katSampah = Kategori::where('nama', 'like', '%Sampah%')->first();
        $katDrainase = Kategori::where('nama', 'like', '%Drainase%')->first();
        $katPJU = Kategori::where('nama', 'like', '%Penerangan%')->first();

        $now = Carbon::now();

        // 1. Kasus Sedang Diproses (Sampah)
        $kasusDiproses = Kasus::firstOrCreate(
            ['kode' => 'KAS-202611-0001'],
            [
                'id' => (string) Str::uuid(),
                'wilayah_id' => $rw05->id,
                'kategori_id' => $katSampah?->id ?? Kategori::first()->id,
                'koordinator_id' => $adminRw->id,
                'judul' => 'Penanganan Timbunan Sampah Liar Sekitar TPS',
                'deskripsi' => 'Pengangkutan tumpukan sampah dan sterilisasi area sekitar tempat pembuangan sementara.',
                'prioritas' => 'tinggi',
                'status' => 'diproses',
                'perlu_verifikasi_rw' => false,
                'target_selesai' => $now->copy()->addDays(2),
                'created_at' => $now->copy()->subDays(1),
            ]
        );

        // 2. Kasus Sudah Selesai (PJU)
        $kasusSelesai = Kasus::firstOrCreate(
            ['kode' => 'KAS-202611-0002'],
            [
                'id' => (string) Str::uuid(),
                'wilayah_id' => $rw05->id,
                'kategori_id' => $katPJU?->id ?? Kategori::first()->id,
                'koordinator_id' => $adminRw->id,
                'judul' => 'Perbaikan Bohlam PJU Padam Tikungan Mawar',
                'deskripsi' => 'Penggantian bohlam LED 40W dan pemeriksaan sambungan kabel tiang PJU.',
                'prioritas' => 'rendah',
                'status' => 'selesai',
                'perlu_verifikasi_rw' => false,
                'target_selesai' => $now->copy()->subDays(2),
                'selesai_at' => $now->copy()->subDays(2),
                'created_at' => $now->copy()->subDays(4),
            ]
        );

        // 3. Laporan Warga Siti Aminah
        // Laporan 1: Sedang Ditangani
        Report::firstOrCreate(
            ['kode' => 'LAP-202611-0001'],
            [
                'id' => (string) Str::uuid(),
                'pelapor_id' => $wargaSiti->id,
                'wilayah_id' => $wargaSiti->wilayah_id,
                'kategori_id' => $katSampah?->id ?? Kategori::first()->id,
                'kasus_id' => $kasusDiproses->id,
                'judul' => 'Tumpukan Sampah Liar Dekat Tempat Pembuangan Sementara',
                'uraian' => 'Sampah rumah tangga mulai menumpuk hingga ke bahu jalan dan menimbulkan bau tidak sedap. Dikhawatirkan mengundang lalat dan penyakit.',
                'lokasi_detail' => 'Jl. Mawar No. 10 dekat gardu pos ronda',
                'blok' => 'Blok B-3',
                'dampak' => 'sedang',
                'urgensi' => 'tinggi',
                'status_penerimaan' => 'diverifikasi',
                'created_at' => $now->copy()->subDays(1),
            ]
        );

        // Laporan 2: Menunggu Verifikasi (Baru)
        Report::firstOrCreate(
            ['kode' => 'LAP-202611-0002'],
            [
                'id' => (string) Str::uuid(),
                'pelapor_id' => $wargaSiti->id,
                'wilayah_id' => $wargaSiti->wilayah_id,
                'kategori_id' => $katDrainase?->id ?? Kategori::first()->id,
                'kasus_id' => null,
                'judul' => 'Saluran Air Mampet Menyebabkan Genangan di Depan Rumah',
                'uraian' => 'Saluran air tersumbat endapan lumpur dan sampah daun, air meluap ke halaman saat hujan deras kemarin sore.',
                'lokasi_detail' => 'Jl. Mawar No. 08 depan rumah',
                'blok' => 'Blok B-2',
                'dampak' => 'sedang',
                'urgensi' => 'sedang',
                'status_penerimaan' => 'baru',
                'created_at' => $now->copy()->subHours(4),
            ]
        );

        // Laporan 3: Selesai
        Report::firstOrCreate(
            ['kode' => 'LAP-202611-0003'],
            [
                'id' => (string) Str::uuid(),
                'pelapor_id' => $wargaSiti->id,
                'wilayah_id' => $wargaSiti->wilayah_id,
                'kategori_id' => $katPJU?->id ?? Kategori::first()->id,
                'kasus_id' => $kasusSelesai->id,
                'judul' => 'Lampu Penerangan Jalan Umum Padam di Tikungan Mawar Barat',
                'uraian' => 'Lampu jalan tiang nomor 4 sudah 3 malam mati total membuat jalanan gelap dan rawan.',
                'lokasi_detail' => 'Tiang PJU No. 4, Tikungan Mawar Barat',
                'blok' => 'Blok A-1',
                'dampak' => 'rendah',
                'urgensi' => 'sedang',
                'status_penerimaan' => 'diverifikasi',
                'created_at' => $now->copy()->subDays(4),
            ]
        );
    }
}
