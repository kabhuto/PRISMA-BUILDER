<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Agenda on the dashboard calendar: briefings, meetings, deadlines and the like, added by hand.
 */
class AgendaController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'kind' => ['required', Rule::in(array_keys(Agenda::KINDS))],
            'date' => ['required', 'date'],
            'time' => ['nullable', 'date_format:H:i'],
            'place' => ['nullable', 'string', 'max:150'],
            'note' => ['nullable', 'string', 'max:1000'],
            'survey_id' => ['nullable', 'integer', 'exists:surveys,id'],
        ], [], ['title' => 'judul', 'kind' => 'jenis', 'date' => 'tanggal', 'time' => 'jam', 'place' => 'tempat', 'survey_id' => 'kuesioner']);

        $agenda = Agenda::query()->create([...$data, 'user_id' => $request->user()?->id]);

        return redirect()->route('dashboard', ['bulan' => $agenda->date->format('Y-m')])->withFragment('kalender')
            ->with('status', 'Agenda "'.$agenda->title.'" ditambahkan ke kalender.');
    }

    public function destroy(Agenda $agenda): RedirectResponse
    {
        $month = $agenda->date->format('Y-m');
        $agenda->delete();

        return redirect()->route('dashboard', ['bulan' => $month])->withFragment('kalender')->with('status', 'Agenda dihapus.');
    }
}
