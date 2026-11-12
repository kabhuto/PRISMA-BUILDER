<?php

namespace App\Support;

use App\Models\Agenda;
use App\Models\Assignment;
use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Models\SurveyVersion;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * What the home page and the dashboard show about the work: responses day by day, the calendar
 * (agenda set by hand plus deadlines and milestones the forms give), and the history of the forms.
 */
class Monitoring
{
    /**
     * Responses per day over the last $days days: per form and in total, oldest day first.
     *
     * @return array{days: list<string>, perSurvey: array<int, list<int>>, total: list<int>}
     */
    public static function daily(int $days): array
    {
        $dates = collect(range($days - 1, 0))->map(fn (int $ago): string => now()->subDays($ago)->toDateString());
        $rows = SurveyResponse::query()
            ->where('created_at', '>=', now()->subDays($days - 1)->startOfDay())
            ->selectRaw('survey_id, DATE(created_at) as day, COUNT(*) as total')
            ->groupBy('survey_id', 'day')
            ->get();

        $perSurvey = [];
        foreach ($rows->groupBy('survey_id') as $surveyId => $surveyRows) {
            $counts = $surveyRows->pluck('total', 'day');
            $perSurvey[(int) $surveyId] = $dates->map(fn (string $day): int => (int) ($counts[$day] ?? 0))->all();
        }
        $byDay = $rows->groupBy('day')->map(fn (Collection $dayRows): int => (int) $dayRows->sum('total'));

        return [
            'days' => $dates->all(),
            'perSurvey' => $perSurvey,
            'total' => $dates->map(fn (string $day): int => (int) ($byDay[$day] ?? 0))->all(),
        ];
    }

    /**
     * Everything on the calendar between two dates, by date: agenda set by hand, the last day of
     * assignment links, and the days forms were published or closed.
     *
     * @return Collection<string, Collection<int, array{title: string, kind: string, label: string, color: string, time: ?string, place: ?string, url: ?string, agenda: ?Agenda}>>
     */
    public static function events(Carbon $from, Carbon $to): Collection
    {
        $events = collect();

        foreach (Agenda::query()->with('survey:id,title')->whereBetween('date', [$from->toDateString(), $to->toDateString()])->orderBy('date')->orderBy('time')->get() as $agenda) {
            $events->push([
                'date' => $agenda->date->toDateString(), 'title' => $agenda->title, 'kind' => $agenda->kind, 'label' => $agenda->kindLabel(),
                'color' => $agenda->color(), 'time' => $agenda->time ? substr($agenda->time, 0, 5) : null, 'place' => $agenda->place,
                'url' => $agenda->survey_id ? route('surveys.edit', $agenda->survey_id) : null, 'agenda' => $agenda,
            ]);
        }

        // Assignment links stop working at the end of their day: a deadline for the field work.
        $deadlines = Assignment::query()
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->selectRaw('survey_id, DATE(expires_at) as day, COUNT(*) as links, SUM(CASE WHEN response_id IS NULL THEN 1 ELSE 0 END) as open')
            ->groupBy('survey_id', 'day')
            ->get();
        $titles = Survey::query()->whereIn('id', $deadlines->pluck('survey_id'))->pluck('title', 'id');
        foreach ($deadlines as $deadline) {
            $events->push([
                'date' => $deadline->day, 'title' => 'Batas tautan penugasan: '.($titles[$deadline->survey_id] ?? 'Kuesioner'), 'kind' => 'tenggat',
                'label' => 'Tenggat · '.$deadline->open.' dari '.$deadline->links.' belum mengisi', 'color' => Agenda::KINDS['tenggat']['color'],
                'time' => null, 'place' => null, 'url' => route('assignments.index', $deadline->survey_id), 'agenda' => null,
            ]);
        }

        foreach (['published_at' => ['Terbit', '#1f9d55'], 'closed_at' => ['Selesai', '#5b6b7a']] as $column => [$label, $color]) {
            foreach (Survey::query()->whereBetween($column, [$from->copy()->startOfDay(), $to->copy()->endOfDay()])->get(['id', 'title', $column]) as $survey) {
                $events->push([
                    'date' => $survey->{$column}->toDateString(), 'title' => $label.': '.$survey->title, 'kind' => $column === 'published_at' ? 'terbit' : 'selesai',
                    'label' => $label, 'color' => $color, 'time' => $survey->{$column}->format('H:i'), 'place' => null,
                    'url' => route('surveys.responses.index', $survey), 'agenda' => null,
                ]);
            }
        }

        return $events->sortBy(fn (array $event): string => $event['date'].' '.($event['time'] ?? '00:00'))->groupBy('date');
    }

    /**
     * The next things coming up, from today on.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public static function upcoming(int $limit = 5, int $withinDays = 60): Collection
    {
        return self::events(now()->startOfDay(), now()->addDays($withinDays))
            ->flatMap(fn (Collection $day, string $date) => $day->map(fn (array $event): array => $event + ['date' => $date]))
            ->reject(fn (array $event): bool => in_array($event['kind'], ['terbit', 'selesai'], true))
            ->take($limit)
            ->values();
    }

    /**
     * What happened to the forms lately: made, published, a new version, closed, locked.
     *
     * @return Collection<int, array{at: Carbon, text: string, kind: string, url: string}>
     */
    public static function history(int $limit = 12): Collection
    {
        $items = collect();
        foreach (Survey::query()->latest('updated_at')->limit(50)->get() as $survey) {
            $items->push(['at' => $survey->created_at, 'text' => 'Kuesioner dibuat: '.$survey->title, 'kind' => 'dibuat', 'url' => route('surveys.edit', $survey)]);
            if ($survey->published_at) {
                $items->push(['at' => $survey->published_at, 'text' => 'Diterbitkan: '.$survey->title, 'kind' => 'terbit', 'url' => route('surveys.responses.index', $survey)]);
            }
            if ($survey->closed_at) {
                $items->push(['at' => $survey->closed_at, 'text' => 'Ditutup (selesai): '.$survey->title, 'kind' => 'selesai', 'url' => route('surveys.responses.index', $survey)]);
            }
            if ($survey->locked_at) {
                $items->push(['at' => $survey->locked_at, 'text' => 'Dikunci untuk pendataan: '.$survey->title, 'kind' => 'kunci', 'url' => route('surveys.edit', $survey)]);
            }
        }
        foreach (SurveyVersion::query()->with('survey:id,title')->latest()->limit(30)->get() as $version) {
            if ($version->survey) {
                $items->push(['at' => $version->created_at, 'text' => 'Versi '.$version->number.' disimpan: '.$version->survey->title, 'kind' => 'versi', 'url' => route('surveys.versions.index', $version->survey_id)]);
            }
        }

        return $items->filter(fn (array $item): bool => $item['at'] !== null)->sortByDesc('at')->take($limit)->values();
    }

    /**
     * What needs a look: running forms nobody has answered, assignment links ending within three
     * days with people still to answer, instruments held by a field activity, drafts not ready.
     *
     * @param  Collection<int, Survey>  $surveys  with responses_count
     * @return Collection<int, array{text: string, detail: string, url: string, tone: string}>
     */
    public static function attention(Collection $surveys, int $limit = 6): Collection
    {
        $items = collect();
        $ending = Assignment::query()
            ->whereNull('response_id')
            ->whereBetween('expires_at', [now(), now()->addDays(3)->endOfDay()])
            ->selectRaw('survey_id, COUNT(*) as open, MIN(expires_at) as first_end')
            ->groupBy('survey_id')
            ->get()
            ->keyBy('survey_id');

        foreach ($surveys as $survey) {
            if ($ending->has($survey->id)) {
                $row = $ending[$survey->id];
                $items->push(['text' => $survey->title, 'detail' => $row->open.' tautan penugasan berakhir '.Carbon::parse($row->first_end)->diffForHumans().' dan belum diisi', 'url' => route('assignments.index', $survey), 'tone' => '#e2493b']);
            }
            if ($survey->status() === 'running' && (int) $survey->responses_count === 0) {
                $items->push(['text' => $survey->title, 'detail' => 'Sudah terbit '.$survey->published_at->diffForHumans().' tetapi belum ada respons', 'url' => route('surveys.responses.index', $survey), 'tone' => '#e8892b']);
            }
            if ($survey->isLocked()) {
                $items->push(['text' => $survey->title, 'detail' => 'Instrumen dikunci untuk pendataan'.($survey->locked_note ? ' ('.$survey->locked_note.')' : ''), 'url' => route('surveys.edit', $survey), 'tone' => '#2f6fd6']);
            }
        }
        foreach ($surveys->filter(fn (Survey $survey): bool => $survey->status() === 'draft')->take(5) as $survey) {
            if (($errors = count(SurveyCheck::errors($survey))) > 0) {
                $items->push(['text' => $survey->title, 'detail' => 'Draf belum siap terbit: '.$errors.' masalah pada pemeriksaan publikasi', 'url' => route('surveys.edit', $survey), 'tone' => '#7a8f8b']);
            }
        }

        return $items->take($limit)->values();
    }

    /**
     * A small line chart: the line and the area under it.
     *
     * @param  list<int>  $values
     * @return array{0: string, 1: string}
     */
    public static function spark(array $values, int $width, int $height): array
    {
        $max = max(1, max($values ?: [0]));
        $step = $width / max(1, count($values) - 1);
        $points = collect($values)->values()->map(fn (int $value, int $index): string => round($index * $step, 1).','.round($height - 2 - ($value / $max) * ($height - 6), 1));
        $line = 'M'.$points->implode(' L');

        return [$line, $line.' L'.$width.','.$height.' L0,'.$height.' Z'];
    }
}
