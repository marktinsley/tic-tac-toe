<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Match extends Model
{
    public const TYPE_VS_COMPUTER = 1;
    public const TYPE_VS_HUMAN = 2;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['ended_at'];

    /**
     * type_name accessor
     *
     * @return null|string
     */
    public function getTypeNameAttribute()
    {
        if ($this->type_key === self::TYPE_VS_COMPUTER) {
            return 'vs Computer';
        } else if ($this->type_key === self::TYPE_VS_HUMAN) {
            return 'vs Human';
        }

        return null;
    }

    /**
     * Tells you if the given tile has already been taken.
     *
     * @param Tile $tile
     *
     * @return bool
     */
    public function tileIsTaken(Tile $tile)
    {
        return $this->moves()->where('column', $tile->column())
            ->where('row', $tile->row())
            ->exists();
    }

    /**
     * Is is the given player's turn?
     *
     * @param User $player
     *
     * @return bool
     */
    public function isPlayersTurn(User $player)
    {
        $lastMove = $this->moves()->latest()->first();

        return !$lastMove || $lastMove->wasMadeBy($player);
    }

    /**
     * Is the match still in progress?
     *
     * @return bool
     */
    public function isInProgress()
    {
        return $this->ended_at === null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function player1()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Will be null if we're playing against the computer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function player2()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function winner()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Moves made in this match.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function moves()
    {
        return $this->hasMany(Move::class);
    }
}
