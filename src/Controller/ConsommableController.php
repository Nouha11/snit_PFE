<?php

namespace App\Controller;

use App\Repository\ConsommableRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConsommableController extends AbstractController
{
    #[Route('/consommables', name: 'app_consommable')]
    public function index(ConsommableRepository $ConsommableRepository): Response
    {
        $consommables = $ConsommableRepository->findAll();

        return $this->render('Consommable/Consommable.html.twig', [
            'consommables' => $consommables,
        ]);
    }
}
