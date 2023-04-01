<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;

class AuthenticationTest extends ApiTestCase
{
    public function testLogin(): void
    {
        $client = self::createClient();
        $container = self::getContainer();

        $user = new User();
        $user->setUsername('usertest');
        $user->setPassword(
            $container->get('security.user_password_hasher')->hashPassword($user, '$3CR3T')
        );

        $manager = $container->get('doctrine')->getManager();
        $manager->persist($user);
        $manager->flush();

        // retrieve a token
        $response = $client->request('POST', '/auth', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'username' => 'usertest',
                'password' => '$3CR3T',
            ],
        ]);

        $json = $response->toArray();
        self::assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $json);

        // test not authorized
        $client->request('GET', '/greetings');
        self::assertResponseStatusCodeSame(401);

        // test authorized
        $client->request('GET', '/greetings', ['auth_bearer' => $json['token']]);
        self::assertResponseIsSuccessful();
    }
}