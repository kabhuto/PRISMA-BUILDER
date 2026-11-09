{{-- Decoration for the dashboard: the Suramadu bridge over the strait, Bangkalan's landmark. --}}
<svg class="hero-bridge" viewBox="0 0 640 300" preserveAspectRatio="xMaxYMax meet" aria-hidden="true">
    <defs>
        <radialGradient id="hb-sun" cx="0.62" cy="0.62" r="0.5">
            <stop offset="0" stop-color="var(--hb-sun)" stop-opacity=".95"/>
            <stop offset="1" stop-color="var(--hb-sun)" stop-opacity="0"/>
        </radialGradient>
        <linearGradient id="hb-sea" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0" stop-color="var(--hb-sea-top)"/>
            <stop offset="1" stop-color="var(--hb-sea-bottom)"/>
        </linearGradient>
        <linearGradient id="hb-fade" x1="0" y1="0" x2="1" y2="0">
            <stop offset="0" stop-color="#fff" stop-opacity="0"/>
            <stop offset=".35" stop-color="#fff" stop-opacity="1"/>
        </linearGradient>
        <mask id="hb-mask"><rect width="640" height="300" fill="url(#hb-fade)"/></mask>
    </defs>
    <g mask="url(#hb-mask)">
        <circle cx="420" cy="190" r="170" fill="url(#hb-sun)"/>
        <path d="M120 222 C200 170 260 196 330 178 S470 150 560 176 L640 168 V240 H120 Z" fill="var(--hb-hill-far)"/>
        <path d="M60 236 C170 204 250 222 340 206 S520 196 640 214 V250 H60 Z" fill="var(--hb-hill-near)"/>
        <rect x="0" y="232" width="640" height="68" fill="url(#hb-sea)"/>
        <g stroke="var(--hb-glint)" stroke-width="2" stroke-linecap="round" opacity=".7">
            <path d="M300 258h40M380 268h26M470 256h52M540 276h30M250 280h22"/>
        </g>

        {{-- Deck: the approach rising from the left, then the main span between the towers. --}}
        <path d="M150 238 C260 222 330 206 400 200 L640 194" fill="none" stroke="var(--hb-deck)" stroke-width="7" stroke-linecap="round"/>
        <g stroke="var(--hb-deck)" stroke-width="3">
            <path d="M190 233v14M232 227v18M274 220v22M316 213v26M358 206v30"/>
        </g>

        {{-- Fan cables from both towers. --}}
        <g stroke="var(--hb-cable)" stroke-width="1.4" opacity=".9">
            <path d="M454 74 L352 205M454 74 L374 203M454 74 L396 201M454 74 L418 199M454 74 L440 198M454 82 L330 207"/>
            <path d="M454 74 L470 197M454 74 L494 197M454 74 L518 196M454 74 L542 196M454 74 L566 195M454 82 L590 195"/>
            <path d="M610 92 L560 195M610 92 L578 195M610 92 L596 194"/>
        </g>

        {{-- Towers: two legs joined by cross beams, like Suramadu's H-shaped pylons. --}}
        <g fill="var(--hb-tower)">
            <path d="M443 70h24l-2-8h-20Z"/>
            <path d="M444 70h7l3 170h-14Z"/>
            <path d="M458 70h7l4 170h-14Z"/>
            <rect x="443" y="100" width="24" height="5" rx="1"/>
            <rect x="442" y="150" width="26" height="5" rx="1"/>
            <rect x="441" y="214" width="28" height="6" rx="1"/>
            <path d="M599 90h20l-2-7h-16Z"/>
            <path d="M600 90h6l3 150h-12Z"/>
            <path d="M612 90h6l3 150h-12Z"/>
            <rect x="599" y="118" width="21" height="4" rx="1"/>
            <rect x="598" y="170" width="23" height="5" rx="1"/>
        </g>
    </g>
</svg>
