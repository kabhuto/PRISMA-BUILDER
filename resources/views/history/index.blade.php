@extends('layouts.admin')

@section('title', 'Riwayat')

@push('head')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/monitor.css') }}">
    <link rel="stylesheet" href="{{ asset('css/manage.css') }}">
@endpush

@php
    $pictures = \App\Support\Ui::PICTURES;
@endphp

@section('content')
    <div class="page-intro">
        <div>
            <div class="eyebrow"><a class="back-link" href="{{ route('surveys.index') }}">← BERANDA</a> <span>/</span> RIWAYAT</div>
            <h1>Riwayat saya.</h1>
            <p>Semua yang pernah Anda buat: kuesioner menurut tahapnya, apa yang terjadi padanya, dan agenda yang Anda tambahkan.</p>
        </div>
    </div>

    <div class="monitor-kpis is-five">
        @foreach ([
            ['Dibuat', $surveys->count(), 'clipboard', '#2f6fd6', 'kuesioner buatan Anda'],
            ['Berjalan', $statuses['running'], 'layers', '#1f9d55', 'terbit, menerima isian'],
            ['Selesai', $statuses['done'], 'layers', '#5b6b7a', 'sudah ditutup'],
            ['Draf', $statuses['draft'], 'clipboard', '#e0a23c', 'belum pernah terbit'],
            ['Respons', $responsesTotal, 'users', '#e8892b', 'ke kuesioner Anda'],
        ] as [$label, $value, $icon, $tone, $note])
            <div class="monitor-kpi" style="--tone: {{ $tone }}">
                <span class="monitor-kpi-icon"><svg class="icon-fill" viewBox="0 0 24 24" aria-hidden="true">{!! $pictures[$icon] !!}</svg></span>
                <strong>{{ number_format($value, 0, ',', '.') }}</strong>
                <span>{{ $label }}</span>
                <small>{{ $note }}</small>
            </div>
        @endforeach
    </div>

    @include('surveys.partials.board', ['boardTitle' => 'Kuesioner buatan saya'])

    <div class="manage-pair">
        <section class="monitor-card" aria-labelledby="timeline-heading">
            <header><h2 id="timeline-heading">Jejak aktivitas</h2></header>
            @if ($history->isEmpty())
                <p class="muted">Belum ada aktivitas. Buat kuesioner pertama Anda dari tombol <a href="{{ route('surveys.start') }}">Buat</a>.</p>
            @else
                <ol class="monitor-history">
                    @foreach ($history as $item)
                        <li class="is-{{ $item['kind'] }}">
                            <time datetime="{{ $item['at']->toIso8601String() }}">{{ $item['at']->translatedFormat('d M Y, H:i') }}</time>
                            <a href="{{ $item['url'] }}">{{ $item['text'] }}</a>
                        </li>
                    @endforeach
                </ol>
            @endif
        </section>

        <section class="monitor-card" aria-labelledby="my-agenda-heading">
            <header><h2 id="my-agenda-heading">Agenda yang saya buat</h2>@if (\App\Support\AppSettings::menuOn('dashboard'))<a class="home-link" href="{{ route('dashboard') }}#kalender">Kalender</a>@endif</header>
            <ul class="agenda-list">
                @forelse ($agendas as $agenda)
                    <li style="--tone: {{ $agenda->color() }}">
                        <span class="agenda-date">{{ $agenda->date->translatedFormat('d M') }}</span>
                        <span class="agenda-text"><strong>{{ $agenda->title }}</strong><small>{{ $agenda->kindLabel() }}@if ($agenda->time) · {{ substr($agenda->time, 0, 5) }}@endif @if ($agenda->place) · {{ $agenda->place }}@endif</small></span>
                    </li>
                @empty
                    <li class="agenda-empty">Belum ada agenda dari Anda.</li>
                @endforelse
            </ul>
        </section>
    </div>
@endsection
