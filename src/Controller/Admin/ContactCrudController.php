<?php

namespace App\Controller\Admin;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use App\Entity\Contact;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class ContactCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Contact::class;
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Demande de contact')
            ->setEntityLabelInPlural('Demandes de contact')
            ->setPageTitle('index', 'Administration des demandes de contact')
            ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            DateTimeField::new('createdAt')
                ->setFormTypeOptions(['disabled' => 'disabled']),
            TextField::new('email')
                ->setFormTypeOptions(['disabled' => 'disabled']),
            TextField::new('fullName'),

            TextareaField::new('message')
                ->setFormType(CKEditorType::class)
                ->hideOnIndex(),
        ];
    }
}
