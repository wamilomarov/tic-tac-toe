<?php

namespace App\Http\Controllers;

use App\Http\Requests\SetPieceRequest;
use App\Http\Resources\GameResource;
use App\Models\Game;
use App\Services\GameService;
use Illuminate\Http\JsonResponse;

class GameController extends Controller
{
    protected GameService $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    public function store(): GameResource
    {
        $game = $this->gameService->create();

        return GameResource::make($game);
    }

    public function show(Game $game): GameResource
    {
        $game->loadMissing(['board']);
        return GameResource::make($game);
    }

    public function restart(Game $game): GameResource
    {
        $game = $this->gameService->restart($game);

        return GameResource::make($game);
    }

    public function delete(Game $game): JsonResponse
    {
        $this->gameService->delete($game);
        return response()->json(null, 204);
    }

    public function setPiece(Game $game, $piece, SetPieceRequest $request): GameResource
    {
        $columnNumber = $request->get('x');
        $rowNumber = $request->get('y');
        $columnNumber = $columnNumber + 1;
        $rowNumber = $rowNumber + 1;

        $this->gameService->validateSetPieceRequest($game, $piece, $columnNumber, $rowNumber);
        $this->gameService->setPiece($game, $piece, $columnNumber, $rowNumber);
        $this->gameService->checkGameEnd($game);
        $this->gameService->toggleTurn($game);

        $game->refresh();

        return GameResource::make($game);
    }

}
