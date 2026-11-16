{{-- The PRISMA emblem: the hexagonal "P" of glossy ribbons with a cube inside (the sketch), and the five PRISMA apps on five
     of its sides as white icons riding the ribbons. $active: the app this page belongs to (builder, sm, collect, datalab,
     dashboard); its icon glows and breathes. $class: class of the <svg>; $orbit: false for the mark alone;
     $markImage: an uploaded logo in place of the drawing. --}}
@php
    $uid = 'pe'.substr(md5(uniqid('', true)), 0, 6);
    $active ??= null;
    $orbit ??= true;
    // [name, role, centre x, centre y, colour, icon (filled, 24 grid)], clockwise from the left as the data goes.
    $apps = [
        'builder' => ['PRISMA Builder', 'Menyusun kuesioner', 268, 760, '#1e7be0', 'M9 2h6a1 1 0 0 1 1 1v1h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2V3a1 1 0 0 1 1-1Zm1 2v1h4V4h-4ZM8 10v2h8v-2H8Zm0 4v2h6v-2H8Z'],
        'collect' => ['PRISMA Collect', 'Pendataan lapangan', 392, 405, '#2f9e44', 'M12 2a7 7 0 0 1 7 7c0 5.2-7 13-7 13S5 14.2 5 9a7 7 0 0 1 7-7Zm0 4.2a2.8 2.8 0 1 0 0 5.6 2.8 2.8 0 0 0 0-5.6Z'],
        'sm' => ['PRISMA SM', 'Manajemen survei', 694, 405, '#0f9d8f', 'M9 11a4 4 0 1 1 0-8 4 4 0 0 1 0 8Zm7.5 1a3.2 3.2 0 1 1 0-6.4 3.2 3.2 0 0 1 0 6.4ZM2 20c0-3.6 3.1-6 7-6s7 2.4 7 6v1H2v-1Zm15.4 1v-1c0-1.9-.7-3.5-1.9-4.7.3 0 .7-.1 1-.1 3.2 0 5.5 1.9 5.5 4.8v1h-4.6Z'],
        'datalab' => ['PRISMA DataLab', 'Pengolahan data', 812, 712, '#f07a12', 'M12 2.5 22 8l-10 5.5L2 8l10-5.5Zm-7.7 9.2L12 16l7.7-4.3L22 13l-10 5.5L2 13l2.3-1.3Zm0 5L12 21l7.7-4.3L22 18l-10 5.5L2 18l2.3-1.3Z'],
        'dashboard' => ['PRISMA Dashboard', 'Monitoring', 690, 1010, '#7a52cc', 'M3 20h3v-6H3v6Zm5 0h3v-9H8v9Zm5 0h3v-5h-3v5Zm5 0h3V9h-3v11ZM3 10.5 9 5l4 3.5L19.6 2l1.4 1.4-8 8L9 7.8l-4.6 4.1L3 10.5Z'],
    ];
    $bands = [
        'b' => '216,493 543,242 543,428 350,575 350,1028 216,935',
        'o' => '543,242 868,490 868,932 733,855 733,575 543,428',
        'g' => '425,860 573,962 733,855 868,932 567,1145 425,1048',
    ];
@endphp
<svg @class([$class ?? 'prisma-emblem', 'pe', 'pe-solo' => ! $orbit, 'has-active' => $active]) viewBox="160 190 770 1050" role="img" aria-label="PRISMA{{ $active ? ' · '.$apps[$active][0] : '' }}">
    <defs>
        <linearGradient id="{{ $uid }}-b" x1="0" y1="0" x2=".4" y2="1"><stop offset="0" stop-color="#22c4ff"/><stop offset=".45" stop-color="#1477ec"/><stop offset="1" stop-color="#0a3fb5"/></linearGradient>
        <linearGradient id="{{ $uid }}-o" x1="1" y1="0" x2=".4" y2="1"><stop offset="0" stop-color="#ffb21a"/><stop offset=".5" stop-color="#ff8a0f"/><stop offset="1" stop-color="#f05a08"/></linearGradient>
        <linearGradient id="{{ $uid }}-g" x1="0" y1="0" x2="1" y2=".6"><stop offset="0" stop-color="#c6f33a"/><stop offset=".5" stop-color="#5cc21a"/><stop offset="1" stop-color="#1f8a2c"/></linearGradient>
        <linearGradient id="{{ $uid }}-ct" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#ff7d0d"/><stop offset="1" stop-color="#ffd21a"/></linearGradient>
        <linearGradient id="{{ $uid }}-cl" x1="1" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#26c2ff"/><stop offset="1" stop-color="#0b55c6"/></linearGradient>
        <linearGradient id="{{ $uid }}-cr" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#b9f02c"/><stop offset="1" stop-color="#2a9a0c"/></linearGradient>
        <linearGradient id="{{ $uid }}-gloss" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#fff" stop-opacity=".75"/><stop offset="1" stop-color="#fff" stop-opacity="0"/></linearGradient>
        <linearGradient id="{{ $uid }}-sheen" x1="0" y1="0" x2="1" y2="0">
            <stop offset="0" stop-color="#fff" stop-opacity="0"/><stop offset=".5" stop-color="#fff" stop-opacity=".6"/><stop offset="1" stop-color="#fff" stop-opacity="0"/>
        </linearGradient>
        <radialGradient id="{{ $uid }}-glow"><stop offset="0" stop-color="#fff" stop-opacity=".95"/><stop offset=".45" stop-color="#fff" stop-opacity=".35"/><stop offset="1" stop-color="#fff" stop-opacity="0"/></radialGradient>
        <radialGradient id="{{ $uid }}-floor" cx=".5" cy=".5" r=".5"><stop offset="0" stop-color="#2e86ff" stop-opacity=".45"/><stop offset=".55" stop-color="#3cc06a" stop-opacity=".22"/><stop offset="1" stop-color="#3cc06a" stop-opacity="0"/></radialGradient>
        @foreach ($bands as $key => $points)
            <clipPath id="{{ $uid }}-clip-{{ $key }}"><polygon points="{{ $points }}" stroke-width="40" stroke-linejoin="round" stroke="#000"/></clipPath>
        @endforeach
        <clipPath id="{{ $uid }}-clip-all">@foreach ($bands as $points)<polygon points="{{ $points }}"/>@endforeach<polygon points="543,535 658,613 658,765 543,845 426,765 426,613"/></clipPath>
        <filter id="{{ $uid }}-soft" x="-20%" y="-20%" width="140%" height="140%"><feDropShadow dx="0" dy="14" stdDeviation="16" flood-color="#0b2a55" flood-opacity=".22"/></filter>
        <filter id="{{ $uid }}-icon" x="-50%" y="-50%" width="200%" height="200%"><feDropShadow dx="0" dy="4" stdDeviation="5" flood-color="#082a5c" flood-opacity=".35"/></filter>
    </defs>

    {{-- A coloured glow on the floor, as if the mark floats. --}}
    <ellipse class="pe-floor" cx="560" cy="1196" rx="300" ry="34" fill="url(#{{ $uid }}-floor)"/>

    @if ($markImage ?? null)
        <image class="pe-mark" href="{{ $markImage }}" x="196" y="222" width="692" height="943" preserveAspectRatio="xMidYMid meet"/>
    @else
        <g class="pe-mark" filter="url(#{{ $uid }}-soft)">
            {{-- The three ribbons of the sketch, their corners rounded. --}}
            @foreach ($bands as $key => $points)
                <polygon points="{{ $points }}" fill="url(#{{ $uid }}-{{ $key }})" stroke="url(#{{ $uid }}-{{ $key }})" stroke-width="26" stroke-linejoin="round"/>
            @endforeach

            {{-- Blue: the inner edge in shade, a fold where the top turns down the side, gloss along the top. --}}
            <g clip-path="url(#{{ $uid }}-clip-b)">
                <path d="M300 560 L543 400 L543 450 L360 590 L360 1060 L300 1020 Z" fill="#06267a" opacity=".35"/>
                <path d="M180 560 C 240 470, 290 520, 380 560 C 300 640, 250 700, 180 720 Z" fill="#0a2f9c" opacity=".35"/>
                <path d="M180 700 C 250 650, 300 640, 360 600 L360 640 C 300 700, 240 740, 180 760 Z" fill="#fff" opacity=".18"/>
                <path d="M190 500 C 300 420, 420 330, 548 236 L 548 290 C 430 380, 320 460, 200 540 Z" fill="url(#{{ $uid }}-gloss)" opacity=".7"/>
                <path d="M200 880 C 250 840, 300 860, 360 820 L360 1060 L 200 960 Z" fill="#05207a" opacity=".3"/>
            </g>
            {{-- Orange: shade inside, a soft wave across the top, a fold down the right side. --}}
            <g clip-path="url(#{{ $uid }}-clip-o)">
                <path d="M543 400 L760 560 L720 590 L720 860 L780 900 L760 560 Z" fill="#a83300" opacity=".3"/>
                <path d="M548 240 C 600 330, 640 420, 720 470 C 790 510, 840 520, 890 520 L 890 470 C 800 460, 720 420, 660 330 C 620 280, 590 250, 560 230 Z" fill="url(#{{ $uid }}-gloss)" opacity=".75"/>
                <path d="M720 600 C 790 640, 840 620, 890 560 L 890 760 C 840 800, 790 820, 720 800 Z" fill="#fff" opacity=".14"/>
                <path d="M720 820 C 780 840, 840 860, 890 920 L 890 960 L 720 880 Z" fill="#b13a00" opacity=".25"/>
            </g>
            {{-- Green: a bright lip along the top, deeper green beneath. --}}
            <g clip-path="url(#{{ $uid }}-clip-g)">
                <path d="M410 860 C 500 940, 600 960, 740 880 C 800 860, 850 880, 890 920 L 890 960 C 820 930, 760 940, 690 980 C 590 1040, 480 1010, 410 950 Z" fill="url(#{{ $uid }}-gloss)" opacity=".8"/>
                <path d="M410 1000 C 500 1080, 640 1080, 890 940 L 890 1180 L 410 1180 Z" fill="#0d5e1a" opacity=".22"/>
            </g>

            {{-- The cube, its edges rounded and lit from the top. --}}
            <g class="pe-cube">
                <polygon points="543,535 658,613 543,688 426,613" fill="url(#{{ $uid }}-ct)" stroke="url(#{{ $uid }}-ct)" stroke-width="16" stroke-linejoin="round"/>
                <polygon points="426,613 543,688 543,845 426,765" fill="url(#{{ $uid }}-cl)" stroke="url(#{{ $uid }}-cl)" stroke-width="16" stroke-linejoin="round"/>
                <polygon points="543,688 658,613 658,765 543,845" fill="url(#{{ $uid }}-cr)" stroke="url(#{{ $uid }}-cr)" stroke-width="16" stroke-linejoin="round"/>
            </g>
        </g>
        <g clip-path="url(#{{ $uid }}-clip-all)" aria-hidden="true">
            <rect class="pe-sheen" x="-260" y="200" width="220" height="1000" fill="url(#{{ $uid }}-sheen)" transform="skewX(-18)"/>
        </g>
    @endif

    @if ($orbit)
        {{-- The five apps, white icons riding the middle of five sides. --}}
        @foreach ($apps as $key => [$name, $role, $x, $y, $color, $icon])
            @php($on = $key === $active)
            <g @class(['pe-app', 'is-active' => $on]) style="--pe-tone: {{ $color }}">
                <title>{{ $name }} · {{ $role }}{{ $on ? ' (sedang dibuka)' : '' }}</title>
                @if ($on)
                    <circle class="pe-halo" cx="{{ $x }}" cy="{{ $y }}" r="80" fill="url(#{{ $uid }}-glow)"/>
                    <circle class="pe-pulse" cx="{{ $x }}" cy="{{ $y }}" r="52"/>
                    <circle class="pe-pulse pe-pulse-2" cx="{{ $x }}" cy="{{ $y }}" r="52"/>
                @endif
                <g class="pe-glyph" filter="url(#{{ $uid }}-icon)">
                    <svg x="{{ $x - 34 }}" y="{{ $y - 34 }}" width="68" height="68" viewBox="0 0 24 24" class="pe-icon"><path d="{{ $icon }}"/></svg>
                </g>
            </g>
        @endforeach
    @endif
</svg>
@once
    <style>
        .pe{overflow:visible}
        .pe-sheen{animation:pe-sheen 7s ease-in-out infinite}
        .pe-cube{transform-box:fill-box;transform-origin:center;animation:pe-float 5s ease-in-out infinite}
        .pe-floor{transform-box:fill-box;transform-origin:center;animation:pe-floor 5s ease-in-out infinite}
        .pe-icon path{fill:#fff}
        .pe-app:not(.is-active) .pe-glyph{opacity:.82;transition:opacity .25s}
        .pe-app:hover .pe-glyph{opacity:1}
        /* The app this page belongs to: its icon brighter and larger, with light breathing around it. */
        .pe-app.is-active .pe-glyph{transform-box:fill-box;transform-origin:center;animation:pe-bob 3s ease-in-out infinite}
        .pe-halo{transform-box:fill-box;transform-origin:center;animation:pe-halo 3s ease-in-out infinite}
        .pe-pulse{fill:none;stroke:#fff;stroke-width:5;transform-box:fill-box;transform-origin:center;animation:pe-pulse 2.6s ease-out infinite}
        .pe-pulse-2{animation-delay:1.3s}
        @keyframes pe-sheen{0%,55%{transform:skewX(-18deg) translateX(0)}85%,100%{transform:skewX(-18deg) translateX(1500px)}}
        @keyframes pe-float{0%,100%{transform:translateY(0)}50%{transform:translateY(-14px)}}
        @keyframes pe-floor{0%,100%{transform:scaleX(1);opacity:1}50%{transform:scaleX(.9);opacity:.75}}
        @keyframes pe-bob{0%,100%{transform:scale(1.12)}50%{transform:scale(1.24)}}
        @keyframes pe-halo{0%,100%{opacity:.55;transform:scale(.9)}50%{opacity:1;transform:scale(1.12)}}
        @keyframes pe-pulse{from{transform:scale(1);opacity:.85}to{transform:scale(1.7);opacity:0}}
        @media(prefers-reduced-motion:reduce){.pe *{animation:none!important}}
    </style>
@endonce
