{{-- The admin menu, in the order a superadmin set. On phones the first four sit in the bottom tab bar
     with "Buat" in the middle; the rest move to the account menu. Panduan is a tab on phones only,
     since wide screens have the guide button in the top bar. Kelola is for a superadmin. --}}
@php
    $menu = \App\Support\AppSettings::menu();
    $phoneKeys = collect($menu)->pluck('key')->take(4)->all();
    $superadmin = \App\Support\Access::isSuperadmin();
@endphp
@foreach ($menu as $index => $item)
    @if ($index === 2)
        <a @class(['nav-add', 'is-active' => request()->routeIs('surveys.start', 'surveys.create')]) href="{{ route('surveys.start') }}" title="Buat kuesioner baru">
            <span class="nav-add-circle"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg></span>
            <span class="nav-add-label">Buat</span>
        </a>
    @endif
    @php($active = request()->routeIs(...$item['routes']) && ! request()->routeIs('form.*', 'surveys.start', 'surveys.create'))
    <a href="{{ route($item['route']) }}" @class([
        'nav-link',
        'is-active' => $active,
        'is-desktop-only' => ! in_array($item['key'], $phoneKeys, true),
        'is-phone-only' => $item['key'] === 'panduan',
        'is-off' => $item['off'],
    ]) title="{{ $item['label'] }}{{ $item['off'] ? ' (disembunyikan untuk admin)' : '' }}" @if ($active) aria-current="page" @endif>
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $item['icon'] }}"/></svg>
        <span>{{ $item['label'] }}</span>
        @if ($item['short'])<span class="nav-short">{{ $item['short'] }}</span>@endif
    </a>
@endforeach
@if (count($menu) <= 2)
    <a class="nav-add" href="{{ route('surveys.start') }}" title="Buat kuesioner baru">
        <span class="nav-add-circle"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg></span>
        <span class="nav-add-label">Buat</span>
    </a>
@endif
@if ($superadmin)
    @php($active = request()->routeIs('settings.*', 'admins.*', 'api-access.*'))
    <a href="{{ route('settings.index') }}" @class(['nav-link', 'is-desktop-only', 'is-active' => $active]) title="Kelola aplikasi (superadmin)" @if ($active) aria-current="page" @endif>
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm7.4-3a7.4 7.4 0 0 0-.1-1.2l2-1.6-2-3.4-2.4 1a7.6 7.6 0 0 0-2-1.2L14.5 3h-5l-.4 2.6a7.6 7.6 0 0 0-2 1.2l-2.4-1-2 3.4 2 1.6a7.4 7.4 0 0 0 0 2.4l-2 1.6 2 3.4 2.4-1a7.6 7.6 0 0 0 2 1.2l.4 2.6h5l.4-2.6a7.6 7.6 0 0 0 2-1.2l2.4 1 2-3.4-2-1.6c.1-.4.1-.8.1-1.2Z"/></svg>
        <span>Kelola</span>
    </a>
@endif
