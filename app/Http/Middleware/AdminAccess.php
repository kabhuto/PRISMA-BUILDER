<?php

namespace App\Http\Middleware;

use App\Models\Survey;
use App\Support\Access;
use App\Support\AppSettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin pages need a signed-in admin. On a development PC (ADMIN_OPEN_ON_LAN=true) requests from
 * this computer or the local network are let in without signing in; visitors coming through a
 * public https tunnel (whose address the trusted local proxy forwards) still have to sign in.
 */
class AdminAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() !== null || (config('app.admin_open_on_lan') && self::isLocal($request))) {
            self::checkSwitchedOff($request);

            return $next($request);
        }

        if ($request->expectsJson()) {
            abort(401, 'Masuk sebagai admin terlebih dahulu.');
        }

        return redirect()->guest(route('login'));
    }

    /**
     * An admin (pegawai) cannot open a menu or tool a superadmin switched off, nor a form a
     * superadmin hid; a superadmin can open everything.
     */
    private static function checkSwitchedOff(Request $request): void
    {
        if (Access::isSuperadmin()) {
            return;
        }
        $survey = $request->route('survey');
        abort_if($survey instanceof Survey && $survey->isHidden(), 404);

        $off = AppSettings::switchedOffFor((string) $request->route()?->getName());
        abort_if($off !== null, 403, '"'.$off.'" sedang dinonaktifkan oleh superadmin.');
    }

    public static function isLocal(Request $request): bool
    {
        return filter_var((string) $request->ip(), FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
    }
}
