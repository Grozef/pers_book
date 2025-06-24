<?php

namespace App\Controller\Admin;

use App\Entity\FiercePublisher;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class FiercePublisherCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FiercePublisher::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('name'),
            TextField::new('address'),
            TextField::new('tel'),
            TextField::new('mail'),
            TextField::new('postalCode'),
            TextField::new('country'),
            AssociationField::new('astonishingVideos'),
            AssociationField::new('stunningImages'),
            AssociationField::new('wonderfullBooks'),
        ];
    }

    // Configure search fields for the entity
    public function configureSearchFields(): iterable
    {
        return [
            'id',
            'name',
            'address',
            'tel',
            'mail',
            'postalCode',
            'country',
        ];
    }

    // Configure pagination for the entity
    public function configurePagination(): array
    {
        return [
            'pageSize' => 8, // Number of items per page
        ];
    }
}
