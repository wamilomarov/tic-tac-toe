<?php

namespace App\Services;

use App\Models\Board;
use App\Models\Game;

class GameService
{
    public function create(): Game
    {
        /** @var Game $game */
        $game = Game::query()
            ->create();

        for ($i = 1; $i <= 3; $i++)
        {
            Board::query()
                ->create([
                    'game_id' => $game->id,
                    'row' => $i
                ]);
        }

        $game->loadMissing(['board']);
        return $game;
    }

    public function restart(Game $game): Game
    {
        $game->resetBoard();

        $game
            ->refresh()
            ->loadMissing(['board']);

        return $game;
    }

    public function delete(Game $game): void
    {
        $game->resetBoard();
        $game->resetScores();
    }

    public function validateSetPieceRequest(Game $game, string $piece, int $column, int $row)
    {
        if ($game->current_turn != $piece)
        {
            response()->json(null, 406)->throwResponse();
        }

        if (!is_null($game->victory))
        {
            response()->json(null, 425)->throwResponse();
        }

        $game->loadMissing(['board']);
        /** @var Board $boardRow */
        $boardRow = $game->board
            ->firstWhere('row', $row);

        if (!is_null($boardRow->{"column_$column"}))
        {
            response()->json(null, 409)->throwResponse();
        }
    }

    public function setPiece(Game $game, string $piece, int $column, int $row)
    {
        $game->board()
            ->where('row', $row)
            ->update([
                "column_$column" => $piece
            ]);
        $game->refresh();

        if ($game->isDiagonalWin($piece))
        {
            $game->{"score_$piece"} = $game->{"score_$piece"} + 1;
        }

        if ($game->isHorizontalWin($piece, $row))
        {
            $game->{"score_$piece"} = $game->{"score_$piece"} + 1;
        }

        if ($game->isVerticalWin($piece, $column))
        {
            $game->{"score_$piece"} = $game->{"score_$piece"} + 1;
        }

        $game->save();
    }

    public function checkGameEnd(Game $game)
    {
        $emptyPiecesCount = $game->board()
            ->whereNull('column_1')
            ->orWhereNull('column_2')
            ->orWhereNull('column_3')
            ->count();

        if ($emptyPiecesCount === 0)
        {
            if ($game->score_x > $game->score_y)
            {
                $game->victory = 'x';
            }
            else if ($game->score_x < $game->score_y)
            {
                $game->victory = 'o';
            }
            else
            {
                $game->victory = 'tie';
            }
            $game->save();
        }
    }

    public function toggleTurn(Game $game)
    {
        if ($game->current_turn === 'x')
        {
            $game->current_turn = 'y';
        }
        else
        {
            $game->current_turn = 'x';
        }
        $game->save();
    }
}
