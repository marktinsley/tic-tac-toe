<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Move;
use Faker\Generator as Faker;

$factory->define(Move::class, function (Faker $faker) {
    return [
        'match_id' => function () {
            return factory(\App\Match::class)->create()->id;
        },
        'player_id' => function () {
            return factory(\App\User::class)->create()->id;
        },
        'column' => $faker->randomElement(['A', 'B', 'C']),
        'row' => $faker->numberBetween(1, 9),
    ];
});
