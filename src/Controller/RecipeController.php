<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecipeController extends AbstractController
{
    /**
     *This controller display all recipes
     * @param IngredientRepository $ingredientRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    /**
     * this controller display all recipes
     *
     * @param RecipeRepository $recipeRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/recette', name: 'app_recipe', methods: ['GET'])]
    public function index(RecipeRepository $recipeRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $recipes = $paginator->paginate(
            $recipeRepository->findAll(),
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes
        ]);
    }

    /**
     * this controller adds news recipes
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/recette/creation', name: 'app_recipe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash('success', 'La recette a bien été ajoutée');
            return $this->redirectToRoute('app_recipe');
        }

        return $this->render('pages/recipe/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

     /**
     * This controller updates recipes
     *
     * @param Recipe $recipe
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return response
     */
    #[Route('recette/edit/{id}', name: 'app_recipe_edit', methods: ['GET', 'POST'])]
    public function edit(Recipe $recipe, EntityManagerInterface $manager, Request $request): response
    {
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash('success', 'La recette a bien été modifiée');
            return $this->redirectToRoute('app_recipe');
        }
        return $this->render('pages/recipe/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

      /**
     * this controller deletes recipes
     *
     * @param Recipe $ingredient
     * @param EntityManagerInterface $manager
     * @return response
     */
    #[Route('recette/supp/{id}', name: 'app_recipe_delete', methods: ['GET'])]
    public function delete(Recipe $recipe, EntityManagerInterface $manager): response
    {
        if (!$recipe) {
            $this->addFlash('warning', 'L\'ingredient n\'existe pas');
        } else {
            $manager->remove($recipe);
            $manager->flush();
            $this->addFlash('success', 'La recette a bien été supprimée');
        }
        return $this->redirectToRoute('app_recipe');
    }
}
