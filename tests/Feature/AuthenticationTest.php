<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use DatabaseTransactions;

    protected Wilayah $rw;

    protected Wilayah $rt;

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
            'kelurahan' => null,
        ]);
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Masuk ke TUNTAS');
        $response->assertSee('Lingkungan lebih baik,');
    }

    public function test_admin_rt_can_authenticate_and_redirect_to_admin_rt_dashboard(): void
    {
        $user = User::create([
            'id' => (string) Str::uuid(),
            'wilayah_id' => $this->rt->id,
            'nama' => 'Pak RT',
            'email' => 'admin.rt@tuntas.id',
            'password' => Hash::make('password'),
            'peran' => 'admin_rt',
            'aktif' => true,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('admin-rt.dashboard'));
    }

    public function test_admin_rw_can_authenticate_and_redirect_to_admin_rw_dashboard(): void
    {
        $user = User::create([
            'id' => (string) Str::uuid(),
            'wilayah_id' => $this->rw->id,
            'nama' => 'Pak RW',
            'email' => 'admin.rw@tuntas.id',
            'password' => Hash::make('password'),
            'peran' => 'admin_rw',
            'aktif' => true,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('admin-rw.dashboard'));
    }

    public function test_warga_can_authenticate_and_redirect_to_warga_dashboard(): void
    {
        $user = User::create([
            'id' => (string) Str::uuid(),
            'wilayah_id' => $this->rt->id,
            'nama' => 'Ibu Warga',
            'email' => 'warga@tuntas.id',
            'password' => Hash::make('password'),
            'peran' => 'warga',
            'aktif' => true,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('warga.dashboard'));
    }

    public function test_petugas_can_authenticate_and_redirect_to_petugas_dashboard(): void
    {
        $user = User::create([
            'id' => (string) Str::uuid(),
            'wilayah_id' => $this->rw->id,
            'nama' => 'Mas Petugas',
            'email' => 'petugas@tuntas.id',
            'password' => Hash::make('password'),
            'peran' => 'petugas',
            'aktif' => true,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('petugas.dashboard'));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::create([
            'id' => (string) Str::uuid(),
            'wilayah_id' => $this->rt->id,
            'nama' => 'Pak RT',
            'email' => 'admin.rt@tuntas.id',
            'password' => Hash::make('password'),
            'peran' => 'admin_rt',
            'aktif' => true,
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('login_failed');
    }

    public function test_inactive_users_can_not_authenticate(): void
    {
        $user = User::create([
            'id' => (string) Str::uuid(),
            'wilayah_id' => $this->rt->id,
            'nama' => 'User Nonaktif',
            'email' => 'nonaktif@tuntas.id',
            'password' => Hash::make('password'),
            'peran' => 'warga',
            'aktif' => false,
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('login_failed');
    }

    public function test_authenticated_users_can_logout(): void
    {
        $user = User::create([
            'id' => (string) Str::uuid(),
            'wilayah_id' => $this->rt->id,
            'nama' => 'Pak RT',
            'email' => 'admin.rt@tuntas.id',
            'password' => Hash::make('password'),
            'peran' => 'admin_rt',
            'aktif' => true,
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }
}
