<?php

namespace App\Controller\Admin;

use App\Entity\WonderfullBook;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class WonderfullBookCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return WonderfullBook::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextField::new('authorFirstName'),
            TextField::new('authorLastName'),
            IntegerField::new('rating'),
            BooleanField::new('isPublic'),
            TextField::new('price'),
            DateField::new('publishedDate'),
            TextField::new('genre'),
            TextField::new('publisher'),
            TextField::new('isbn'),
            AssociationField::new('fiercePublishers'),
        ];
    }

    // Configure search fields for the entity
    public function configureSearchFields(): iterable
    {
        return [
            'id',
            'title',
            'authorFirstName',
            'authorLastName',
            'rating',
            'price',
            'publishedDate',
            'genre',
            'publisher',
            'isbn',
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
