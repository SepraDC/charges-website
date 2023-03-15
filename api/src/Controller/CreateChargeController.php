<?php

namespace App\Controller;

use App\Repository\ChargeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CreateChargeController extends AbstractController
{
    public function __construct(private ChargeRepository $chargeRepository)
    {
    }

    public function __invoke($input)
    {
        dd($input);
    }


}