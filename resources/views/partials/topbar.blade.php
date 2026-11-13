{{-- The top bar of every admin page: brand, menu, the bell, and the account menu (which also holds the guide, colour and theme).
     $newTabHelp opens the guide in a new tab, for pages with unsaved work. --}}
<header class="topbar">
    <a class="brand" href="{{ route('surveys.index') }}" aria-label="Dashboard PRISMA">
        @include('partials.brand')
    </a>
    <nav class="main-nav" aria-label="Navigasi utama">
        @include('partials.admin-nav')
    </nav>
    <div class="topbar-tools">
        @unless (request()->routeIs('login'))
            {{-- The bell: agenda and deadlines from today to the day after tomorrow, and late progress reports. --}}
            @php($urgent = \App\Support\Monitoring::urgent())
            <details class="bell-menu" data-bell data-bell-key="{{ md5($urgent->pluck('title')->implode('|')) }}">
                <summary class="bell-button" aria-label="Notifikasi: {{ $urgent->count() }} perlu perhatian" title="Notifikasi">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 8a6 6 0 1 1 12 0c0 7 3 9 3 9H3s3-2 3-9M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                    @if ($urgent->isNotEmpty())
                        <span class="bell-count" data-bell-count>{{ $urgent->count() > 9 ? '9+' : $urgent->count() }}</span>
                    @endif
                </summary>
                <div class="bell-pop">
                    <header>
                        <strong>Notifikasi</strong>
                        <small>{{ $urgent->isEmpty() ? 'Tidak ada yang mendesak' : $urgent->count().' perlu perhatian' }}</small>
                    </header>
                    <div class="bell-list">
                        @forelse ($urgent as $item)
                            <a class="bell-item" href="{{ $item['url'] }}" style="--tone: {{ $item['color'] }}">
                                <span class="bell-item-when">{{ $item['when'] }}</span>
                                <span class="bell-item-text"><strong>{{ $item['title'] }}</strong><small>{{ $item['detail'] }}</small></span>
                            </a>
                        @empty
                            <div class="bell-empty">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                                <p>Aman. Tidak ada agenda atau tenggat dalam tiga hari ini.</p>
                            </div>
                        @endforelse
                    </div>
                    @if (\App\Support\AppSettings::menuOn('dashboard'))
                        <a class="bell-all" href="{{ route('dashboard') }}#kalender">Lihat kalender lengkap</a>
                    @endif
                </div>
            </details>
        @endunless
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
                        <a href="{{ \App\Support\Guide::linkFor(request()->route()?->getName()) }}" @if ($newTabHelp ?? false) target="_blank" rel="noopener" @endif>Panduan</a>
                    @endif
                    {{-- Look of the pages: colour and light/dark, like the account menu of a phone app. --}}
                    <div class="user-menu-look">
                        @include('partials.accent-picker', ['showLabel' => true])
                        <button class="theme-toggle user-menu-theme" type="button" data-theme-toggle aria-label="Ganti tema terang/gelap">
                            <svg class="theme-icon-dark" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 14.5A8 8 0 0 1 9.5 4a8 8 0 1 0 10.5 10.5Z"/></svg>
                            <svg class="theme-icon-light" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.9 4.9l1.4 1.4m11.4 11.4 1.4 1.4M2 12h2m16 0h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
                            <span class="theme-icon-dark">Mode gelap</span><span class="theme-icon-light">Mode terang</span>
                        </button>
                    </div>
                    <form class="user-menu-logout" method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit">Keluar</button>
                    </form>
                </div>
            </details>
        @else
            @include('partials.theme-toggle')
        @endauth
    </div>
</header>
@include('partials.theme-toggle-script')
@once
    <script>
        // The account menu and the bell close on a click anywhere else; one open at a time.
        document.addEventListener('click', event => {
            document.querySelectorAll('[data-user-menu][open],[data-bell][open]').forEach(menu => {
                if (!menu.contains(event.target)) menu.open = false;
            });
        });
        // The count on the bell goes once its list has been seen, until something new comes up.
        (() => {
            const bell = document.querySelector('[data-bell]');
            const count = bell?.querySelector('[data-bell-count]');
            if (!count) return;
            const key = 'prisma:bell-seen';
            try { if (localStorage.getItem(key) === bell.dataset.bellKey) count.classList.add('is-seen'); } catch {}
            bell.addEventListener('toggle', () => {
                if (!bell.open) return;
                count.classList.add('is-seen');
                try { localStorage.setItem(key, bell.dataset.bellKey); } catch {}
            });
        })();
    </script>
@endonce
