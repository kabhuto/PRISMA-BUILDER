<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Accounts (superadmin only): superadmins run the app, admins (pegawai) make and test forms. A
 * superadmin adds accounts, changes their name, role, and password, or removes them. One's own
 * account and the last superadmin cannot be removed or made an admin.
 */
class AdminUserController extends Controller
{
    public function index(): View
    {
        return view('admins.index', ['admins' => User::query()->orderBy('name')->orderBy('id')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validateWithBag('create', [
            'name' => ['required', 'string', 'max:100'],
            'role' => ['nullable', Rule::in(array_keys(User::ROLES))],
            'username' => ['nullable', 'required_without:email', 'regex:'.User::USERNAME_PATTERN, Rule::unique('users', 'username')],
            'email' => ['nullable', 'required_without:username', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'max:100', 'confirmed'],
        ], self::messages());

        $username = $data['username'] ?? null;
        $data['role'] ??= 'admin';
        User::query()->create([
            'name' => $data['name'],
            'username' => $username,
            'role' => $data['role'],
            'email' => $data['email'] ?? User::placeholderEmail((string) $username),
            'password' => $data['password'],
        ]);

        return redirect()->route('admins.index')->with('status', User::ROLES[$data['role']].' '.($username ?? $data['email']).' ditambahkan.');
    }

    public function update(Request $request, User $admin): RedirectResponse
    {
        $data = $request->validateWithBag('edit'.$admin->id, [
            'name' => ['required', 'string', 'max:100'],
            'role' => ['nullable', Rule::in(array_keys(User::ROLES))],
            'password' => ['nullable', 'string', 'min:8', 'max:100', 'confirmed'],
        ], self::messages());

        $role = $data['role'] ?? $admin->role;
        if ($role !== 'superadmin' && $admin->isSuperadmin()) {
            if ($request->user()?->is($admin)) {
                throw ValidationException::withMessages(['admin' => 'Peran akun sendiri tidak dapat diturunkan.']);
            }
            if (self::superadminCount() <= 1) {
                throw ValidationException::withMessages(['admin' => 'Harus tetap ada satu superadmin.']);
            }
        }

        $admin->name = $data['name'];
        $admin->role = $role;
        if (filled($data['password'] ?? null)) {
            $admin->password = $data['password'];
        }
        $admin->save();

        return redirect()->route('admins.index')->with('status', 'Akun '.$admin->loginName().' diperbarui.');
    }

    public function destroy(Request $request, User $admin): RedirectResponse
    {
        if ($request->user()?->is($admin)) {
            throw ValidationException::withMessages(['admin' => 'Akun yang sedang dipakai tidak dapat dihapus.']);
        }
        if (User::query()->count() <= 1) {
            throw ValidationException::withMessages(['admin' => 'Admin terakhir tidak dapat dihapus.']);
        }
        if ($admin->isSuperadmin() && self::superadminCount() <= 1) {
            throw ValidationException::withMessages(['admin' => 'Superadmin terakhir tidak dapat dihapus.']);
        }

        $admin->delete();

        return redirect()->route('admins.index')->with('status', 'Admin '.$admin->loginName().' dihapus.');
    }

    private static function superadminCount(): int
    {
        return User::query()->where('role', 'superadmin')->count();
    }

    /**
     * @return array<string, string>
     */
    private static function messages(): array
    {
        return [
            'name.required' => 'Isi nama admin.',
            'role.required' => 'Pilih peran akun.',
            'role.in' => 'Pilih peran akun.',
            'username.required_without' => 'Isi username atau email (salah satu).',
            'email.required_without' => 'Isi username atau email (salah satu).',
            'username.regex' => 'Username 3–50 karakter: huruf, angka, titik, garis bawah, atau tanda hubung.',
            'username.unique' => 'Username ini sudah dipakai.',
            'email.email' => 'Email tidak valid.',
            'email.unique' => 'Email ini sudah dipakai.',
            'password.required' => 'Isi kata sandi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Ulangan kata sandi tidak sama.',
        ];
    }
}
