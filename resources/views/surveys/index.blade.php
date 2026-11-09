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
    // Filled two-tone pictures (inner SVG) for the number cards and the templates.
    $people = '<circle cx="9" cy="8" r="3.6"/><path d="M2 20a7 7 0 0 1 14 0Z"/><circle cx="17" cy="8.5" r="3" opacity=".45"/><path d="M15.5 13.2A6 6 0 0 1 22 19.5h-4.5a8.5 8.5 0 0 0-2-6.3Z" opacity=".45"/>';
    $pictures = [
        'clipboard' => '<rect x="4" y="4" width="16" height="18" rx="2.5"/><rect x="8" y="2" width="8" height="4.5" rx="1.5" fill="#fff" stroke="currentColor" stroke-width="1.6"/><path d="M8 11.5h8M8 15.5h5" stroke="#fff" stroke-width="1.8" stroke-linecap="round"/>',
        'layers' => '<path d="M12 2.5 2.5 7.5 12 12.5l9.5-5Z"/><path d="M4.4 11.1 2.5 12 12 17l9.5-5-1.9-.9L12 15.1Z" opacity=".7"/><path d="M4.4 15.6 2.5 16.5 12 21.5l9.5-5-1.9-.9L12 19.6Z" opacity=".45"/>',
        'users' => $people,
    ];
    // Each template's picture and colour on its card.
    $looks = [
        'blank' => ['<path d="M6 2h8l5 5v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2Z" opacity=".4"/><path d="M14 2v5h5Z" opacity=".7"/><circle cx="17.5" cy="17.5" r="5"/><path d="M17.5 15v5M15 17.5h5" stroke="#fff" stroke-width="1.8" stroke-linecap="round"/>', '#2f6fd6'],
        'household' => ['<path d="M12 3 2.5 11H5v9a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-9h2.5Z"/><path d="M10 21v-6h4v6Z" fill="#fff" opacity=".85"/>', '#1f9d55'],
        'business' => ['<path d="M4 10h16v10a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1Z" opacity=".4"/><path d="M3 4h18l1 5a3 3 0 0 1-5 2 3 3 0 0 1-5 0 3 3 0 0 1-5 0 3 3 0 0 1-5-2Z"/><path d="M8 4.5 7 10.5M12 4.5v6M16 4.5l1 6" stroke="#fff" stroke-width="1.4" opacity=".75"/><path d="M10 21v-5h4v5Z"/>', '#e8892b'],
        'listing' => ['<rect x="3" y="4.5" width="3.5" height="3.5" rx="1"/><rect x="3" y="10.25" width="3.5" height="3.5" rx="1"/><rect x="3" y="16" width="3.5" height="3.5" rx="1"/><rect x="9" y="5" width="12" height="2.5" rx="1.25" opacity=".5"/><rect x="9" y="10.75" width="12" height="2.5" rx="1.25" opacity=".5"/><rect x="9" y="16.5" width="12" height="2.5" rx="1.25" opacity=".5"/>', 'var(--tone-navy)'],
        'evaluation' => ['<path d="M12 1.5l2.4 1.7 2.9-.3 1.2 2.7 2.7 1.2-.3 2.9 1.7 2.4-1.7 2.4.3 2.9-2.7 1.2-1.2 2.7-2.9-.3L12 22.5l-2.4-1.7-2.9.3-1.2-2.7-2.7-1.2.3-2.9L1.5 12l1.7-2.4-.3-2.9 2.7-1.2 1.2-2.7 2.9.3Z"/><path d="M12 6.8l1.6 3.3 3.6.5-2.6 2.5.6 3.6-3.2-1.7-3.2 1.7.6-3.6-2.6-2.5 3.6-.5Z" fill="#fff" opacity=".92"/>', '#43a047'],
        'registration' => [$people, '#2f6fd6'],
    ];
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
        <div class="home-hero-body">
            <div class="home-title">
                @include('partials.prisma-mark', ['class' => 'home-mark'])
                <div>
                    <div class="home-eyebrow"><span class="home-eyebrow-dots" aria-hidden="true"></span> DASHBOARD
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
                ['Terbit', $published, 'layers', '#1f9d55', ($surveys->count() ? round($published / $surveys->count() * 100) : 0).'% dari semua'],
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

    <section class="home-section" aria-labelledby="template-heading">
        <header class="home-section-head">
            <span class="home-section-icon is-dots" aria-hidden="true"></span>
            <div>
                <h2 id="template-heading">Template Kuesioner</h2>
                <p>Mulai lebih cepat dengan template siap pakai sesuai kebutuhan survei.</p>
            </div>
            <button class="home-link" type="button" data-open-templates>Lihat semua template <svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icons['arrow'] }}"/></svg></button>
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
                    @php([$rowLine, $rowArea] = $spark($trends[$survey->id] ?? array_fill(0, 14, 0), 80, 22))
                    <article @class(['survey-row', 'is-published' => $survey->isPublished()]) style="--i: {{ min($loop->index, 8) }}" data-status="{{ $survey->isPublished() ? 'published' : 'other' }}" data-period="{{ $survey->periodLabel() }}" data-text="{{ mb_strtolower($survey->title.' '.$survey->form_code.' '.$survey->activity) }}">
                        <span class="survey-row-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icons['clipboard'] }}"/></svg></span>
                        <div class="survey-row-main">
                            <div class="survey-row-meta">
                                <span @class(['status-badge', 'is-published' => $survey->isPublished()])>{{ $survey->isPublished() ? 'Terbit' : 'Draf' }}</span>
                                @if ($survey->form_code)<span class="mono-chip">{{ $survey->form_code }}</span>@endif
                                <span class="muted">Diubah {{ $survey->updated_at->translatedFormat('d M Y, H:i') }}</span>
                                @if ($survey->responses_max_created_at)
                                    <span class="row-fresh">Respons terakhir {{ \Illuminate\Support\Carbon::parse($survey->responses_max_created_at)->diffForHumans() }}</span>
                                @endif
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
                            <svg class="row-spark" viewBox="0 0 80 22" preserveAspectRatio="none" role="img" aria-label="Respons 14 hari terakhir: {{ array_sum($trends[$survey->id] ?? []) }}"><title>Respons 14 hari terakhir: {{ array_sum($trends[$survey->id] ?? []) }}</title><path class="area" d="{{ $rowArea }}"/><path class="line" d="{{ $rowLine }}"/></svg>
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
            document.querySelectorAll('.home-hero-body, .home-stat, .template-tile, .survey-row, .home-section-head, .home-list-head').forEach(item => shown.observe(item));

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
            addEventListener('scroll', () => (top.hidden = scrollY < 500), { passive: true });
            top.addEventListener('click', event => { event.preventDefault(); scrollTo({ top: 0, behavior: calm ? 'auto' : 'smooth' }); });
        })();

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
            const dialog = document.querySelector('[data-template-dialog]');
            document.querySelector('[data-open-templates]')?.addEventListener('click', () => dialog.showModal());
            dialog.querySelector('[data-close-templates]').addEventListener('click', () => dialog.close());
            dialog.addEventListener('click', event => { if (event.target === dialog) dialog.close(); });
        })();
    </script>
@endsection
