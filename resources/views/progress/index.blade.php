@extends('layouts.admin')

@section('title', 'Tenggat & laporan · '.$survey->title)

@push('head')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/monitor.css') }}">
    <link rel="stylesheet" href="{{ asset('css/manage.css') }}">
@endpush

@php
    $pictures = \App\Support\Ui::PICTURES;
    $daysLeft = $survey->collect_deadline ? (int) now()->startOfDay()->diffInDays($survey->collect_deadline, false) : null;
    $troubled = $latest->where('status', '!=', 'lancar')->count();
    $stateLabels = ['masuk' => 'Laporan masuk', 'terlewat' => 'Belum ada laporan', 'hari-ini' => 'Hari ini', 'akan' => 'Akan datang'];
    $shownDue = $due->filter(fn ($day) => $day['date'] >= now()->subDays(21)->toDateString())->take(30);
@endphp

@section('content')
    <div class="page-intro">
        <div>
            <div class="eyebrow"><a class="back-link" href="{{ route('surveys.index') }}">← BERANDA</a> <span>/</span> TENGGAT &amp; LAPORAN</div>
            <h1>Tenggat &amp; laporan progres.</h1>
            <p class="manage-for">{{ $survey->title }}</p>
            <p>Tentukan kapan pendataan berakhir dan kapan petugas melapor: sudah sampai mana, ada kendala apa, dan apa rencananya.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="validation-alert" role="alert">{{ $errors->first() }}</div>
    @endif

    <div class="monitor-kpis is-four">
        @foreach ([
            ['Tenggat pendataan', $survey->collect_deadline?->translatedFormat('d M Y') ?? 'Belum diatur', $daysLeft === null ? 'atur di bawah' : ($daysLeft > 0 ? $daysLeft.' hari lagi' : ($daysLeft === 0 ? 'hari ini' : 'lewat '.abs($daysLeft).' hari')), '#e2493b'],
            ['Jadwal laporan', $survey->reportScheduleLabel() ?? 'Belum diatur', $survey->nextReportDue() ? 'berikutnya '.\Illuminate\Support\Carbon::parse($survey->nextReportDue())->translatedFormat('l, d M') : 'tidak ada jadwal berikutnya', '#0f9d8f'],
            ['Laporan masuk', number_format($reports->count(), 0, ',', '.'), $latest->count().' petugas melapor', '#2f6fd6'],
            ['Ada kendala', number_format($troubled, 0, ',', '.'), 'menurut laporan terakhir tiap petugas', '#e8892b'],
        ] as [$label, $value, $note, $tone])
            <div class="monitor-kpi is-text" style="--tone: {{ $tone }}">
                <strong>{{ $value }}</strong>
                <span>{{ $label }}</span>
                <small>{{ $note }}</small>
            </div>
        @endforeach
    </div>

    <div class="manage-pair">
        <section class="monitor-card" aria-labelledby="schedule-heading">
            <header><h2 id="schedule-heading">Jadwal pendataan &amp; laporan</h2></header>
            <form method="POST" action="{{ route('progress.schedule', $survey) }}" class="manage-form" data-schedule>
                @csrf
                @method('PUT')
                <div class="manage-row">
                    <label class="field"><span>Mulai pendataan</span>
                        <input type="date" name="collect_start" value="{{ old('collect_start', $survey->collect_start?->toDateString()) }}">
                    </label>
                    <label class="field"><span>Tenggat pendataan (collect)</span>
                        <input type="date" name="collect_deadline" value="{{ old('collect_deadline', $survey->collect_deadline?->toDateString()) }}">
                    </label>
                </div>
                <label class="field"><span>Petugas melapor progres</span>
                    <select name="report_every" data-report-every>
                        <option value="">Tidak perlu laporan</option>
                        @foreach ($every as $key => $label)
                            <option value="{{ $key }}" @selected(old('report_every', $survey->report_every) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <div class="manage-row">
                    <label class="field" data-weekday><span>Hari laporan</span>
                        <select name="report_weekday">
                            @foreach ($weekdays as $number => $label)
                                <option value="{{ $number }}" @selected((int) old('report_weekday', $survey->report_weekday ?: 5) === $number)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="field"><span>Paling lambat pukul</span>
                        <input type="time" name="report_time" value="{{ old('report_time', $survey->report_time ?? '16:00') }}">
                    </label>
                </div>
                <button class="button button-primary" type="submit">Simpan jadwal</button>
                <p class="setting-note">Tenggat pendataan dan setiap hari laporan tampil di kalender Dashboard dan di "Perlu perhatian" bila laporan belum masuk.</p>
            </form>
        </section>

        <section class="monitor-card" aria-labelledby="link-heading">
            <header><h2 id="link-heading">Tautan laporan untuk petugas</h2></header>
            @if ($survey->report_every)
                <p class="muted">Bagikan ke petugas collect. Mereka mengisi nama, wilayah, jumlah yang sudah selesai, keadaan, kendala, dan rencana.</p>
                <div class="share-row">
                    <input type="text" id="report-link" value="{{ route('report.create', $survey) }}" readonly>
                    <button class="button button-ghost" type="button" data-copy="#report-link">Salin</button>
                    <a class="button button-ghost" href="{{ route('report.create', $survey) }}" target="_blank" rel="noopener">Buka</a>
                </div>
                <p class="setting-note">PRISMA Collect dapat mengirim laporan yang sama lewat API: <code>POST /api/v1/surveys/{{ $survey->id }}/laporan</code>.</p>
            @else
                <p class="muted">Pilih seberapa sering petugas melapor, lalu simpan jadwal. Tautan laporan muncul di sini.</p>
            @endif

            @if ($shownDue->isNotEmpty())
                <h3 class="manage-sub">Hari laporan</h3>
                <ul class="due-list">
                    @foreach ($shownDue as $day)
                        <li class="is-{{ $day['state'] }}" title="{{ $stateLabels[$day['state']] }}">
                            <b>{{ \Illuminate\Support\Carbon::parse($day['date'])->translatedFormat('d') }}</b>
                            <small>{{ \Illuminate\Support\Carbon::parse($day['date'])->translatedFormat('M') }}</small>
                        </li>
                    @endforeach
                </ul>
                <p class="due-legend"><span class="is-masuk">Laporan masuk</span><span class="is-terlewat">Belum ada laporan</span><span class="is-hari-ini">Hari ini</span><span class="is-akan">Akan datang</span></p>
            @endif
        </section>
    </div>

    <section class="monitor-card" aria-labelledby="latest-heading">
        <header><h2 id="latest-heading">Keadaan terakhir tiap petugas</h2><span class="home-chip">{{ $latest->count() }} petugas</span></header>
        @if ($latest->isEmpty())
            <p class="muted">Belum ada laporan.</p>
        @else
            <div class="report-cards">
                @foreach ($latest as $report)
                    <article class="report-card" style="--tone: {{ $report->color() }}">
                        <header>
                            <strong>{{ $report->reporter }}</strong>
                            <span class="report-status">{{ $report->statusLabel() }}</span>
                        </header>
                        <small>{{ $report->area ?: 'Tanpa wilayah' }} · {{ $report->report_date->translatedFormat('d M Y') }}</small>
                        <div class="report-progress">
                            <span><i style="width: {{ $report->percent() ?? 0 }}%"></i></span>
                            <b>{{ number_format($report->done, 0, ',', '.') }}@if ($report->target) / {{ number_format($report->target, 0, ',', '.') }} · {{ $report->percent() }}%@endif</b>
                        </div>
                        @if ($report->obstacle)<p><b>Kendala:</b> {{ $report->obstacle }}</p>@endif
                        @if ($report->plan)<p><b>Rencana:</b> {{ $report->plan }}</p>@endif
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    <section class="monitor-card" aria-labelledby="all-reports-heading">
        <header><h2 id="all-reports-heading">Semua laporan</h2><span class="home-chip">{{ $reports->count() }}</span></header>
        @if ($reports->isEmpty())
            <p class="muted">Laporan dari petugas akan tampil di sini, terbaru di atas.</p>
        @else
            <div class="table-wrap">
                <table class="data-table">
                    <thead><tr><th>Tanggal</th><th>Petugas</th><th>Wilayah</th><th>Selesai</th><th>Keadaan</th><th>Kendala</th><th>Rencana</th><th>Dari</th><th></th></tr></thead>
                    <tbody>
                        @foreach ($reports as $report)
                            <tr>
                                <td class="nowrap">{{ $report->report_date->translatedFormat('d M Y') }}</td>
                                <td>{{ $report->reporter }}</td>
                                <td>{{ $report->area ?: '—' }}</td>
                                <td class="nowrap">{{ number_format($report->done, 0, ',', '.') }}@if ($report->target) / {{ number_format($report->target, 0, ',', '.') }}@endif</td>
                                <td><span class="report-pill" style="--tone: {{ $report->color() }}">{{ $report->statusLabel() }}</span></td>
                                <td>{{ $report->obstacle ?: '—' }}</td>
                                <td>{{ $report->plan ?: '—' }}</td>
                                <td>{{ $report->source === 'api' ? 'Collect' : 'Tautan' }}</td>
                                <td>
                                    <form method="POST" action="{{ route('progress.destroy', [$survey, $report]) }}" onsubmit="return confirm('Hapus laporan ini?')">
                                        @csrf @method('DELETE')
                                        <button class="link-danger" type="submit">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    <script>
        (() => {
            const every = document.querySelector('[data-report-every]');
            const weekday = document.querySelector('[data-weekday]');
            const sync = () => (weekday.hidden = every.value !== 'weekly');
            every.addEventListener('change', sync);
            sync();
        })();
    </script>
@endsection
