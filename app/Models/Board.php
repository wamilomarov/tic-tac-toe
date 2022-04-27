<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $game_id
 * @property int $row
 * @property string $column_1
 * @property string $column_2
 * @property string $column_3
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Game $game
 */
class Board extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'row',
        'column_1',
        'column_2',
        'column_3',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
