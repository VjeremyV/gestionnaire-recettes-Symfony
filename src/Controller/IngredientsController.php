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
    public function index(IngredientRepository $ingredientRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $ingredients = $paginator->paginate(
            $ingredientRepository->findAll(),
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('pages/ingredients/index.html.twig', [
            'ingredients' => $ingredients
        ]);
    }

    #[Route('/ingredients/new', name: 'app_ingredients_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager): response
    {

        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();
            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash('success', 'L\'ingredient a bien été ajouté');
            return $this->redirectToRoute('app_ingredients');
        }
        return $this->render('pages/ingredients/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('ingredients/edit/{id}', name: 'app_ingredients_edit', methods: ['GET', 'POST'])]
    public function edit(Ingredient $ingredient, EntityManagerInterface $manager, Request $request): response
    {
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
            'form' => $form->createView()
        ]);
    }

    #[Route('ingredients/supp/{id}', name: 'app_ingredients_delete', methods: ['GET'])]
    public function delete(Ingredient $ingredient, EntityManagerInterface $manager): response
    {
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
