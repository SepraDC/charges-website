<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/reset-password', name: 'reset_password', methods: ['POST'])]
class ResetPasswordController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $hasher,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $token = $data['token'] ?? null;
        $newPassword = $data['password'] ?? null;

        if (!$token || !$newPassword) {
            return new JsonResponse(['message' => 'Token et mot de passe requis.'], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userRepository->findOneBy(['resetPasswordToken' => $token]);

        if (!$user) {
            return new JsonResponse(['message' => 'Token invalide ou expiré.'], Response::HTTP_BAD_REQUEST);
        }

        $expiresAt = $user->getResetPasswordTokenExpiresAt();
        if (!$expiresAt || $expiresAt < new \DateTimeImmutable()) {
            return new JsonResponse(['message' => 'Token invalide ou expiré.'], Response::HTTP_BAD_REQUEST);
        }

        $user->setPassword($this->hasher->hashPassword($user, $newPassword));
        $user->setResetPasswordToken(null);
        $user->setResetPasswordTokenExpiresAt(null);

        $this->em->flush();

        return new JsonResponse(['message' => 'Mot de passe réinitialisé avec succès.'], Response::HTTP_OK);
    }
}
