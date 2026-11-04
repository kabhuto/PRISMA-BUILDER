@extends('layouts.admin')

@section('title', 'Panduan')

@section('content')
    <div class="page-intro">
        <div>
            <div class="eyebrow">PANDUAN</div>
            <h1>Panduan {{ config('panduan.aplikasi') }}.</h1>
            <p>{{ config('panduan.pengantar') }}</p>
        </div>
    </div>

    {{-- The body of the guide, shared by every PRISMA app; the text comes from config/panduan.php. --}}
    @php
        $guide = config('panduan');
        $groups = collect($guide['halaman'])->groupBy('kelompok');
    @endphp
    <div class="manual-search">
        <input type="search" placeholder="Cari di panduan, mis. predata, logika, setujui, peta…" aria-label="Cari di panduan" data-manual-search>
    </div>

    <div class="manual-layout">
        <nav class="manual-toc" aria-label="Daftar isi panduan">
            <a href="#alur">Alur PRISMA</a>
            @foreach ($groups as $group => $sections)
                <p class="manual-toc-group">{{ $group }}</p>
                @foreach ($sections as $section)
                    <a href="#{{ $section['id'] }}">{{ $section['judul'] }}</a>
                @endforeach
            @endforeach
            @if (! empty($guide['tanya']))
                <p class="manual-toc-group">Lainnya</p>
                <a href="#tanya">Pertanyaan umum</a>
            @endif
        </nav>

        <div class="manual-body">
            <section class="manual-section" id="alur" data-manual-item>
                <header><span class="eyebrow">GAMBARAN BESAR</span><h2>Alur PRISMA</h2></header>
                <p>PRISMA terdiri dari beberapa aplikasi yang saling tersambung. Data berjalan dari kiri ke kanan; aplikasi yang sedang Anda buka ditandai.</p>
                <ol class="manual-flow">
                    @foreach ($guide['alur'] as $step)
                        <li @class(['is-here' => $step['aplikasi'] === $guide['aplikasi']])>
                            <b>{{ $step['aplikasi'] }}</b>
                            <span class="manual-tag">{{ $step['tahap'] }}</span>
                            <p>{{ $step['isi'] }}</p>
                            <small>{{ $step['oleh'] }}</small>
                        </li>
                    @endforeach
                </ol>
                @if (! empty($guide['peran']))
                    <h3>Siapa memakai apa</h3>
                    <dl class="manual-features">
                        @foreach ($guide['peran'] as $role => $text)
                            <div><dt>{{ $role }}</dt><dd>{{ $text }}</dd></div>
                        @endforeach
                    </dl>
                @endif
            </section>

            @foreach ($guide['halaman'] as $section)
                <section class="manual-section" id="{{ $section['id'] }}" data-manual-item>
                    <header>
                        <span class="eyebrow">{{ mb_strtoupper($section['kelompok']) }}</span>
                        <h2>{{ $section['judul'] }}</h2>
                        @if (! empty($section['buka']) && \Illuminate\Support\Facades\Route::has($section['buka']))
                            <a class="button button-ghost button-small" href="{{ route($section['buka']) }}">Buka halaman →</a>
                        @endif
                    </header>
                    <p>{{ $section['ringkas'] }}</p>
                    @if (! empty($section['untuk']))
                        <p class="manual-for"><b>Dipakai oleh:</b> {{ $section['untuk'] }}</p>
                    @endif
                    @if (! empty($section['langkah']))
                        <h3>Langkah</h3>
                        <ol>@foreach ($section['langkah'] as $step)<li>{{ $step }}</li>@endforeach</ol>
                    @endif
                    @if (! empty($section['fitur']))
                        <h3>Fitur</h3>
                        <dl class="manual-features">
                            @foreach ($section['fitur'] as $name => $text)
                                <div><dt>{{ $name }}</dt><dd>{{ $text }}</dd></div>
                            @endforeach
                        </dl>
                    @endif
                    @if (! empty($section['tips']))
                        <h3>Tips</h3>
                        <ul class="manual-tips">@foreach ($section['tips'] as $tip)<li>{{ $tip }}</li>@endforeach</ul>
                    @endif
                </section>
            @endforeach

            @if (! empty($guide['tanya']))
                <section class="manual-section manual-faq" id="tanya" data-manual-item>
                    <header><span class="eyebrow">LAINNYA</span><h2>Pertanyaan umum</h2></header>
                    @foreach ($guide['tanya'] as $question => $answer)
                        <details data-manual-faq><summary>{{ $question }}</summary><p>{{ $answer }}</p></details>
                    @endforeach
                </section>
            @endif
            <p class="manual-empty" data-manual-empty hidden>Tidak ada bagian panduan yang cocok dengan pencarian.</p>
        </div>
    </div>

    <script>
        (() => {
            const input = document.querySelector('[data-manual-search]');
            const items = [...document.querySelectorAll('[data-manual-item]')];
            input?.addEventListener('input', () => {
                const term = input.value.trim().toLowerCase();
                let any = false;
                items.forEach(item => {
                    const match = !term || item.textContent.toLowerCase().includes(term);
                    item.hidden = !match;
                    any ||= match;
                    // An answer that matches opens, so the reader sees why it was kept.
                    item.querySelectorAll('[data-manual-faq]').forEach(faq => (faq.open = Boolean(term) && faq.textContent.toLowerCase().includes(term)));
                });
                document.querySelector('[data-manual-empty]').hidden = any;
            });
        })();
    </script>
@endsection
