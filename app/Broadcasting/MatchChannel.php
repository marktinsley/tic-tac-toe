<?php

namespace App\Broadcasting;

use App\Match;
use Illuminate\Broadcasting\Channel;

class MatchChannel extends Channel
{
    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct(Match $match)
    {
        parent::__construct("App.Match.{$match->id}");
    }
}
