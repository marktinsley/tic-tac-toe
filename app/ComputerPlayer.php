<?php

namespace App;

class ComputerPlayer extends User
{
    public function __get($key)
    {
        return null;
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
        $tile = $match->openTiles()->first();

        if ($tile) {
            return $match->recordMove($tile, $this);
        }
    }
}
