<?php

namespace App\Controller;

use App\Entity\Charge;
use App\Repository\ChargeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Bundle\SecurityBundle\Security;

#[AsController]
class ResetChargesController extends AbstractController
{
    public function __construct(
        protected ChargeRepository $repo,
        private readonly Security $security,
    ) {}

    public function __invoke(): Response
    {
        $response = new Response();
        $user = $this->security->getUser();
        try {
            $this->repo->resetByUser($user);
            $response->setStatusCode(Response::HTTP_OK);
        } catch (Exception $e) {
            $response->setContent($e->getMessage());
        }

        return $response;
    }
}
