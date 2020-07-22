<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'code' => $faker->unique()->numberBetween(1000,2000),
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'role' => 3,
        'password' => '$2y$10$KCHcUzoc2AODmR7TrmscMuVWSI2BNilVKV2SylnyYhVyNv9GN2cuq', // password
    ];
});
