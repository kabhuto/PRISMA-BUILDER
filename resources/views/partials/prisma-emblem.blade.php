{{-- The PRISMA emblem: the hexagonal "P" with the cube inside, and the five PRISMA apps on five sides around it.
     $active: the app this page belongs to (builder, sm, collect, datalab, dashboard); its emblem is in colour and alive.
     $class: class of the <svg>; $orbit: false for the mark alone (small places). --}}
@php
    $uid = 'pe'.substr(md5(uniqid('', true)), 0, 6);
    $active ??= null;
    $apps = [
        // Clockwise from the left, the way the data goes: build, collect, manage, process, show.
        'builder' => ['PRISMA Builder', 'Menyusun kuesioner', 76, 714, 216, 714, '#1e7be0', 'M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z'],
        'collect' => ['PRISMA Collect', 'Pendataan lapangan', 295, 257, 380, 368, '#2f9e44', 'M12 22s7-6.2 7-12a7 7 0 0 0-14 0c0 5.8 7 12 7 12Zm0-9a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z'],
        'sm' => ['PRISMA SM', 'Manajemen survei', 790, 255, 705, 366, '#0f9d8f', 'M16 19v-1a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v1M9 10a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm13 9v-1a4 4 0 0 0-3-3.9M16 3.1a3.5 3.5 0 0 1 0 6.8'],
        'datalab' => ['PRISMA DataLab', 'Pengolahan data', 1008, 711, 868, 711, '#f07a12', 'M4 6c0-1.7 3.6-3 8-3s8 1.3 8 3-3.6 3-8 3-8-1.3-8-3Zm0 0v12c0 1.7 3.6 3 8 3s8-1.3 8-3V6M4 12c0 1.7 3.6 3 8 3s8-1.3 8-3'],
        'dashboard' => ['PRISMA Dashboard', 'Monitoring', 798, 1153, 717, 1038, '#7a52cc', 'M3 3v18h18M7 15l4-4 3 3 6-6'],
    ];
    $orbit ??= true;
@endphp
<svg @class([$class ?? 'prisma-emblem', 'pe', 'pe-solo' => ! $orbit]) viewBox="{{ $orbit ? '-14 160 1110 1084' : '196 222 692 943' }}" role="img" aria-label="PRISMA{{ $active ? ' · '.$apps[$active][0] : '' }}">
    <defs>
        <linearGradient id="{{ $uid }}-b1" x1="0" y1="1" x2="1" y2="0"><stop offset="0" stop-color="#0a5fc4"/><stop offset="1" stop-color="#14a8ff"/></linearGradient>
        <linearGradient id="{{ $uid }}-b2" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#11a2f6"/><stop offset="1" stop-color="#0b4fae"/></linearGradient>
        <linearGradient id="{{ $uid }}-o1" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#ff8c0a"/><stop offset="1" stop-color="#ffb00f"/></linearGradient>
        <linearGradient id="{{ $uid }}-o2" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#ffa21a"/><stop offset="1" stop-color="#f05a05"/></linearGradient>
        <linearGradient id="{{ $uid }}-g1" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#5cbc0a"/><stop offset="1" stop-color="#b6ef2a"/></linearGradient>
        <linearGradient id="{{ $uid }}-g2" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#58b80a"/><stop offset="1" stop-color="#2f8f06"/></linearGradient>
        <linearGradient id="{{ $uid }}-ct" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#ff7a0a"/><stop offset="1" stop-color="#ffc80f"/></linearGradient>
        <linearGradient id="{{ $uid }}-cl" x1="1" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#14b0ff"/><stop offset="1" stop-color="#0a50b8"/></linearGradient>
        <linearGradient id="{{ $uid }}-cr" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#a6e824"/><stop offset="1" stop-color="#2f8f06"/></linearGradient>
    </defs>

    @if ($orbit)
        {{-- The five apps, each on its own side, joined to the mark. --}}
        @foreach ($apps as $key => [$name, $role, $x, $y, $sx, $sy, $color, $icon])
            @php($on = $key === $active)
            <g @class(['pe-app', 'is-active' => $on]) style="--pe-tone: {{ $color }}">
                <title>{{ $name }} · {{ $role }}{{ $on ? ' (sedang dibuka)' : '' }}</title>
                <line class="pe-link" x1="{{ $sx }}" y1="{{ $sy }}" x2="{{ $x }}" y2="{{ $y }}"/>
                @if ($on)
                    <circle class="pe-pulse" cx="{{ $x }}" cy="{{ $y }}" r="70"/>
                    <circle class="pe-pulse pe-pulse-2" cx="{{ $x }}" cy="{{ $y }}" r="70"/>
                    <circle class="pe-ring" cx="{{ $x }}" cy="{{ $y }}" r="88"/>
                @endif
                <g class="pe-badge" style="transform-origin: {{ $x }}px {{ $y }}px">
                    <circle class="pe-disc" cx="{{ $x }}" cy="{{ $y }}" r="70"/>
                    <svg x="{{ $x - 36 }}" y="{{ $y - 36 }}" width="72" height="72" viewBox="0 0 24 24" class="pe-icon"><path d="{{ $icon }}"/></svg>
                </g>
            </g>
        @endforeach
    @endif

    {{-- The mark, drawn from the sketch: blue on the left and top, orange on the right, green along the bottom, the cube inside. --}}
    @if ($markImage ?? null)
        {{-- A logo uploaded in place of the drawn mark keeps the five apps around it. --}}
        <image class="pe-mark" href="{{ $markImage }}" x="196" y="222" width="692" height="943" preserveAspectRatio="xMidYMid meet"/>
    @else
    <g class="pe-mark">
        <polygon points="216,493 543,242 543,368 308,550" fill="url(#{{ $uid }}-b1)"/>
        <polygon points="216,493 308,550 308,993 216,935" fill="url(#{{ $uid }}-b2)"/>
        <polygon points="308,550 543,368 543,428 350,575" fill="#0a4aa8"/>
        <polygon points="308,550 350,575 350,1028 308,993" fill="#063a86"/>
        <polygon points="543,242 868,490 776,548 543,368" fill="url(#{{ $uid }}-o1)"/>
        <polygon points="776,548 868,490 868,932 776,885" fill="url(#{{ $uid }}-o2)"/>
        <polygon points="543,368 776,548 733,575 543,428" fill="#c4470a"/>
        <polygon points="733,575 776,548 776,885 733,855" fill="#e0560a"/>
        <polygon points="425,860 573,962 733,855 800,892 567,1048 425,946" fill="url(#{{ $uid }}-g1)"/>
        <polygon points="425,946 567,1048 567,1145 425,1048" fill="url(#{{ $uid }}-g2)"/>
        <polygon points="567,1048 800,892 868,932 567,1145" fill="#3a9a08"/>
        <g class="pe-cube">
            <polygon points="543,535 658,613 543,688 426,613" fill="url(#{{ $uid }}-ct)"/>
            <polygon points="426,613 543,688 543,845 426,765" fill="url(#{{ $uid }}-cl)"/>
            <polygon points="543,688 658,613 658,765 543,845" fill="url(#{{ $uid }}-cr)"/>
        </g>
    </g>
    @endif
</svg>
@once
    <style>
        .pe{overflow:visible}
        .pe-link{stroke:color-mix(in srgb,var(--pe-tone) 30%,#9aa9b4);stroke-width:5;stroke-dasharray:4 14;stroke-linecap:round;opacity:.55}
        .pe-disc{fill:var(--surface,#fff);stroke:color-mix(in srgb,#9aa9b4 55%,transparent);stroke-width:5}
        .pe-icon path{fill:none;stroke:#8a99a5;stroke-width:1.9;stroke-linecap:round;stroke-linejoin:round}
        .pe-app{transition:opacity .3s}
        .pe-app:not(.is-active){opacity:.82}
        .pe-app:hover .pe-disc{stroke:var(--pe-tone)}
        .pe-app:hover .pe-icon path{stroke:var(--pe-tone)}
        /* The app this page belongs to: in colour, breathing, with data running along its line to the mark. */
        .pe-app.is-active .pe-disc{fill:var(--pe-tone);stroke:#fff;stroke-width:6;filter:drop-shadow(0 10px 16px color-mix(in srgb,var(--pe-tone) 45%,transparent))}
        .pe-app.is-active .pe-icon path{stroke:#fff;stroke-width:2.2}
        .pe-app.is-active .pe-link{stroke:var(--pe-tone);opacity:1;stroke-width:7;animation:pe-flow 1.1s linear infinite}
        .pe-app.is-active .pe-badge{animation:pe-bob 3.2s ease-in-out infinite}
        .pe-pulse{fill:none;stroke:var(--pe-tone);stroke-width:5;transform-box:fill-box;transform-origin:center;animation:pe-pulse 2.4s ease-out infinite}
        .pe-pulse-2{animation-delay:1.2s}
        .pe-ring{fill:none;stroke:var(--pe-tone);stroke-width:4;stroke-dasharray:10 16;opacity:.6;transform-box:fill-box;transform-origin:center;animation:pe-spin 14s linear infinite}
        .pe-cube{transform-box:fill-box;transform-origin:center;animation:pe-float 5s ease-in-out infinite}
        @keyframes pe-flow{to{stroke-dashoffset:-36}}
        @keyframes pe-bob{0%,100%{transform:scale(1)}50%{transform:scale(1.08)}}
        @keyframes pe-pulse{from{transform:scale(1);opacity:.7}to{transform:scale(1.7);opacity:0}}
        @keyframes pe-spin{to{transform:rotate(360deg)}}
        @keyframes pe-float{0%,100%{transform:translateY(0)}50%{transform:translateY(-14px)}}
        @media(prefers-reduced-motion:reduce){.pe *{animation:none!important}}
    </style>
@endonce
