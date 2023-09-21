<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LoginTest extends WebTestCase
{
    public function testIfLoginIsSuccesfull(): void
    {
        $client = static::createClient();
        // get route bu URL generator
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get('router');

        $crawler = $client->request('GET', $urlGenerator->generate('app_security'));
        // Form

        $form = $crawler->filter('form[name=login]')->form([
            "_username" => 'admin@recette-facile.fr',
            "_password" => "password"
        ]);
        $client->submit($form);

        // Redirect + home 
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertRouteSame('app_home');

    }
    public function testIfLoginWrong(){
        $client = static::createClient();
        
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get('router');

        $crawler = $client->request('GET', $urlGenerator->generate('app_security'));

        $form = $crawler->filter('form[name=login]')->form([
            "_username" => 'admin@recette-facile.fr',
            "_password" => "wrong_paswword"
        ]);
        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertRouteSame('app_security');
        $this->assertSelectorTextContains(
            '.alert.alert-danger', 
            'Invalid credentials.'
        );

    }
}
