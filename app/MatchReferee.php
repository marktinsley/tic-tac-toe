<?php

namespace App;

use App\Events\MoveRecorded;
use App\Rules\IsPlayersTurn;
use App\Rules\IsTile;
use App\Rules\MatchInProgress;
use App\Rules\TileIsAvailable;
use App\Rules\TileShorthand;
use Illuminate\Support\Facades\Validator;

class MatchReferee
{
    /**
     * @var Match
     */
    protected $match;

    /**
     * MatchReferee constructor.
     *
     * @param Match $match
     */
    public function __construct(Match $match)
    {
        $this->match = $match;
    }

    /**
     * Attempt to make a move on the board.
     *
     * @param User $player
     * @param Tile $tile
     *
     * @return Move
     */
    public function attemptMove(User $player, Tile $tile)
    {
        $this->moveValidator($player, $tile)->validate();

        return $this->match->recordMove($tile, $player);
    }

    public function lookForWinner()
    {
    }

    /**
     * Validator for a given move and player in this match.
     *
     * @param User $player
     * @param Tile $tile
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function moveValidator(User $player, Tile $tile)
    {
        return Validator::make(
            [
                'match_id' => $this->match->id,
                'player_id' => $player->id,
                'tile' => $tile->shorthand()
            ],
            [
                'match_id' => [new MatchInProgress],
                'player_id' => [new IsPlayersTurn($this->match)],
                'tile' => [new IsTile, new TileIsAvailable($this->match)]
            ]
        );
    }
}
