<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Lampiran;
use App\Models\Notifikasi;
use App\Models\Report;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class WargaCreateReportTest extends TestCase
{
    use RefreshDatabase;

    protected Wilayah $rw;

    protected Wilayah $rt;

    protected User $wargaUser;

    protected User $adminRt;

    protected Kategori $kategoriSampah;

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
            'alamat' => 'Jl. Mawar No. 8',
            'notifikasi_aktif' => true,
            'aktif' => true,
        ]);

        $this->adminRt = User::create([
            'id' => (string) Str::uuid(),
            'wilayah_id' => $this->rt->id,
            'nama' => 'Bambang Supriyanto',
            'email' => 'admin.rt@tuntas.id',
            'password' => Hash::make('password'),
            'peran' => 'admin_rt',
            'telepon' => '081234567890',
            'alamat' => 'Jl. Mawar No. 1',
            'notifikasi_aktif' => true,
            'aktif' => true,
        ]);

        $this->kategoriSampah = Kategori::create([
            'id' => (string) Str::uuid(),
            'nama' => 'Sampah & Kebersihan Lingkungan',
            'warna_peta' => '#EF4444',
            'aktif' => true,
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('warga.laporan.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_halaman_buat_laporan_dapat_diakses_oleh_warga(): void
    {
        $response = $this->actingAs($this->wargaUser)->get(route('warga.laporan.create'));

        $response->assertOk();
        $response->assertViewIs('dashboard.buat_laporan');
        $response->assertSee('Buat Laporan');
        $response->assertSee('Informasi Permasalahan');
        $response->assertSee('Sampah');
        $response->assertSee('LANGKAH KECIL, DAMPAK BESAR');
    }

    public function test_warga_dapat_mengirim_laporan_baru_dengan_sukses(): void
    {
        $payload = [
            'kategori_id' => $this->kategoriSampah->id,
            'judul' => 'Tumpukan Sampah Menumpuk di Dekat Pos Ronda',
            'lokasi_detail' => 'Jl. Mawar Gang 2 samping pos ronda RT 02',
            'blok' => 'Blok A',
            'dampak' => 'sedang',
            'urgensi' => 'sedang',
            'uraian' => 'Tumpukan sampah sudah mulai meluber ke jalan dan menimbulkan aroma tidak sedap sejak kemarin sore.',
        ];

        $response = $this->actingAs($this->wargaUser)->post(route('warga.laporan.store'), $payload);

        $response->assertRedirect(route('warga.dashboard'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('reports', [
            'pelapor_id' => $this->wargaUser->id,
            'kategori_id' => $this->kategoriSampah->id,
            'judul' => 'Tumpukan Sampah Menumpuk di Dekat Pos Ronda',
            'status_penerimaan' => 'baru',
            'blok' => 'Blok A',
        ]);

        // Cek notifikasi terkirim untuk pelapor
        $this->assertDatabaseHas('notifikasis', [
            'penerima_id' => $this->wargaUser->id,
            'judul' => 'Laporan Berhasil Terkirim',
        ]);

        // Cek notifikasi terkirim untuk admin RT
        $this->assertDatabaseHas('notifikasis', [
            'penerima_id' => $this->adminRt->id,
            'judul' => 'Laporan Warga Baru Masuk',
        ]);
    }

    public function test_warga_dapat_mengirim_laporan_beserta_foto_pendukung(): void
    {
        Storage::fake('public');

        $foto = UploadedFile::fake()->image('bukti_kondisi.jpg', 800, 600)->size(1500);

        $payload = [
            'kategori_id' => $this->kategoriSampah->id,
            'judul' => 'Saluran Air Mampet Terkena Sumbatan Sampah',
            'lokasi_detail' => 'Depan rumah nomor 12',
            'blok' => 'Blok B',
            'dampak' => 'tinggi',
            'urgensi' => 'tinggi',
            'uraian' => 'Air tidak mengalir di selokan depan rumah nomor 12 sehingga terjadi genangan saat hujan.',
            'foto' => $foto,
        ];

        $response = $this->actingAs($this->wargaUser)->post(route('warga.laporan.store'), $payload);

        $response->assertRedirect(route('warga.dashboard'));

        $report = Report::where('judul', 'Saluran Air Mampet Terkena Sumbatan Sampah')->first();
        $this->assertNotNull($report);

        $this->assertDatabaseHas('lampirans', [
            'laporan_id' => $report->id,
            'pengunggah_id' => $this->wargaUser->id,
            'nama_file' => 'bukti_kondisi.jpg',
        ]);

        $lampiran = Lampiran::where('laporan_id', $report->id)->first();
        $this->assertNotNull($lampiran);
        Storage::disk('public')->assertExists($lampiran->storage_key);
    }

    public function test_validasi_laporan_memerlukan_field_wajib(): void
    {
        $response = $this->actingAs($this->wargaUser)->post(route('warga.laporan.store'), []);

        $response->assertSessionHasErrors(['kategori_id', 'judul', 'lokasi_detail', 'dampak', 'urgensi', 'uraian']);
    }

    public function test_validasi_uraian_minimal_20_karakter(): void
    {
        $payload = [
            'kategori_id' => $this->kategoriSampah->id,
            'judul' => 'Sampah Bau',
            'lokasi_detail' => 'Jl. Mawar',
            'dampak' => 'sedang',
            'urgensi' => 'sedang',
            'uraian' => 'Terlalu pendek',
        ];

        $response = $this->actingAs($this->wargaUser)->post(route('warga.laporan.store'), $payload);

        $response->assertSessionHasErrors(['uraian']);
    }

    public function test_semua_kategori_terpetakan_unik_termasuk_penerangan_jalan_umum(): void
    {
        Kategori::create([
            'id' => (string) Str::uuid(),
            'nama' => 'Drainase & Saluran Air',
            'warna_peta' => '#3B82F6',
            'aktif' => true,
        ]);
        Kategori::create([
            'id' => (string) Str::uuid(),
            'nama' => 'Jalan Lingkungan & Trotoar',
            'warna_peta' => '#F59E0B',
            'aktif' => true,
        ]);
        Kategori::create([
            'id' => (string) Str::uuid(),
            'nama' => 'Penerangan Jalan Umum',
            'warna_peta' => '#10B981',
            'aktif' => true,
        ]);
        Kategori::create([
            'id' => (string) Str::uuid(),
            'nama' => 'Fasilitas Umum & Ruang Terbuka',
            'warna_peta' => '#8B5CF6',
            'aktif' => true,
        ]);

        $response = $this->actingAs($this->wargaUser)->get(route('warga.laporan.create'));

        $response->assertOk();
        $response->assertSeeInOrder(['Sampah', 'Drainase', 'Jalan', 'Fasilitas Umum', 'Penerangan']);
        $response->assertSee('Panduan Kategori');
        $response->assertSee('Saran judul cepat:');
    }
}
