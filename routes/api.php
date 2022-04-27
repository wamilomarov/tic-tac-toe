<?php

use App\Http\Controllers\GameController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('games', [GameController::class, 'store']);
Route::get('games/{game:uuid}', [GameController::class, 'show']);
Route::post('games/{game:uuid}/restart', [GameController::class, 'restart']);
Route::delete('games/{game:uuid}', [GameController::class, 'delete']);
Route::post('games/{game:uuid}/{piece}', [GameController::class, 'setPiece']);
