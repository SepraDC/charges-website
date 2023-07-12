<?php

namespace App\Controller\Admin;

use App\Entity\ChargeType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ChargeTypeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ChargeType::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
