<?php

namespace Tests\Feature;

use App\Models\Kasus;
use App\Models\Kategori;
use App\Models\Report;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class WargaLaporanSayaTest extends TestCase
{
    use RefreshDatabase;

    protected Wilayah $rw;

    protected Wilayah $rt;

    protected User $warga;

    protected User $otherWarga;

    protected User $koordinator;

    protected Kategori $katSampah;

    protected Kategori $katPju;

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
            'nomor' => '01',
        ]);

        $this->koordinator = User::create([
            'id' => (string) Str::uuid(),
            'wilayah_id' => $this->rw->id,
            'nama' => 'Hendro Prasetyo',
            'email' => 'admin.rw@tuntas.id',
            'password' => Hash::make('password'),
            'peran' => 'admin_rw',
            'telepon' => '081234567890',
            'alamat' => 'Jl. Bougenville No. 1',
            'notifikasi_aktif' => true,
            'aktif' => true,
        ]);

        $this->warga = User::create([
            'id' => (string) Str::uuid(),
            'wilayah_id' => $this->rt->id,
            'nama' => 'Siti Aminah',
            'email' => 'warga@tuntas.id',
            'password' => Hash::make('password'),
            'peran' => 'warga',
            'telepon' => '081234567893',
            'alamat' => 'Jl. Mawar No. 8',
            'notifikasi_aktif' => true,
            'aktif' => true,
        ]);

        $this->otherWarga = User::create([
            'id' => (string) Str::uuid(),
            'wilayah_id' => $this->rt->id,
            'nama' => 'Budi Santoso',
            'email' => 'budi@tuntas.id',
            'password' => Hash::make('password'),
            'peran' => 'warga',
            'telepon' => '081234567899',
            'alamat' => 'Jl. Mawar No. 20',
            'notifikasi_aktif' => true,
            'aktif' => true,
        ]);

        $this->katSampah = Kategori::create([
            'id' => (string) Str::uuid(),
            'nama' => 'Sampah & Kebersihan Lingkungan',
            'warna_peta' => '#EF4444',
            'aktif' => true,
        ]);

        $this->katPju = Kategori::create([
            'id' => (string) Str::uuid(),
            'nama' => 'Penerangan Jalan Umum',
            'warna_peta' => '#10B981',
            'aktif' => true,
        ]);
    }

    public function test_guest_is_redirected_from_laporan_saya(): void
    {
        $this->get(route('warga.laporan.index'))
            ->assertRedirect(route('login'));
    }

    public function test_warga_hanya_melihat_laporan_miliknya_sendiri_dan_filter_bekerja(): void
    {
        $kasusKonfirmasi = Kasus::create([
            'id' => (string) Str::uuid(),
            'wilayah_id' => $this->rw->id,
            'kategori_id' => $this->katPju->id,
            'koordinator_id' => $this->koordinator->id,
            'kode' => 'KAS-202611-0101',
            'judul' => 'Perbaikan PJU Tikungan',
            'deskripsi' => 'Penggantian lampu jalan.',
            'prioritas' => 'sedang',
            'status' => 'menunggu_konfirmasi',
        ]);

        Report::create([
            'id' => (string) Str::uuid(),
            'kode' => 'LAP-202611-0101',
            'pelapor_id' => $this->warga->id,
            'wilayah_id' => $this->rt->id,
            'kategori_id' => $this->katSampah->id,
            'judul' => 'Tumpukan Sampah Gang Mawar',
            'uraian' => 'Sampah menumpuk di pojok gang sejak kemarin sore.',
            'lokasi_detail' => 'Gang Mawar 1',
            'dampak' => 'sedang',
            'urgensi' => 'sedang',
            'status_penerimaan' => 'baru',
        ]);

        Report::create([
            'id' => (string) Str::uuid(),
            'kode' => 'LAP-202611-0102',
            'pelapor_id' => $this->warga->id,
            'wilayah_id' => $this->rt->id,
            'kategori_id' => $this->katPju->id,
            'kasus_id' => $kasusKonfirmasi->id,
            'judul' => 'Lampu PJU Padam Total',
            'uraian' => 'Lampu mati selama dua malam berturut-turut.',
            'lokasi_detail' => 'Tikungan Mawar Barat',
            'dampak' => 'sedang',
            'urgensi' => 'tinggi',
            'status_penerimaan' => 'diverifikasi',
        ]);

        Report::create([
            'id' => (string) Str::uuid(),
            'kode' => 'LAP-202611-9999',
            'pelapor_id' => $this->otherWarga->id,
            'wilayah_id' => $this->rt->id,
            'kategori_id' => $this->katSampah->id,
            'judul' => 'Laporan Rahasia Warga Lain',
            'uraian' => 'Ini milik warga lain yang tidak boleh tampil.',
            'lokasi_detail' => 'Gang Melati',
            'dampak' => 'rendah',
            'urgensi' => 'rendah',
            'status_penerimaan' => 'baru',
        ]);

        // 1. Halaman utama Laporan Saya
        $response = $this->actingAs($this->warga)->get(route('warga.laporan.index'));
        $response->assertOk();
        $response->assertViewIs('dashboard.laporan_saya');
        $response->assertSee('Tumpukan Sampah Gang Mawar');
        $response->assertSee('Lampu PJU Padam Total');
        $response->assertDontSee('Laporan Rahasia Warga Lain');
        $response->assertSee('1 laporan menunggu konfirmasi Anda');

        // 2. Filter status konfirmasi
        $konfirmasiRes = $this->actingAs($this->warga)->get(route('warga.laporan.index', ['status' => 'konfirmasi']));
        $konfirmasiRes->assertOk();
        $konfirmasiRes->assertSee('Lampu PJU Padam Total');
        $konfirmasiRes->assertDontSee('Tumpukan Sampah Gang Mawar');

        // 3. Pencarian kata kunci
        $searchRes = $this->actingAs($this->warga)->get(route('warga.laporan.index', ['q' => 'Sampah Gang']));
        $searchRes->assertOk();
        $searchRes->assertSee('Tumpukan Sampah Gang Mawar');
        $searchRes->assertDontSee('Lampu PJU Padam Total');
    }

    public function test_warga_dapat_mengonfirmasi_laporan_tuntas_atau_buka_kembali(): void
    {
        $kasus = Kasus::create([
            'id' => (string) Str::uuid(),
            'wilayah_id' => $this->rw->id,
            'kategori_id' => $this->katPju->id,
            'koordinator_id' => $this->koordinator->id,
            'kode' => 'KAS-202611-0201',
            'judul' => 'Perbaikan PJU',
            'deskripsi' => 'PJU diperbaiki.',
            'prioritas' => 'tinggi',
            'status' => 'menunggu_konfirmasi',
        ]);

        $report = Report::create([
            'id' => (string) Str::uuid(),
            'kode' => 'LAP-202611-0201',
            'pelapor_id' => $this->warga->id,
            'wilayah_id' => $this->rt->id,
            'kategori_id' => $this->katPju->id,
            'kasus_id' => $kasus->id,
            'judul' => 'Lampu Mati Depan Pos',
            'uraian' => 'Lampu depan pos ronda mati total perlu diganti.',
            'lokasi_detail' => 'Pos Ronda RT 01',
            'dampak' => 'sedang',
            'urgensi' => 'tinggi',
            'status_penerimaan' => 'diverifikasi',
        ]);

        // Warga lain dilarang konfirmasi (403)
        $this->actingAs($this->otherWarga)
            ->post(route('warga.laporan.confirm', $report), ['keputusan' => 'tuntas'])
            ->assertForbidden();

        // Pelapor konfirmasi belum tuntas tanpa catatan harus gagal validasi
        $this->actingAs($this->warga)
            ->post(route('warga.laporan.confirm', $report), ['keputusan' => 'belum_tuntas'])
            ->assertSessionHasErrors(['catatan']);

        // Pelapor konfirmasi tuntas
        $this->actingAs($this->warga)
            ->post(route('warga.laporan.confirm', $report), [
                'keputusan' => 'tuntas',
                'catatan' => 'Lampu sudah menyala terang kembali.',
            ])
            ->assertRedirect(route('warga.laporan.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('cases', [
            'id' => $kasus->id,
            'status' => 'selesai',
        ]);

        $this->assertDatabaseHas('reviews', [
            'laporan_id' => $report->id,
            'kasus_id' => $kasus->id,
            'pemeriksa_id' => $this->warga->id,
            'jenis' => 'konfirmasi_warga',
            'keputusan' => 'tuntas',
        ]);

        $this->assertDatabaseHas('notifikasis', [
            'penerima_id' => $this->koordinator->id,
            'laporan_id' => $report->id,
            'judul' => 'Warga Mengonfirmasi Masalah Tuntas',
        ]);
    }
}
