<?php

namespace App\Controller;

use App\Repository\BankRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\User;
class BanksByUserController extends AbstractController
{
    public function __construct(protected BankRepository $repository, private readonly Security $security) {
    }

    public function __invoke(): ?array
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return null;
        }
        return $this->repository->findByUser($user);
    }
}