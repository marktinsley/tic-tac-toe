<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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
        return !$this->wasMadeByComputer() && $player->id == $this->player_id;
    }

    /**
     * Was this move made by the computer?
     *
     * @return bool
     */
    public function wasMadeByComputer()
    {
        return $this->player_id == ComputerPlayer::getInstance()->id;
    }

    /**
     * Orders the moves in the sequence they were made.
     *
     * @param Builder $query
     */
    public function scopeInOrder(Builder $query)
    {
        $query->orderBy('id');
    }

    /**
     * Orders the moves in reverse of the sequence they were made.
     *
     * @param Builder $query
     */
    public function scopeReverseOrder(Builder $query)
    {
        $query->orderByDesc('id');
    }

    /**
     * Filters query down to only moves on the given tile.
     *
     * @param Builder $query
     * @param Tile $tile
     */
    public function scopeOnTile(Builder $query, Tile $tile)
    {
        $query->where('column', $tile->column())
            ->where('row', $tile->row());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function match()
    {
        return $this->belongsTo(Match::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function player()
    {
        return $this->belongsTo(User::class);
    }
}
