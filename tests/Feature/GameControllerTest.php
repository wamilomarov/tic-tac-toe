<?php

namespace Tests\Feature;

use App\Models\Board;
use App\Models\Game;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class GameControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_game_store()
    {
        $response = $this
            ->postJson('/api/games', []);
        $response
            ->assertCreated()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'board',
                    'score' => [
                        'x',
                        'y'
                    ],
                    'currentTurn',
                    'victory'
                ]
            ])
            ->assertJsonCount(3, 'data.board');

        $id = $response->json('data.id');
        $this->assertDatabaseHas('games', [
            'uuid' => $id
        ]);
    }

    public function test_game_show()
    {
        /** @var Game $game */
        $game = Game::factory(1)
            ->has(Board::factory(3), 'board')
            ->createOne();

        $response = $this->get("/api/games/$game->uuid");
        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'board',
                    'score' => [
                        'x',
                        'y'
                    ],
                    'currentTurn',
                    'victory'
                ]
            ]);

        $response = $this->get("/api/games/$game->uuid-a");
        $response
            ->assertNotFound();
    }

    public function test_game_restart()
    {
        /** @var Game $game */
        $game = Game::factory(1)
            ->has(Board::factory(3), 'board')
            ->createOne();

        $response = $this->post("/api/games/$game->uuid/restart");
        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'board',
                    'score' => [
                        'x',
                        'y'
                    ],
                    'currentTurn',
                    'victory'
                ]
            ]);

        $this->assertDatabaseMissing('boards', [
            'game_id' => $game->id,
            'column_1' => 'x'
        ]);
        $this->assertDatabaseMissing('boards', [
            'game_id' => $game->id,
            'column_1' => 'y'
        ]);

        $this->assertDatabaseMissing('boards', [
            'game_id' => $game->id,
            'column_2' => 'x'
        ]);
        $this->assertDatabaseMissing('boards', [
            'game_id' => $game->id,
            'column_2' => 'y'
        ]);

        $this->assertDatabaseMissing('boards', [
            'game_id' => $game->id,
            'column_3' => 'x'
        ]);
        $this->assertDatabaseMissing('boards', [
            'game_id' => $game->id,
            'column_3' => 'y'
        ]);
    }

    public function test_game_delete()
    {
        /** @var Game $game */
        $game = Game::factory(1)
            ->has(Board::factory(3), 'board')
            ->createOne();

        $response = $this->delete("/api/games/$game->uuid");
        $response->assertNoContent();

        $this->assertDatabaseHas('games', [
            'uuid' => $game->uuid,
            'score_x' => 0,
            'score_y' => 0,
        ]);
    }

    public function test_set_piece_on_finished_game()
    {
        /** @var Game $game */
        $game = Game::factory(1)
            ->createOne([
                'victory' => 'x'
            ]);

        $this->postJson("/api/games/$game->uuid/$game->current_turn", [
            'x' => 2,
            'y' => 1
        ])->assertStatus(425);
    }

    public function test_set_piece_on_all_occupied_game()
    {
        /** @var Game $game */
        $game = Game::factory(1)
            ->has(Board::factory(3)->state(function (array $attributes) {
                return [
                    'column_1' => 'x',
                    'column_2' => 'y',
                    'column_3' => 'x',
                ];
            }))
            ->createOne();

        $this->postJson("/api/games/$game->uuid/$game->current_turn", [
            'x' => 2,
            'y' => 1
        ])->assertStatus(409);
    }

    public function test_game_set_piece_validations()
    {
        $response = $this
            ->postJson('/api/games', []);

        $id = $response->json('data.id');

        $this->postJson("/api/games/$id/y", [
            'x' => 1,
            'y' => 1
        ])->assertStatus(406);

        $this->postJson("/api/games/$id/x", [
            'x' => 4,
            'y' => 1
        ])->assertUnprocessable();

        $this->postJson("/api/games/$id-q/x/", [
            'x' => 1,
            'y' => 1
        ])->assertNotFound();

    }

    public function test_set_piece_successfully()
    {
        $response = $this
            ->postJson('/api/games', []);

        $id = $response->json('data.id');

        $this->postJson("/api/games/$id/x", [
            'x' => 1,
            'y' => 1
        ])->assertOk();

        /** @var Game $game */
        $game = Game::query()->firstWhere('uuid', $id);

        $this->assertDatabaseHas('boards', [
            'game_id' => $game->id,
            'row' => 2,
            'column_2' => 'x'
        ]);
    }
}
