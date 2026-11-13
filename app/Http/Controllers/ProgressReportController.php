<?php

namespace App\Http\Controllers;

use App\Models\ProgressReport;
use App\Models\Survey;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * The field work of a form: when collection starts and must end, how often petugas report their
 * progress, and the reports themselves (how far, what holds them up, what comes next). Petugas
 * report through a public link, or Collect sends the report through the API.
 */
class ProgressReportController extends Controller
{
    public function index(Survey $survey): View
    {
        $reports = $survey->progressReports()->latest('report_date')->latest('id')->get();
        $dueDates = $survey->collect_deadline ? $survey->reportDueDates(now()->subYear(), $survey->collect_deadline) : [];
        $reportedDays = $reports->map(fn (ProgressReport $report): string => $report->report_date->toDateString())->unique();

        return view('progress.index', [
            'survey' => $survey,
            'reports' => $reports,
            'latest' => self::latestPerReporter($reports),
            'due' => collect($dueDates)->map(fn (string $date): array => [
                'date' => $date,
                'state' => $reportedDays->contains($date) ? 'masuk' : ($date < now()->toDateString() ? 'terlewat' : ($date === now()->toDateString() ? 'hari-ini' : 'akan')),
            ]),
            'every' => Survey::REPORT_EVERY,
            'weekdays' => Survey::WEEKDAYS,
            'statuses' => ProgressReport::STATUSES,
        ]);
    }

    public function schedule(Request $request, Survey $survey): RedirectResponse
    {
        $data = $request->validate([
            'collect_start' => ['nullable', 'date'],
            'collect_deadline' => ['nullable', 'required_with:report_every', 'date', 'after_or_equal:collect_start'],
            'report_every' => ['nullable', 'in:'.implode(',', array_keys(Survey::REPORT_EVERY))],
            'report_weekday' => ['nullable', 'integer', 'between:1,7'],
            'report_time' => ['nullable', 'date_format:H:i'],
        ], [
            'collect_deadline.required_with' => 'Laporan progres butuh tenggat pendataan.',
            'collect_deadline.after_or_equal' => 'Tenggat tidak boleh sebelum tanggal mulai.',
            'report_time.date_format' => 'Jam laporan berformat JJ:MM.',
        ]);
        $data['report_weekday'] = ($data['report_every'] ?? null) === 'weekly' ? ($data['report_weekday'] ?? 5) : null;
        $survey->update($data);

        return redirect()->route('progress.index', $survey)->with('status', 'Jadwal pendataan dan laporan disimpan. Tenggatnya tampil di kalender Dashboard.');
    }

    public function destroy(Survey $survey, ProgressReport $progressReport): RedirectResponse
    {
        $progressReport->delete();

        return redirect()->route('progress.index', $survey)->with('status', 'Laporan dari '.$progressReport->reporter.' dihapus.');
    }

    /**
     * The public report form for petugas, open once a report schedule is set.
     */
    public function create(Survey $survey): View
    {
        abort_if($survey->isHidden() || $survey->report_every === null, 404);

        return view('progress.create', [
            'survey' => $survey,
            'statuses' => ProgressReport::STATUSES,
            'next' => $survey->nextReportDue(),
        ]);
    }

    public function store(Request $request, Survey $survey): RedirectResponse
    {
        abort_if($survey->isHidden() || $survey->report_every === null, 404);

        $data = $request->validate(ProgressReport::rules(), ProgressReport::messages());
        $survey->progressReports()->create([...$data, 'report_date' => $data['report_date'] ?? now()->toDateString(), 'source' => 'web']);

        return redirect()->route('report.create', $survey)->with('status', 'Terima kasih, laporan progres terkirim.');
    }

    /**
     * Each petugas's most recent report, newest first.
     *
     * @param  Collection<int, ProgressReport>  $reports
     * @return Collection<int, ProgressReport>
     */
    private static function latestPerReporter(Collection $reports): Collection
    {
        return $reports->groupBy(fn (ProgressReport $report): string => mb_strtolower(trim($report->reporter)))
            ->map(fn (Collection $group): ProgressReport => $group->first())
            ->values();
    }
}
