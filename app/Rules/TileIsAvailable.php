<?php

namespace App\Rules;

use App\Match;
use App\Tile;
use Illuminate\Contracts\Validation\Rule;

class TileIsAvailable implements Rule
{
    /**
     * @var Match
     */
    private $match;

    /**
     * Create a new rule instance.
     *
     * @param Match $match
     */
    public function __construct(Match $match)
    {
        $this->match = $match;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return !$this->match->tileIsTaken(Tile::fromShorthand($value));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Tile already has a mark on it.';
    }
}
