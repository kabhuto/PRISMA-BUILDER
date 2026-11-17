{{-- The PRISMA emblem, drawn faithfully to the sketch: a hexagonal "P" built as a 3D block. Blue runs up the left and across
     the top left, orange across the top right and down the right, and a green "V" block closes the bowl of the P at the bottom;
     the cube floats inside. Each ribbon has a light front face and a darker inner face, as in the sketch.
     The block is cut into five pieces, one per PRISMA app, each carrying that app's icon in a small hexagon.
     $active: the app this page belongs to (builder, sm, collect, datalab, dashboard): its piece breaks away from the P and
     opens out of the P and settles back until the P is whole again, its icon lit. $class: class of the <svg>; $orbit: false for the mark alone (no icons);
     $markImage: an uploaded logo in place of the drawing. --}}
@php
    $uid = 'pe'.substr(md5(uniqid('', true)), 0, 6);
    $active ??= null;
    $orbit ??= true;
    if ($markImage ?? null) {
        $active = null;
    }
    // Per app: name, role, icon centre, colour, icon (bold, filled, 24 grid), the way its piece drifts out (dx, dy),
    // the deep colour of its ribbon, its faces [points, gradient] and the light lines on its folds.
    $apps = [
        'builder' => ['PRISMA Builder', 'Menyusun kuesioner', 242, 735, '#1e7be0', 'M9 1.5h6A1.5 1.5 0 0 1 16.5 3v.5H18A2.5 2.5 0 0 1 20.5 6v14.5A2.5 2.5 0 0 1 18 23H6a2.5 2.5 0 0 1-2.5-2.5V6A2.5 2.5 0 0 1 6 3.5h1.5V3A1.5 1.5 0 0 1 9 1.5Zm.5 2.5v1.5h5V4h-5Zm6.8 7.2-1.6-1.4-4.2 4.6-2-1.9-1.5 1.5 3.6 3.4 5.7-6.2Z',
            [-72, 0], '#0a3d9e',
            [['163,459.4 271,521.7 271,966.7 163,904.4', 'bl'], ['271,521.7 320,550 320,995 271,966.7', 'bli']],
            ['271,521.7 271,966.7']],
        'collect' => ['PRISMA Collect', 'Pendataan lapangan', 392, 418, '#2f9e44', 'M12 1a8 8 0 0 1 8 8c0 6-8 14-8 14S4 15 4 9a8 8 0 0 1 8-8Zm0 4.6a3.4 3.4 0 1 0 0 6.8 3.4 3.4 0 0 0 0-6.8Z',
            [-36, -62], '#0a4fb8',
            [['163,459.4 543,240 543,364.7 271,521.7', 'bt'], ['271,521.7 543,364.7 543,421.3 320,550', 'bi']],
            ['163,459.4 271,521.7 543,364.7']],
        'sm' => ['PRISMA SM', 'Manajemen survei', 694, 418, '#0f9d8f', 'M8.5 11.5a4.25 4.25 0 1 1 0-8.5 4.25 4.25 0 0 1 0 8.5Zm8.5.5a3.5 3.5 0 1 1 0-7 3.5 3.5 0 0 1 0 7ZM1 21.5c0-4 3.3-7 7.5-7s7.5 3 7.5 7v.5H1v-.5Zm16.4.5v-.5c0-2.3-.8-4.3-2.2-5.8.6-.1 1.2-.2 1.8-.2 3.6 0 6 2.3 6 5.6v.9h-5.6Z',
            [36, -62], '#c2440a',
            [['543,240 923,459.4 815,521.7 543,364.7', 'ot'], ['543,364.7 815,521.7 766,550 543,421.3', 'oi']],
            ['543,364.7 815,521.7 923,459.4']],
        'datalab' => ['PRISMA DataLab', 'Pengolahan data', 845, 683, '#f07a12', 'M12 1.5c5 0 9 1.6 9 3.8S17 9 12 9 3 7.5 3 5.3s4-3.8 9-3.8ZM3 8.4c1.8 1.4 5.2 2.3 9 2.3s7.2-.9 9-2.3v4.3c0 2.1-4 3.8-9 3.8s-9-1.7-9-3.8V8.4Zm0 7.4c1.8 1.4 5.2 2.3 9 2.3s7.2-.9 9-2.3v2.9c0 2.1-4 3.8-9 3.8s-9-1.7-9-3.8v-2.9Z',
            [72, 0], '#b8400a',
            [['815,521.7 923,459.4 923,904.4 815,842', 'or'], ['766,550 815,521.7 815,842 766,813.7', 'ori']],
            ['815,521.7 815,842']],
        'dashboard' => ['PRISMA Dashboard', 'Monitoring', 568, 1004, '#7a52cc', 'M2 21h4v-7H2v7Zm6 0h4V10H8v11Zm6 0h4v-5h-4v5Zm6 0h3V7h-3v14ZM2.6 9.6 8.8 4l4.1 3.4L19.3 1.6l1.8 2-8 7.2L8.9 7.4 4.4 11.4 2.6 9.6Z',
            [36, 62], '#11651f',
            [['423,815.9 592.6,913.9 766,813.7 815,842 568,984.6 423,900.9', 'gt'], ['423,900.9 568,984.6 568,1109.4 423,1025.6', 'gf'], ['568,984.6 815,842 923,904.4 568,1109.4', 'gr']],
            ['423,900.9 568,984.6 815,842', '568,984.6 568,1109.4']],
    ];
    $outline = [
        '163,459.4 543,240 543,421.3 320,550 320,995 163,904.4',
        '543,240 923,459.4 923,904.4 815,842 766,813.7 766,550 543,421.3',
        '423,815.9 592.6,913.9 766,813.7 923,904.4 568,1109.4 423,1025.6',
        '543,492 663,561.3 663,699.8 543,769.1 423,699.8 423,561.3',
    ];
    $hex = '0,-54 46.8,-27 46.8,27 0,54 -46.8,27 -46.8,-27';
@endphp
<svg @class([$class ?? 'prisma-emblem', 'pe', 'pe-solo' => ! $orbit, 'has-active' => $active]) viewBox="100 195 886 1000" role="img" aria-label="PRISMA{{ $active ? ' · '.$apps[$active][0] : '' }}">
    <defs>
        <linearGradient id="{{ $uid }}-bt" x1="0" y1="1" x2="1" y2="0"><stop offset="0" stop-color="#0a6fd8"/><stop offset="1" stop-color="#14b4ff"/></linearGradient>
        <linearGradient id="{{ $uid }}-bi" x1="0" y1="1" x2="1" y2="0"><stop offset="0" stop-color="#0b4fc0"/><stop offset="1" stop-color="#083a92"/></linearGradient>
        <linearGradient id="{{ $uid }}-bl" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#0aaaff"/><stop offset="1" stop-color="#0a4fb2"/></linearGradient>
        <linearGradient id="{{ $uid }}-bli" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#0a5ad0"/><stop offset="1" stop-color="#062e78"/></linearGradient>
        <linearGradient id="{{ $uid }}-ot" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#ff8a00"/><stop offset="1" stop-color="#ffae12"/></linearGradient>
        <linearGradient id="{{ $uid }}-oi" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#b8400a"/><stop offset="1" stop-color="#e05a0a"/></linearGradient>
        <linearGradient id="{{ $uid }}-or" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#ffa21c"/><stop offset=".55" stop-color="#ff7a08"/><stop offset="1" stop-color="#ee5506"/></linearGradient>
        <linearGradient id="{{ $uid }}-ori" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#ff6a08"/><stop offset="1" stop-color="#c8460a"/></linearGradient>
        <linearGradient id="{{ $uid }}-gt" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#6cc214"/><stop offset=".6" stop-color="#a6e82a"/><stop offset="1" stop-color="#c2f43a"/></linearGradient>
        <linearGradient id="{{ $uid }}-gf" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#5cb812"/><stop offset="1" stop-color="#2f8f0a"/></linearGradient>
        <linearGradient id="{{ $uid }}-gr" x1="1" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#2f8f0a"/><stop offset="1" stop-color="#3fa012"/></linearGradient>
        <linearGradient id="{{ $uid }}-ct" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#ff7d0d"/><stop offset="1" stop-color="#ffc414"/></linearGradient>
        <linearGradient id="{{ $uid }}-cl" x1="1" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#14b4ff"/><stop offset="1" stop-color="#0a4fb8"/></linearGradient>
        <linearGradient id="{{ $uid }}-cr" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#a6e82a"/><stop offset="1" stop-color="#2a8f0a"/></linearGradient>
        <linearGradient id="{{ $uid }}-sheen" x1="0" y1="0" x2="1" y2="0">
            <stop offset="0" stop-color="#fff" stop-opacity="0"/><stop offset=".5" stop-color="#fff" stop-opacity=".5"/><stop offset="1" stop-color="#fff" stop-opacity="0"/>
        </linearGradient>
        <radialGradient id="{{ $uid }}-glow"><stop offset="0" stop-color="#fff" stop-opacity=".9"/><stop offset=".5" stop-color="#fff" stop-opacity=".3"/><stop offset="1" stop-color="#fff" stop-opacity="0"/></radialGradient>
        <radialGradient id="{{ $uid }}-floor" cx=".5" cy=".5" r=".5"><stop offset="0" stop-color="#2e86ff" stop-opacity=".42"/><stop offset=".55" stop-color="#3cc06a" stop-opacity=".2"/><stop offset="1" stop-color="#3cc06a" stop-opacity="0"/></radialGradient>
        <clipPath id="{{ $uid }}-clip-all">@foreach ($outline as $points)<polygon points="{{ $points }}"/>@endforeach</clipPath>
        <filter id="{{ $uid }}-soft" x="-20%" y="-20%" width="140%" height="140%"><feDropShadow dx="0" dy="16" stdDeviation="18" flood-color="#1556b8" flood-opacity=".24"/></filter>
        <filter id="{{ $uid }}-lift" x="-30%" y="-30%" width="160%" height="160%"><feDropShadow dx="0" dy="10" stdDeviation="12" flood-color="#0b2f6e" flood-opacity=".35"/></filter>
        <filter id="{{ $uid }}-icon" x="-50%" y="-50%" width="200%" height="200%"><feDropShadow dx="0" dy="3" stdDeviation="4" flood-color="#05204a" flood-opacity=".45"/></filter>
    </defs>

    {{-- A coloured glow on the floor, as if the mark floats. --}}
    <ellipse class="pe-floor" cx="543" cy="1152" rx="320" ry="30" fill="url(#{{ $uid }}-floor)"/>

    @if ($markImage ?? null)
        <image class="pe-mark" href="{{ $markImage }}" x="163" y="240" width="760" height="869" preserveAspectRatio="xMidYMid meet"/>
    @else
        <g class="pe-mark" filter="url(#{{ $uid }}-soft)">
            {{-- The five pieces of the P; the open app's piece is drawn last so it drifts over the rest. --}}
            @foreach (collect($apps)->sortBy(fn ($app, $key) => $key === $active) as $key => $app)
                @php([$name, $role, $x, $y, $color, $icon, [$dx, $dy], $deep, $faces, $edges] = $app)
                @php($on = $key === $active)
                <g @class(['pe-piece', 'is-active' => $on]) style="--pe-dx: {{ $dx }}px; --pe-dy: {{ $dy }}px; --pe-tone: {{ $color }}; --pe-ribbon: {{ $deep }}" @if ($on) filter="url(#{{ $uid }}-lift)" @endif>
                    @foreach ($faces as [$points, $fill])
                        <polygon points="{{ $points }}" fill="url(#{{ $uid }}-{{ $fill }})" stroke="url(#{{ $uid }}-{{ $fill }})" stroke-width="1.5" stroke-linejoin="round"/>
                    @endforeach
                    @foreach ($edges as $points)
                        <polyline class="pe-edge" points="{{ $points }}"/>
                    @endforeach
                    @if ($orbit)
                        <g class="pe-app" transform="translate({{ $x }} {{ $y }})">
                            <title>{{ $name }} · {{ $role }}{{ $on ? ' (sedang dibuka)' : '' }}</title>
                            @if ($on)
                                <circle class="pe-halo" r="82" fill="url(#{{ $uid }}-glow)"/>
                                <polygon class="pe-pulse" points="{{ $hex }}"/>
                                <polygon class="pe-pulse pe-pulse-2" points="{{ $hex }}"/>
                            @endif
                            <g class="pe-badge" filter="url(#{{ $uid }}-icon)">
                                <polygon class="pe-badge-bg" points="{{ $hex }}"/>
                                <polygon class="pe-badge-shine" points="0,-54 46.8,-27 0,-6 -46.8,-27" fill="#fff"/>
                                <svg x="-26" y="-26" width="52" height="52" viewBox="0 0 24 24" class="pe-icon"><path d="{{ $icon }}" fill-rule="evenodd"/></svg>
                            </g>
                        </g>
                    @endif
                </g>
            @endforeach

            {{-- The cube, as in the sketch: orange on top, blue on the left, green on the right. --}}
            <g class="pe-cube">
                <polygon points="543,492 663,561.3 543,630.6 423,561.3" fill="url(#{{ $uid }}-ct)" stroke="url(#{{ $uid }}-ct)" stroke-width="1.5" stroke-linejoin="round"/>
                <polygon points="423,561.3 543,630.6 543,769.1 423,699.8" fill="url(#{{ $uid }}-cl)" stroke="url(#{{ $uid }}-cl)" stroke-width="1.5" stroke-linejoin="round"/>
                <polygon points="543,630.6 663,561.3 663,699.8 543,769.1" fill="url(#{{ $uid }}-cr)" stroke="url(#{{ $uid }}-cr)" stroke-width="1.5" stroke-linejoin="round"/>
                <polyline class="pe-edge" points="423,561.3 543,630.6 663,561.3"/>
                <line class="pe-edge" x1="543" y1="630.6" x2="543" y2="769.1"/>
            </g>
        </g>
        @unless ($active)
            <g clip-path="url(#{{ $uid }}-clip-all)" aria-hidden="true">
                <rect class="pe-sheen" x="-260" y="200" width="220" height="1000" fill="url(#{{ $uid }}-sheen)" transform="skewX(-18)"/>
            </g>
        @endunless
    @endif
</svg>
@once
    <style>
        .pe{overflow:visible}
        .pe-edge{fill:none;stroke:#fff;stroke-opacity:.4;stroke-width:3;stroke-linejoin:round;stroke-linecap:round}
        .pe-sheen{animation:pe-sheen 7s ease-in-out infinite}
        .pe-cube{transform-box:fill-box;transform-origin:center;animation:pe-float 5s ease-in-out infinite}
        .pe-floor{transform-box:fill-box;transform-origin:center;animation:pe-floor 5s ease-in-out infinite}
        .pe-icon path{fill:#fff}
        /* Icon hexagons: the deep colour of their ribbon, a crisp white rim, a shine on the upper half. */
        .pe-badge-bg{fill:var(--pe-ribbon);fill-opacity:.7;stroke:rgb(255 255 255 / .92);stroke-width:4;stroke-linejoin:round}
        .pe-badge-shine{opacity:.14}
        .pe-app:hover .pe-badge-bg{stroke:#fff}
        /* The open app: its piece breaks away from the P and opens out of the P and settles back until the P is whole again; its hexagon solid white, the icon in the
           app's colour, light breathing around it. */
        .pe-piece.is-active{animation:pe-drift 3s cubic-bezier(.45,0,.55,1) infinite;will-change:transform}
        .pe-piece.is-active .pe-badge-bg{fill:#fff;fill-opacity:1;stroke:#fff}
        .pe-piece.is-active .pe-badge-shine{opacity:0}
        .pe-piece.is-active .pe-icon path{fill:var(--pe-tone)}
        .pe-piece.is-active .pe-badge{transform-box:fill-box;transform-origin:center;animation:pe-bob 3s cubic-bezier(.45,0,.55,1) infinite}
        .pe-halo{transform-box:fill-box;transform-origin:center;animation:pe-halo 3s cubic-bezier(.45,0,.55,1) infinite}
        .pe-pulse{fill:none;stroke:#fff;stroke-width:4;transform-box:fill-box;transform-origin:center;animation:pe-pulse 2.6s ease-out infinite}
        .pe-pulse-2{animation-delay:1.3s}
        @keyframes pe-drift{0%,18%,88%,100%{transform:translate(0,0)}50%,56%{transform:translate(var(--pe-dx),var(--pe-dy))}}
        @keyframes pe-sheen{0%,55%{transform:skewX(-18deg) translateX(0)}85%,100%{transform:skewX(-18deg) translateX(1500px)}}
        @keyframes pe-float{0%,100%{transform:translateY(0)}50%{transform:translateY(-12px)}}
        @keyframes pe-floor{0%,100%{transform:scaleX(1);opacity:1}50%{transform:scaleX(.9);opacity:.75}}
        @keyframes pe-bob{0%,100%{transform:scale(1.04)}50%{transform:scale(1.09)}}
        @keyframes pe-halo{0%,100%{opacity:.55;transform:scale(.9)}50%{opacity:1;transform:scale(1.08)}}
        @keyframes pe-pulse{from{transform:scale(1);opacity:.85}to{transform:scale(1.3);opacity:0}}
        /* Dark pages: no dark shadow under the mark but a soft light around it and a stronger glow below. */
        :root[data-theme="dark"] .pe-mark{filter:drop-shadow(0 0 26px rgb(40 140 255 / .28))}
        :root[data-theme="dark"] .pe-floor{opacity:.9}
        @media(prefers-reduced-motion:reduce){.pe *{animation:none!important}}
    </style>
@endonce
