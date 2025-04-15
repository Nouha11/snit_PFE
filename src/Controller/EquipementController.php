<?php

namespace App\Controller;

use App\Repository\EquipementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EquipementController extends AbstractController
{
    #[Route('/equipements', name: 'app_equipements_index')]
    public function index(EquipementRepository $equipementRepository): Response
    {
        $equipements = $equipementRepository->findAll();

        return $this->render('equipement/materiel.html.twig', [
            'equipements' => $equipements,
        ]);
    }
}
