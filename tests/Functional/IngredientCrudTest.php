<?php

namespace App\Tests\Functional;

use App\Entity\Ingredient;
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
            "ingredient[name]" => 'un ingredient',
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

    public function testIfListIngredientIsSuccessfull() : void {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $user = $entityManager->find(User::class, 1);
        $client->loginUser($user);
        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('app_ingredients'));
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('app_ingredients');
    }

    public function testIfUpdateAnIngredientIsSuccessfull ():void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $user = $entityManager->find(User::class, 1);
        $client->loginUser($user);
        $ingredient = $entityManager->getRepository(Ingredient::class)->findOneBy([
            'user' => $user
        ]);
        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('app_ingredients_edit', ['id' => $ingredient->getId()]));
        $this->assertResponseIsSuccessful();

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
            'L\'ingredient a bien été modifié'
        );         
    }

    public function testIfDeletingIngredientSuccessfully() : void {
        
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $user = $entityManager->find(User::class, 1);
        $client->loginUser($user);
        $ingredient = $entityManager->getRepository(Ingredient::class)->findOneBy([
            'name' => "un ingredient"
        ]);

        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('app_ingredients_delete', ['id' => $ingredient->getId()]));
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();

        $this->assertRouteSame('app_ingredients');
        $this->assertSelectorTextContains(
            '.alert.alert-success', 
            'L\'ingredient a bien été supprimé'
        );  
    }
}
