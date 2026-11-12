@extends('layouts.admin')

@section('title', 'Beranda')

@push('head')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@php
    $icons = \App\Support\Ui::ICONS;
    $pictures = \App\Support\Ui::PICTURES;
    $looks = \App\Support\Ui::TEMPLATE_LOOKS;
    $published = $surveys->filter->isPublished()->count();
    $today = (int) end($totalTrend);
    // A small line chart: the line and the area under it, for values over the last 14 days.
    $spark = function (array $values, int $width = 96, int $height = 28): array {
        $max = max(1, max($values));
        $step = $width / max(1, count($values) - 1);
        $points = collect($values)->values()->map(fn (int $value, int $index): string => round($index * $step, 1).','.round($height - 2 - ($value / $max) * ($height - 6), 1));
        $line = 'M'.$points->implode(' L');

        return [$line, $line.' L'.$width.','.$height.' L0,'.$height.' Z'];
    };
    [$totalLine, $totalArea] = $spark($totalTrend, 120, 30);
    $hour = (int) now('Asia/Jakarta')->format('G');
    $greeting = match (true) { $hour < 11 => 'Selamat pagi', $hour < 15 => 'Selamat siang', $hour < 18 => 'Selamat sore', default => 'Selamat malam' };
    $tones = ['#2f6fd6', '#0f9d8f', '#e8892b', '#7a8f8b', '#1f9d55'];
@endphp

@section('content')
    @if ($errors->has('file'))
        <div class="toast toast-error" role="alert">{{ $errors->first('file') }}</div>
    @endif

    <section class="home-hero">
        <svg class="hero-dots" viewBox="0 0 1200 340" preserveAspectRatio="none" aria-hidden="true">
            @foreach (range(0, 6) as $line)
                <path d="M-40 {{ 250 + $line * 14 }} C220 {{ 120 + $line * 18 }} 420 {{ 360 - $line * 6 }} 700 {{ 170 + $line * 16 }} S1040 {{ 60 + $line * 12 }} 1240 {{ 120 + $line * 10 }}"/>
            @endforeach
        </svg>
        <svg class="hero-net" viewBox="0 0 600 260" aria-hidden="true">
            @php($nodes = [[40, 60], [120, 30], [190, 90], [260, 40], [330, 110], [400, 60], [470, 120], [540, 70], [150, 160], [300, 190], [450, 200], [80, 200]])
            <g class="hero-net-lines">
                @foreach ([[0, 1], [1, 2], [2, 3], [3, 4], [4, 5], [5, 6], [6, 7], [2, 8], [8, 9], [4, 9], [9, 10], [6, 10], [8, 11], [0, 11]] as [$from, $to])
                    <line x1="{{ $nodes[$from][0] }}" y1="{{ $nodes[$from][1] }}" x2="{{ $nodes[$to][0] }}" y2="{{ $nodes[$to][1] }}"/>
                @endforeach
            </g>
            @foreach ($nodes as $index => [$x, $y])
                <circle cx="{{ $x }}" cy="{{ $y }}" r="{{ $index % 3 ? 2 : 3 }}" style="--d: {{ ($index * 0.45) % 4 }}s"/>
            @endforeach
        </svg>
        @include('partials.hero-bridge')
        <div class="home-welcome">
            <div>
                <p>{{ $greeting }},</p>
                <h1>{{ auth()->user()?->name ?? 'Admin' }}</h1>
                <span>{{ $today ? number_format($today, 0, ',', '.').' respons masuk hari ini' : 'Belum ada respons hari ini' }}</span>
            </div>
            @include('partials.prisma-mark', ['class' => 'home-welcome-mark', 'idSuffix' => 'w'])
        </div>
        <div class="home-quick">
            <button type="button" data-open-templates-quick>
                <span style="--tone: #1f9d55"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icons['plus'] }}"/></svg></span>Template
            </button>
            <a href="{{ route('validation.create') }}">
                <span style="--tone: #2f6fd6"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icons['shield'] }}"/></svg></span>Validasi
            </a>
            <form method="POST" action="{{ route('surveys.import') }}" enctype="multipart/form-data">
                @csrf
                <label>
                    <span style="--tone: #e8892b"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icons['upload'] }}"/></svg></span>Impor
                    <input class="sr-only" type="file" name="files[]" accept=".json,application/json" multiple onchange="this.form.submit()">
                </label>
            </form>
        </div>
        <div class="home-hero-body">
            <div class="home-title">
                @include('partials.prisma-mark', ['class' => 'home-mark'])
                <div>
                    <div class="home-eyebrow"><span class="home-eyebrow-dots" aria-hidden="true"></span> BERANDA
                        <span class="home-live"><i aria-hidden="true"></i>{{ $today ? number_format($today, 0, ',', '.').' respons masuk hari ini' : 'Belum ada respons hari ini' }}</span>
                    </div>
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
            @foreach ([
                ['Kuesioner', $surveys->count(), 'clipboard', '#2f6fd6', ($surveys->count() - $published).' draf'],
                ['Berjalan', $published, 'layers', '#1f9d55', ($surveys->count() ? round($published / $surveys->count() * 100) : 0).'% dari semua'],
                ['Total respons', $surveys->sum('responses_count'), 'users', '#e8892b', '+'.number_format($today, 0, ',', '.').' hari ini'],
            ] as $index => [$label, $value, $icon, $tone, $note])
                <div class="home-stat" style="--tone: {{ $tone }}; --i: {{ $index }}">
                    <span class="home-stat-icon"><svg class="icon-fill" viewBox="0 0 24 24" aria-hidden="true">{!! $pictures[$icon] !!}</svg></span>
                    <span class="home-stat-text">
                        <small>{{ $label }}</small>
                        <strong data-count="{{ $value }}">{{ number_format($value, 0, ',', '.') }}</strong>
                        <em>{{ $note }}</em>
                    </span>
                    @if ($icon === 'users')
                        <svg class="home-stat-spark" viewBox="0 0 120 30" preserveAspectRatio="none" aria-hidden="true"><path class="area" d="{{ $totalArea }}"/><path class="line" d="{{ $totalLine }}"/></svg>
                    @endif
                </div>
            @endforeach
        </div>
    </section>

    <section class="home-section" aria-labelledby="continue-heading">
        <header class="home-section-head">
            <span class="home-section-icon" style="--tone: #2f6fd6"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icons['edit'] }}"/></svg></span>
            <div>
                <h2 id="continue-heading">Lanjutkan pekerjaan</h2>
                <p>Kuesioner yang terakhir Anda ubah.</p>
            </div>
            <a class="home-link" href="{{ route('dashboard') }}#list-heading">Semua kuesioner <svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icons['arrow'] }}"/></svg></a>
        </header>
        @if ($continue->isEmpty())
            <p class="home-panel-empty">Belum ada kuesioner. Mulai dari template di bawah atau tombol Buat kuesioner.</p>
        @else
            <div class="continue-cards">
                @foreach ($continue as $survey)
                    @php($status = $survey->status())
                    <a @class(['continue-card', 'is-'.$status]) href="{{ route('surveys.edit', $survey) }}" style="--i: {{ $loop->index }}">
                        <span class="continue-top">
                            <span @class(['status-badge', 'is-published' => $status === 'running', 'is-done' => $status === 'done'])>{{ $survey->statusLabel() }}</span>
                            <small>{{ $survey->updated_at->diffForHumans() }}</small>
                        </span>
                        <strong>{{ $survey->title }}</strong>
                        <span class="continue-meta">{{ $survey->activity ?: 'Tanpa kegiatan' }} · {{ number_format($survey->responses_count, 0, ',', '.') }} respons</span>
                        <span class="continue-go">{{ $status === 'draft' ? 'Lanjutkan menyusun' : 'Buka' }} <svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icons['arrow'] }}"/></svg></span>
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    <div class="home-pair">
        <section class="home-panel" aria-labelledby="attention-heading">
            <header><h2 id="attention-heading"><span class="home-panel-dot" style="--tone: #e8892b"></span>Perlu perhatian</h2>@if ($attention->isNotEmpty())<span class="home-chip">{{ $attention->count() }}</span>@endif</header>
            @forelse ($attention as $item)
                <a class="home-activity" href="{{ $item['url'] }}" style="--tone: {{ $item['tone'] }}">
                    <span class="home-activity-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 9v4m0 4h.01M10.3 3.9 2.4 18a2 2 0 0 0 1.7 3h15.8a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z"/></svg></span>
                    <span class="home-activity-text"><strong>{{ $item['text'] }}</strong><small>{{ $item['detail'] }}</small></span>
                </a>
            @empty
                <p class="home-panel-empty is-good">Semua aman. Tidak ada yang perlu ditindaklanjuti sekarang.</p>
            @endforelse
        </section>

        <section class="home-panel" aria-labelledby="agenda-heading">
            <header><h2 id="agenda-heading"><span class="home-panel-dot" style="--tone: #2f6fd6"></span>Agenda terdekat</h2><a href="{{ route('dashboard') }}#kalender">Kalender</a></header>
            @forelse ($upcoming as $event)
                @php($date = \Illuminate\Support\Carbon::parse($event['date']))
                <a class="home-agenda" href="{{ $event['url'] ?? route('dashboard', ['bulan' => $date->format('Y-m')]).'#kalender' }}" style="--tone: {{ $event['color'] }}">
                    <span class="home-agenda-date"><b>{{ $date->format('d') }}</b><small>{{ $date->translatedFormat('M') }}</small></span>
                    <span class="home-activity-text"><strong>{{ $event['title'] }}</strong><small>{{ $event['label'] }}@if ($event['time']) · {{ $event['time'] }}@endif @if ($event['place']) · {{ $event['place'] }}@endif</small></span>
                </a>
            @empty
                <p class="home-panel-empty">Belum ada agenda. Tambahkan briefing, rapat, atau tenggat di <a href="{{ route('dashboard') }}#kalender">kalender Dashboard</a>.</p>
            @endforelse
        </section>
    </div>

    <section class="home-section" aria-labelledby="template-heading">
        <header class="home-section-head">
            <span class="home-section-icon is-dots" aria-hidden="true"></span>
            <div>
                <h2 id="template-heading">Template Kuesioner</h2>
                <p>Mulai lebih cepat dengan template siap pakai sesuai kebutuhan survei.</p>
            </div>
            <button class="home-link" type="button" aria-expanded="false" data-open-templates>Lihat semua template <svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icons['arrow'] }}"/></svg></button>
        </header>
        <div class="template-cards">
            @foreach ($templates as $key => $template)
                @php([$picture, $tone] = $looks[$key] ?? [$pictures['clipboard'], '#2f6fd6'])
                <a class="template-tile" href="{{ route('surveys.create', ['template' => $key]) }}" style="--tone: {{ $tone }}; --i: {{ $loop->index }}" @if ($loop->index >= 6) hidden @endif>
                    <svg class="template-tile-wave" viewBox="0 0 120 60" preserveAspectRatio="none" aria-hidden="true"><path d="M0 0H120V44C92 58 70 30 44 40S12 30 0 20Z"/></svg>
                    <span class="template-tile-icon"><svg class="icon-fill" viewBox="0 0 24 24" aria-hidden="true">{!! $picture !!}</svg></span>
                    <strong>{{ $template['label'] }}</strong>
                    <span class="template-tile-text">{{ $template['description'] }}</span>
                    <span class="template-tile-go">Gunakan <svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icons['arrow'] }}"/></svg></span>
                </a>
            @endforeach
        </div>
        <dialog class="template-dialog" aria-labelledby="template-dialog-title" data-template-dialog>
            <header>
                <h2 id="template-dialog-title">Semua template</h2>
                <button type="button" class="row-button is-icon" aria-label="Tutup" data-close-templates>✕</button>
            </header>
            <p>Setiap template hanya titik awal: blok dan pertanyaannya bisa diubah setelah dibuat.</p>
            @foreach ($templates as $key => $template)
                @php([$picture, $tone] = $looks[$key] ?? [$pictures['clipboard'], '#2f6fd6'])
                <article class="template-detail" style="--tone: {{ $tone }}">
                    <span class="template-tile-icon"><svg class="icon-fill" viewBox="0 0 24 24" aria-hidden="true">{!! $picture !!}</svg></span>
                    <div>
                        <strong>{{ $template['label'] }}</strong>
                        <span>{{ $template['description'] }}</span>
                        <ul>
                            @foreach (\App\Support\SurveyTemplates::blocks($key) as $block)
                                <li>{{ $block['title'] ?: 'Blok tanpa judul' }} · {{ count($block['questions'] ?? []) }} pertanyaan</li>
                            @endforeach
                        </ul>
                    </div>
                    <a class="home-button" href="{{ route('surveys.create', ['template' => $key]) }}">Gunakan</a>
                </article>
            @endforeach
        </dialog>
    </section>


    <div class="home-pair">
        <section class="home-panel" aria-labelledby="recent-heading">
            <header><h2 id="recent-heading"><span class="home-panel-dot" style="--tone: #0f9d8f"></span>Respons terbaru</h2><a href="{{ route('dashboard') }}">Dashboard</a></header>
            @forelse ($recentResponses as $response)
                <a class="home-activity" href="{{ route('surveys.responses.show', [$response->survey_id, $response->id]) }}" style="--tone: {{ $tones[$response->survey_id % count($tones)] }}">
                    <span class="home-activity-icon is-fill"><svg class="icon-fill" viewBox="0 0 24 24" aria-hidden="true">{!! $pictures['clipboard'] !!}</svg></span>
                    <span class="home-activity-text"><strong>Respons baru #{{ $response->id }}</strong><small>{{ $response->survey?->title }}</small></span>
                    <time datetime="{{ $response->created_at->toIso8601String() }}">{{ $response->created_at->diffForHumans(short: true) }}</time>
                </a>
            @empty
                <p class="home-panel-empty">Belum ada respons.</p>
            @endforelse
        </section>

        <section class="home-panel" aria-labelledby="tour-heading">
            <header><h2 id="tour-heading"><span class="home-panel-dot" style="--tone: #1f9d55"></span>Mengenal PRISMA</h2><a href="{{ route('panduan') }}#alur">Panduan</a></header>
            <ol class="home-tour">
                @foreach (config('panduan.alur', []) as $step)
                    <li @class(['is-here' => $step['aplikasi'] === 'PRISMA Builder'])><b>{{ $step['aplikasi'] }}</b><span>{{ $step['tahap'] }}</span></li>
                @endforeach
            </ol>
        </section>
    </div>

    <div class="home-waves" aria-hidden="true">
        <svg class="wave-orange" viewBox="0 0 1440 170" preserveAspectRatio="none">
            <defs><linearGradient id="wave-orange" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="var(--wave-orange-1)"/><stop offset="1" stop-color="var(--wave-orange-2)"/></linearGradient></defs>
            <path d="M820 170C1000 150 1160 78 1290 50 1360 36 1410 32 1440 32V170Z" fill="url(#wave-orange)"/>
        </svg>
        @foreach ([['mist', 'M0 60 C240 30 480 90 720 60 C960 30 1200 90 1440 60 C1680 30 1920 90 2160 60 C2400 30 2640 90 2880 60 V170 H0Z'], ['teal', 'M0 100 C320 70 640 130 960 100 C1280 70 1600 130 1920 100 C2240 70 2560 130 2880 100 V170 H0Z'], ['deep', 'M0 130 C240 112 480 148 720 130 C960 112 1200 148 1440 130 C1680 112 1920 148 2160 130 C2400 112 2640 148 2880 130 V170 H0Z']] as [$name, $path])
            <svg class="wave-{{ $name }}" viewBox="0 0 2880 170" preserveAspectRatio="none"><path d="{{ $path }}"/></svg>
        @endforeach
    </div>
    <a class="home-top" href="#" aria-label="Kembali ke atas" title="Kembali ke atas" data-to-top hidden><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 19V5m-6 6 6-6 6 6"/></svg></a>

    <script>
        (() => {
            const calm = matchMedia('(prefers-reduced-motion: reduce)').matches;
            document.documentElement.classList.add('home-anim');
            const shown = new IntersectionObserver(entries => entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-in');
                shown.unobserve(entry.target);
            }), { rootMargin: '0px 0px -40px 0px' });
            document.querySelectorAll('.home-hero-body, .home-stat, .template-tile, .continue-card, .home-panel, .home-section-head').forEach(item => shown.observe(item));

            document.querySelectorAll('[data-count]').forEach(number => {
                const target = Number(number.dataset.count);
                if (calm || !target) return;
                const start = performance.now();
                const step = now => {
                    const t = Math.min(1, (now - start) / 1100);
                    number.textContent = Math.round(target * (1 - Math.pow(1 - t, 3))).toLocaleString('id-ID');
                    if (t < 1) requestAnimationFrame(step);
                };
                requestAnimationFrame(step);
            });

            const top = document.querySelector('[data-to-top]');
            // Shown once scrolled down, and out of the way again at the foot of the page.
            addEventListener('scroll', () => (top.hidden = scrollY < 500 || scrollY + innerHeight > document.documentElement.scrollHeight - 220), { passive: true });
            top.addEventListener('click', event => { event.preventDefault(); scrollTo({ top: 0, behavior: calm ? 'auto' : 'smooth' }); });
        })();

        (() => {
            // One open menu at a time; a click elsewhere closes it.
            document.addEventListener('click', event => {
                document.querySelectorAll('.row-menu[open]').forEach(menu => { if (!menu.contains(event.target)) menu.open = false; });
            });
            const dialog = document.querySelector('[data-template-dialog]');
            // On a phone the cards are hidden until asked for, then open in place; elsewhere the full list opens.
            const phone = matchMedia('(max-width: 760px)');
            const cards = document.querySelector('.template-cards');
            document.querySelector('[data-open-templates-quick]')?.addEventListener('click', () => {
                const more = document.querySelector('[data-open-templates]');
                if (!document.querySelector('.template-cards').classList.contains('is-open')) more.click();
                document.getElementById('template-heading').scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
            document.querySelector('[data-open-templates]')?.addEventListener('click', event => {
                if (!phone.matches) return dialog.showModal();
                const open = cards.classList.toggle('is-open');
                event.currentTarget.classList.toggle('is-open', open);
                event.currentTarget.firstChild.textContent = open ? 'Sembunyikan template ' : 'Lihat semua template ';
                event.currentTarget.setAttribute('aria-expanded', String(open));
            });
            dialog.querySelector('[data-close-templates]').addEventListener('click', () => dialog.close());
            dialog.addEventListener('click', event => { if (event.target === dialog) dialog.close(); });
        })();
    </script>
@endsection
