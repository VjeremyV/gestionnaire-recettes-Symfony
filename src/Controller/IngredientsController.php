<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class IngredientsController extends AbstractController
{
    /**
     *This controller display all ingredients
     * @param IngredientRepository $ingredientRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */

    #[Route('/ingredients', name: 'app_ingredients', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(IngredientRepository $ingredientRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $ingredients = $paginator->paginate(
            $ingredientRepository->findBy(['user' => $this->getUser()]),
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('pages/ingredients/index.html.twig', [
            'ingredients' => $ingredients
        ]);
    }

    /**
     * this controller adds news ingredients
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return response
     */
    #[Route('/ingredients/new', name: 'app_ingredients_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $manager): response
    {

        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();
            $ingredient->setUser($this->getUser());
            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash('success', 'L\'ingredient a bien été ajouté');
            return $this->redirectToRoute('app_ingredients');
        }
        return $this->render('pages/ingredients/new.html.twig', [
            'form' => $form->createView()
        ]);
    }



    /**
     * This controller updates ingredients
     *
     * @param Ingredient $ingredient
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return response
     */
    #[IsGranted('ROLE_USER')]
    #[Route('ingredients/edit/{id}', name: 'app_ingredients_edit', methods: ['GET', 'POST'])]
    public function edit(#[CurrentUser] ?User $user, Ingredient $ingredient, EntityManagerInterface $manager, Request $request): response
    {
        if ($ingredient->getUser() != $user) {
            $this->addFlash('warning', 'L\'ingredient que vous essayez de modifier ne vous appartient pas');
            return $this->redirectToRoute('app_ingredients');
                   }
        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();
            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash('success', 'L\'ingredient a bien été modifié');
            return $this->redirectToRoute('app_ingredients');
        }
        return $this->render('pages/ingredients/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * this controller deletes ingredients
     *
     * @param Ingredient $ingredient
     * @param EntityManagerInterface $manager
     * @return response
     */
    #[IsGranted('ROLE_USER')]
    #[Route('ingredients/supp/{id}', name: 'app_ingredients_delete', methods: ['GET'])]
    public function delete(#[CurrentUser] ?User $user,Ingredient $ingredient, EntityManagerInterface $manager): response
    {
        if ($ingredient->getUser() != $user) {
            $this->addFlash('warning', 'L\'ingredient que vous essayez de supprimer ne vous appartient pas');
            return $this->redirectToRoute('app_ingredients');
                   }
        if (!$ingredient) {
            $this->addFlash('warning', 'L\'ingredient n\'existe pas');
        } else {
            $manager->remove($ingredient);
            $manager->flush();
            $this->addFlash('success', 'L\'ingredient a bien été supprimé');
        }
        return $this->redirectToRoute('app_ingredients');
    }
}
