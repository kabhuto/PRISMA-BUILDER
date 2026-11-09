{{-- Decoration for the dashboard: the Suramadu bridge over the strait, Bangkalan's landmark. --}}
<svg class="hero-bridge" viewBox="0 0 700 300" preserveAspectRatio="xMaxYMax meet" aria-hidden="true">
    <defs>
        <radialGradient id="hb-sun" cx="0.5" cy="0.5" r="0.5">
            <stop offset="0" stop-color="var(--hb-sun)" stop-opacity=".9"/>
            <stop offset="1" stop-color="var(--hb-sun)" stop-opacity="0"/>
        </radialGradient>
        <linearGradient id="hb-sea" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0" stop-color="var(--hb-sea-top)"/>
            <stop offset="1" stop-color="var(--hb-sea-bottom)" stop-opacity="0"/>
        </linearGradient>
        <linearGradient id="hb-hill" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0" stop-color="var(--hb-hill-near)"/>
            <stop offset="1" stop-color="var(--hb-hill-near)" stop-opacity=".2"/>
        </linearGradient>
        <linearGradient id="hb-fade" x1="0" y1="0" x2="1" y2="0">
            <stop offset="0" stop-color="#fff" stop-opacity="0"/>
            <stop offset=".22" stop-color="#fff" stop-opacity="1"/>
        </linearGradient>
        <mask id="hb-mask"><rect width="700" height="300" fill="url(#hb-fade)"/></mask>
    </defs>
    <g mask="url(#hb-mask)">
        <circle cx="560" cy="170" r="150" fill="url(#hb-sun)"/>
        <path d="M0 196C80 150 150 138 230 160 300 112 390 96 470 132 540 104 620 112 700 140V240H0Z" fill="var(--hb-hill-far)" opacity=".75"/>
        <path d="M0 214C110 178 190 190 290 202 370 178 450 182 540 198L700 186V246H0Z" fill="url(#hb-hill)"/>
        <rect y="226" width="700" height="74" fill="url(#hb-sea)"/>
        <path d="M560 228C600 196 650 182 700 184V232H560Z" fill="var(--hb-island)"/>
        <path d="M610 230C640 210 670 204 700 206V234H610Z" fill="var(--hb-island-dark)"/>
        <g stroke="var(--hb-glint)" stroke-width="2" stroke-linecap="round" opacity=".75">
            <path d="M330 252h46M420 262h30M500 250h58M590 270h34M250 272h24M150 262h30"/>
        </g>

        <g stroke="var(--hb-tower)" stroke-width="3" stroke-linecap="round">
            <path d="M40 240.2V247M62 239.4V248M84 238.6V246M106 237.7V247M128 236.9V248M150 236.0V246M172 235.2V247M194 234.3V248M216 233.5V246M238 232.7V247M260 231.8V248M282 231.0V246"/>
        </g>
        <path d="M20 238L700 212" stroke="var(--hb-deck)" stroke-width="6" stroke-linecap="round"/>
        <path d="M20 243L700 217" stroke="var(--hb-tower)" stroke-width="2" opacity=".55"/>

        <g fill="none" stroke="var(--hb-cable)" stroke-width="1.1" opacity=".9">
            <path d="M227.0 225.7V230.1M232.0 218.3V229.9M236.9 210.8V229.7M241.9 203.2V229.5M246.8 195.5V229.3M251.7 187.7V229.1M256.6 179.8V229.0M261.5 171.8V228.8M266.4 163.6V228.6M271.2 155.4V228.4M276.1 147.1V228.2M280.9 138.7V228.0M285.7 130.2V227.8M290.5 121.5V227.7M312.5 112.9V226.8M325.0 120.1V226.3M337.5 125.7V225.9M350.0 129.5V225.4M362.5 131.7V224.9M375.0 132.1V224.4M387.5 130.9V223.9M400.0 128.0V223.5M412.5 123.4V223.0M425.0 117.1V222.5M437.5 109.2V222.0M450.0 99.5V221.6M462.5 88.2V221.1M475.0 75.1V220.6M487.5 60.4V220.1M512.5 58.2V219.2M524.8 72.0V218.7M537.1 85.2V218.2M549.4 97.9V217.8M561.5 110.1V217.3M573.6 121.7V216.8M585.6 132.9V216.4M597.5 143.5V215.9M609.3 153.6V215.5M621.1 163.2V215.0M632.8 172.3V214.6M644.4 180.9V214.1M655.9 188.9V213.7M667.3 196.5V213.2M678.7 203.5V212.8"/>
        </g>
        <path d="M222 233Q262 175 300 104M300 104Q400 182 500 44M500 44Q600 160 690 210" fill="none" stroke="var(--hb-deck)" stroke-width="2.4"/>

        <g fill="var(--hb-tower)">
            <path d="M486 46h9l2 190h-13Z"/><path d="M506 46h9l2 190h-13Z"/>
            <rect x="484" y="40" width="33" height="7" rx="2"/>
            <rect x="486" y="92" width="29" height="5" rx="1"/>
            <rect x="485" y="150" width="31" height="5" rx="1"/>
            <rect x="484" y="226" width="34" height="12" rx="2"/>
            <path d="M291 106h6l1.5 132h-9Z"/><path d="M304 106h6l1.5 132h-9Z"/>
            <rect x="289" y="101" width="23" height="6" rx="2"/>
            <rect x="290" y="150" width="21" height="4" rx="1"/>
            <rect x="289" y="232" width="24" height="9" rx="2"/>
        </g>
    </g>
</svg>
