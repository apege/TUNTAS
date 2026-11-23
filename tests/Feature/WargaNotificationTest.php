<?php

namespace Tests\Feature;

use App\Models\Notifikasi;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class WargaNotificationTest extends TestCase
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

    public function test_warga_dashboard_renders_notifications_and_unread_count(): void
    {
        Notifikasi::create([
            'id' => (string) Str::uuid(),
            'penerima_id' => $this->wargaUser->id,
            'judul' => 'Laporan Diterima RT',
            'pesan' => 'Laporan Anda sudah diterima oleh pengurus RT.',
            'dibaca_at' => null,
            'created_at' => now(),
        ]);

        Notifikasi::create([
            'id' => (string) Str::uuid(),
            'penerima_id' => $this->wargaUser->id,
            'judul' => 'Pengumuman Baru',
            'pesan' => 'Ada kerja bakti hari Minggu.',
            'dibaca_at' => now(),
            'created_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($this->wargaUser)->get(route('warga.dashboard'));

        $response->assertOk();
        $response->assertSee('Laporan Diterima RT');
        $response->assertSee('Pengumuman Baru');
        $response->assertSee('1 baru');
        $response->assertSee('notification-dropdown-menu');
    }

    public function test_warga_can_mark_single_notification_as_read(): void
    {
        $notif = Notifikasi::create([
            'id' => (string) Str::uuid(),
            'penerima_id' => $this->wargaUser->id,
            'judul' => 'Laporan Ditindaklanjuti',
            'pesan' => 'Petugas mulai bekerja.',
            'dibaca_at' => null,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->wargaUser)
            ->postJson(route('notifikasi.baca', $notif));

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertNotNull($notif->fresh()->dibaca_at);
    }

    public function test_warga_cannot_mark_other_users_notification_as_read(): void
    {
        $otherUser = User::create([
            'id' => (string) Str::uuid(),
            'wilayah_id' => $this->rt->id,
            'nama' => 'Budi Santoso',
            'email' => 'budi@tuntas.id',
            'password' => Hash::make('password'),
            'peran' => 'warga',
            'telepon' => '081234567894',
            'alamat' => 'Jl. Mawar No. 10',
            'notifikasi_aktif' => true,
            'aktif' => true,
        ]);

        $notif = Notifikasi::create([
            'id' => (string) Str::uuid(),
            'penerima_id' => $otherUser->id,
            'judul' => 'Privat User Budi',
            'pesan' => 'Rahasia.',
            'dibaca_at' => null,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->wargaUser)
            ->postJson(route('notifikasi.baca', $notif));

        $response->assertForbidden();
    }

    public function test_warga_can_mark_all_notifications_as_read(): void
    {
        Notifikasi::create([
            'id' => (string) Str::uuid(),
            'penerima_id' => $this->wargaUser->id,
            'judul' => 'Notif 1',
            'pesan' => 'Pesan 1',
            'dibaca_at' => null,
            'created_at' => now(),
        ]);

        Notifikasi::create([
            'id' => (string) Str::uuid(),
            'penerima_id' => $this->wargaUser->id,
            'judul' => 'Notif 2',
            'pesan' => 'Pesan 2',
            'dibaca_at' => null,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->wargaUser)
            ->postJson(route('notifikasi.baca-semua'));

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertEquals(
            0,
            Notifikasi::where('penerima_id', $this->wargaUser->id)->whereNull('dibaca_at')->count()
        );
    }
}
