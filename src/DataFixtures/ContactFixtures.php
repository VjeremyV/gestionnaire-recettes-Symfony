<?php

namespace App\DataFixtures;

use App\Entity\Contact;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ContactFixtures extends Fixture
{
    /**
     *
     * @var Generator
     */
    private $faker;
    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }
    public function load(ObjectManager $manager): void
    {
     for($i = 0; $i<5; $i++){
        $contact = new Contact();
        $contact->setFullname($this->faker->name())
        ->setEmail($this->faker->email())
        ->setSubject('Demande : '. $i+1)
        ->setMessage($this->faker->text());
        $manager->persist($contact);

     }

        $manager->flush();
    }
}
