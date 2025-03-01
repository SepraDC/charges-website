<?php

namespace App\Controller;

use App\Entity\Bank;
use App\Repository\BankRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;

class BanksByUserController extends AbstractController
{
    public function __construct(protected BankRepository $repository, private readonly Security $security) {
    }

    /** @return Bank[]|null */
    public function __invoke(): ?array
    {
        return $this->repository->findByUser($this->security->getUser());
    }
}