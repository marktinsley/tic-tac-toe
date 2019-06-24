<?php

namespace App;

class ComputerPlayer extends User
{
    public const EMAIL = 'computer@tic-tac-toe.marknjen.com';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Gives you an instance of the computer player.
     *
     * @return ComputerPlayer
     */
    public static function getInstance()
    {
        return ComputerPlayer::where('email', self::EMAIL)->firstOrFail();
    }

    /**
     * Generate the computer player in the DB.
     */
    public static function generate()
    {
        User::create([
            'name' => 'Computer Player',
            'email' => self::EMAIL,
            'password' => bcrypt(\Illuminate\Support\Str::random(30))
        ]);
    }

    /**
     * Make a move in the given match.
     *
     * @param Match $match
     *
     * @return Move
     */
    public function makeMove(Match $match)
    {
        $tile = $match->openTiles()->random();

        if ($tile) {
            return $match->recordMove($tile, $this);
        }
    }
}
