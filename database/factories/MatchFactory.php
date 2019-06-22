<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Match;
use Faker\Generator as Faker;

$factory->define(Match::class, function (Faker $faker) {
    return [
        'type_key' => Match::TYPE_VS_COMPUTER,
        'player1_id' => function () {
            return factory(\App\User::class)->create()->id;
        },
        'player2_id' => null,
        'winner_id' => null,
        'ended_at' => null
    ];
});
