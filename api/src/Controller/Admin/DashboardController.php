<?php

namespace App\Controller\Admin;

use App\Entity\Bank;
use App\Entity\Charge;
use App\Entity\ChargeType;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
         $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
         return $this->redirect($adminUrlGenerator->setController(BankCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Administration EasyCharge');
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linkToCrud('Banks', 'fa fa-landmark', Bank::class),
            MenuItem::linkToCrud('ChargeTypes', 'fa fa-tags', ChargeType::class),
            MenuItem::linkToCrud('Users', 'fa fa-user', User::class),
        ];
    }
}
