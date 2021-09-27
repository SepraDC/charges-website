<?php


namespace App\Controller;

use App\Entity\Bank;
use App\Entity\ChargeType;
use App\Entity\User;
use App\Form\BankType;
use App\Form\ChargeTypeType;
use App\Form\UserType;
use App\Repository\BankRepository;
use App\Repository\ChargeTypeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use \Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'admin')]
class AdminController extends AbstractController
{
    public function __construct(
        private BankRepository         $bankRepository,
        private UserRepository         $userRepository,
        private ChargeTypeRepository   $chargeTypeRepository,
        private EntityManagerInterface $entityManager
    )
    {
    }

    #[Route('/', name: '_index')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    #[Route('/users', name: '_users')]
    public function users(Request $request): Response
    {
        $users = $this->userRepository->findAll();

        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles([$form->get("roles")->getData()]);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/user.html.twig', [
            'controller_name' => 'AdminController',
            'users' => $users,
            'form' => $form->createView()
        ]);
    }

    #[Route('/users/delete', name: '_users_delete')]
    public function usersDelete(Request $request): Response
    {
        $userName = $request->get('username');
        $user = $this->userRepository->findOneBy(['username' => $userName]);

        if ($user) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute("admin_users");
    }

    #[Route('/banks', name: '_banks')]
    public function banks(Request $request): Response
    {
        $banks = $this->bankRepository->findAll();
        $bank = new Bank();

        $form = $this->createForm(BankType::class, $bank);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bank->setName($form->get("name")->getData());
            $bank->setAbbreviation($form->get("abbreviation")->getData());

            $this->entityManager->persist($bank);
            $this->entityManager->flush();

            $this->redirectToRoute('admin_banks');
        }

        return $this->render('admin/banks.html.twig', [
            'controller_name' => 'AdminController',
            'banks' => $banks,
            'form' => $form->createView()
        ]);
    }

    #[Route('/banks/delete', name: '_banks_delete')]
    public function banksDelete(Request $request): Response
    {
        $id = $request->get('id');
        $bank = $this->bankRepository->find($id);

        if ($bank) {
            $this->entityManager->remove($bank);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute("admin_banks");
    }

    #[Route('/charge-type', name: '_charge_types')]
    public function chargeTypes(Request $request): Response
    {
        $chargeTypes = $this->chargeTypeRepository->findAll();
        $chargeType = new ChargeType();

        $form = $this->createForm(ChargeTypeType::class, $chargeType);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $chargeType->setName($form->get("name")->getData());

            $this->entityManager->persist($chargeType);
            $this->entityManager->flush();

            $this->redirectToRoute('admin_charge_types');
        }

        return $this->render('admin/chargeType.html.twig', [
            'controller_name' => 'AdminController',
            'chargeTypes' => $chargeTypes,
            'form' => $form->createView()
        ]);
    }

    #[Route('/charge-type/delete', name: '_charge_types_delete')]
    public function chargeTypesDelete(Request $request): Response
    {
        $id = $request->get('id');
        $chargeType = $this->chargeTypeRepository->find($id);

        if ($chargeType) {
            $this->entityManager->remove($chargeType);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('admin_charge_types');
    }


}
