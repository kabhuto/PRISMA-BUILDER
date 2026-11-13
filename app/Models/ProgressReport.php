<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A field officer's progress report: how far the work is, what holds it up, and what comes next.
 */
#[Fillable(['reporter', 'area', 'report_date', 'done', 'target', 'status', 'obstacle', 'plan', 'source'])]
class ProgressReport extends Model
{
    /**
     * How the work goes, with the colour it is shown in.
     */
    public const STATUSES = [
        'lancar' => ['label' => 'Lancar', 'color' => '#1f9d55'],
        'kendala' => ['label' => 'Ada kendala', 'color' => '#e8892b'],
        'terhenti' => ['label' => 'Terhenti', 'color' => '#e2493b'],
    ];

    protected function casts(): array
    {
        return ['report_date' => 'date', 'done' => 'integer', 'target' => 'integer'];
    }

    /**
     * @return BelongsTo<Survey, $this>
     */
    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status]['label'] ?? $this->status;
    }

    public function color(): string
    {
        return self::STATUSES[$this->status]['color'] ?? '#5b6b7a';
    }

    public function percent(): ?int
    {
        return $this->target ? (int) min(100, round($this->done / $this->target * 100)) : null;
    }

    /**
     * What may be sent in a report, from the web form or the API.
     *
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return [
            'reporter' => ['required', 'string', 'max:100'],
            'area' => ['nullable', 'string', 'max:150'],
            'report_date' => ['nullable', 'date', 'before_or_equal:today'],
            'done' => ['required', 'integer', 'min:0', 'max:1000000'],
            'target' => ['nullable', 'integer', 'min:1', 'max:1000000'],
            'status' => ['required', 'in:'.implode(',', array_keys(self::STATUSES))],
            'obstacle' => ['nullable', 'required_unless:status,lancar', 'string', 'max:2000'],
            'plan' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function messages(): array
    {
        return [
            'reporter.required' => 'Isi nama petugas.',
            'done.required' => 'Isi jumlah yang sudah selesai.',
            'done.integer' => 'Jumlah selesai berupa angka.',
            'target.min' => 'Target minimal 1.',
            'status.required' => 'Pilih keadaan pendataan.',
            'status.in' => 'Pilih keadaan pendataan.',
            'obstacle.required_unless' => 'Ceritakan kendalanya.',
            'report_date.before_or_equal' => 'Tanggal laporan tidak boleh di masa depan.',
        ];
    }
}
