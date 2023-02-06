<?php

use App\Entity\User;
use League\FactoryMuffin\FactoryMuffin;
use League\FactoryMuffin\Faker\Facade as Faker;

/** @var FactoryMuffin $fm */
$fm->define(User::class)->setDefinitions([
    'username' => Faker::name(),
    'plainPassword' => Faker::password(),
    'roles' => [],
]);