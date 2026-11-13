{{-- The PRISMA bar above a form: logo and theme for respondents, and the admin menu too for a signed-in admin. --}}
<header class="fill-topbar">
    @auth
        <a class="fill-brand" href="{{ route('surveys.index') }}" aria-label="Dashboard PRISMA">
    @else
        <span class="fill-brand">
    @endauth
        @if ($navbarLogo = \App\Support\AppSettings::logo('navbar'))<img class="brand-logo" src="{{ $navbarLogo }}" alt="Logo">@else<span class="brand-mark">b<span>p</span>s</span>@endif
        <span class="brand-name">PRISMA<span>RISET · SURVEI · DATA</span></span>
    @auth
        </a>
        <nav class="fill-nav" aria-label="Navigasi utama">@include('partials.admin-nav')</nav>
    @else
        </span>
    @endauth
    <div class="fill-tools">
        @auth
            <a class="fill-pill" href="{{ \App\Support\Guide::linkFor(request()->route()?->getName()) }}" title="Panduan untuk halaman ini">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20ZM9.1 9a3 3 0 0 1 5.8 1c0 2-3 3-3 3M12 17h.01"/></svg>
                <span>Panduan</span>
            </a>
        @endauth
        @include('partials.theme-toggle', ['showLabel' => true])
        @auth
            <span class="fill-user" title="{{ auth()->user()->loginName() }}">
                <span class="fill-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                <span class="fill-user-name">{{ auth()->user()->name }}</span>
            </span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="fill-logout" type="submit" aria-label="Keluar" title="Keluar">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5m5 5H3"/></svg>
                </button>
            </form>
        @endauth
    </div>
</header>
