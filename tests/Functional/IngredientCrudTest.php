<?php

namespace App\Tests\Functional;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IngredientCrudTest extends WebTestCase
{
    public function testIfCreatingIngredientSuccessfully(): void
    {
        $client = static::createClient();

        $urlGenerator = $client->getContainer()->get('router');

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $user = $entityManager->find(User::class, 1);
        $client->loginUser($user);

        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('app_ingredients_new'));
        $form = $crawler->filter('form[name=ingredient]')
        ->form([
            "ingredient[name]" => 'ingredient 1',
            "ingredient[price]" => floatval(33)
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();

        $this->assertRouteSame('app_ingredients');
        $this->assertSelectorTextContains(
            '.alert.alert-success', 
            'L\'ingredient a bien été ajouté'
        );
         
    }
}
