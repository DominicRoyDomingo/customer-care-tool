<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\JobCategory;
use Faker\Generator as Faker;

$factory->define(JobCategory::class, function (Faker $faker) {
   return [
      'category' => $faker->jobTitle,
   ];
});
