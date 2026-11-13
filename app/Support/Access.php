<?php

namespace App\Support;

/**
 * Who may do what. A superadmin runs the app; an admin (pegawai) makes and tests forms and follows
 * the field work, without the menus, tools, and forms a superadmin has switched off.
 */
class Access
{
    public static function isSuperadmin(): bool
    {
        $user = auth()->user();

        // Without an account the admin pages are only open on the office network of a development PC.
        return $user ? $user->isSuperadmin() : (bool) config('app.admin_open_on_lan');
    }
}
