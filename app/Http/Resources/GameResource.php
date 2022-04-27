<?php

namespace App\Http\Resources;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Game
 */
class GameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'board' => BoardResource::collection($this->whenLoaded('board')),
            'score' => [
                'x' => $this->score_x,
                'y' => $this->score_y,
            ],
            'currentTurn' => $this->current_turn,
            'victory' => $this->victory
        ];
    }
}
