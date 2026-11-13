{{-- The top bar of every admin page: menu, guide, colour, theme, account. $newTabHelp opens the guide in a new tab, for pages with unsaved work. --}}
<header class="topbar">
    <a class="brand" href="{{ route('surveys.index') }}" aria-label="Dashboard PRISMA">
        @include('partials.brand')
    </a>
    <nav class="main-nav" aria-label="Navigasi utama">
        @include('partials.admin-nav')
    </nav>
    <div class="topbar-tools">
        @if (! request()->routeIs('login') && \App\Support\AppSettings::menuOn('panduan'))
            <a class="button button-ghost button-small help-link" href="{{ \App\Support\Guide::linkFor(request()->route()?->getName()) }}" @if ($newTabHelp ?? false) target="_blank" rel="noopener" title="Panduan untuk halaman ini (tab baru)" @else title="Panduan untuk halaman ini" @endif>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20ZM9.1 9a3 3 0 0 1 5.8 1c0 2-3 3-3 3M12 17h.01"/></svg>
                Panduan
            </a>
        @endif
        @include('partials.accent-picker', ['showLabel' => true])
        @include('partials.theme-toggle', ['showLabel' => true])
        @auth
            <details class="user-menu" data-user-menu>
                <summary class="user-chip" title="{{ auth()->user()->loginName() }}">
                    <span class="user-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                    <span class="user-name">{{ auth()->user()->name }}</span>
                    <svg class="user-caret" viewBox="0 0 24 24" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                </summary>
                <div class="user-menu-pop">
                    <strong>{{ auth()->user()->name }}</strong>
                    <small>{{ auth()->user()->loginName() }} · {{ auth()->user()->roleLabel() }}</small>
                    @if (\App\Support\AppSettings::menuOn('dashboard'))
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    @endif
                    @if (\App\Support\AppSettings::menuOn('riwayat'))
                        <a href="{{ route('history.index') }}">Riwayat saya</a>
                    @endif
                    @if (auth()->user()->isSuperadmin())
                        <a href="{{ route('settings.index') }}">Kelola aplikasi</a>
                        <a href="{{ route('admins.index') }}">Kelola admin</a>
                        <a href="{{ route('api-access.index') }}">Akses API</a>
                    @endif
                    @if (\App\Support\AppSettings::menuOn('panduan'))
                        <a href="{{ route('panduan') }}">Panduan</a>
                    @endif
                    {{-- On a phone the logout icon leaves the bar, so it lives here. --}}
                    <form class="user-menu-logout" method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit">Keluar</button>
                    </form>
                </div>
            </details>
            <form class="nav-logout" method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="icon-logout" type="submit" aria-label="Keluar" title="Keluar">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5m5 5H3"/></svg>
                </button>
            </form>
        @endauth
    </div>
</header>
@once
    <script>
        // The account menu closes on a click anywhere else.
        document.addEventListener('click', event => {
            const menu = document.querySelector('[data-user-menu][open]');
            if (menu && !menu.contains(event.target)) menu.open = false;
        });
    </script>
@endonce
