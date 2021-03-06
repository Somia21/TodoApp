<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Task;
use Faker\Generator as Faker;

$factory->define(Task::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence,
        'is_complete' => $faker->boolean,
        'deadline_utc' => $faker->dateTime(),
        'deadline_local' => $faker->dateTime(),
        'local_timezone' => 'Asia/Karachi'
    ];
});
