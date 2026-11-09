<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Terima kasih | {{ $survey->title }}</title>
    @include('partials.theme')
    <link rel="stylesheet" href="{{ asset('css/fill.css') }}">
</head>
<body class="fill-page" style="--accent: {{ $survey->themeColor() }}; --accent-ink: {{ $survey->themeInk() }}">
    @if (request()->boolean('bingkai'))
        @include('partials.theme-toggle', ['class' => 'theme-toggle-floating'])
    @else
        @include('partials.fill-topbar')
    @endif
    <main class="fill-thanks">
        <section class="cawi-card">
            @include('partials.kop', ['class' => 'kop-cawi'])
            @if (($assignmentClosed ?? null) !== null)
                <span class="thanks-mark" aria-hidden="true">{{ $assignmentClosed === 'submitted' ? '✓' : ($assignmentClosed === 'assignment-only' ? '🔒' : '⏱') }}</span>
                <h1>{{ match ($assignmentClosed) { 'submitted' => 'Penugasan ini sudah dikirim. Terima kasih.', 'assignment-only' => 'Formulir ini hanya dapat diisi lewat tautan penugasan dari petugas.', default => 'Tautan penugasan ini sudah kedaluwarsa.' } }}</h1>
                <p>{{ $survey->title }}</p>
            @else
            <span class="thanks-mark" aria-hidden="true">✓</span>
            <h1>{{ $survey->formSettings()['thank_you_message'] ?: 'Terima kasih, jawaban Anda sudah kami terima.' }}</h1>
            <p>{{ $survey->title }}</p>
            @endif
            @if ($survey->isPublished() && ($assignmentClosed ?? null) === null)
                <a class="fill-link" href="{{ route('form.create', $survey) }}">Kirim respons lain</a>
            @endif
        </section>
        <footer class="fill-footer">Badan Pusat Statistik</footer>
    </main>
    <script>
        // The submission went through, so the saved draft is no longer needed.
        try {
            localStorage.removeItem(@json('quisioner:'.$survey->slug));
        } catch {}
    </script>
</body>
</html>
