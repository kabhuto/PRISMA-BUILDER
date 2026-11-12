<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * An item on the dashboard calendar set by hand: a briefing, a meeting, a deadline, or anything else.
 */
#[Fillable(['title', 'kind', 'date', 'time', 'place', 'note', 'survey_id', 'user_id'])]
class Agenda extends Model
{
    /**
     * The kinds of agenda, each with its label and colour on the calendar.
     */
    public const KINDS = [
        'tenggat' => ['label' => 'Tenggat', 'color' => '#e2493b'],
        'briefing' => ['label' => 'Briefing', 'color' => '#2f6fd6'],
        'rapat' => ['label' => 'Rapat', 'color' => '#0f9d8f'],
        'pelatihan' => ['label' => 'Pelatihan', 'color' => '#e8892b'],
        'lainnya' => ['label' => 'Lainnya', 'color' => '#7a8f8b'],
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Survey, $this>
     */
    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    public function kindLabel(): string
    {
        return self::KINDS[$this->kind]['label'] ?? 'Lainnya';
    }

    public function color(): string
    {
        return self::KINDS[$this->kind]['color'] ?? self::KINDS['lainnya']['color'];
    }
}
