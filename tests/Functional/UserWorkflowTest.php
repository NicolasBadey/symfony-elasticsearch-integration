<?php

/*
 * This file is part of the elasticsearch-etl-integration package.
 * (c) Nicolas Badey https://www.linkedin.com/in/nicolasbadey
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Functional;

use Symfony\Component\Panther\PantherTestCase;

class UserWorkflowTest extends PantherTestCase
{
    public function testHome()
    {
        $client = static::createPantherClient();
        $crawler = $client->request('GET', '/');

        $this->assertStringContainsString('Welcom', $crawler->html());
    }

    public function testRegister()
    {
        $client = static::createPantherClient();
        $crawler = $client->request('GET', '/register');

        $this->assertStringContainsString('Register', $crawler->html());

        $form = $crawler->filter('form[name=registration_form]')->form([
            'registration_form[username]' => 'test@test.com',
            'registration_form[plainPassword]' => 'Password42',
        ]);
        $crawler = $client->submit($form);

        $this->assertStringContainsString('Welcome test@test.com', $crawler->html());
    }

    public function testLogout()
    {
        $client = static::createPantherClient();
        $crawler = $client->request('GET', '/logout');

        $this->assertStringNotContainsString('Welcome test@test.com', $crawler->html());
        $this->assertStringContainsString('Welcome', $crawler->html());
    }

    public function testLogin()
    {
        $client = static::createPantherClient();

        $crawler = $client->request('GET', '/login');

        $this->assertStringContainsString('Please sign in', $crawler->html());

        $form = $crawler->filter('#login')->form([
            'username' => 'test@test.com',
            'password' => 'Password42',
        ]);
        $crawler = $client->submit($form);

        $this->assertStringContainsString('Welcome test@test.com', $crawler->html());
    }
}
