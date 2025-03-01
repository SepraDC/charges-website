<?php

namespace App\Controller;

use App\Entity\Charge;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Bundle\SecurityBundle\Security;

#[AsController]
class ResetChargesController extends AbstractController
{
    public function __construct(protected EntityManagerInterface $em, private readonly Security $security)
    {
    }

    public function __invoke(): Response
    {
        $response = new Response();
        $user = $this->security->getUser();
        try {
            $query = $this->em->getRepository(Charge::class)
                ->createQueryBuilder('c')
                ->update()
                ->set('c.state', '0')
                ->where('c.user = :user')
                ->setParameter('user', $user)->getQuery();

            $query->execute();
            $response->setStatusCode(Response::HTTP_OK);

        } catch (Exception $e) {
            $response->setContent($e->getMessage());
        }

        return $response;
    }


}