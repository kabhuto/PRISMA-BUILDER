@extends('layouts.admin')

@section('title', 'Kelola aplikasi')

@push('head')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/monitor.css') }}">
    <link rel="stylesheet" href="{{ asset('css/manage.css') }}">
@endpush

@php
    $icons = \App\Support\Ui::ICONS;
    $pictures = \App\Support\Ui::PICTURES;
    $looks = \App\Support\Ui::TEMPLATE_LOOKS;
@endphp

@section('content')
    <div class="page-intro">
        <div>
            <div class="eyebrow"><a class="back-link" href="{{ route('surveys.index') }}">← BERANDA</a> <span>/</span> KELOLA · SUPERADMIN</div>
            <h1>Kelola aplikasi.</h1>
            <p>Atur menu, alat, template, dan kuesioner yang boleh dipakai admin (pegawai). Superadmin selalu melihat semuanya.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="validation-alert" role="alert">{{ $errors->first() }}</div>
    @endif

    <nav class="manage-jump" aria-label="Bagian halaman">
        <a href="#menu">Menu</a>
        <a href="#alat">Alat &amp; jenis isian</a>
        <a href="#template">Template</a>
        <a href="#kuesioner">Kuesioner</a>
        <a href="{{ route('admins.index') }}">Kelola admin →</a>
        <a href="{{ route('api-access.index') }}">Akses API →</a>
    </nav>

    <div class="manage-pair">
        <section class="monitor-card" id="menu" aria-labelledby="menu-heading">
            <header><h2 id="menu-heading">Menu</h2></header>
            <p class="muted">Geser naik/turun untuk mengubah urutan. Di HP, empat menu teratas tampil di bilah bawah dengan tombol <b>Buat</b> di tengah; sisanya ada di menu akun. Hapus centang untuk menyembunyikan menu dari admin.</p>
            <form method="POST" action="{{ route('settings.menu') }}" data-menu-form>
                @csrf
                @method('PUT')
                <ol class="menu-order" data-menu-list>
                    @foreach ($menu as $item)
                        <li>
                            <input type="hidden" name="order[]" value="{{ $item['key'] }}">
                            <span class="menu-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $item['icon'] }}"/></svg></span>
                            <strong>{{ $item['label'] }}</strong>
                            <small class="menu-place" data-place></small>
                            <label class="switch" title="{{ $item['fixed'] ? 'Beranda selalu tampil' : 'Tampil untuk admin' }}">
                                <input type="checkbox" name="visible[]" value="{{ $item['key'] }}" @checked(! in_array($item['key'], $hiddenMenus, true)) @disabled($item['fixed'])>
                                <span>Tampil</span>
                            </label>
                            @if ($item['fixed'])<input type="hidden" name="visible[]" value="{{ $item['key'] }}">@endif
                            <span class="menu-move">
                                <button type="button" data-move="-1" aria-label="Naikkan {{ $item['label'] }}">↑</button>
                                <button type="button" data-move="1" aria-label="Turunkan {{ $item['label'] }}">↓</button>
                            </span>
                        </li>
                    @endforeach
                </ol>
                <button class="button button-primary" type="submit">Simpan menu</button>
            </form>
        </section>

        <section class="monitor-card" id="alat" aria-labelledby="tools-heading">
            <header><h2 id="tools-heading">Alat &amp; jenis isian</h2></header>
            <form method="POST" action="{{ route('settings.tools') }}">
                @csrf
                @method('PUT')
                <h3 class="manage-sub">Alat yang boleh dipakai admin</h3>
                <div class="check-list">
                    @foreach ($tools as $key => $tool)
                        <label class="check-item">
                            <input type="checkbox" name="tools[]" value="{{ $key }}" @checked(! in_array($key, $hiddenTools, true))>
                            <span><b>{{ $tool['label'] }}</b><small>{{ $tool['description'] }}</small></span>
                        </label>
                    @endforeach
                </div>
                <h3 class="manage-sub">Jenis isian di penyusun kuesioner <small class="muted">({{ count($questionTypes) - count($hiddenTypes) }} dari {{ count($questionTypes) }} aktif)</small></h3>
                <div class="check-grid">
                    @foreach ($questionTypes as $key => $label)
                        <label class="check-chip">
                            <input type="checkbox" name="types[]" value="{{ $key }}" @checked(! in_array($key, $hiddenTypes, true))>
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                <p class="setting-note">Isian yang sudah ada di kuesioner tetap berfungsi; jenis yang dimatikan hanya tidak bisa ditambahkan lagi oleh admin.</p>
                <button class="button button-primary" type="submit">Simpan alat</button>
            </form>
        </section>
    </div>

    <section class="monitor-card" id="template" aria-labelledby="template-heading">
        <header><h2 id="template-heading">Template</h2><span class="home-chip">{{ count($builtIn) + $customTemplates->count() }}</span></header>
        <div class="template-manage">
            <div>
                <h3 class="manage-sub">Template bawaan</h3>
                <ul class="manage-list">
                    @foreach ($builtIn as $key => $template)
                        @php($off = in_array($key, $hiddenTemplates, true))
                        @php([$picture, $tone] = $looks[$key] ?? [$pictures['clipboard'], '#2f6fd6'])
                        <li @class(['is-off' => $off]) style="--tone: {{ $tone }}">
                            <span class="template-tile-icon"><svg class="icon-fill" viewBox="0 0 24 24" aria-hidden="true">{!! $picture !!}</svg></span>
                            <span class="manage-text"><strong>{{ $template['label'] }}</strong><small>{{ $template['description'] }}</small></span>
                            @if ($key === \App\Support\SurveyTemplates::DEFAULT)
                                <span class="home-chip">Selalu tampil</span>
                            @else
                                <form method="POST" action="{{ route('templates.built-in', $key) }}">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="hidden" value="{{ $off ? 0 : 1 }}">
                                    <button class="button button-ghost button-small" type="submit">{{ $off ? 'Tampilkan' : 'Sembunyikan' }}</button>
                                </form>
                            @endif
                        </li>
                    @endforeach
                </ul>

                <h3 class="manage-sub">Template kantor</h3>
                <ul class="manage-list">
                    @forelse ($customTemplates as $template)
                        <li @class(['is-off' => $template->hidden]) style="--tone: #0f9d8f">
                            <span class="template-tile-icon"><svg class="icon-fill" viewBox="0 0 24 24" aria-hidden="true">{!! $pictures['clipboard'] !!}</svg></span>
                            <span class="manage-text">
                                <strong>{{ $template->name }} @if ($template->hidden)<span class="status-badge is-hidden">Disembunyikan</span>@endif</strong>
                                <small>{{ $template->description ?: 'Tanpa keterangan' }} · {{ count($template->blocks ?? []) }} blok · oleh {{ $template->creator?->name ?? '—' }}</small>
                            </span>
                            <details class="manage-edit">
                                <summary class="button button-ghost button-small">Ubah</summary>
                                <form method="POST" action="{{ route('templates.update', $template) }}" class="manage-form">
                                    @csrf @method('PUT')
                                    <label class="field"><span>Nama</span><input type="text" name="name" value="{{ $template->name }}" maxlength="120" required></label>
                                    <label class="field"><span>Keterangan</span><input type="text" name="description" value="{{ $template->description }}" maxlength="255"></label>
                                    <div class="manage-row">
                                        <label class="field"><span>Urutan</span><input type="number" name="sort" value="{{ $template->sort }}" min="0" max="9999"></label>
                                        <label class="field"><span>Perbarui isi dari</span>
                                            <select name="survey_id">
                                                <option value="">Tetap seperti sekarang</option>
                                                @foreach ($surveys as $survey)
                                                    <option value="{{ $survey->id }}">{{ $survey->title }}</option>
                                                @endforeach
                                            </select>
                                        </label>
                                    </div>
                                    <label class="check-item"><input type="checkbox" name="hidden" value="1" @checked($template->hidden)><span><b>Sembunyikan</b><small>tidak tampil di halaman Buat</small></span></label>
                                    <button class="button button-primary button-small" type="submit">Simpan</button>
                                </form>
                                <form method="POST" action="{{ route('templates.destroy', $template) }}" onsubmit="return confirm('Hapus template ini? Kuesioner yang dibuat darinya tidak terpengaruh.')">
                                    @csrf @method('DELETE')
                                    <button class="link-danger" type="submit">Hapus template</button>
                                </form>
                            </details>
                        </li>
                    @empty
                        <li class="manage-empty">Belum ada. Jadikan kuesioner yang sudah rapi sebagai template di samping.</li>
                    @endforelse
                </ul>
            </div>

            <form method="POST" action="{{ route('templates.store') }}" class="manage-form manage-new">
                @csrf
                <h3 class="manage-sub">Jadikan kuesioner sebagai template</h3>
                <label class="field"><span>Kuesioner sumber</span>
                    <select name="survey_id" required>
                        <option value="">Pilih kuesioner…</option>
                        @foreach ($surveys as $survey)
                            <option value="{{ $survey->id }}" @selected((int) old('survey_id') === $survey->id)>{{ $survey->title }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="field"><span>Nama template</span><input type="text" name="name" value="{{ old('name') }}" maxlength="120" required placeholder="mis. Survei Kepuasan Konsumen"></label>
                <label class="field"><span>Keterangan</span><input type="text" name="description" value="{{ old('description') }}" maxlength="255" placeholder="Isi singkat template"></label>
                <button class="button button-primary" type="submit">Buat template</button>
                <p class="setting-note">Blok dan pertanyaan disalin; kuesioner sumber tidak berubah. Template baru langsung tampil di halaman Buat.</p>
            </form>
        </div>
    </section>

    <section class="monitor-card" id="kuesioner" aria-labelledby="surveys-heading">
        <header><h2 id="surveys-heading">Kuesioner yang bisa dipakai</h2><span class="home-chip">{{ $surveys->whereNotNull('hidden_at')->count() }} disembunyikan</span></header>
        <p class="muted">Kuesioner yang disembunyikan tidak tampil bagi admin, tidak bisa dibuka, dan tautan pengisiannya tertutup sampai ditampilkan lagi.</p>
        <ul class="manage-list is-surveys">
            @forelse ($surveys as $survey)
                @php($status = $survey->status())
                <li @class(['is-off' => $survey->isHidden()])>
                    <span class="manage-text">
                        <strong><a href="{{ route('surveys.edit', $survey) }}">{{ $survey->title }}</a></strong>
                        <small>
                            <span @class(['status-badge', 'is-published' => $status === 'running', 'is-done' => $status === 'done'])>{{ $survey->statusLabel() }}</span>
                            {{ $survey->activity ?: 'Tanpa kegiatan' }} · {{ number_format($survey->responses_count, 0, ',', '.') }} respons · oleh {{ $survey->creator?->name ?? '—' }}
                        </small>
                    </span>
                    <form method="POST" action="{{ route('settings.survey', $survey) }}">
                        @csrf @method('PUT')
                        <input type="hidden" name="hidden" value="{{ $survey->isHidden() ? 0 : 1 }}">
                        <button @class(['button', 'button-small', 'button-ghost' => ! $survey->isHidden(), 'button-primary' => $survey->isHidden()]) type="submit">{{ $survey->isHidden() ? 'Tampilkan' : 'Sembunyikan' }}</button>
                    </form>
                </li>
            @empty
                <li class="manage-empty">Belum ada kuesioner.</li>
            @endforelse
        </ul>
    </section>

    <script>
        (() => {
            // Move a menu up or down, then say where it lands on a phone.
            const list = document.querySelector('[data-menu-list]');
            const label = () => [...list.children].forEach((row, index) => {
                row.querySelector('[data-place]').textContent = index < 4 ? 'Bilah bawah HP' : 'Menu akun di HP';
            });
            list.addEventListener('click', event => {
                const button = event.target.closest('[data-move]');
                if (!button) return;
                const row = button.closest('li');
                if (button.dataset.move === '-1' && row.previousElementSibling) list.insertBefore(row, row.previousElementSibling);
                if (button.dataset.move === '1' && row.nextElementSibling) list.insertBefore(row.nextElementSibling, row);
                label();
                button.focus();
            });
            label();
        })();
    </script>
@endsection
