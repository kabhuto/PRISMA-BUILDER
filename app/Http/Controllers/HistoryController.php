<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Survey;
use App\Support\Monitoring;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * "Riwayat": everything the person signed in has made: their forms by stage, what happened to
 * them, and the agenda they set.
 */
class HistoryController extends Controller
{
    public function index(Request $request): View
    {
        $userId = $request->user()?->id;
        $surveys = Survey::query()->usable()
            ->where('user_id', $userId)
            ->with('creator:id,name')
            ->withCount(['responses', 'assignments', 'assignments as assignments_done_count' => fn ($query) => $query->whereNotNull('response_id')])
            ->withMax('responses', 'created_at')
            ->latest('updated_at')
            ->get();
        $daily = Monitoring::daily(14);
        $statuses = $surveys->countBy(fn (Survey $survey): string => $survey->status());

        return view('history.index', [
            'surveys' => $surveys,
            'trends' => $daily['perSurvey'],
            'periods' => $surveys->map->periodLabel()->filter()->unique()->sort()->values(),
            'statuses' => ['running' => $statuses['running'] ?? 0, 'done' => $statuses['done'] ?? 0, 'draft' => $statuses['draft'] ?? 0],
            'responsesTotal' => (int) $surveys->sum('responses_count'),
            'history' => $userId ? Monitoring::history(20, $userId) : collect(),
            'agendas' => Agenda::query()->where('user_id', $userId)->latest('date')->limit(10)->get(),
        ]);
    }
}
