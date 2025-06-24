<?php

namespace App\Controller\Admin;

use App\Entity\UserInfo;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserInfoCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return UserInfo::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('firstName'),
            TextField::new('lastName'),
            TextField::new('address'),
            TextField::new('tel'),
            TextField::new('postalCode'),
            TextField::new('country'),
        ];
    }

    // Configure search fields for the entity
    public function configureSearchFields(): iterable
    {
        return [
            'id',
            'firstName',
            'lastName',
            'address',
            'tel',
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
