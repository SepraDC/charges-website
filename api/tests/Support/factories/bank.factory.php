<?php

use App\Entity\Bank;
use League\FactoryMuffin\FactoryMuffin;
use League\FactoryMuffin\Faker\Facade as Faker;

/** @var FactoryMuffin $fm */
$fm->define(Bank::class)->setDefinitions([
    'name' => Faker::text(),
    'abbreviation' => Faker::numberBetween(10, 99),
]);