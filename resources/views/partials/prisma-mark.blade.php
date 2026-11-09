{{-- The PRISMA mark: a faceted "P", its colours the stages data goes through. --}}
@php($id = 'pm'.($idSuffix ?? ''))
<svg class="{{ $class ?? 'prisma-mark' }}" viewBox="0 0 64 74" aria-hidden="true">
    <defs>
        <linearGradient id="{{ $id }}-stem" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0" stop-color="#2f80e0"/><stop offset=".55" stop-color="#14a38b"/><stop offset="1" stop-color="#2fae4e"/>
        </linearGradient>
        <linearGradient id="{{ $id }}-bowl" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0" stop-color="#1f63c6"/><stop offset=".45" stop-color="#f6913a"/><stop offset="1" stop-color="#e5502f"/>
        </linearGradient>
    </defs>
    <polygon points="6,9 25,1 25,73 6,65" fill="url(#{{ $id }}-stem)"/>
    <polygon points="6,9 25,1 25,30" fill="#ffffff" opacity=".16"/>
    <polygon points="6,46 25,40 25,73 6,65" fill="#000000" opacity=".08"/>
    <polygon points="25,1 59,15 47,23 25,14" fill="url(#{{ $id }}-bowl)"/>
    <polygon points="59,15 59,35 47,31 47,23" fill="#f28c28"/>
    <polygon points="59,35 25,50 25,39 47,31" fill="#ee6a2c"/>
    <polygon points="25,14 47,23 25,26" fill="#2b77d6" opacity=".7"/>
    <polygon points="25,26 47,31 25,39" fill="#f6a24a" opacity=".55"/>
</svg>
