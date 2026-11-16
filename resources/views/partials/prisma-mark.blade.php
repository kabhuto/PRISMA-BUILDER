{{-- The PRISMA mark alone: the hexagonal "P" with the cube, or the logo a superadmin uploaded. --}}
@if ($prismaLogo = \App\Support\AppSettings::logo('prisma'))
    <img class="{{ $class ?? 'prisma-mark' }} is-uploaded" src="{{ $prismaLogo }}" alt="" aria-hidden="true">
@else
    @include('partials.prisma-emblem', ['class' => $class ?? 'prisma-mark', 'orbit' => false])
@endif
