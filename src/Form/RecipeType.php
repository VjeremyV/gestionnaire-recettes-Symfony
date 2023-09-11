<?php

namespace App\Form;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Repository\IngredientRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class RecipeType extends AbstractType
{
    private $token;
    
    public function __construct(TokenStorageInterface $token){
        $this->token = $token;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlenght' => 2,
                    'maxlenght' => 50
                ],
                'label' => 'Nom',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
            ])
            ->add('time', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                    'max' => 1440
                ],
                'label' => 'Temps (en minutes)',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ]
            ])
            ->add('nbPeople', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                    'max' => 50
                ],
                'label' => 'Nombre de personnes',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'required' => false,
            ])
            ->add('difficulty', RangeType::class, [
                'attr' => [
                    'class' => 'form-range',
                    'min' => 1,
                    'max' => 5
                ],
                'label' => 'Difficulté',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ]
            ])
            ->add('description', CKEditorType::class,  [
                'config' => [
                    'language' => 'fr'
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'Description',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ]
            ])
            
            ->add('price', MoneyType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'Prix',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'required' => false,
            ])
            ->add('isFavorite', CheckboxType::class, [
                'attr' => [
                    'class' => '',
                ],
                'required' => false,
                'label' => 'Mettre en favoris ?',
                'label_attr' => [
                    'class' => 'form-check-label'
                ],
            ])
            ->add('isPublic', CheckboxType::class, [
                'attr' => [
                    'class' => '',
                ],
                'required' => false,
                'label' => 'Mettre en publique ?',
                'label_attr' => [
                    'class' => 'form-check-label'
                ],
            ])
            ->add('imageFile', VichImageType::class, [
                'attr' => [
                    'class' => '',
                ],
                'label' => 'image de la recette',
                'label_attr' => [
                    'class' => 'form-label mt-4'],
                    'required' => false,
                    'allow_delete' => false,
                    'download_uri' => false,
            ])
            ->add('ingredients', EntityType::class, [
                'label' => 'Les ingrédients',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'class' => Ingredient::class,
                'query_builder' => function (IngredientRepository $ingredientRepository) {
                    return $ingredientRepository->createQueryBuilder('i')
                    ->where('i.user = :user')    
                    ->orderBy('i.name', 'ASC')
                    ->setParameter('user', $this->token->getToken()->getUser());
                },
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary mt-4'
                ],
                'label' => 'Ajouter ma recette'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
