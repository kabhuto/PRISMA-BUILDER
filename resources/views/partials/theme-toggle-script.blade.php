@once
    <script>
        document.addEventListener('click', event => {
            if (!event.target.closest('[data-theme-toggle]')) return;
            const theme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
            document.documentElement.dataset.theme = theme;
            try { localStorage.setItem('ruang-kuesioner:theme', theme); } catch {}
        });
    </script>
@endonce
