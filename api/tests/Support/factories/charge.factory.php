<?php

use App\Entity\Bank;
use App\Entity\Charge;
use App\Entity\ChargeType;
use App\Entity\User;
use League\FactoryMuffin\FactoryMuffin;
use League\FactoryMuffin\Faker\Facade as Faker;

/** @var FactoryMuffin $fm */
$fm->define(Charge::class)->setDefinitions([
    'name' => Faker::text(),
    'amount' => Faker::numberBetween(10,200),
    'state' => Faker::boolean(),
    'bank_id' => Bank::class,
    'charge_type_id' => ChargeType::class,
    'user_id' => User::class
]);