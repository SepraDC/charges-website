<?php

namespace App\Controller;

use App\Entity\Charge;
use App\Form\ChargeFormType;
use App\Form\PrelevementType;
use App\Repository\BankRepository;
use App\Repository\ChargeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use \Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/charges_front', name: 'charges')]
class ChargeController extends AbstractController
{
    public function __construct(private BankRepository   $bankRepository,
                                private ChargeRepository $chargeRepository,
    private EntityManagerInterface $entityManager)
    {
    }

    #[Route('/', name: '_index')]
    public function index()
    {
        $user = $this->getUser();
        $banks = $this->bankRepository->findByUser($user);

        if(empty($banks)) {
            return $this->redirectToRoute('charges_create');
        }

        return $this->render('charges/selectBank.html.twig', [
            'banks'=> $banks
        ]);
    }

    #[Route('/create', name: '_create')]
    public function create(Request $request) {
        $charge = new Charge();
        $form = $this->createForm(ChargeFormType::class, $charge);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $charge->setState(false);
            $charge->setUser($this->getUser());

            $this->entityManager->persist($charge);
            $this->entityManager->flush();
            return $this->redirectToRoute('charges_by_abbreviations', ['abb' => $charge->getBank()->getAbbreviation()], 201);
        }

        return $this->render('charges/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: '_edit')]
    public function chargesEdit($id, Request $request, ValidatorInterface $validator)
    {
        $charge = $this->chargeRepository->find($id);

        if (!$charge instanceof \App\Entity\Charge) {
            throw $this->createNotFoundException("Le prelevement n'existe pas");
        }

        $form = $this->createForm(ChargeFormType::class, $charge);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->persist($charge);
            $this->entityManager->flush();
            return $this->redirectToRoute('charges_by_abbreviations', ['abb' => $charge->getBank()->getAbbreviation()], 201);
        }

        return $this->render('charges/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/delete/{id}', name: '_delete')]
    public function supprimerPrelevement($id){
        $charge = $this->chargeRepository->find($id);

        if (!$charge instanceof \App\Entity\Charge) {
            throw $this->createNotFoundException("Le prelevement n'existe pas");
        }

        $this->entityManager->remove($charge);
        $this->entityManager->flush();

        return $this->redirectToRoute('charges_by_abbreviations', ['abb' => $charge->getBank()->getAbbreviation()], 201);
    }

    #[Route('/change', name: '_change_state', methods: "POST")]
    public function changerEtatAjax(Request $req){
        if($req->isXmlHttpRequest()){
            $id =$req->get('id');
            $reste = $req->get('reste');

            $em = $this->getDoctrine()->getManager();
            $charge = $this->chargeRepository->find($id);

            if (!$charge instanceof \App\Entity\Charge) {
                throw $this->createNotFoundException("Le prelevement n'existe pas");
            }

            $charge->setState(!$charge->getState());
            $em->persist($charge);
            $em->flush();

            if($charge->getState())
                $reste -= $charge->getAmount();
            else
                $reste += $charge->getAmount();

            return new JsonResponse(array(
                'state' => $charge->getState(),
                'reste' => $reste
            ));
        }

        return new Response("La requete n'est pas correcte", 400);
    }


    #[Route('/reset', name: '_reset_state')]
    public function chargesResetState()
    {
        $user = $this->getUser();
        $this->chargeRepository->resetState($user);
        return $this->redirectToRoute('charges_by_abbreviations', ['abb' => $this->bankRepository->findByUser($user)[0]->getAbbreviation()], 201);
    }

    #[Route('/{abb}', name: '_by_abbreviations')]
    public function chargeByAbbreviations(string $abb)
    {
        $user = $this->getUser();

        $banks = $this->bankRepository->findByUser($user);

        $currentBank = $this->bankRepository->findOneBy(['abbreviation' => $abb]);

        $charges = $this->chargeRepository->findBy(['user' => $user, 'bank' => $currentBank]);

        return $this->render('charges/charges.html.twig', [
            'currentBank' => $currentBank,
            'banks' => $banks,
            'charges' => $charges
        ]);
    }

}
