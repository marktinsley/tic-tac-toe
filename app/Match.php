<?php

namespace App;

use App\Events\MatchEnded;
use App\Events\MoveRecorded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

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
        return $this->moves()->onTile($tile)->exists();
    }

    /**
     * Gives you the tiles that are not taken yet.
     *
     * @return \Illuminate\Support\Collection
     */
    public function openTiles()
    {
        return collect([
            'A1', 'B1', 'C1',
            'A2', 'B2', 'C2',
            'A3', 'B3', 'C3',
        ])->map(function ($shorthand) {
            return Tile::fromShorthand($shorthand);
        })->reject(function (Tile $tile) {
            return $this->tileIsTaken($tile);
        });
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
        $lastMove = $this->moves()->reverseOrder()->first();

        return !$lastMove || !$lastMove->wasMadeBy($player);
    }

    /**
     * Is this match against the computer?
     *
     * @return bool
     */
    public function isVsComputer()
    {
        return $this->type_key == self::TYPE_VS_COMPUTER;
    }

    /**
     * Record a move on the board.
     *
     * @param Tile $tile
     * @param User $player
     *
     * @return Move
     */
    public function recordMove(Tile $tile, User $player)
    {
        $move = $this->moves()->create([
            'player_id' => $player->id,
            'column' => $tile->column(),
            'row' => $tile->row(),
        ]);

        MoveRecorded::dispatch($move);

        $this->closeIfDone();

        if ($this->isVsComputer() && !$this->refresh()->hasEnded() && !($player instanceof ComputerPlayer)) {
            ComputerPlayer::getInstance()->makeMove($this);
        }

        $this->closeIfDone();

        return $move;
    }

    /**
     * Closes the match if it's done.
     */
    public function closeIfDone()
    {
        if ($this->hasEnded()) {
            return;
        }

        $winner = (new MatchReferee($this))->lookForWinner();

        if ($winner) {
            $this->update(['winner_id' => $winner->id, 'ended_at' => now()]);
            MatchEnded::dispatch($this);
            return;
        }

        if ($this->openTiles()->isEmpty()) {
            $this->update(['ended_at' => now()]);
            MatchEnded::dispatch($this);
            return;
        }
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
     * Is the match done?
     *
     * @return bool
     */
    public function hasEnded()
    {
        return !$this->isInProgress();
    }

    /**
     * Filters down to only matches that are currently in progress.
     *
     * @param Builder $query
     */
    public function scopeInProgress(Builder $query)
    {
        $query->whereNull('ended_at');
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
