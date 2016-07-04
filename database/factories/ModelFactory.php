<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->safeEmail,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Entities\Client::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name
    ];
});

$factory->define(App\Entities\Product::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->sentence(random_int(1, 4)),
        'price' => random_int(10, 1000),
        'currency' => 'USD'
    ];
});

$factory->define(App\Entities\Order::class, function (Faker\Generator $faker) {

    $client = factory(App\Entities\Client::class)->create();

    $product = factory(App\Entities\Product::class)->create();

    return [
        'client_id' => $client->id,
        'product_id' => $product->id,
        'total_price' => $product->price,
        'currency' => 'USD'
    ];

});