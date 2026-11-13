<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Support\Monitoring;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

/**
 * The monitoring page: how every form is doing (running, finished, draft), responses over time,
 * the calendar with deadlines and agenda, and what has been made.
 */
class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $surveys = Survey::query()->usable()
            ->with('creator:id,name')
            ->withCount(['responses', 'assignments', 'assignments as assignments_done_count' => fn ($query) => $query->whereNotNull('response_id')])
            ->withMax('responses', 'created_at')
            ->latest('updated_at')
            ->get();
        $daily = Monitoring::daily(30);
        $trends = collect($daily['perSurvey'])->map(fn (array $days): array => array_slice($days, -14))->all();

        // The month shown on the calendar: ?bulan=2026-11, this month otherwise.
        $month = preg_match('/^\d{4}-\d{2}$/', (string) $request->query('bulan'))
            ? Carbon::createFromFormat('Y-m-d', $request->query('bulan').'-01')->startOfMonth()
            : now()->startOfMonth();
        $gridStart = $month->copy()->startOfWeek(Carbon::MONDAY);
        $gridEnd = $month->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $statuses = $surveys->countBy(fn (Survey $survey): string => $survey->status());

        return view('dashboard.index', [
            'surveys' => $surveys,
            'periods' => $surveys->map->periodLabel()->filter()->unique()->sort()->values(),
            'trends' => $trends,
            'daily' => $daily,
            'statuses' => ['running' => $statuses['running'] ?? 0, 'done' => $statuses['done'] ?? 0, 'draft' => $statuses['draft'] ?? 0],
            'responsesTotal' => $surveys->sum('responses_count'),
            'responsesWeek' => SurveyResponse::query()->whereIn('survey_id', $surveys->modelKeys())->where('created_at', '>=', now()->subDays(6)->startOfDay())->count(),
            'byActivity' => $surveys->groupBy(fn (Survey $survey): string => $survey->activity ?: 'Tanpa kegiatan')
                ->map(fn ($group): int => (int) $group->sum('responses_count'))->sortDesc()->take(6),
            'month' => $month,
            'gridStart' => $gridStart,
            'gridEnd' => $gridEnd,
            'events' => Monitoring::events($gridStart, $gridEnd),
            'history' => Monitoring::history(12),
            'kinds' => Agenda::KINDS,
        ]);
    }
}
