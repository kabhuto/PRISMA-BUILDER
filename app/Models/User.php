<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Support\Palette;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'username', 'role', 'email', 'password', 'accent_color'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const USERNAME_PATTERN = '/^[A-Za-z0-9._-]{3,50}$/';

    /**
     * A superadmin runs the app (API access, admins, templates, menu and tools, which forms can be
     * used); an admin is an employee who makes and tests forms and follows the field work.
     */
    public const ROLES = ['superadmin' => 'Superadmin', 'admin' => 'Admin (pegawai)'];

    public function isSuperadmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function roleLabel(): string
    {
        return self::ROLES[$this->role] ?? self::ROLES['admin'];
    }

    /**
     * @return HasMany<Survey, $this>
     */
    public function surveys(): HasMany
    {
        return $this->hasMany(Survey::class);
    }

    /**
     * Users need an email; a username-only admin gets one on this reserved domain that is never mailed.
     */
    public const PLACEHOLDER_EMAIL_DOMAIN = 'admin.ruang-kuesioner.invalid';

    public static function placeholderEmail(string $username): string
    {
        return $username.'@'.self::PLACEHOLDER_EMAIL_DOMAIN;
    }

    public function hasRealEmail(): bool
    {
        return ! str_ends_with((string) $this->email, '@'.self::PLACEHOLDER_EMAIL_DOMAIN);
    }

    /**
     * What the admin types to sign in.
     */
    public function loginName(): string
    {
        return $this->username ?? $this->email;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * The admin's chosen colour, or null for the default look.
     */
    public function accentColor(): ?string
    {
        $color = $this->accent_color;

        return Palette::isHex($color) && Palette::normalize($color) !== Palette::DEFAULT_ACCENT ? Palette::normalize($color) : null;
    }
}
