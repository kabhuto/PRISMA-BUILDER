{{-- The PRISMA emblem: the hexagonal "P" of glossy ribbons with a cube inside (the sketch), drawn on a regular hexagon so every
     ribbon is equally wide and equally far from the cube. The five PRISMA apps ride the middle of five sides as white icons.
     $active: the app this page belongs to (builder, sm, collect, datalab, dashboard); its icon glows and breathes.
     $class: class of the <svg>; $orbit: false for the mark alone; $markImage: an uploaded logo in place of the drawing. --}}
@php
    $uid = 'pe'.substr(md5(uniqid('', true)), 0, 6);
    $active ??= null;
    $orbit ??= true;
    // Centre (543, 693); outer hexagon radius 440, inner 250: each ribbon 165 wide, each icon 299 from the centre.
    // [name, role, x, y, colour, icon (bold, filled, 24 grid)], clockwise from the left as the data goes.
    $apps = [
        'builder' => ['PRISMA Builder', 'Menyusun kuesioner', 244, 693, '#1e7be0', 'M9 1.5h6A1.5 1.5 0 0 1 16.5 3v.5H18A2.5 2.5 0 0 1 20.5 6v14.5A2.5 2.5 0 0 1 18 23H6a2.5 2.5 0 0 1-2.5-2.5V6A2.5 2.5 0 0 1 6 3.5h1.5V3A1.5 1.5 0 0 1 9 1.5Zm.5 2.5v1.5h5V4h-5Zm6.8 7.2-1.6-1.4-4.2 4.6-2-1.9-1.5 1.5 3.6 3.4 5.7-6.2Z'],
        'collect' => ['PRISMA Collect', 'Pendataan lapangan', 394, 434, '#2f9e44', 'M12 1a8 8 0 0 1 8 8c0 6-8 14-8 14S4 15 4 9a8 8 0 0 1 8-8Zm0 4.6a3.4 3.4 0 1 0 0 6.8 3.4 3.4 0 0 0 0-6.8Z'],
        'sm' => ['PRISMA SM', 'Manajemen survei', 692, 434, '#0f9d8f', 'M8.5 11.5a4.25 4.25 0 1 1 0-8.5 4.25 4.25 0 0 1 0 8.5Zm8.5.5a3.5 3.5 0 1 1 0-7 3.5 3.5 0 0 1 0 7ZM1 21.5c0-4 3.3-7 7.5-7s7.5 3 7.5 7v.5H1v-.5Zm16.4.5v-.5c0-2.3-.8-4.3-2.2-5.8.6-.1 1.2-.2 1.8-.2 3.6 0 6 2.3 6 5.6v.9h-5.6Z'],
        'datalab' => ['PRISMA DataLab', 'Pengolahan data', 842, 693, '#f07a12', 'M12 1.5c5 0 9 1.6 9 3.8S17 9 12 9 3 7.5 3 5.3s4-3.8 9-3.8ZM3 8.4c1.8 1.4 5.2 2.3 9 2.3s7.2-.9 9-2.3v4.3c0 2.1-4 3.8-9 3.8s-9-1.7-9-3.8V8.4Zm0 7.4c1.8 1.4 5.2 2.3 9 2.3s7.2-.9 9-2.3v2.9c0 2.1-4 3.8-9 3.8s-9-1.7-9-3.8v-2.9Z'],
        'dashboard' => ['PRISMA Dashboard', 'Monitoring', 692, 952, '#7a52cc', 'M2 21h4v-7H2v7Zm6 0h4V10H8v11Zm6 0h4v-5h-4v5Zm6 0h3V7h-3v14ZM2.6 9.6 8.8 4l4.1 3.4L19.3 1.6l1.8 2-8 7.2L8.9 7.4 4.4 11.4 2.6 9.6Z'],
    ];
    // The ribbons by side (outer edge first, then inner): blue up the left and across the top left, orange across the top
    // right and down the right, green along the bottom right and a little way up the bottom left, leaving the gap of the P.
    $sides = [
        ['162,473 543,253 543,443 326.5,568', 'bt'],
        ['162,473 326.5,568 326.5,818 162,913', 'bl'],
        ['543,253 924,473 759.5,568 543,443', 'ot'],
        ['924,473 924,913 759.5,818 759.5,568', 'or'],
        ['924,913 543,1133 543,943 759.5,818', 'gr'],
        ['543,1133 391,1045 473,902 543,943', 'gl'],
    ];
    $bands = [
        'b' => ['162,913 162,473 543,253 543,443 326.5,568 326.5,818', 'bl'],
        'o' => ['543,253 924,473 924,913 759.5,818 759.5,568 543,443', 'ot'],
        'g' => ['924,913 543,1133 391,1045 473,902 543,943 759.5,818', 'gr'],
    ];
@endphp
<svg @class([$class ?? 'prisma-emblem', 'pe', 'pe-solo' => ! $orbit, 'has-active' => $active]) viewBox="130 220 826 1000" role="img" aria-label="PRISMA{{ $active ? ' · '.$apps[$active][0] : '' }}">
    <defs>
        <linearGradient id="{{ $uid }}-bt" x1="0" y1="1" x2="1" y2="0"><stop offset="0" stop-color="#1263de"/><stop offset="1" stop-color="#2ccbff"/></linearGradient>
        <linearGradient id="{{ $uid }}-bl" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#1a8cf2"/><stop offset="1" stop-color="#0a3aa8"/></linearGradient>
        <linearGradient id="{{ $uid }}-ot" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#ff8a10"/><stop offset="1" stop-color="#ffbb1e"/></linearGradient>
        <linearGradient id="{{ $uid }}-or" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#ffa21c"/><stop offset="1" stop-color="#ee5506"/></linearGradient>
        <linearGradient id="{{ $uid }}-gr" x1="1" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#3fa418"/><stop offset="1" stop-color="#1d7d22"/></linearGradient>
        <linearGradient id="{{ $uid }}-gl" x1="1" y1="0" x2="0" y2="0"><stop offset="0" stop-color="#7fd327"/><stop offset="1" stop-color="#c8f43e"/></linearGradient>
        <linearGradient id="{{ $uid }}-ct" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#ff7d0d"/><stop offset="1" stop-color="#ffd21a"/></linearGradient>
        <linearGradient id="{{ $uid }}-cl" x1="1" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#26c2ff"/><stop offset="1" stop-color="#0b55c6"/></linearGradient>
        <linearGradient id="{{ $uid }}-cr" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#b9f02c"/><stop offset="1" stop-color="#2a9a0c"/></linearGradient>
        <linearGradient id="{{ $uid }}-gloss" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#fff" stop-opacity=".55"/><stop offset="1" stop-color="#fff" stop-opacity="0"/></linearGradient>
        <linearGradient id="{{ $uid }}-sheen" x1="0" y1="0" x2="1" y2="0">
            <stop offset="0" stop-color="#fff" stop-opacity="0"/><stop offset=".5" stop-color="#fff" stop-opacity=".55"/><stop offset="1" stop-color="#fff" stop-opacity="0"/>
        </linearGradient>
        <radialGradient id="{{ $uid }}-glow"><stop offset="0" stop-color="#fff" stop-opacity=".9"/><stop offset=".5" stop-color="#fff" stop-opacity=".3"/><stop offset="1" stop-color="#fff" stop-opacity="0"/></radialGradient>
        <radialGradient id="{{ $uid }}-floor" cx=".5" cy=".5" r=".5"><stop offset="0" stop-color="#2e86ff" stop-opacity=".42"/><stop offset=".55" stop-color="#3cc06a" stop-opacity=".2"/><stop offset="1" stop-color="#3cc06a" stop-opacity="0"/></radialGradient>
        <clipPath id="{{ $uid }}-clip-all">@foreach ($bands as [$points])<polygon points="{{ $points }}"/>@endforeach<polygon points="543,568 651,630 651,756 543,818 435,756 435,630"/></clipPath>
        <filter id="{{ $uid }}-soft" x="-20%" y="-20%" width="140%" height="140%"><feDropShadow dx="0" dy="14" stdDeviation="16" flood-color="#0b2a55" flood-opacity=".22"/></filter>
        <filter id="{{ $uid }}-icon" x="-50%" y="-50%" width="200%" height="200%"><feDropShadow dx="0" dy="3" stdDeviation="4" flood-color="#05204a" flood-opacity=".45"/></filter>
    </defs>

    {{-- A coloured glow on the floor, as if the mark floats. --}}
    <ellipse class="pe-floor" cx="543" cy="1190" rx="300" ry="32" fill="url(#{{ $uid }}-floor)"/>

    @if ($markImage ?? null)
        <image class="pe-mark" href="{{ $markImage }}" x="162" y="253" width="762" height="880" preserveAspectRatio="xMidYMid meet"/>
    @else
        <g class="pe-mark" filter="url(#{{ $uid }}-soft)">
            {{-- Each ribbon once with rounded corners, then each side in its own light so the ribbon seems to turn at the corners. --}}
            @foreach ($bands as [$points, $fill])
                <polygon points="{{ $points }}" fill="url(#{{ $uid }}-{{ $fill }})" stroke="url(#{{ $uid }}-{{ $fill }})" stroke-width="22" stroke-linejoin="round"/>
            @endforeach
            @foreach ($sides as [$points, $fill])
                <polygon points="{{ $points }}" fill="url(#{{ $uid }}-{{ $fill }})"/>
            @endforeach
            {{-- Gloss on the outer half of every side, a fine bright edge along the top, a little shade on the inner edge. --}}
            <polygon points="162,473 543,253 543,348 244,520" fill="url(#{{ $uid }}-gloss)"/>
            <polygon points="543,253 924,473 842,520 543,348" fill="url(#{{ $uid }}-gloss)"/>
            <polygon points="162,473 244,520 244,866 162,913" fill="#fff" opacity=".1"/>
            <polygon points="924,473 842,520 842,866 924,913" fill="#fff" opacity=".12"/>
            <polygon points="432,974 473,902 543,943 759.5,818 842,866 543,1038" fill="#fff" opacity=".16"/>
            <polyline class="pe-edge" points="162,473 543,253 924,473"/>
            <polyline points="326.5,568 543,443 759.5,568 759.5,818 543,943 473,902" fill="none" stroke="#08204d" stroke-opacity=".18" stroke-width="6" stroke-linejoin="round"/>
            <polyline points="326.5,568 326.5,818" fill="none" stroke="#08204d" stroke-opacity=".18" stroke-width="6"/>

            {{-- The cube, as wide from every ribbon as the ribbons are wide. --}}
            <g class="pe-cube">
                <polygon points="543,568 651,630 543,693 435,630" fill="url(#{{ $uid }}-ct)" stroke="url(#{{ $uid }}-ct)" stroke-width="14" stroke-linejoin="round"/>
                <polygon points="435,630 543,693 543,818 435,756" fill="url(#{{ $uid }}-cl)" stroke="url(#{{ $uid }}-cl)" stroke-width="14" stroke-linejoin="round"/>
                <polygon points="543,693 651,630 651,756 543,818" fill="url(#{{ $uid }}-cr)" stroke="url(#{{ $uid }}-cr)" stroke-width="14" stroke-linejoin="round"/>
                <polyline class="pe-edge" points="435,630 543,693 651,630"/>
                <line class="pe-edge" x1="543" y1="693" x2="543" y2="818"/>
            </g>
        </g>
        <g clip-path="url(#{{ $uid }}-clip-all)" aria-hidden="true">
            <rect class="pe-sheen" x="-260" y="200" width="220" height="1000" fill="url(#{{ $uid }}-sheen)" transform="skewX(-18)"/>
        </g>
    @endif

    @if ($orbit)
        {{-- The five apps, white icons on the middle of five sides, all the same distance from the centre. --}}
        @foreach ($apps as $key => [$name, $role, $x, $y, $color, $icon])
            @php($on = $key === $active)
            <g @class(['pe-app', 'is-active' => $on]) style="--pe-tone: {{ $color }}">
                <title>{{ $name }} · {{ $role }}{{ $on ? ' (sedang dibuka)' : '' }}</title>
                @if ($on)
                    <circle class="pe-halo" cx="{{ $x }}" cy="{{ $y }}" r="86" fill="url(#{{ $uid }}-glow)"/>
                    <circle class="pe-pulse" cx="{{ $x }}" cy="{{ $y }}" r="66"/>
                    <circle class="pe-pulse pe-pulse-2" cx="{{ $x }}" cy="{{ $y }}" r="66"/>
                @endif
                <g class="pe-glyph" filter="url(#{{ $uid }}-icon)">
                    <svg x="{{ $x - 48 }}" y="{{ $y - 48 }}" width="96" height="96" viewBox="0 0 24 24" class="pe-icon"><path d="{{ $icon }}" fill-rule="evenodd"/></svg>
                </g>
            </g>
        @endforeach
    @endif
</svg>
@once
    <style>
        .pe{overflow:visible}
        .pe-edge{fill:none;stroke:#fff;stroke-opacity:.45;stroke-width:4;stroke-linejoin:round;stroke-linecap:round}
        .pe-sheen{animation:pe-sheen 7s ease-in-out infinite}
        .pe-cube{transform-box:fill-box;transform-origin:center;animation:pe-float 5s ease-in-out infinite}
        .pe-floor{transform-box:fill-box;transform-origin:center;animation:pe-floor 5s ease-in-out infinite}
        .pe-icon path{fill:#fff}
        .pe-app:not(.is-active) .pe-glyph{opacity:.97}
        /* The app this page belongs to: its icon a little larger, with light breathing around it. */
        .pe-app.is-active .pe-glyph{transform-box:fill-box;transform-origin:center;animation:pe-bob 3s ease-in-out infinite}
        .pe-halo{transform-box:fill-box;transform-origin:center;animation:pe-halo 3s ease-in-out infinite}
        .pe-pulse{fill:none;stroke:#fff;stroke-width:5;transform-box:fill-box;transform-origin:center;animation:pe-pulse 2.6s ease-out infinite}
        .pe-pulse-2{animation-delay:1.3s}
        @keyframes pe-sheen{0%,55%{transform:skewX(-18deg) translateX(0)}85%,100%{transform:skewX(-18deg) translateX(1500px)}}
        @keyframes pe-float{0%,100%{transform:translateY(0)}50%{transform:translateY(-12px)}}
        @keyframes pe-floor{0%,100%{transform:scaleX(1);opacity:1}50%{transform:scaleX(.9);opacity:.75}}
        @keyframes pe-bob{0%,100%{transform:scale(1.05)}50%{transform:scale(1.15)}}
        @keyframes pe-halo{0%,100%{opacity:.55;transform:scale(.9)}50%{opacity:1;transform:scale(1.08)}}
        @keyframes pe-pulse{from{transform:scale(1);opacity:.85}to{transform:scale(1.3);opacity:0}}
        @media(prefers-reduced-motion:reduce){.pe *{animation:none!important}}
    </style>
@endonce
