{{-- The PRISMA name next to the BPS mark; the full tagline where there is room. --}}
@if ($navbarLogo = \App\Support\AppSettings::logo('navbar'))<img class="brand-logo" src="{{ $navbarLogo }}" alt="Logo">@else<span class="brand-mark">b<span>p</span>s</span>@endif
<span class="brand-name">PRISMA<span>{{ ($full ?? false) ? 'Platform Riset, Survei, dan Manajemen Data' : 'RISET · SURVEI · DATA' }}</span></span>
