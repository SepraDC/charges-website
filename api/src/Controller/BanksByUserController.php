<?php

namespace App\Controller;

use App\Repository\BankRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;

class BanksByUserController extends AbstractController
{
    public function __construct(protected BankRepository $repository, private readonly Security $security) {
    }

    public function __invoke(): ?array
    {
        return $this->repository->findByUser($this->security->getUser());
    }


}