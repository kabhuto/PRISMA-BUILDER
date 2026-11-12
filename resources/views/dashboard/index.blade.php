@extends('layouts.admin')

@section('title', 'Dashboard')

@push('head')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/monitor.css') }}">
@endpush

@php
    $icons = \App\Support\Ui::ICONS;
    $pictures = \App\Support\Ui::PICTURES;
    $total = $surveys->count();
    [$line, $area] = \App\Support\Monitoring::spark($daily['total'], 600, 170);
    $peak = max(1, max($daily['total']));
    // The ring of stages: each a share of the circle, one after another.
    $ring = [];
    $offset = 0;
    foreach (['running' => ['Berjalan', '#1f9d55'], 'done' => ['Selesai', '#5b6b7a'], 'draft' => ['Draf', '#e0a23c']] as $key => [$label, $color]) {
        $share = $total ? $statuses[$key] / $total : 0;
        $ring[] = ['label' => $label, 'color' => $color, 'count' => $statuses[$key], 'share' => $share, 'offset' => $offset];
        $offset += $share;
    }
    $maxActivity = max(1, $byActivity->max() ?? 1);
@endphp

@section('content')
    <div class="page-intro">
        <div>
            <div class="eyebrow">DASHBOARD</div>
            <h1>Dashboard.</h1>
            <p>Pantau semua kuesioner: yang sedang berjalan, yang sudah selesai, dan draf; respons dari hari ke hari; kalender tenggat dan agenda; serta riwayat apa saja yang pernah dibuat.</p>
        </div>
    </div>

    <div class="monitor-kpis">
        @foreach ([
            ['Kuesioner', $total, 'clipboard', '#2f6fd6', 'semua yang pernah dibuat'],
            ['Berjalan', $statuses['running'], 'layers', '#1f9d55', 'terbit, menerima isian'],
            ['Selesai', $statuses['done'], 'layers', '#5b6b7a', 'sudah ditutup'],
            ['Draf', $statuses['draft'], 'clipboard', '#e0a23c', 'belum pernah terbit'],
            ['Total respons', $responsesTotal, 'users', '#e8892b', 'dari semua kuesioner'],
            ['Respons 7 hari', $responsesWeek, 'users', '#0f9d8f', 'masuk minggu ini'],
        ] as [$label, $value, $icon, $tone, $note])
            <div class="monitor-kpi" style="--tone: {{ $tone }}">
                <span class="monitor-kpi-icon"><svg class="icon-fill" viewBox="0 0 24 24" aria-hidden="true">{!! $pictures[$icon] !!}</svg></span>
                <strong>{{ number_format($value, 0, ',', '.') }}</strong>
                <span>{{ $label }}</span>
                <small>{{ $note }}</small>
            </div>
        @endforeach
    </div>

    <div class="monitor-charts">
        <section class="monitor-card monitor-trend" aria-labelledby="trend-heading">
            <header><h2 id="trend-heading">Respons 30 hari terakhir</h2><span class="home-chip">{{ number_format(array_sum($daily['total']), 0, ',', '.') }} respons</span></header>
            <div class="monitor-trend-plot">
                <span class="monitor-axis-max">{{ $peak }}</span>
                <svg viewBox="0 0 600 170" preserveAspectRatio="none" role="img" aria-label="Respons per hari selama 30 hari terakhir">
                    @foreach ([0.25, 0.5, 0.75] as $grid)<line x1="0" x2="600" y1="{{ 170 * $grid }}" y2="{{ 170 * $grid }}"/>@endforeach
                    <path class="area" d="{{ $area }}"/><path class="line" d="{{ $line }}"/>
                </svg>
            </div>
            <div class="monitor-axis">
                @foreach ([0, 10, 20, 29] as $index)<span>{{ \Illuminate\Support\Carbon::parse($daily['days'][$index])->translatedFormat('d M') }}</span>@endforeach
            </div>
        </section>

        <section class="monitor-card" aria-labelledby="status-heading">
            <header><h2 id="status-heading">Status kuesioner</h2></header>
            <div class="monitor-ring">
                <svg viewBox="0 0 120 120" role="img" aria-label="Sebaran status kuesioner">
                    <circle cx="60" cy="60" r="46" class="monitor-ring-track"/>
                    @foreach ($ring as $part)
                        @if ($part['share'] > 0)
                            <circle cx="60" cy="60" r="46" stroke="{{ $part['color'] }}" stroke-dasharray="{{ round($part['share'] * 289.03, 2) }} 289.03" stroke-dashoffset="{{ round(-$part['offset'] * 289.03, 2) }}" transform="rotate(-90 60 60)"/>
                        @endif
                    @endforeach
                    <text x="60" y="58" text-anchor="middle">{{ $total }}</text>
                    <text x="60" y="74" text-anchor="middle" class="small">kuesioner</text>
                </svg>
                <ul>
                    @foreach ($ring as $part)
                        <li style="--tone: {{ $part['color'] }}"><i></i>{{ $part['label'] }}<b>{{ $part['count'] }}</b></li>
                    @endforeach
                </ul>
            </div>
        </section>

        <section class="monitor-card" aria-labelledby="activity-heading">
            <header><h2 id="activity-heading">Respons per kegiatan</h2></header>
            <div class="monitor-bars">
                @forelse ($byActivity as $activity => $count)
                    <div class="monitor-bar"><span title="{{ $activity }}">{{ $activity }}</span><span class="monitor-bar-track"><i style="width: {{ round($count / $maxActivity * 100) }}%"></i></span><b>{{ number_format($count, 0, ',', '.') }}</b></div>
                @empty
                    <p class="home-panel-empty">Belum ada respons.</p>
                @endforelse
            </div>
        </section>
    </div>

    <section class="monitor-card monitor-calendar" id="kalender" aria-labelledby="calendar-heading">
        <header>
            <h2 id="calendar-heading">Kalender tenggat &amp; agenda</h2>
            <nav class="monitor-month" aria-label="Pilih bulan">
                <a href="{{ route('dashboard', ['bulan' => $month->copy()->subMonth()->format('Y-m')]) }}#kalender" aria-label="Bulan sebelumnya">‹</a>
                <strong>{{ $month->translatedFormat('F Y') }}</strong>
                <a href="{{ route('dashboard', ['bulan' => $month->copy()->addMonth()->format('Y-m')]) }}#kalender" aria-label="Bulan berikutnya">›</a>
                @unless ($month->isSameMonth(now()))<a class="monitor-today" href="{{ route('dashboard') }}#kalender">Hari ini</a>@endunless
            </nav>
        </header>
        <div class="monitor-calendar-body">
            <div class="calendar-grid" role="grid" aria-label="Kalender {{ $month->translatedFormat('F Y') }}">
                @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $dayName)<span class="calendar-head">{{ $dayName }}</span>@endforeach
                @for ($day = $gridStart->copy(); $day->lte($gridEnd); $day->addDay())
                    @php($items = $events->get($day->toDateString(), collect()))
                    <button type="button" @class(['calendar-day', 'is-other' => ! $day->isSameMonth($month), 'is-today' => $day->isToday(), 'has-items' => $items->isNotEmpty()]) data-day="{{ $day->toDateString() }}" title="Tambah agenda {{ $day->translatedFormat('d F Y') }}">
                        <span class="calendar-num">{{ $day->day }}</span>
                        @foreach ($items->take(2) as $item)
                            <span class="calendar-pill" style="--tone: {{ $item['color'] }}">{{ $item['time'] ? $item['time'].' ' : '' }}{{ $item['title'] }}</span>
                        @endforeach
                        @if ($items->count() > 2)<span class="calendar-more">+{{ $items->count() - 2 }} lagi</span>@endif
                        @if ($items->isNotEmpty())<span class="calendar-dots">@foreach ($items->take(4) as $item)<i style="--tone: {{ $item['color'] }}"></i>@endforeach</span>@endif
                    </button>
                @endfor
            </div>

            <aside class="monitor-agenda">
                <form class="agenda-form" method="POST" action="{{ route('agenda.store') }}">
                    @csrf
                    <h3>Tambah agenda</h3>
                    @if ($errors->any())<p class="validation-alert">{{ $errors->first() }}</p>@endif
                    <input type="text" name="title" value="{{ old('title') }}" placeholder="mis. Briefing petugas SE2026" maxlength="150" required aria-label="Judul agenda" data-agenda-title>
                    <div class="agenda-form-row">
                        <select name="kind" aria-label="Jenis agenda">
                            @foreach ($kinds as $key => $kind)<option value="{{ $key }}" @selected(old('kind', 'briefing') === $key)>{{ $kind['label'] }}</option>@endforeach
                        </select>
                        <input type="date" name="date" value="{{ old('date', now()->toDateString()) }}" required aria-label="Tanggal" data-agenda-date>
                        <input type="time" name="time" value="{{ old('time') }}" aria-label="Jam">
                    </div>
                    <input type="text" name="place" value="{{ old('place') }}" placeholder="Tempat (opsional)" maxlength="150" aria-label="Tempat">
                    <select name="survey_id" aria-label="Kuesioner terkait">
                        <option value="">Tanpa kuesioner terkait</option>
                        @foreach ($surveys as $survey)<option value="{{ $survey->id }}" @selected((string) old('survey_id') === (string) $survey->id)>{{ $survey->title }}</option>@endforeach
                    </select>
                    <button class="button button-primary" type="submit">Simpan agenda</button>
                </form>

                <h3>Bulan ini</h3>
                @php($monthItems = $events->filter(fn ($items, $date) => \Illuminate\Support\Carbon::parse($date)->isSameMonth($month))->flatMap(fn ($items, $date) => $items->map(fn ($item) => $item + ['date' => $date])))
                <ul class="agenda-list">
                    @forelse ($monthItems as $item)
                        <li style="--tone: {{ $item['color'] }}">
                            <span class="agenda-date">{{ \Illuminate\Support\Carbon::parse($item['date'])->translatedFormat('d M') }}</span>
                            <span class="agenda-text">
                                @if ($item['url'])<a href="{{ $item['url'] }}">{{ $item['title'] }}</a>@else<strong>{{ $item['title'] }}</strong>@endif
                                <small>{{ $item['label'] }}@if ($item['time']) · {{ $item['time'] }}@endif @if ($item['place']) · {{ $item['place'] }}@endif</small>
                            </span>
                            @if ($item['agenda'])
                                <form method="POST" action="{{ route('agenda.destroy', $item['agenda']) }}" onsubmit="return confirm('Hapus agenda ini?')">@csrf @method('DELETE')<button type="submit" aria-label="Hapus agenda {{ $item['title'] }}" title="Hapus">✕</button></form>
                            @endif
                        </li>
                    @empty
                        <li class="agenda-empty">Belum ada agenda bulan ini. Klik tanggal di kalender untuk menambah.</li>
                    @endforelse
                </ul>
                <p class="agenda-legend">@foreach ($kinds as $kind)<span style="--tone: {{ $kind['color'] }}"><i></i>{{ $kind['label'] }}</span>@endforeach<span style="--tone: #1f9d55"><i></i>Terbit</span><span style="--tone: #5b6b7a"><i></i>Selesai</span></p>
            </aside>
        </div>
    </section>

    @include('surveys.partials.board')

    <section class="monitor-card" aria-labelledby="history-heading">
        <header><h2 id="history-heading">Riwayat aktivitas</h2><span class="home-chip">apa saja yang pernah dibuat</span></header>
        <ol class="monitor-history">
            @forelse ($history as $item)
                <li @class(['is-'.$item['kind']])>
                    <time datetime="{{ $item['at']->toIso8601String() }}">{{ $item['at']->translatedFormat('d M Y, H:i') }}</time>
                    <a href="{{ $item['url'] }}">{{ $item['text'] }}</a>
                </li>
            @empty
                <li>Belum ada aktivitas.</li>
            @endforelse
        </ol>
    </section>

    <script>
        // A click on a day puts that date in the form, ready for a title.
        document.querySelectorAll('[data-day]').forEach(day => day.addEventListener('click', () => {
            document.querySelector('[data-agenda-date]').value = day.dataset.day;
            document.querySelectorAll('.calendar-day.is-picked').forEach(other => other.classList.remove('is-picked'));
            day.classList.add('is-picked');
            const title = document.querySelector('[data-agenda-title]');
            title.focus({ preventScroll: matchMedia('(min-width: 761px)').matches });
        }));
    </script>
@endsection
