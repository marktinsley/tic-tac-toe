<?php

namespace App\Rules;

use App\Tile;
use Illuminate\Contracts\Validation\Rule;

class IsTile implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return Tile::isValidShorthand($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Not a valid tile.';
    }
}
