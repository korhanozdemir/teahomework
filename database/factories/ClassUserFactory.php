<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\ClassUser;
use Faker\Generator as Faker;

$factory->define(ClassUser::class, function (Faker $faker) {
    return [
        'class_id' => mt_rand(1,69),
        'user_id' => $faker->unique(true)->numberBetween(3,1003),
    ];
});
