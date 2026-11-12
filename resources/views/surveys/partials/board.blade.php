{{-- Every form, by stage (running, finished, draft), with search, period filter, list or cards, and assignment progress. --}}
@php
    $icons = \App\Support\Ui::ICONS;
    $counts = $surveys->countBy(fn ($survey) => $survey->status());
@endphp
<section class="home-section" aria-labelledby="list-heading" data-survey-board>
    <header class="home-list-head">
        <h2 id="list-heading"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icons['clipboard'] }}"/></svg> Semua kuesioner</h2>
        <div class="home-tabs" role="tablist" aria-label="Status kuesioner">
            @foreach (['running' => 'Berjalan', 'done' => 'Selesai', 'draft' => 'Draf', 'all' => 'Semua'] as $key => $label)
                <button type="button" role="tab" aria-selected="{{ $key === 'running' ? 'true' : 'false' }}" data-tab="{{ $key }}">{{ $label }} <b>{{ $key === 'all' ? $surveys->count() : ($counts[$key] ?? 0) }}</b></button>
            @endforeach
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
            <p>Mulai dari template di Beranda atau dari formulir kosong, lalu bagikan tautannya ke responden.</p>
            <a class="button button-primary" href="{{ route('surveys.create') }}">Buat kuesioner kosong</a>
        </div>
    @else
        <div class="survey-rows" data-survey-list>
            @foreach ($surveys as $survey)
                @php
                    [$rowLine, $rowArea] = \App\Support\Monitoring::spark($trends[$survey->id] ?? array_fill(0, 14, 0), 80, 22);
                    $status = $survey->status();
                    $done = (int) ($survey->assignments_done_count ?? 0);
                    $total = (int) ($survey->assignments_count ?? 0);
                @endphp
                <article @class(['survey-row', 'is-published' => $status === 'running', 'is-done' => $status === 'done']) style="--i: {{ min($loop->index, 8) }}" data-status="{{ $status }}" data-period="{{ $survey->periodLabel() }}" data-text="{{ mb_strtolower($survey->title.' '.$survey->form_code.' '.$survey->activity) }}">
                    <span class="survey-row-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icons['clipboard'] }}"/></svg></span>
                    <div class="survey-row-main">
                        <div class="survey-row-meta">
                            <span @class(['status-badge', 'is-published' => $status === 'running', 'is-done' => $status === 'done'])>{{ $survey->statusLabel() }}</span>
                            @if ($survey->form_code)<span class="mono-chip">{{ $survey->form_code }}</span>@endif
                            @if ($survey->isLocked())<span class="status-badge is-locked" title="{{ $survey->locked_note }}">Dikunci</span>@endif
                            <span class="muted">Diubah {{ $survey->updated_at->translatedFormat('d M Y, H:i') }}</span>
                            @if ($survey->responses_max_created_at)
                                <span class="row-fresh">Respons terakhir {{ \Illuminate\Support\Carbon::parse($survey->responses_max_created_at)->diffForHumans() }}</span>
                            @endif
                        </div>
                        <h3><a href="{{ route('surveys.edit', $survey) }}">{{ $survey->title }}</a></h3>
                        <p>{{ collect([$survey->activity, $survey->periodLabel()])->filter()->implode(' · ') }} · {{ count($survey->blocks ?? []) }} blok · {{ count($survey->questions()) }} pertanyaan</p>
                        @if ($total)
                            <div class="row-progress" title="Penugasan yang sudah diisi">
                                <span class="row-progress-bar"><i style="width: {{ round($done / $total * 100) }}%"></i></span>
                                <small><b>{{ number_format($done, 0, ',', '.') }}</b> dari {{ number_format($total, 0, ',', '.') }} penugasan terisi · {{ round($done / $total * 100) }}%</small>
                            </div>
                        @endif
                    </div>
                    <div class="survey-row-actions">
                        <a class="row-button is-edit" href="{{ route('surveys.edit', $survey) }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icons['edit'] }}"/></svg> Edit</a>
                        <a class="row-button is-preview" href="{{ route('surveys.preview', $survey) }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icons['eye'] }}"/></svg> Pratinjau</a>
                        <a class="row-button is-responses" href="{{ route('surveys.responses.index', $survey) }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icons['bars'] }}"/></svg> Respons</a>
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
                        <svg class="row-spark" viewBox="0 0 80 22" preserveAspectRatio="none" role="img" aria-label="Respons 14 hari terakhir: {{ array_sum($trends[$survey->id] ?? []) }}"><title>Respons 14 hari terakhir: {{ array_sum($trends[$survey->id] ?? []) }}</title><path class="area" d="{{ $rowArea }}"/><path class="line" d="{{ $rowLine }}"/></svg>
                    </div>
                    <details class="row-menu">
                        <summary class="row-kebab" aria-label="Kelola {{ $survey->title }}" title="Kelola">⋮</summary>
                        <div class="row-menu-pop">
                            @if ($survey->isPublished())
                                <form method="POST" action="{{ route('published-surveys.destroy', $survey) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit">Tutup formulir (selesai)</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('published-surveys.store', $survey) }}">
                                    @csrf
                                    <button type="submit">{{ $status === 'done' ? 'Buka lagi' : 'Publikasikan' }}</button>
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
        // Start on the tab used last, or on "Berjalan"; a tab with nothing in it gives way to "Semua".
        const has = status => status === 'all' ? rows.length > 0 : rows.some(row => row.dataset.status === status);
        let tab = ['running', 'done', 'draft', 'all'].includes(recall('tab')) ? recall('tab') : 'running';
        if (!has(tab)) tab = 'all';

        const apply = () => {
            const term = search.value.trim().toLowerCase();
            let shown = 0;
            rows.forEach(row => {
                const match = (tab === 'all' || row.dataset.status === tab)
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
    })();
</script>
