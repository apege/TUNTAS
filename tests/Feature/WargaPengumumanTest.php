<?php

namespace Tests\Feature;

use App\Models\Pengumuman;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class WargaPengumumanTest extends TestCase
{
    use RefreshDatabase;

    protected Wilayah $rw;

    protected Wilayah $rt01;

    protected Wilayah $rt02;

    protected User $wargaRt01;

    protected User $adminRt01;

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

        $this->adminRt01 = User::create([
            'id' => (string) Str::uuid(),
            'wilayah_id' => $this->rt01->id,
            'nama' => 'Pak Hendra',
            'email' => 'ketuart@tuntas.id',
            'password' => Hash::make('password'),
            'peran' => 'admin_rt',
            'telepon' => '081234567890',
            'alamat' => 'Jl. Mawar No. 1',
            'notifikasi_aktif' => true,
            'aktif' => true,
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('warga.pengumuman.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_warga_sees_published_rt_and_parent_rw_announcements_only(): void
    {
        $this->createPengumuman($this->rw, [
            'judul' => 'Darurat Fogging DBD RW 05',
            'isi' => 'Pelaksanaan fogging serentak di seluruh RT RW 05.',
            'jenis' => 'darurat',
            'status' => 'diterbitkan',
        ]);

        $this->createPengumuman($this->rt01, [
            'judul' => 'Jadwal Pengangkutan Sampah RT 01',
            'isi' => 'Mohon keluarkan kantong sampah sebelum pukul 07.00 WIB.',
            'jenis' => 'informasi',
            'status' => 'diterbitkan',
        ]);

        $this->createPengumuman($this->rt01, [
            'judul' => 'Draf Internal Pengurus RT 01',
            'isi' => 'Catatan ini masih berstatus draf.',
            'jenis' => 'imbauan',
            'status' => 'draft',
        ]);

        $this->createPengumuman($this->rt01, [
            'judul' => 'Arsip Pengumuman Lama RT 01',
            'isi' => 'Pengumuman ini sudah diarsipkan.',
            'jenis' => 'informasi',
            'status' => 'diarsipkan',
        ]);

        $this->createPengumuman($this->rt02, [
            'judul' => 'Pengumuman Khusus Warga RT 02',
            'isi' => 'Rapat khusus warga RT 02.',
            'jenis' => 'kegiatan',
            'status' => 'diterbitkan',
        ]);

        $response = $this->actingAs($this->wargaRt01)->get(route('warga.pengumuman.index'));

        $response->assertOk();
        $response->assertSee('Darurat Fogging DBD RW 05');
        $response->assertSee('Jadwal Pengangkutan Sampah RT 01');
        $response->assertDontSee('Draf Internal Pengurus RT 01');
        $response->assertDontSee('Arsip Pengumuman Lama RT 01');
        $response->assertDontSee('Pengumuman Khusus Warga RT 02');
    }

    public function test_warga_can_filter_announcements_by_scope_rt_and_rw(): void
    {
        $this->createPengumuman($this->rw, [
            'judul' => 'Kerja Bakti Serentak RW 05',
            'jenis' => 'kegiatan',
            'status' => 'diterbitkan',
        ]);

        $this->createPengumuman($this->rt01, [
            'judul' => 'Perbaikan Lampu Gang Mawar RT 01',
            'jenis' => 'informasi',
            'status' => 'diterbitkan',
        ]);

        $rtResponse = $this->actingAs($this->wargaRt01)->get(route('warga.pengumuman.index', [
            'scope' => 'rt',
        ]));

        $rtResponse->assertOk();
        $rtResponse->assertSee('Perbaikan Lampu Gang Mawar RT 01');
        $rtResponse->assertDontSee('Kerja Bakti Serentak RW 05');

        $rwResponse = $this->actingAs($this->wargaRt01)->get(route('warga.pengumuman.index', [
            'scope' => 'rw',
        ]));

        $rwResponse->assertOk();
        $rwResponse->assertSee('Kerja Bakti Serentak RW 05');
        $rwResponse->assertDontSee('Perbaikan Lampu Gang Mawar RT 01');
    }

    public function test_warga_can_filter_announcements_by_jenis(): void
    {
        $this->createPengumuman($this->rw, [
            'judul' => 'Waspada Luapan Saluran Air',
            'jenis' => 'darurat',
            'status' => 'diterbitkan',
        ]);

        $this->createPengumuman($this->rw, [
            'judul' => 'Kerja Bakti Mingguan Lingkungan',
            'jenis' => 'kegiatan',
            'status' => 'diterbitkan',
        ]);

        $this->createPengumuman($this->rt01, [
            'judul' => 'Pembaruan Jadwal Pos Ronda',
            'jenis' => 'informasi',
            'status' => 'diterbitkan',
        ]);

        $daruratResponse = $this->actingAs($this->wargaRt01)->get(route('warga.pengumuman.index', [
            'jenis' => 'darurat',
        ]));

        $daruratResponse->assertOk();
        $daruratResponse->assertSee('Waspada Luapan Saluran Air');
        $daruratResponse->assertDontSee('Kerja Bakti Mingguan Lingkungan');
        $daruratResponse->assertDontSee('Pembaruan Jadwal Pos Ronda');

        $kegiatanResponse = $this->actingAs($this->wargaRt01)->get(route('warga.pengumuman.index', [
            'jenis' => 'kegiatan',
        ]));

        $kegiatanResponse->assertOk();
        $kegiatanResponse->assertSee('Kerja Bakti Mingguan Lingkungan');
        $kegiatanResponse->assertDontSee('Waspada Luapan Saluran Air');
        $kegiatanResponse->assertDontSee('Pembaruan Jadwal Pos Ronda');
    }

    public function test_warga_can_search_announcements_by_keyword(): void
    {
        $this->createPengumuman($this->rw, [
            'judul' => 'Peringatan Fogging Nyamuk DBD',
            'isi' => 'Warga dimohon menutup makanan selama proses pengasapan.',
            'jenis' => 'darurat',
            'status' => 'diterbitkan',
        ]);

        $this->createPengumuman($this->rt01, [
            'judul' => 'Sosialisasi Bank Sampah Mandiri',
            'isi' => 'Penimbangan botol plastik dilakukan setiap akhir pekan.',
            'jenis' => 'informasi',
            'status' => 'diterbitkan',
        ]);

        $response = $this->actingAs($this->wargaRt01)->get(route('warga.pengumuman.index', [
            'q' => 'Fogging',
        ]));

        $response->assertOk();
        $response->assertSee('Peringatan Fogging Nyamuk DBD');
        $response->assertDontSee('Sosialisasi Bank Sampah Mandiri');
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createPengumuman(Wilayah $wilayah, array $attributes = []): Pengumuman
    {
        return Pengumuman::create(array_merge([
            'id' => (string) Str::uuid(),
            'pembuat_id' => $this->adminRt01->id,
            'wilayah_id' => $wilayah->id,
            'judul' => 'Pengumuman Lingkungan',
            'isi' => 'Isi pengumuman resmi pengurus lingkungan.',
            'jenis' => 'informasi',
            'status' => 'diterbitkan',
            'terbit_at' => now(),
        ], $attributes));
    }
}
