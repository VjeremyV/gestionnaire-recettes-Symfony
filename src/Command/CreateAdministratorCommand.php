<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-administrator',
    description: 'Create an administrator',
)]
class CreateAdministratorCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct('app:create-administrator');
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('full_name', InputArgument::OPTIONAL, 'full name')
            ->addArgument('pseudo', InputArgument::OPTIONAL, 'pseudo')
            ->addArgument('email', InputArgument::OPTIONAL, 'email')
            ->addArgument('password', InputArgument::OPTIONAL, 'Password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $io = new SymfonyStyle($input, $output);
        $full_name = $input->getArgument('full_name');
        if(!$full_name){
            $question = new Question('Quelle est le nom de l\'administrateur ?');
            $full_name = $helper->ask($input, $output, $question);
        }

        $email = $input->getArgument('email');
        if(!$email){
            $question = new Question('Quelle est l\'email de l\'administrateur ?');
            $email = $helper->ask($input, $output, $question);
        }

        $password = $input->getArgument('password');
        if(!$password){
            $question = new Question('Quelle est le mot de passe de l\'administrateur ?');
            $password = $helper->ask($input, $output, $question);
        }

        $pseudo = $input->getArgument('pseudo');
        if(!$pseudo){
            $question = new Question('Quelle est le pseudo de l\'administrateur ?');
            $pseudo = $helper->ask($input, $output, $question);
        }

        $user = (new User())->setFullName($full_name)
            ->setEmail($email)
            ->setPlainPassword($password)
            ->setPseudo($pseudo)
            ->setRoles(['ROLE_USER', 'ROLE_ADMIN']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('Le compte administrateur a bien été créé');

        return Command::SUCCESS;
    }
}
