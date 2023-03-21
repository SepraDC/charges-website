<?php

namespace App\Controller;

use App\Entity\Charge;
use App\Repository\ChargeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserOperationsController extends AbstractController
{

    public function __construct(private readonly EntityManagerInterface $em, private readonly ChargeRepository $chargeRepository, private readonly UserRepository $userRepository)
    {
    }

    public function __invoke()
    {
        return $this->chargeRepository->findAll();
    }
}