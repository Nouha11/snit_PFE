<?php

namespace App\Controller;

use App\Repository\UtilisateurRepository;
use App\Repository\ConsommableRepository;
use App\Repository\ContratRepository;
use App\Repository\EquipementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function index(
        UtilisateurRepository $utilisateurRepo,
        ConsommableRepository $consommableRepo,
        ContratRepository $contratRepo,
        EquipementRepository $equipementRepo
    ): Response {
        // Get statistics for dashboard
        $stats = [
            'total_users' => $utilisateurRepo->count([]),
            'total_consommables' => $consommableRepo->count([]),
            'total_contrats' => $contratRepo->count([]),
            'total_equipements' => $equipementRepo->count([]),
            'recent_users' => $utilisateurRepo->findBy([], ['id_u' => 'DESC'], 5),
        ];

        return $this->render('admin_interface/dashboard.html.twig', [
            'stats' => $stats,
            'current_user' => $this->getUser(),
        ]);
    }
}