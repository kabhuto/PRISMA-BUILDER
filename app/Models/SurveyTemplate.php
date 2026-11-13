<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A template a superadmin made from a form: a starting point offered on the "Buat" page.
 */
#[Fillable(['name', 'description', 'blocks', 'hidden', 'sort', 'user_id'])]
class SurveyTemplate extends Model
{
    protected function casts(): array
    {
        return ['blocks' => 'array', 'hidden' => 'boolean', 'sort' => 'integer'];
    }

    /**
     * The key it goes by among the built-in templates.
     */
    public function key(): string
    {
        return 'c'.$this->id;
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
