<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomePageTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $button = $crawler->filter('.btn.btn-primary.btn-lg');
        $this->assertEquals(2, count($button));
        $recipes = $crawler->filter('.card.text-white.bg-primary');
        $this->assertEquals(3, count($recipes));

        $this->assertSelectorTextContains('h1', 'Bienvenue sur Recettes pour tous');
    }
}
