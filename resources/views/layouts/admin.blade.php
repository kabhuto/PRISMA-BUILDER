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
    <link rel="stylesheet" href="{{ asset('css/pages.css') }}">
    @stack('head')
</head>
<body class="admin-page">
    @include('partials.topbar')

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

    <footer class="app-footer">
        <span>© {{ now()->year }} BPS Kabupaten Bangkalan. Hak cipta dilindungi.</span>
        <span>PRISMA · Platform Riset, Survei, dan Manajemen Data</span>
    </footer>

    <script>
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
