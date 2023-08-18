<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserPasswordType;
use App\Form\UserType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/utilisateur/modifier/{id}', name: 'app_user_edit', methods:['GET', 'POST'])]
    public function index(User $user, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_security');
        }
        if($this->getUser() != $user){
            return $this->redirectToRoute('app_recipe');
        }

        $form= $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            if($hasher->isPasswordValid($user, $form->getData()->getPlainPassword())){

                $user = $form->getData();
                $manager->persist($user);
                $manager->flush();
    
                $this->addFlash('success', 'Vos informations ont bien été modifiées');
            } else {
                $this->addFlash('warning', 'le mot de passe renseigné est incorrect');

            }

        }
        return $this->render('pages/user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    #[Route('/utilisateur/modifier/mot-de-passe/{id}', name: 'app_user_edit_pwd', methods:['GET', 'POST'])]
    public function editPwd(User $user, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher) : response {

        $form= $this->createForm(UserPasswordType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if($hasher->isPasswordValid($user, $form->getData()['plainPassword'])){
                $user->setUpdatedAt(new DateTimeImmutable());
                $user->setPlainPassword($form->getData()['newPassword']);
                
                $manager->persist($user);
                $manager->flush();
    
                $this->addFlash('success', 'Le mot de passe a bien été modifié');
            } else {
                $this->addFlash('warning', 'le mot de passe renseigné est incorrect');

            }

        }

        return $this->render('pages/user/edit_pwd.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
