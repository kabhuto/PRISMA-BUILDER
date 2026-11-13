<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function signedIn(): User
    {
        config(['app.admin_open_on_lan' => false]);
        $admin = User::query()->create(['name' => 'Admin Utama', 'username' => 'admin3526', 'role' => 'superadmin', 'email' => User::placeholderEmail('admin3526'), 'password' => 'sandi-uji-123']);
        $this->actingAs($admin);

        return $admin;
    }

    public function test_the_page_needs_a_signed_in_admin(): void
    {
        config(['app.admin_open_on_lan' => false]);

        $this->get(route('admins.index'))->assertRedirect(route('login'));
        $this->post(route('admins.store'), ['name' => 'Penyusup', 'username' => 'penyusup', 'password' => 'rahasia-123', 'password_confirmation' => 'rahasia-123'])->assertRedirect(route('login'));
        $this->assertSame(0, User::query()->count());
    }

    public function test_an_admin_adds_another_admin_who_can_then_sign_in(): void
    {
        $this->signedIn();

        $this->get(route('admins.index'))->assertOk()->assertSee('Kelola admin')->assertSee('admin3526')->assertSee('Anda');

        $this->post(route('admins.store'), ['name' => 'Humas', 'username' => 'humas3526', 'email' => '', 'password' => 'humas-12345', 'password_confirmation' => 'humas-12345'])
            ->assertRedirect(route('admins.index'))
            ->assertSessionHas('status', 'Admin (pegawai) humas3526 ditambahkan.');
        $this->post(route('admins.store'), ['name' => 'IPDS', 'role' => 'superadmin', 'email' => 'ipds@bps.go.id', 'password' => 'ipds-12345', 'password_confirmation' => 'ipds-12345'])->assertRedirect();

        $humas = User::query()->where('username', 'humas3526')->sole();
        $this->assertFalse($humas->hasRealEmail());
        $this->assertNull(User::query()->where('email', 'ipds@bps.go.id')->sole()->username);

        $this->assertSame('admin', $humas->role);
        $this->assertTrue(User::query()->where('email', 'ipds@bps.go.id')->sole()->isSuperadmin());

        // An admin (pegawai) works on forms but does not run the app.
        $this->post(route('logout'));
        $this->post(route('login.store'), ['login' => 'humas3526', 'password' => 'humas-12345'])->assertRedirect(route('surveys.index'));
        $this->get(route('surveys.index'))->assertOk();
        $this->get(route('admins.index'))->assertForbidden();

        // A superadmin does.
        $this->post(route('logout'));
        $this->post(route('login.store'), ['login' => 'ipds@bps.go.id', 'password' => 'ipds-12345']);
        $this->get(route('admins.index'))->assertOk()->assertSee('humas3526');
    }

    public function test_new_admins_are_validated(): void
    {
        $this->signedIn();

        $this->post(route('admins.store'), ['name' => 'Tanpa akun', 'password' => 'rahasia-123', 'password_confirmation' => 'rahasia-123'])
            ->assertSessionHasErrorsIn('create', ['username' => 'Isi username atau email (salah satu).']);
        $this->post(route('admins.store'), ['name' => 'Kembar', 'username' => 'admin3526', 'password' => 'rahasia-123', 'password_confirmation' => 'rahasia-123'])
            ->assertSessionHasErrorsIn('create', ['username' => 'Username ini sudah dipakai.']);
        $this->post(route('admins.store'), ['name' => 'Spasi', 'username' => 'admin 2', 'password' => 'rahasia-123', 'password_confirmation' => 'rahasia-123'])
            ->assertSessionHasErrorsIn('create', 'username');
        $this->post(route('admins.store'), ['name' => 'Beda', 'username' => 'beda', 'password' => 'rahasia-123', 'password_confirmation' => 'rahasia-456'])
            ->assertSessionHasErrorsIn('create', ['password' => 'Ulangan kata sandi tidak sama.']);
        $this->post(route('admins.store'), ['name' => 'Pendek', 'username' => 'pendek', 'password' => 'pendek', 'password_confirmation' => 'pendek'])
            ->assertSessionHasErrorsIn('create', ['password' => 'Kata sandi minimal 8 karakter.']);

        $this->assertSame(1, User::query()->count());
    }

    public function test_names_and_passwords_are_changed_and_admins_removed(): void
    {
        $me = $this->signedIn();
        $other = User::query()->create(['name' => 'Lama', 'username' => 'lama', 'email' => User::placeholderEmail('lama'), 'password' => 'sandi-lama-1']);

        $this->put(route('admins.update', $other), ['name' => 'Baru', 'password' => '', 'password_confirmation' => ''])->assertRedirect(route('admins.index'));
        $this->assertSame('Baru', $other->fresh()->name);
        $this->assertTrue(Hash::check('sandi-lama-1', $other->fresh()->password));

        $this->put(route('admins.update', $other), ['name' => 'Baru', 'password' => 'sandi-baru-2', 'password_confirmation' => 'sandi-baru-2'])->assertRedirect();
        $this->assertTrue(Hash::check('sandi-baru-2', $other->fresh()->password));

        // Roles change; one's own role and the last superadmin stay.
        $this->put(route('admins.update', $other), ['name' => 'Baru', 'role' => 'superadmin'])->assertRedirect();
        $this->assertTrue($other->fresh()->isSuperadmin());
        $this->put(route('admins.update', $me), ['name' => 'Admin Utama', 'role' => 'admin'])->assertSessionHasErrors(['admin' => 'Peran akun sendiri tidak dapat diturunkan.']);
        $this->put(route('admins.update', $other), ['name' => 'Baru', 'role' => 'admin'])->assertRedirect();
        $this->assertFalse($other->fresh()->isSuperadmin());

        // One's own account and the last admin stay.
        $this->delete(route('admins.destroy', $me))->assertSessionHasErrors(['admin' => 'Akun yang sedang dipakai tidak dapat dihapus.']);
        $this->delete(route('admins.destroy', $other))->assertRedirect(route('admins.index'));
        $this->assertModelMissing($other);
        $this->assertModelExists($me);
    }
}
