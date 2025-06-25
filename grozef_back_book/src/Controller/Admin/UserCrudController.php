<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('email'),
            TextField::new('password'),
            BooleanField::new('isActive'),
            AssociationField::new('userInfo')
                ->setFormTypeOption('by_reference', false)
                ->setFormTypeOption('attr', ['class' => 'user-info-field']),
        ];
    }

    // Configure search fields for the entity, including fields from UserInfo
    public function configureSearchFields(): iterable
    {
        return [
            'id',
            'email',
            'isActive',
            'userInfo.firstName',
            'userInfo.lastName',
            'userInfo.address',
            'userInfo.tel',
            'userInfo.postalCode',
            'userInfo.country',
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
