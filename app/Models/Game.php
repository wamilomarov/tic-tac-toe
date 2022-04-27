<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $uuid
 * @property int $score_x
 * @property int $score_y
 * @property string $current_turn
 * @property string $victory
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Collection|Board[] $board
 */
class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'score_x',
        'score_y',
        'current_turn',
        'victory',
    ];

    protected $casts = [
        'score_x' => 'integer',
        'score_y' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        self::creating(function (Game $game) {
            $game->uuid = $game->generateUuid();
        });
    }

    public function generateUuid(): string
    {
        if (!is_null($this->uuid)) {
            return $this->uuid;
        }

        $uuid = Str::uuid();

        $gameExists = Game::query()->where('uuid', $uuid)->exists();
        if ($gameExists) {
            return $this->generateUuid();
        } else {
            return $uuid;
        }
    }

    public function board(): HasMany
    {
        return $this->hasMany(Board::class)->orderBy('row');
    }

    public function resetBoard(): void
    {
        $this->board()
            ->update([
                'column_1' => null,
                'column_2' => null,
                'column_3' => null,
            ]);

        $this->update([
            'current_turn' => 'x',
            'victory' => null
        ]);
    }

    public function resetScores()
    {
        $this->update([
            'score_x' => 0,
            'score_y' => 0,
            'current_turn' => 'x',
            'victory' => null
        ]);
    }

    public function isHorizontalWin(string $piece, int $row): bool
    {
        $this->loadMissing(['board']);

        /** @var Board $boardRow */
        $boardRow = $this->board->firstWhere('row', $row);
        if (
            $boardRow->column_1 === $piece &&
            $boardRow->column_1 === $boardRow->column_2 &&
            $boardRow->column_1 === $boardRow->column_3
        ) {
            return true;
        }
        return false;
    }

    public function isVerticalWin(string $piece, int $column): bool
    {
        $this->loadMissing(['board']);

        /** @var Board $boardRow */
        foreach ($this->board as $boardRow) {
            if ($boardRow->{"column_$column"} != $piece)
            {
                return false;
            }
        }
        return true;
    }

    public function isDiagonalWin(string $piece): bool
    {
        $win = true;
        $this->loadMissing(['board']);

        /** @var Board $boardRow */
        foreach ($this->board as $index => $boardRow) {
            $i = $index + 1;
            if ($boardRow->{"column_$i"} != $piece)
            {
                $win = false;
            }
        }

        /** @var Board $boardRow */
        foreach ($this->board as $index => $boardRow) {
            $i = 2 - $index;
            if ($boardRow->{"column_$i"} != $piece)
            {
                $win = false;
            }
        }

        return $win;
    }
}
