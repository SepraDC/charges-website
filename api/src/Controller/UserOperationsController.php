<?php

namespace App\Controller;

use App\Entity\Charge;
use App\Repository\ChargeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserOperationsController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em, private ChargeRepository $chargeRepository, private UserRepository $userRepository)
    {
    }

    public function __invoke()
    {
        return $this->chargeRepository->findAll();
    }
}