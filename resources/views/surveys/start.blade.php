@extends('layouts.admin')

@section('title', 'Buat kuesioner')

@push('head')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/monitor.css') }}">
    <link rel="stylesheet" href="{{ asset('css/manage.css') }}">
@endpush

@php
    $icons = \App\Support\Ui::ICONS;
    $pictures = \App\Support\Ui::PICTURES;
    $looks = \App\Support\Ui::TEMPLATE_LOOKS;
    $customTones = ['#0f9d8f', '#2f6fd6', '#e8892b', '#1f9d55', '#7a5cc8'];
@endphp

@section('content')
    <div class="page-intro">
        <div>
            <div class="eyebrow"><a class="back-link" href="{{ route('surveys.index') }}">← BERANDA</a> <span>/</span> BUAT</div>
            <h1>Buat kuesioner.</h1>
            <p>Pilih titik awal: formulir kosong atau template siap pakai. Semua blok dan pertanyaannya tetap bisa diubah setelah dibuat.</p>
        </div>
    </div>

    @if ($errors->has('file'))
        <div class="validation-alert" role="alert">{{ $errors->first('file') }}</div>
    @endif

    <div class="start-other">
        @if (\App\Support\AppSettings::toolOn('impor'))
            <form method="POST" action="{{ route('surveys.import') }}" enctype="multipart/form-data">
                @csrf
                <label class="start-chip" title="File ekspor JSON PRISMA, atau template FormGear/FASIH (boleh pilih template.json + validation.json + preset.json sekaligus)">
                    <span style="--tone: #e8892b"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icons['upload'] }}"/></svg></span>
                    <b>Impor JSON / FormGear</b><small>dari file ekspor</small>
                    <input class="sr-only" type="file" name="files[]" accept=".json,application/json" multiple onchange="this.form.submit()">
                </label>
            </form>
        @endif
        @if (\App\Support\AppSettings::toolOn('validasi'))
            <a class="start-chip" href="{{ route('validation.create') }}">
                <span style="--tone: #2f6fd6"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icons['shield'] }}"/></svg></span>
                <b>Validasi data</b><small>periksa file Excel/CSV</small>
            </a>
        @endif
        @if (\App\Support\Access::isSuperadmin())
            <a class="start-chip" href="{{ route('settings.index') }}#template">
                <span style="--tone: #5b6b7a"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icons['edit'] }}"/></svg></span>
                <b>Kelola template</b><small>tambah, ubah, sembunyikan</small>
            </a>
        @endif
    </div>

    <h2 class="start-heading">Template <span class="home-chip">{{ count($templates) }}</span></h2>
    <div class="start-grid">
        @foreach ($templates as $key => $template)
            @php([$picture, $tone] = $looks[$key] ?? [$pictures['clipboard'], $customTones[$loop->index % count($customTones)]])
            <article class="start-card" style="--tone: {{ $tone }}; --i: {{ min($loop->index, 8) }}">
                <header>
                    <span class="template-tile-icon"><svg class="icon-fill" viewBox="0 0 24 24" aria-hidden="true">{!! $picture !!}</svg></span>
                    <div>
                        <h3>{{ $template['label'] }}</h3>
                        @if (str_starts_with($key, 'c'))<span class="start-tag">Template kantor</span>@endif
                    </div>
                </header>
                <p>{{ $template['description'] ?: 'Template dari kuesioner yang sudah ada.' }}</p>
                <ul>
                    @foreach (array_slice($blocks[$key], 0, 4) as $block)
                        <li><span>{{ $block['title'] ?: 'Blok tanpa judul' }}</span><b>{{ count($block['questions'] ?? []) }}</b></li>
                    @endforeach
                    @if (count($blocks[$key]) > 4)
                        <li class="is-more">+{{ count($blocks[$key]) - 4 }} blok lagi</li>
                    @endif
                </ul>
                <a class="button button-primary" href="{{ route('surveys.create', ['template' => $key]) }}">
                    {{ $key === \App\Support\SurveyTemplates::DEFAULT ? 'Mulai dari kosong' : 'Gunakan template' }}
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icons['arrow'] }}"/></svg>
                </a>
            </article>
        @endforeach
    </div>
@endsection
