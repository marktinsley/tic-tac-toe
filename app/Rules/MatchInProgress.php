<?php

namespace App\Rules;

use App\Match;
use Illuminate\Contracts\Validation\Rule;

class MatchInProgress implements Rule
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
        return Match::find($value)->isInProgress();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'This match has ended.';
    }
}
