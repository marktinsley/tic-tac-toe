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

    /**
     * @return User
     */
    public function lookForWinner()
    {
        if ($this->match->refresh()->hasEnded()) {
            return $this->match->winner;
        }

        $moves = $this->match->moves;
        foreach (['A', 'B', 'C'] as $column) {
            foreach ($moves->where('column', $column)->groupBy->player_id as $movesByPlayer) {
                if ($movesByPlayer->count() === 3) {
                    return $movesByPlayer->first()->player;
                }
            }
        }

        foreach (['1', '2', '3'] as $row) {
            foreach ($moves->where('row', $row)->groupBy->player_id as $movesByPlayer) {
                if ($movesByPlayer->count() === 3) {
                    return $movesByPlayer->first()->player;
                }
            }
        }

        foreach ([['A1', 'B2', 'C3'], ['C1', 'B2', 'A3']] as $crossShorthands) {
            $movesOnCross = collect($crossShorthands)->map(function ($tileShorthand) use ($moves) {
                $tile = Tile::fromShorthand($tileShorthand);
                return $moves->where('column', $tile->column())->where('row', (string)$tile->row())->first();
            })->filter();

            // Have a move on each of the three tiles, and all were done by the same player.
            if ($movesOnCross->count() === 3 && $movesOnCross->groupBy->player_id->count() === 1) {
                return $movesOnCross->first()->player;
            }
        }
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
