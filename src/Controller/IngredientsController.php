<?php

namespace App\Controller;

use App\Repository\IngredientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class IngredientsController extends AbstractController
{
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
}
