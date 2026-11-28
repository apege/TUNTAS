<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class WargaProfilTest extends TestCase
{
    use RefreshDatabase;

    protected Wilayah $rw;

    protected Wilayah $rt02;

    protected User $warga;

    protected User $wargaLain;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rw = Wilayah::create([
            'id' => (string) Str::uuid(),
            'tingkat' => 'rw',
            'nomor' => '05',
            'kelurahan' => 'Sukamaju',
        ]);

        $this->rt02 = Wilayah::create([
            'id' => (string) Str::uuid(),
            'parent_id' => $this->rw->id,
            'tingkat' => 'rt',
            'nomor' => '02',
        ]);

        $this->warga = User::create([
            'id' => (string) Str::uuid(),
            'wilayah_id' => $this->rt02->id,
            'nama' => 'Siti Aminah',
            'email' => 'warga@tuntas.id',
            'password' => Hash::make('password123'),
            'peran' => 'warga',
            'telepon' => '081234567890',
            'alamat' => 'Jl. Kenanga No. 4',
            'notifikasi_aktif' => true,
            'aktif' => true,
        ]);

        $this->wargaLain = User::create([
            'id' => (string) Str::uuid(),
            'wilayah_id' => $this->rt02->id,
            'nama' => 'Budi Santoso',
            'email' => 'budi@tuntas.id',
            'password' => Hash::make('password123'),
            'peran' => 'warga',
            'telepon' => '081299998888',
            'alamat' => 'Jl. Melati No. 10',
            'notifikasi_aktif' => true,
            'aktif' => true,
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('warga.profil.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_warga_can_view_profil_page_with_wilayah_details(): void
    {
        $response = $this->actingAs($this->warga)->get(route('warga.profil.index'));

        $response->assertOk();
        $response->assertSee('Siti Aminah');
        $response->assertSee('warga@tuntas.id');
        $response->assertSee('RT 02 / RW 05');
        $response->assertSee('Sukamaju');
    }

    public function test_warga_can_update_profile_contact_and_notification_preference(): void
    {
        $response = $this->actingAs($this->warga)->put(route('warga.profil.update'), [
            'nama' => 'Siti Aminah Kusuma',
            'email' => 'siti.kusuma@tuntas.id',
            'telepon' => '081355556666',
            'alamat' => 'Jl. Mawar Blok B-3 No. 12',
            'notifikasi_aktif' => '0',
        ]);

        $response->assertRedirect(route('warga.profil.index'));
        $response->assertSessionHas('success');

        $this->warga->refresh();
        $this->assertSame('Siti Aminah Kusuma', $this->warga->nama);
        $this->assertSame('siti.kusuma@tuntas.id', $this->warga->email);
        $this->assertSame('081355556666', $this->warga->telepon);
        $this->assertSame('Jl. Mawar Blok B-3 No. 12', $this->warga->alamat);
        $this->assertFalse($this->warga->notifikasi_aktif);
    }

    public function test_profile_update_validates_duplicate_email_and_required_name(): void
    {
        $response = $this->actingAs($this->warga)
            ->from(route('warga.profil.index'))
            ->put(route('warga.profil.update'), [
                'nama' => '',
                'email' => 'budi@tuntas.id',
            ]);

        $response->assertRedirect(route('warga.profil.index'));
        $response->assertSessionHasErrors(['nama', 'email']);

        $this->warga->refresh();
        $this->assertSame('Siti Aminah', $this->warga->nama);
        $this->assertSame('warga@tuntas.id', $this->warga->email);
    }

    public function test_warga_can_update_password_with_valid_current_password(): void
    {
        $response = $this->actingAs($this->warga)->put(route('warga.profil.password'), [
            'current_password' => 'password123',
            'password' => 'rahasiaBaru99',
            'password_confirmation' => 'rahasiaBaru99',
        ]);

        $response->assertRedirect(route('warga.profil.index'));
        $response->assertSessionHas('success');

        $this->warga->refresh();
        $this->assertTrue(Hash::check('rahasiaBaru99', $this->warga->password));
    }

    public function test_password_update_rejects_wrong_current_password(): void
    {
        $response = $this->actingAs($this->warga)
            ->from(route('warga.profil.index'))
            ->put(route('warga.profil.password'), [
                'current_password' => 'salahBanget',
                'password' => 'rahasiaBaru99',
                'password_confirmation' => 'rahasiaBaru99',
            ]);

        $response->assertRedirect(route('warga.profil.index'));
        $response->assertSessionHasErrors('current_password', null, 'passwordUpdate');

        $this->warga->refresh();
        $this->assertTrue(Hash::check('password123', $this->warga->password));
    }
}
