<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Laporan progres | {{ $survey->title }}</title>
    @include('partials.theme')
    <link rel="stylesheet" href="{{ asset('css/fill.css') }}">
    <link rel="stylesheet" href="{{ asset('css/manage.css') }}">
</head>
<body class="fill-page" style="--accent: {{ $survey->themeColor() }}; --accent-ink: {{ $survey->themeInk() }}">
    @include('partials.fill-topbar')
    <main class="report-page">
        <section class="cawi-card report-sheet">
            <span class="report-eyebrow">LAPORAN PROGRES PETUGAS</span>
            <h1>{{ $survey->title }}</h1>
            <p class="report-meta">
                @if ($survey->collect_deadline)Tenggat pendataan <b>{{ $survey->collect_deadline->translatedFormat('d F Y') }}</b> · @endif
                {{ $survey->reportScheduleLabel() }}
                @if ($next) · laporan berikutnya <b>{{ \Illuminate\Support\Carbon::parse($next)->translatedFormat('l, d M') }}</b>@endif
            </p>

            @if (session('status'))
                <div class="report-done" role="status">✓ {{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="report-error" role="alert">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('report.store', $survey) }}" class="report-form">
                @csrf
                <div class="report-grid">
                    <label><span>Nama petugas <i>*</i></span>
                        <input type="text" name="reporter" value="{{ old('reporter') }}" maxlength="100" required autocomplete="name" data-remember="reporter">
                    </label>
                    <label><span>Wilayah tugas</span>
                        <input type="text" name="area" value="{{ old('area') }}" maxlength="150" placeholder="mis. Kec. Arosbaya, Desa Tengket" data-remember="area">
                    </label>
                    <label><span>Tanggal laporan</span>
                        <input type="date" name="report_date" value="{{ old('report_date', now()->toDateString()) }}" max="{{ now()->toDateString() }}">
                    </label>
                    <div class="report-pair">
                        <label><span>Sudah selesai <i>*</i></span>
                            <input type="number" name="done" value="{{ old('done') }}" min="0" inputmode="numeric" required>
                        </label>
                        <label><span>Dari target</span>
                            <input type="number" name="target" value="{{ old('target') }}" min="1" inputmode="numeric" data-remember="target">
                        </label>
                    </div>
                </div>

                <fieldset class="report-status-pick">
                    <legend>Keadaan pendataan <i>*</i></legend>
                    @foreach ($statuses as $key => $status)
                        <label style="--tone: {{ $status['color'] }}">
                            <input type="radio" name="status" value="{{ $key }}" @checked(old('status', 'lancar') === $key)>
                            <span>{{ $status['label'] }}</span>
                        </label>
                    @endforeach
                </fieldset>

                <label class="report-wide"><span>Kendala di lapangan</span>
                    <textarea name="obstacle" rows="3" maxlength="2000" placeholder="mis. responden tidak di rumah, sinyal sulit, alamat tidak ditemukan">{{ old('obstacle') }}</textarea>
                </label>
                <label class="report-wide"><span>Rencana / tindak lanjut</span>
                    <textarea name="plan" rows="2" maxlength="2000" placeholder="mis. kunjungan ulang sore hari">{{ old('plan') }}</textarea>
                </label>

                <button class="report-submit" type="submit">Kirim laporan</button>
            </form>
        </section>
        <footer class="fill-footer">Badan Pusat Statistik · PRISMA</footer>
    </main>
    <script>
        // The officer's name, area, and target are remembered on this phone for the next report.
        document.querySelectorAll('[data-remember]').forEach(field => {
            const key = 'prisma:laporan:' + field.dataset.remember;
            try { if (!field.value) field.value = localStorage.getItem(key) || ''; } catch {}
            field.form.addEventListener('submit', () => { try { localStorage.setItem(key, field.value); } catch {} });
        });
    </script>
</body>
</html>
