<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/forgot-password', name: 'forgot_password', methods: ['POST'])]
class ForgotPasswordController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $em,
        private readonly MailerInterface $mailer,
        #[Autowire('%env(FRONTEND_URL)%')]
        private readonly string $frontendUrl,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;

        $response = new JsonResponse(
            ['message' => 'Si un compte est associé à cet email, un lien de réinitialisation a été envoyé.'],
            Response::HTTP_OK,
        );

        if (!$email) {
            return $response;
        }

        $user = $this->userRepository->findOneBy(['email' => $email]);
        if (!$user) {
            return $response;
        }

        $token = bin2hex(random_bytes(32));
        $user->setResetPasswordToken($token);
        $user->setResetPasswordTokenExpiresAt(new \DateTimeImmutable('+1 hour'));

        $this->em->flush();

        $resetUrl = sprintf('%s/reset-password?token=%s', rtrim($this->frontendUrl, '/'), $token);

        $emailMessage = (new TemplatedEmail())
            ->to($user->getEmail())
            ->subject('Réinitialisation de votre mot de passe')
            ->htmlTemplate('email/reset_password.html.twig')
            ->context(['reset_url' => $resetUrl]);

        $this->mailer->send($emailMessage);

        return $response;
    }
}
