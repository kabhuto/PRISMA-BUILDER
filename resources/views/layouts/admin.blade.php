<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | PRISMA</title>
    @include('partials.theme', ['accent' => true])
    <link rel="stylesheet" href="{{ asset('css/builder.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ui.css') }}">
    @stack('head')
</head>
<body class="admin-page">
    <header class="topbar">
        <a class="brand" href="{{ route('surveys.index') }}" aria-label="Dashboard PRISMA">
            @include('partials.brand')
        </a>
        <nav class="main-nav" aria-label="Navigasi utama">
            @foreach ([
                ['surveys.index', 'surveys.*', 'Dashboard', 'M3 13h8V3H3v10Zm0 8h8v-6H3v6Zm10 0h8V11h-8v10Zm0-18v6h8V3h-8Z'],
                ['references.index', 'references.*', 'Data referensi', 'M4 6c0-1.7 3.6-3 8-3s8 1.3 8 3-3.6 3-8 3-8-1.3-8-3Zm0 0v12c0 1.7 3.6 3 8 3s8-1.3 8-3V6M4 12c0 1.7 3.6 3 8 3s8-1.3 8-3'],
                ['api-access.index', 'api-access.*', 'Akses API', 'm8 8-5 4 5 4m8-8 5 4-5 4M14 4l-4 16'],
                ['admins.index', 'admins.*', 'Admin', 'M16 19v-1a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v1M9 10a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm13 9v-1a4 4 0 0 0-3-3.9M16 3.1a3.5 3.5 0 0 1 0 6.8'],
            ] as [$route, $pattern, $label, $icon])
                <a href="{{ route($route) }}" @class(['nav-link', 'is-active' => request()->routeIs($pattern)]) @if (request()->routeIs($pattern)) aria-current="page" @endif>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icon }}"/></svg>
                    <span>{{ $label }}</span>
                </a>
            @endforeach
        </nav>
        <div class="topbar-tools">
            @unless (request()->routeIs('login'))
                <a class="button button-ghost button-small help-link" href="{{ \App\Support\Guide::linkFor(request()->route()?->getName()) }}" title="Panduan untuk halaman ini">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20ZM9.1 9a3 3 0 0 1 5.8 1c0 2-3 3-3 3M12 17h.01"/></svg>
                    Panduan
                </a>
            @endunless
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
                        <small>{{ auth()->user()->loginName() }}</small>
                        <a href="{{ route('admins.index') }}">Kelola admin</a>
                        <a href="{{ route('panduan') }}">Panduan</a>
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

    @if (session('status'))
        <div class="toast" role="status">{{ session('status') }}</div>
    @endif

    <main class="app-shell">
        @hasSection('actions')
            <div class="page-actions" role="toolbar" aria-label="Tindakan halaman">
                @yield('actions')
            </div>
        @endif
        @yield('content')
    </main>

    <script>
        // The account menu closes on a click anywhere else.
        document.addEventListener('click', event => {
            const menu = document.querySelector('[data-user-menu][open]');
            if (menu && !menu.contains(event.target)) menu.open = false;
        });
        document.querySelectorAll('[data-copy]').forEach(button => button.addEventListener('click', async () => {
            const field = document.querySelector(button.dataset.copy);
            try {
                await navigator.clipboard.writeText(field.value);
            } catch {
                field.select();
                document.execCommand('copy');
            }
            const label = button.textContent;
            button.textContent = 'Tersalin';
            setTimeout(() => (button.textContent = label), 1500);
        }));
    </script>
</body>
</html>
