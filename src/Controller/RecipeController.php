<?php

namespace App\Controller;

use App\Entity\Mark;
use App\Entity\Recipe;
use App\Entity\User;
use App\Form\MarkType;
use App\Form\RecipeType;
use App\Repository\MarkRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;


class RecipeController extends AbstractController
{

    /**
     * this controller display all recipes
     *
     * @param RecipeRepository $recipeRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/recette', name: 'app_recipe', methods: ['GET'])]
    public function index(RecipeRepository $recipeRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $recipes = $paginator->paginate(
            $recipeRepository->findBy(['user' => $this->getUser()]),
            $request->query->getInt('page', 1),
            12
        );
        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes
        ]);
    }

    /**
     * this controller allow us to see a recipe wich is public
     *
     * @return Response
     */
    #[Route('/recette/publique', name: 'app_recipe_public', methods: ['GET'])]
    public function indexPublic(PaginatorInterface $paginator, RecipeRepository $recipeRepository, Request $request): Response
    {
        $recipes = $paginator->paginate(
            $recipeRepository->findPublicRecipes(),
            $request->query->getInt('page', 1),
            12
        );
        return $this->render('pages/recipe/indexPublic.html.twig', [
            'recipes' => $recipes
        ]);
    }

    #[Route('/recette/{id}', name: 'app_recipe_show', methods: ['GET', 'POST'])]
    public function show(Recipe $recipe, Request $request, MarkRepository $markRepository, EntityManagerInterface $manager): Response
    {
        if (!$recipe->isIsPublic()) {
            $this->addFlash('warning', 'La recette que vous essayez de consulter n\'est pas publique');
            return $this->redirectToRoute('app_recipe');
        }

        $mark = new Mark();
        $form = $this->createForm(MarkType::class, $mark);
        $form->handleRequest($request);
        $existingMark = $markRepository->findOneBy([
            'user' => $this->getUser(),
            'recipe' => $recipe
        ]);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$existingMark) {
                $mark->setUser($this->getUser())->setRecipe($recipe);
                $manager->persist($mark);
                $this->addFlash('success', 'Merci d\'avoir noter cette recette');

            } else {
                $existingMark->setMark($form['mark']->getData());
                // $manager->persist($existingMark);
                $this->addFlash('success', 'La modification de votre note a bien été prise en compte');
            }
            $manager->flush();
            return $this->redirectToRoute('app_recipe_show', ['id' => $recipe->getId()]);
        }
        return $this->render('pages/recipe/show.html.twig', [
            'recipe' => $recipe,
            'form' => $form->createView(),
            'existing_mark' => $existingMark
        ]);
    }
    /**
     * this controller adds news recipes
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/recette/creation', name: 'app_recipe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
            $recipe->setUser($this->getUser());;
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
    #[IsGranted('ROLE_USER')]
    #[Route('recette/edit/{id}', name: 'app_recipe_edit', methods: ['GET', 'POST'])]
    public function edit(#[CurrentUser] ?User $user, Recipe $recipe, EntityManagerInterface $manager, Request $request): response
    {
        if ($recipe->getUser() != $user) {
            $this->addFlash('warning', 'La recette que vous essayez de modifier ne vous appartient pas');
            return $this->redirectToRoute('app_recipe');
        }
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
    #[IsGranted('ROLE_USER')]
    #[Route('recette/supp/{id}', name: 'app_recipe_delete', methods: ['GET'])]
    public function delete(#[CurrentUser] ?User $user, Recipe $recipe, EntityManagerInterface $manager): response
    {
        if ($recipe->getUser() != $user) {
            $this->addFlash('warning', 'La recette que vous essayez de supprimer ne vous appartient pas');
            return $this->redirectToRoute('app_recipe');
        }
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
