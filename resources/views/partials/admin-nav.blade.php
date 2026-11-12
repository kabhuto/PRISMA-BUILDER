{{-- The admin pages, as links with icons; the current one is marked. --}}
@foreach ([
    ['surveys.index', 'surveys.*', 'Dashboard', 'M3 13h8V3H3v10Zm0 8h8v-6H3v6Zm10 0h8V11h-8v10Zm0-18v6h8V3h-8Z'],
    ['references.index', 'references.*', 'Data referensi', 'M4 6c0-1.7 3.6-3 8-3s8 1.3 8 3-3.6 3-8 3-8-1.3-8-3Zm0 0v12c0 1.7 3.6 3 8 3s8-1.3 8-3V6M4 12c0 1.7 3.6 3 8 3s8-1.3 8-3'],
    ['api-access.index', 'api-access.*', 'Akses API', 'm8 8-5 4 5 4m8-8 5 4-5 4M14 4l-4 16'],
    ['admins.index', 'admins.*', 'Admin', 'M16 19v-1a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v1M9 10a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm13 9v-1a4 4 0 0 0-3-3.9M16 3.1a3.5 3.5 0 0 1 0 6.8'],
] as [$route, $pattern, $label, $icon])
    @php($active = request()->routeIs($pattern) && ! request()->routeIs('form.*'))
    <a href="{{ route($route) }}" @class(['nav-link', 'is-active' => $active]) @if ($active) aria-current="page" @endif>
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icon }}"/></svg>
        <span>{{ $label }}</span>
        @if ($label === 'Data referensi')<span class="nav-short">Referensi</span>@endif
    </a>
@endforeach
