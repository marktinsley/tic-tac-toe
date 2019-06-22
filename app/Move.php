<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

class Move extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Was this move made by the given player?
     *
     * @param User $player
     *
     * @return bool
     */
    public function wasMadeBy(User $player)
    {
        return !$this->wasMadeByComputer() && $player->id === $this->player_id;
    }

    /**
     * Was this move made by the computer?
     *
     * @return bool
     */
    public function wasMadeByComputer()
    {
        return $this->player_id === null;
    }
}
