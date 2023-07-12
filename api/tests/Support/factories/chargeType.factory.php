<?php

use \App\Entity\ChargeType;
use League\FactoryMuffin\FactoryMuffin;
use League\FactoryMuffin\Faker\Facade as Faker;

/** @var FactoryMuffin $fm */
$fm->define(ChargeType::class)->setDefinitions([
    'name' => Faker::text(),
]);