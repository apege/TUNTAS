<?php

namespace Tests\Feature;

use App\Models\AreaLingkungan;
use App\Models\Kategori;
use App\Models\Report;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class WargaPetaLingkunganTest extends TestCase
{
    use RefreshDatabase;

    protected Wilayah $rw;

    protected Wilayah $rt01;

    protected Wilayah $rt02;

    protected User $wargaRt01;

    protected User $wargaRt02;

    protected Kategori $katSampah;

    protected Kategori $katDrainase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rw = Wilayah::create([
            'id' => (string) Str::uuid(),
            'tingkat' => 'rw',
            'nomor' => '05',
            'kelurahan' => 'Sukamaju',
        ]);

        $this->rt01 = Wilayah::create([
            'id' => (string) Str::uuid(),
            'parent_id' => $this->rw->id,
            'tingkat' => 'rt',
            'nomor' => '01',
        ]);

        $this->rt02 = Wilayah::create([
            'id' => (string) Str::uuid(),
            'parent_id' => $this->rw->id,
            'tingkat' => 'rt',
            'nomor' => '02',
        ]);

        $this->wargaRt01 = User::create([
            'id' => (string) Str::uuid(),
            'wilayah_id' => $this->rt01->id,
            'nama' => 'Siti Aminah',
            'email' => 'warga@tuntas.id',
            'password' => Hash::make('password'),
            'peran' => 'warga',
            'telepon' => '081234567893',
            'alamat' => 'Jl. Mawar No. 8',
            'notifikasi_aktif' => true,
            'aktif' => true,
        ]);

        $this->wargaRt02 = User::create([
            'id' => (string) Str::uuid(),
            'wilayah_id' => $this->rt02->id,
            'nama' => 'Budi Santoso',
            'email' => 'budi@tuntas.id',
            'password' => Hash::make('password'),
            'peran' => 'warga',
            'telepon' => '081234567899',
            'alamat' => 'Jl. Melati No. 5',
            'notifikasi_aktif' => true,
            'aktif' => true,
        ]);

        $this->katSampah = Kategori::create([
            'id' => (string) Str::uuid(),
            'nama' => 'Sampah & Kebersihan Lingkungan',
            'warna_peta' => '#EF4444',
            'aktif' => true,
        ]);

        $this->katDrainase = Kategori::create([
            'id' => (string) Str::uuid(),
            'nama' => 'Drainase & Saluran Air',
            'warna_peta' => '#3B82F6',
            'aktif' => true,
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('warga.peta.index'))
            ->assertRedirect(route('login'));
    }

    public function test_peta_lingkungan_menampilkan_laporan_aktif_rt_dan_mendukung_scope_rw_serta_filter(): void
    {
        AreaLingkungan::create([
            'id' => (string) Str::uuid(),
            'wilayah_id' => $this->rt01->id,
            'nama' => 'Jl. Curug Candung',
            'jenis' => 'jalan',
            'aktif' => true,
        ]);

        Report::create([
            'id' => (string) Str::uuid(),
            'kode' => 'LAP-202611-0301',
            'pelapor_id' => $this->wargaRt01->id,
            'wilayah_id' => $this->rt01->id,
            'kategori_id' => $this->katSampah->id,
            'judul' => 'Sampah Liar di Tikungan',
            'uraian' => 'Tumpukan sampah plastik di sudut jalan.',
            'lokasi_detail' => 'Jl. Curug Candung No. 4',
            'dampak' => 'sedang',
            'urgensi' => 'tinggi',
            'status_penerimaan' => 'baru',
        ]);

        Report::create([
            'id' => (string) Str::uuid(),
            'kode' => 'LAP-202611-0302',
            'pelapor_id' => $this->wargaRt01->id,
            'wilayah_id' => $this->rt01->id,
            'kategori_id' => $this->katDrainase->id,
            'judul' => 'Laporan Ditolak Tidak Tampil',
            'uraian' => 'Laporan ini ditolak oleh admin RT.',
            'lokasi_detail' => 'Gang 2',
            'dampak' => 'rendah',
            'urgensi' => 'rendah',
            'status_penerimaan' => 'ditolak',
        ]);

        Report::create([
            'id' => (string) Str::uuid(),
            'kode' => 'LAP-202611-0303',
            'pelapor_id' => $this->wargaRt02->id,
            'wilayah_id' => $this->rt02->id,
            'kategori_id' => $this->katDrainase->id,
            'judul' => 'Drainase Mampet RT 02',
            'uraian' => 'Selokan mampet di wilayah RT 02.',
            'lokasi_detail' => 'Jl. Melati No. 12',
            'dampak' => 'sedang',
            'urgensi' => 'sedang',
            'status_penerimaan' => 'diverifikasi',
        ]);

        // 1. Default scope RT 01: hanya tampil laporan aktif RT 01 & nama area dinamis
        $rtResponse = $this->actingAs($this->wargaRt01)->get(route('warga.peta.index'));
        $rtResponse->assertOk();
        $rtResponse->assertViewIs('dashboard.peta_lingkungan');
        $rtResponse->assertSee('Jl. Curug Candung');
        $rtResponse->assertSee('Sampah Liar di Tikungan');
        $rtResponse->assertSee('1 titik');
        $rtResponse->assertDontSee('Laporan Ditolak Tidak Tampil');
        $rtResponse->assertDontSee('Drainase Mampet RT 02');

        // 2. Scope RW 05: tampil laporan RT 01 dan RT 02
        $rwResponse = $this->actingAs($this->wargaRt01)->get(route('warga.peta.index', ['scope' => 'rw']));
        $rwResponse->assertOk();
        $rwResponse->assertSee('Sampah Liar di Tikungan');
        $rwResponse->assertSee('Drainase Mampet RT 02');
        $rwResponse->assertSee('2 titik');

        // 3. Filter kategori Drainase pada scope RW
        $filterRes = $this->actingAs($this->wargaRt01)->get(route('warga.peta.index', [
            'scope' => 'rw',
            'kategori' => $this->katDrainase->id,
        ]));
        $filterRes->assertOk();
        $filterRes->assertSee('Drainase Mampet RT 02');
        $filterRes->assertDontSee('Sampah Liar di Tikungan');
    }
}
