@extends('layouts.admin')

@section('title', 'Dashboard')

@push('head')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@php
    // Line icons (24×24 strokes) used on this page.
    $icons = [
        'plus' => 'M12 5v14M5 12h14',
        'shield' => 'M12 3l7 3v5c0 4.5-3 8.3-7 10-4-1.7-7-5.5-7-10V6l7-3Zm-3 9 2 2 4-4',
        'upload' => 'M12 15V4m0 0-4 4m4-4 4 4M4 15v3a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-3',
        'clipboard' => 'M9 3h6a1 1 0 0 1 1 1v2H8V4a1 1 0 0 1 1-1Zm-1 2H6a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1h-2M9 12h6M9 16h4',
        'layers' => 'M12 3 3 8l9 5 9-5-9-5ZM3 12l9 5 9-5M3 16l9 5 9-5',
        'users' => 'M16 19v-1a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v1M9 10a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm13 9v-1a4 4 0 0 0-3-3.9M16 3.1a3.5 3.5 0 0 1 0 6.8',
        'search' => 'M11 18a7 7 0 1 0 0-14 7 7 0 0 0 0 14Zm9 2-4-4',
        'calendar' => 'M7 3v3m10-3v3M4 9h16M5 5h14a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z',
        'rows' => 'M8 6h12M8 12h12M8 18h12M4 6h.01M4 12h.01M4 18h.01',
        'tiles' => 'M4 4h7v7H4zM13 4h7v7h-7zM4 13h7v7H4zM13 13h7v7h-7z',
        'edit' => 'M4 20h4L19 9a2.8 2.8 0 0 0-4-4L4 16v4Z',
        'eye' => 'M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Zm10 3a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z',
        'bars' => 'M5 20V11M10 20V4M15 20v-8M20 20V8',
        'arrow' => 'M5 12h14m-5-5 5 5-5 5',
        'chevron' => 'm6 9 6 6 6-6',
    ];
    // Each template's picture and colour on its card.
    $looks = [
        'blank' => ['M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h5M14 3l5 5h-5V3Zm5 5v4m-1 4v6m-3-3h6', '#2f6fd6'],
        'household' => ['M3 11l9-7 9 7M5 10v10h5v-6h4v6h5V10', '#1f9d55'],
        'business' => ['M4 9h16l-1.5-5h-13L4 9Zm0 0a2.7 2.7 0 0 0 5.3 0 2.7 2.7 0 0 0 5.4 0 2.7 2.7 0 0 0 5.3 0M5 12v8h14v-8M10 20v-5h4v5', '#e8892b'],
        'listing' => ['M9 6h11M9 12h11M9 18h11M4.5 6h.01M4.5 12h.01M4.5 18h.01', '#7c5cd6'],
        'evaluation' => ['M12 3l2.6 5.3 5.9.9-4.3 4.1 1 5.8L12 16.4 6.8 19.1l1-5.8L3.5 9.2l5.9-.9L12 3Z', '#43a047'],
        'registration' => [$icons['users'], '#2f6fd6'],
    ];
    $published = $surveys->filter->isPublished()->count();
@endphp

@section('content')
    @if ($errors->has('file'))
        <div class="toast toast-error" role="alert">{{ $errors->first('file') }}</div>
    @endif

    <section class="home-hero">
        @include('partials.hero-bridge')
        <div class="home-hero-body">
            <div class="home-eyebrow"><span class="home-eyebrow-dots" aria-hidden="true"></span> DASHBOARD</div>
            <div class="home-title">
                @include('partials.prisma-mark', ['class' => 'home-mark'])
                <div>
                    <h1>PRISMA<span>.</span></h1>
                    <p>Platform riset, survei, dan manajemen data untuk semua kegiatan BPS: buat formulir, publikasikan, lalu pantau dan olah responsnya.</p>
                </div>
            </div>
            <div class="home-actions">
                <a class="home-button is-green" href="#template-heading">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icons['plus'] }}"/></svg> Buat dari template
                </a>
                <a class="home-button" href="{{ route('validation.create') }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icons['shield'] }}"/></svg> Validasi data
                </a>
                <form class="import-form" method="POST" action="{{ route('surveys.import') }}" enctype="multipart/form-data">
                    @csrf
                    <label class="home-button" title="File ekspor JSON PRISMA, atau template FormGear/FASIH (boleh pilih template.json + validation.json + preset.json sekaligus)">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icons['upload'] }}"/></svg> Impor JSON / FormGear
                        <input class="sr-only" type="file" name="files[]" accept=".json,application/json" multiple onchange="this.form.submit()">
                    </label>
                </form>
                <a class="home-button is-navy" href="{{ route('surveys.create') }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icons['plus'] }}"/></svg> Buat kuesioner
                </a>
            </div>
        </div>
        <div class="home-stats">
            @foreach ([['Kuesioner', $surveys->count(), 'clipboard', 'var(--brand)'], ['Terbit', $published, 'layers', '#1f9d55'], ['Total respons', $surveys->sum('responses_count'), 'users', '#e8892b']] as [$label, $value, $icon, $tone])
                <div class="home-stat" style="--tone: {{ $tone }}">
                    <span class="home-stat-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icons[$icon] }}"/></svg></span>
                    <span class="home-stat-text"><small>{{ $label }}</small><strong>{{ number_format($value, 0, ',', '.') }}</strong></span>
                </div>
            @endforeach
        </div>
    </section>

    <section class="home-section" aria-labelledby="template-heading">
        <header class="home-section-head">
            <span class="home-section-icon is-dots" aria-hidden="true"></span>
            <div>
                <h2 id="template-heading">Template Kuesioner</h2>
                <p>Mulai lebih cepat dengan template siap pakai sesuai kebutuhan survei.</p>
            </div>
            @if (count($templates) > 6)
                <button class="home-link" type="button" data-more-templates>Lihat semua template <svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icons['arrow'] }}"/></svg></button>
            @endif
        </header>
        <div class="template-cards">
            @foreach ($templates as $key => $template)
                @php([$picture, $tone] = $looks[$key] ?? [$icons['clipboard'], '#2f6fd6'])
                <a class="template-tile" href="{{ route('surveys.create', ['template' => $key]) }}" style="--tone: {{ $tone }}" @if ($loop->index >= 6) hidden data-extra-template @endif>
                    <span class="template-tile-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $picture }}"/></svg></span>
                    <strong>{{ $template['label'] }}</strong>
                    <span>{{ $template['description'] }}</span>
                </a>
            @endforeach
        </div>
    </section>

    <section class="home-section" aria-labelledby="list-heading" data-survey-board>
        <header class="home-list-head">
            <h2 id="list-heading"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icons['clipboard'] }}"/></svg> Kuesioner saya</h2>
            <div class="home-tabs" role="tablist" aria-label="Status kuesioner">
                <button type="button" role="tab" aria-selected="true" data-tab="published">Terbit</button>
                <button type="button" role="tab" aria-selected="false" data-tab="other">Draft &amp; Lainnya</button>
            </div>
            <div class="home-filters">
                <label class="home-search">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icons['search'] }}"/></svg>
                    <input type="search" placeholder="Cari judul kuesioner…" aria-label="Cari judul kuesioner" data-survey-search>
                </label>
                <label class="home-select">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icons['calendar'] }}"/></svg>
                    <select aria-label="Saring menurut periode" data-survey-period>
                        <option value="">Semua periode</option>
                        @foreach ($periods as $period)
                            <option value="{{ $period }}">{{ $period }}</option>
                        @endforeach
                    </select>
                </label>
                <div class="home-view" role="group" aria-label="Tampilan daftar">
                    <button type="button" aria-pressed="true" data-view="list" title="Daftar"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icons['rows'] }}"/></svg></button>
                    <button type="button" aria-pressed="false" data-view="grid" title="Kartu"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icons['tiles'] }}"/></svg></button>
                </div>
            </div>
        </header>

        @if ($surveys->isEmpty())
            <div class="empty-state">
                <span class="empty-icon">▤</span>
                <h2>Belum ada kuesioner</h2>
                <p>Pilih salah satu template di atas atau mulai dari formulir kosong, lalu bagikan tautannya ke responden.</p>
                <a class="button button-primary" href="{{ route('surveys.create') }}">Buat kuesioner kosong</a>
            </div>
        @else
            <div class="survey-rows" data-survey-list>
                @foreach ($surveys as $survey)
                    <article class="survey-row" data-status="{{ $survey->isPublished() ? 'published' : 'other' }}" data-period="{{ $survey->periodLabel() }}" data-text="{{ mb_strtolower($survey->title.' '.$survey->form_code.' '.$survey->activity) }}">
                        <span class="survey-row-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icons['clipboard'] }}"/></svg></span>
                        <div class="survey-row-main">
                            <div class="survey-row-meta">
                                <span @class(['status-badge', 'is-published' => $survey->isPublished()])>{{ $survey->isPublished() ? 'Terbit' : 'Draf' }}</span>
                                @if ($survey->form_code)<span class="mono-chip">{{ $survey->form_code }}</span>@endif
                                <span class="muted">Diubah {{ $survey->updated_at->translatedFormat('d M Y, H:i') }}</span>
                            </div>
                            <h3><a href="{{ route('surveys.edit', $survey) }}">{{ $survey->title }}</a></h3>
                            <p>{{ collect([$survey->activity, $survey->periodLabel()])->filter()->implode(' · ') }} · {{ count($survey->blocks ?? []) }} blok · {{ count($survey->questions()) }} pertanyaan</p>
                        </div>
                        <div class="survey-row-actions">
                            <a class="row-button" href="{{ route('surveys.edit', $survey) }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icons['edit'] }}"/></svg> Edit</a>
                            <a class="row-button" href="{{ route('surveys.preview', $survey) }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icons['eye'] }}"/></svg> Pratinjau</a>
                            <a class="row-button" href="{{ route('surveys.responses.index', $survey) }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icons['bars'] }}"/></svg> Respons</a>
                            <details class="row-menu">
                                <summary class="row-button is-icon" aria-label="Tindakan lain untuk {{ $survey->title }}" title="Tindakan lain">⋯</summary>
                                <div class="row-menu-pop">
                                    <a href="{{ route('surveys.export-json', $survey) }}" title="Struktur, aturan validasi, dan semua respons">Ekspor JSON</a>
                                    <a href="{{ route('surveys.export-formgear', $survey) }}" title="Template, validasi, dan preset FormGear/FASIH (ZIP)">Ekspor FormGear</a>
                                    <a href="{{ route('assignments.index', $survey) }}">Penugasan</a>
                                    <a href="{{ route('surveys.versions.index', $survey) }}">Riwayat versi</a>
                                    <a href="{{ route('surveys.print', $survey) }}">Cetak</a>
                                    @if ($survey->isPublished())
                                        <input class="sr-only" type="text" value="{{ route('form.create', $survey) }}" id="share-{{ $survey->id }}" readonly tabindex="-1" aria-hidden="true">
                                        <button type="button" data-copy="#share-{{ $survey->id }}">Salin tautan</button>
                                    @endif
                                </div>
                            </details>
                        </div>
                        <div class="survey-row-count">
                            <strong>{{ number_format($survey->responses_count, 0, ',', '.') }}</strong>
                            <span>respons</span>
                        </div>
                        <details class="row-menu">
                            <summary class="row-kebab" aria-label="Kelola {{ $survey->title }}" title="Kelola">⋮</summary>
                            <div class="row-menu-pop">
                                @if ($survey->isPublished())
                                    <form method="POST" action="{{ route('published-surveys.destroy', $survey) }}">
                                        @csrf @method('DELETE')
                                        <button type="submit">Tutup formulir</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('published-surveys.store', $survey) }}">
                                        @csrf
                                        <button type="submit">Publikasikan</button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('surveys.destroy', $survey) }}" onsubmit="return confirm('Hapus kuesioner ini beserta semua responsnya?')">
                                    @csrf @method('DELETE')
                                    <button class="is-danger" type="submit" aria-label="Hapus {{ $survey->title }}">Hapus</button>
                                </form>
                            </div>
                        </details>
                    </article>
                @endforeach
            </div>
            <p class="home-empty" data-survey-empty hidden>Tidak ada kuesioner yang cocok di sini.</p>
        @endif
    </section>

    <div class="home-waves" aria-hidden="true">
        <svg viewBox="0 0 1440 120" preserveAspectRatio="none"><path d="M0 70 C240 20 480 110 760 64 S1200 30 1440 80 V120 H0 Z" fill="var(--wave-1)"/><path d="M0 92 C300 60 560 120 860 88 S1260 70 1440 100 V120 H0 Z" fill="var(--wave-2)"/></svg>
    </div>

    <script>
        (() => {
            const board = document.querySelector('[data-survey-board]');
            const rows = [...board.querySelectorAll('.survey-row')];
            const list = board.querySelector('[data-survey-list]');
            const search = board.querySelector('[data-survey-search]');
            const period = board.querySelector('[data-survey-period]');
            const empty = board.querySelector('[data-survey-empty]');
            const tabs = [...board.querySelectorAll('[data-tab]')];
            const views = [...board.querySelectorAll('[data-view]')];
            const remember = (key, value) => { try { localStorage.setItem('prisma:dashboard:' + key, value); } catch {} };
            const recall = key => { try { return localStorage.getItem('prisma:dashboard:' + key); } catch { return null; } };
            // Start on the tab used last, or on "Terbit"; a tab with nothing in it gives way to the other.
            const has = status => rows.some(row => row.dataset.status === status);
            let tab = recall('tab') === 'other' ? 'other' : 'published';
            if (!has(tab) && rows.length) tab = tab === 'published' ? 'other' : 'published';

            const apply = () => {
                const term = search.value.trim().toLowerCase();
                let shown = 0;
                rows.forEach(row => {
                    const match = row.dataset.status === tab
                        && (!term || row.dataset.text.includes(term))
                        && (!period.value || row.dataset.period === period.value);
                    row.hidden = !match;
                    shown += match ? 1 : 0;
                });
                tabs.forEach(button => button.setAttribute('aria-selected', String(button.dataset.tab === tab)));
                if (empty) empty.hidden = shown > 0;
            };
            const setView = view => {
                list?.classList.toggle('is-grid', view === 'grid');
                views.forEach(button => button.setAttribute('aria-pressed', String(button.dataset.view === view)));
            };

            tabs.forEach(button => button.addEventListener('click', () => { tab = button.dataset.tab; remember('tab', tab); apply(); }));
            views.forEach(button => button.addEventListener('click', () => { setView(button.dataset.view); remember('view', button.dataset.view); }));
            search.addEventListener('input', apply);
            period.addEventListener('change', apply);
            setView(recall('view') || 'list');
            apply();

            // One open menu at a time; a click elsewhere closes it.
            document.addEventListener('click', event => {
                document.querySelectorAll('.row-menu[open]').forEach(menu => { if (!menu.contains(event.target)) menu.open = false; });
            });
            document.querySelector('[data-more-templates]')?.addEventListener('click', event => {
                document.querySelectorAll('[data-extra-template]').forEach(card => (card.hidden = false));
                event.currentTarget.remove();
            });
        })();
    </script>
@endsection
