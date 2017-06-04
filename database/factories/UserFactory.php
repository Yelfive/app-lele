<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-05-14
 */


/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
    $mobile = time() . rand(0, 9);
    return [
        'name' => $faker->name,
        'password' => $faker->password,
        'mobile' => $mobile,
        'idcard' => '51012219' . sprintf('%010d', $faker->numberBetween()),
        'address' => $faker->address,
    ];
});