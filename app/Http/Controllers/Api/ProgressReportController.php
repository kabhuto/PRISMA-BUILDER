<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProgressReport;
use App\Models\Survey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Progress reports of the field work, read by the dashboards and sent by PRISMA Collect.
 */
class ProgressReportController extends Controller
{
    public function index(Survey $survey): JsonResponse
    {
        abort_if($survey->isHidden(), 404);

        return response()->json([
            'schedule' => [
                'collect_start' => $survey->collect_start?->toDateString(),
                'collect_deadline' => $survey->collect_deadline?->toDateString(),
                'report_every' => $survey->report_every,
                'report_weekday' => $survey->report_weekday,
                'report_time' => $survey->report_time,
                'label' => $survey->reportScheduleLabel(),
                'next_due' => $survey->nextReportDue(),
            ],
            'data' => $survey->progressReports()->latest('report_date')->latest('id')->get()->map(fn (ProgressReport $report): array => self::toApi($report))->values(),
        ]);
    }

    public function store(Request $request, Survey $survey): JsonResponse
    {
        abort_if($survey->isHidden(), 404);

        $data = $request->validate(ProgressReport::rules(), ProgressReport::messages());
        $report = $survey->progressReports()->create([...$data, 'report_date' => $data['report_date'] ?? now()->toDateString(), 'source' => 'api']);

        return response()->json(['data' => self::toApi($report)], 201);
    }

    /**
     * @return array<string, mixed>
     */
    private static function toApi(ProgressReport $report): array
    {
        return [
            'id' => $report->id,
            'reporter' => $report->reporter,
            'area' => $report->area,
            'report_date' => $report->report_date->toDateString(),
            'done' => $report->done,
            'target' => $report->target,
            'percent' => $report->percent(),
            'status' => $report->status,
            'status_label' => $report->statusLabel(),
            'obstacle' => $report->obstacle,
            'plan' => $report->plan,
            'source' => $report->source,
            'created_at' => $report->created_at?->toIso8601String(),
        ];
    }
}
