<?php

namespace Tests\Feature;

use App\Models\Kasus;
use App\Models\Kategori;
use App\Models\Pengumuman;
use App\Models\Report;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class WargaDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected Wilayah $rw;

    protected Wilayah $rt;

    protected User $wargaUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rw = Wilayah::create([
            'id' => (string) Str::uuid(),
            'tingkat' => 'rw',
            'nomor' => '05',
            'kelurahan' => 'Sukamaju',
        ]);

        $this->rt = Wilayah::create([
            'id' => (string) Str::uuid(),
            'parent_id' => $this->rw->id,
            'tingkat' => 'rt',
            'nomor' => '02',
            'kelurahan' => null,
        ]);

        $this->wargaUser = User::create([
            'id' => (string) Str::uuid(),
            'wilayah_id' => $this->rt->id,
            'nama' => 'Siti Aminah',
            'email' => 'warga@tuntas.id',
            'password' => Hash::make('password'),
            'peran' => 'warga',
            'telepon' => '081234567893',
            'alamat' => 'Jl. Kenanga No. 8 RT 02 RW 05',
            'notifikasi_aktif' => true,
            'aktif' => true,
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('warga.dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_warga_can_view_dashboard(): void
    {
        $response = $this->actingAs($this->wargaUser)->get(route('warga.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard Warga');
        $response->assertSee('Selamat datang, Siti');
        $response->assertSee('Lingkungan yang nyaman dimulai dari kepedulian kita bersama.');
        $response->assertSee('Laporkan Masalah');
        $response->assertSee('RT 02');
        $response->assertSee('RW 05');
        $response->assertSee('Laporan Saya');
        $response->assertSee('Sedang Ditangani');
        $response->assertSee('Menunggu Verifikasi');
        $response->assertSee('Laporan Selesai');
        $response->assertSee('Perkembangan Laporan Saya');
        $response->assertSee('Pengumuman Warga');
        $response->assertSee('Belum ada laporan pribadi.');
        $response->assertSee('Belum ada pengumuman untuk wilayah Anda.');
        $response->assertSee('Kenali kondisi di sekitar Anda');
        $response->assertSee('Lihat peta');
    }

    public function test_authenticated_warga_sees_dynamic_database_reports_and_announcements(): void
    {
        $kategori = Kategori::create([
            'id' => (string) Str::uuid(),
            'nama' => 'Kebersihan & Lingkungan',
            'warna_peta' => '#EF4444',
            'aktif' => true,
        ]);

        $kasus = Kasus::create([
            'id' => (string) Str::uuid(),
            'kode' => 'KAS-TEST-001',
            'wilayah_id' => $this->rw->id,
            'kategori_id' => $kategori->id,
            'koordinator_id' => $this->wargaUser->id,
            'judul' => 'Penanganan Sampah Liar',
            'deskripsi' => 'Deskripsi kasus penanganan',
            'prioritas' => 'sedang',
            'status' => 'diproses',
        ]);

        // Laporan 1: Sedang ditangani (masuk ke kasus diproses)
        Report::create([
            'id' => (string) Str::uuid(),
            'kode' => 'LAP-202611-0001',
            'pelapor_id' => $this->wargaUser->id,
            'wilayah_id' => $this->rt->id,
            'kategori_id' => $kategori->id,
            'kasus_id' => $kasus->id,
            'judul' => 'Tumpukan Sampah di Depan Gang',
            'uraian' => 'Ada sampah menumpuk di depan gang tolong segera diangkut.',
            'lokasi_detail' => 'Depan gapura RT 02',
            'dampak' => 'sedang',
            'urgensi' => 'tinggi',
            'status_penerimaan' => 'diverifikasi',
        ]);

        // Laporan 2: Menunggu verifikasi (status baru)
        Report::create([
            'id' => (string) Str::uuid(),
            'kode' => 'LAP-202611-0002',
            'pelapor_id' => $this->wargaUser->id,
            'wilayah_id' => $this->rt->id,
            'kategori_id' => $kategori->id,
            'kasus_id' => null,
            'judul' => 'Lampu Jalan Padam',
            'uraian' => 'Lampu jalan mati sejak kemarin malam.',
            'lokasi_detail' => 'Tiang nomor 5',
            'dampak' => 'rendah',
            'urgensi' => 'sedang',
            'status_penerimaan' => 'baru',
        ]);

        // Pengumuman RW yang relevan
        Pengumuman::create([
            'id' => (string) Str::uuid(),
            'pembuat_id' => $this->wargaUser->id,
            'wilayah_id' => $this->rw->id,
            'judul' => 'Jadwal Kerja Bakti Serentak RW 05',
            'isi' => 'Diharapkan kehadiran seluruh warga pada hari Minggu pagi.',
            'jenis' => 'kegiatan',
            'status' => 'diterbitkan',
            'terbit_at' => now(),
        ]);

        $response = $this->actingAs($this->wargaUser)->get(route('warga.dashboard'));

        $response->assertStatus(200);

        // Angka statistik dinamis dari DB
        $response->assertSee('Tumpukan Sampah di Depan Gang');
        $response->assertSee('LAP-202611-0001');
        $response->assertSee('Sedang Ditangani');
        $response->assertSee('Lampu Jalan Padam');
        $response->assertSee('LAP-202611-0002');
        $response->assertSee('Menunggu Verifikasi');
        $response->assertSee('Jadwal Kerja Bakti Serentak RW 05');

        // Pastikan empty state tidak tampil saat ada data
        $response->assertDontSee('Belum ada laporan pribadi.');
        $response->assertDontSee('Belum ada pengumuman untuk wilayah Anda.');

        // Uji coba fitur pencarian dinamis
        $searchResponse = $this->actingAs($this->wargaUser)->get(route('warga.dashboard', ['q' => 'Sampah']));
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('Tumpukan Sampah di Depan Gang');
        $searchResponse->assertDontSee('Lampu Jalan Padam');
    }
}
