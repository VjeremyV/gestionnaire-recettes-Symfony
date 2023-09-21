<?php

namespace App\Tests\functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ContactTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Envoyez nous un message !');

        //Récuperer le formulaire
        $submitBtn = $crawler->selectButton('Envoyer votre message');
        $form = $submitBtn->form();

        $form['contact[fullname]'] = 'Jean Test beaucoup';
        $form['contact[email]'] = 'jean.test@beaucoup.com';
        $form['contact[subject]'] = 'Test du formulaire';
        $form['contact[message]'] = 'On envoie un message de test';

        //soumettre le formulaire
        $client->submit($form);

        //Vérifier le statut HTTP
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        //vérifier l'envoi du mail
        $this->assertEmailCount(0);
        $client->followRedirect();

        //Vérifier la présence du message de succés
        $this->assertSelectorTextContains(
            '.alert.alert-success', 
            'Votre mail a bien été soumis'
        );
    }
}
