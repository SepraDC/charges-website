<?php

namespace App\Controller\Admin;

use App\Entity\Bank;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Vich\UploaderBundle\Storage\StorageInterface;

class BankCrudController extends AbstractCrudController
{
    public function __construct(private StorageInterface $storage)
    {
    }

    public static function getEntityFqcn(): string
    {
        return Bank::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            TextField::new('abbreviation'),
            TextField::new('imageFile')
                ->setFormType(VichImageType::class)
                ->setFormTypeOptions([
                    'download_uri' => function ($entity) {
                        $uri = $this->storage->resolveUri($entity, 'imageFile');
                        if (!$uri) {
                            return null;
                        }

                    },
                    'allow_delete' => false,
                    'attr' => ['accept' => 'image/* ']
                ])
                ->onlyOnForms()
        ];
    }
}
