{{-- The PRISMA emblem: the hexagonal "P" with the cube inside, and the five PRISMA apps set into five of its sides.
     Each app is a small plate engraved in the face it sits on, lying in that face's plane, so it reads as part of the mark.
     $active: the app this page belongs to (builder, sm, collect, datalab, dashboard); its plate lights up and breathes.
     $class: class of the <svg>; $orbit: false for the mark alone; $markImage: an uploaded logo in place of the drawing. --}}
@php
    $uid = 'pe'.substr(md5(uniqid('', true)), 0, 6);
    $active ??= null;
    $orbit ??= true;
    // [name, role, matrix of the face (along, across, centre), colour, icon], clockwise from the left as the data goes.
    $apps = [
        'builder' => ['PRISMA Builder', 'Menyusun kuesioner', '0.85 0.53 0 1 262 745', '#1e7be0', 'M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z'],
        'collect' => ['PRISMA Collect', 'Pendataan lapangan', '0.79 -0.61 0.85 0.53 402 412', '#2f9e44', 'M12 22s7-6.2 7-12a7 7 0 0 0-14 0c0 5.8 7 12 7 12Zm0-9a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z'],
        'sm' => ['PRISMA SM', 'Manajemen survei', '0.79 0.61 -0.85 0.53 684 412', '#0f9d8f', 'M16 19v-1a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v1M9 10a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm13 9v-1a4 4 0 0 0-3-3.9M16 3.1a3.5 3.5 0 0 1 0 6.8'],
        'datalab' => ['PRISMA DataLab', 'Pengolahan data', '0.85 -0.53 0 1 822 716', '#f07a12', 'M4 6c0-1.7 3.6-3 8-3s8 1.3 8 3-3.6 3-8 3-8-1.3-8-3Zm0 0v12c0 1.7 3.6 3 8 3s8-1.3 8-3V6M4 12c0 1.7 3.6 3 8 3s8-1.3 8-3'],
        'dashboard' => ['PRISMA Dashboard', 'Monitoring', '0.83 -0.56 0 1 690 1012', '#7a52cc', 'M3 3v18h18M7 15l4-4 3 3 6-6'],
    ];
    $faces = [
        ['216,493 543,242 543,368 308,550', 'b1'], ['216,493 308,550 308,993 216,935', 'b2'],
        ['308,550 543,368 543,428 350,575', '#0a4aa8'], ['308,550 350,575 350,1028 308,993', '#063a86'],
        ['543,242 868,490 776,548 543,368', 'o1'], ['776,548 868,490 868,932 776,885', 'o2'],
        ['543,368 776,548 733,575 543,428', '#c4470a'], ['733,575 776,548 776,885 733,855', '#e0560a'],
        ['425,860 573,962 733,855 800,892 567,1048 425,946', 'g1'], ['425,946 567,1048 567,1145 425,1048', 'g2'],
        ['567,1048 800,892 868,932 567,1145', 'g3'],
    ];
@endphp
<svg @class([$class ?? 'prisma-emblem', 'pe', 'pe-solo' => ! $orbit, 'has-active' => $active]) viewBox="170 200 750 990" role="img" aria-label="PRISMA{{ $active ? ' · '.$apps[$active][0] : '' }}">
    <defs>
        <linearGradient id="{{ $uid }}-b1" x1="0" y1="1" x2="1" y2="0"><stop offset="0" stop-color="#0a5fc4"/><stop offset="1" stop-color="#18b0ff"/></linearGradient>
        <linearGradient id="{{ $uid }}-b2" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#14a6f8"/><stop offset="1" stop-color="#0a49a6"/></linearGradient>
        <linearGradient id="{{ $uid }}-o1" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#ff8a0a"/><stop offset="1" stop-color="#ffb411"/></linearGradient>
        <linearGradient id="{{ $uid }}-o2" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#ffa41c"/><stop offset="1" stop-color="#ee5404"/></linearGradient>
        <linearGradient id="{{ $uid }}-g1" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#5cbc0a"/><stop offset="1" stop-color="#bdf22e"/></linearGradient>
        <linearGradient id="{{ $uid }}-g2" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#5ab90a"/><stop offset="1" stop-color="#2c8a05"/></linearGradient>
        <linearGradient id="{{ $uid }}-g3" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#3f9f08"/><stop offset="1" stop-color="#2a8205"/></linearGradient>
        <linearGradient id="{{ $uid }}-ct" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#ff7a0a"/><stop offset="1" stop-color="#ffcc12"/></linearGradient>
        <linearGradient id="{{ $uid }}-cl" x1="1" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#18b4ff"/><stop offset="1" stop-color="#0a4fb6"/></linearGradient>
        <linearGradient id="{{ $uid }}-cr" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#acec26"/><stop offset="1" stop-color="#2c8a05"/></linearGradient>
        {{-- Light passing over the mark now and then. --}}
        <linearGradient id="{{ $uid }}-sheen" x1="0" y1="0" x2="1" y2="0">
            <stop offset="0" stop-color="#fff" stop-opacity="0"/><stop offset=".5" stop-color="#fff" stop-opacity=".55"/><stop offset="1" stop-color="#fff" stop-opacity="0"/>
        </linearGradient>
        <radialGradient id="{{ $uid }}-glow"><stop offset="0" stop-color="#fff" stop-opacity=".9"/><stop offset="1" stop-color="#fff" stop-opacity="0"/></radialGradient>
        <clipPath id="{{ $uid }}-clip">
            @foreach ($faces as [$points])<polygon points="{{ $points }}"/>@endforeach
            <polygon points="543,535 658,613 658,765 543,845 426,765 426,613"/>
        </clipPath>
        <filter id="{{ $uid }}-shadow" x="-20%" y="-20%" width="140%" height="140%">
            <feDropShadow dx="0" dy="18" stdDeviation="18" flood-color="#0b2a55" flood-opacity=".28"/>
        </filter>
    </defs>

    @if ($markImage ?? null)
        <image class="pe-mark" href="{{ $markImage }}" x="196" y="222" width="692" height="943" preserveAspectRatio="xMidYMid meet"/>
    @else
        {{-- The mark, drawn from the sketch: blue on the left and top, orange on the right, green along the bottom, the cube inside. --}}
        <g class="pe-mark" filter="url(#{{ $uid }}-shadow)">
            @foreach ($faces as [$points, $fill])
                <polygon points="{{ $points }}" fill="{{ str_starts_with($fill, '#') ? $fill : 'url(#'.$uid.'-'.$fill.')' }}"/>
            @endforeach
            {{-- A thin bright edge along the top of each band, as if lit from above. --}}
            <polyline class="pe-edge" points="216,493 543,242 868,490"/>
            <polyline class="pe-edge" points="425,860 573,962 733,855"/>
            <g class="pe-cube">
                <polygon points="543,535 658,613 543,688 426,613" fill="url(#{{ $uid }}-ct)"/>
                <polygon points="426,613 543,688 543,845 426,765" fill="url(#{{ $uid }}-cl)"/>
                <polygon points="543,688 658,613 658,765 543,845" fill="url(#{{ $uid }}-cr)"/>
                <polyline class="pe-edge" points="426,613 543,688 658,613"/>
                <line class="pe-edge" x1="543" y1="688" x2="543" y2="845"/>
            </g>
        </g>
        <g clip-path="url(#{{ $uid }}-clip)" class="pe-sheen-wrap" aria-hidden="true">
            <rect class="pe-sheen" x="-260" y="200" width="220" height="1000" fill="url(#{{ $uid }}-sheen)" transform="skewX(-18)"/>
        </g>
    @endif

    @if ($orbit)
        {{-- The five apps, set into the middle of five sides; each plate lies in the plane of its face. --}}
        @foreach ($apps as $key => [$name, $role, $matrix, $color, $icon])
            @php($on = $key === $active)
            <g @class(['pe-app', 'is-active' => $on]) style="--pe-tone: {{ $color }}" transform="matrix({{ str_replace(' ', ',', $matrix) }})">
                <title>{{ $name }} · {{ $role }}{{ $on ? ' (sedang dibuka)' : '' }}</title>
                @if ($on)
                    <circle class="pe-halo" r="78" fill="url(#{{ $uid }}-glow)"/>
                    <rect class="pe-pulse" x="-38" y="-38" width="76" height="76" rx="18"/>
                    <rect class="pe-pulse pe-pulse-2" x="-38" y="-38" width="76" height="76" rx="18"/>
                @endif
                <g class="pe-plate">
                    <rect class="pe-plate-bg" x="-38" y="-38" width="76" height="76" rx="18"/>
                    <svg x="-23" y="-23" width="46" height="46" viewBox="0 0 24 24" class="pe-icon"><path d="{{ $icon }}"/></svg>
                </g>
            </g>
        @endforeach
    @endif
</svg>
@once
    <style>
        .pe{overflow:visible}
        .pe-edge{fill:none;stroke:#fff;stroke-opacity:.35;stroke-width:3;stroke-linejoin:round;stroke-linecap:round}
        .pe-sheen{animation:pe-sheen 7s ease-in-out infinite}
        .pe-cube{transform-box:fill-box;transform-origin:center;animation:pe-float 5s ease-in-out infinite}
        /* The other apps: engraved into the face, a little lighter than it, with a white outline. */
        .pe-plate-bg{fill:rgb(255 255 255 / .16);stroke:rgb(255 255 255 / .55);stroke-width:3}
        .pe-icon path{fill:none;stroke:#fff;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;opacity:.92}
        .pe-app{transition:opacity .3s}
        .pe-app:hover .pe-plate-bg{fill:rgb(255 255 255 / .32)}
        /* The app this page belongs to: a white plate lit from inside, its own colour on the icon, breathing light around it. */
        .pe-app.is-active .pe-plate-bg{fill:#fff;stroke:#fff}
        .pe-app.is-active .pe-icon path{stroke:var(--pe-tone);stroke-width:2.4;opacity:1}
        .pe-app.is-active .pe-plate{transform-box:fill-box;transform-origin:center;animation:pe-bob 3s ease-in-out infinite}
        .pe-halo{opacity:.55;transform-box:fill-box;transform-origin:center;animation:pe-halo 3s ease-in-out infinite}
        .pe-pulse{fill:none;stroke:#fff;stroke-width:4;transform-box:fill-box;transform-origin:center;animation:pe-pulse 2.6s ease-out infinite}
        .pe-pulse-2{animation-delay:1.3s}
        @keyframes pe-sheen{0%,55%{transform:skewX(-18deg) translateX(0)}85%,100%{transform:skewX(-18deg) translateX(1500px)}}
        @keyframes pe-float{0%,100%{transform:translateY(0)}50%{transform:translateY(-14px)}}
        @keyframes pe-bob{0%,100%{transform:scale(1)}50%{transform:scale(1.07)}}
        @keyframes pe-halo{0%,100%{opacity:.35;transform:scale(.9)}50%{opacity:.75;transform:scale(1.1)}}
        @keyframes pe-pulse{from{transform:scale(1);opacity:.8}to{transform:scale(1.65);opacity:0}}
        @media(prefers-reduced-motion:reduce){.pe *{animation:none!important}}
    </style>
@endonce
