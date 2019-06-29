<?php

namespace App\Rules;

use App\Match;
use App\User;
use Illuminate\Contracts\Validation\Rule;

class IsParticipant implements Rule
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
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $this->match->isParticipant(User::findOrFail($value));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'You are not part of this match.';
    }
}
