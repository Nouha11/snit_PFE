<?php

namespace App\Controller;

use App\Entity\Equipement;
use App\Entity\Consommable;
use App\Entity\Intervention;
use App\Entity\Contrat;
use App\Entity\Utilisateur;
use App\Service\StatisticsExportService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/statistics')]
class StatisticsController extends AbstractController
{
    private StatisticsExportService $exportService;

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager , StatisticsExportService $exportService)
    {
        $this->entityManager = $entityManager;
        $this->exportService = $exportService;
    }

    #[Route('/', name: 'admin_statistics')]
    public function index(): Response
    {
        // Equipment statistics
        $equipmentRepo = $this->entityManager->getRepository(Equipement::class);
        $totalEquipments = $equipmentRepo->count([]);
        
        $equipmentsByState = $equipmentRepo->createQueryBuilder('e')
            ->select('e.Etat as etat, COUNT(e.id_eq) as count')
            ->groupBy('e.Etat')
            ->getQuery()
            ->getResult();

        $equipmentsByModel = $equipmentRepo->createQueryBuilder('e')
            ->select('e.Modele as modele, COUNT(e.id_eq) as count')
            ->groupBy('e.Modele')
            ->orderBy('count', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        // Consumables statistics
        $consommableRepo = $this->entityManager->getRepository(Consommable::class);
        $totalConsommables = $consommableRepo->count([]);
        
        $consommablesByState = $consommableRepo->createQueryBuilder('c')
            ->select('c.etat as etat, COUNT(c.id_cons) as count')
            ->groupBy('c.etat')
            ->getQuery()
            ->getResult();

        $lowStockConsommables = $consommableRepo->createQueryBuilder('c')
            ->where('c.quantite < 10')
            ->orderBy('c.quantite', 'ASC')
            ->getQuery()
            ->getResult();

        $totalConsommableValue = $consommableRepo->createQueryBuilder('c')
            ->select('SUM(c.valeur * c.quantite) as total')
            ->getQuery()
            ->getSingleScalarResult() ?? 0;

        // Interventions statistics
        $interventionRepo = $this->entityManager->getRepository(Intervention::class);
        $totalInterventions = $interventionRepo->count([]);

        $interventionsByType = $interventionRepo->createQueryBuilder('i')
            ->select('i.typeIntervention as type, COUNT(i.id) as count')
            ->groupBy('i.typeIntervention')
            ->getQuery()
            ->getResult();

        $recentInterventions = $interventionRepo->createQueryBuilder('i')
            ->orderBy('i.dateIntervention', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        // Monthly interventions (last 12 months)
        $interventionRepo = $this->entityManager->getRepository(Intervention::class);
        $data = $interventionRepo->createQueryBuilder('i')
            ->select('YEAR(i.dateIntervention) as year, MONTH(i.dateIntervention) as month, COUNT(i.id) as count')
            ->where('i.dateIntervention >= :date')
            ->setParameter('date', new \DateTime('-12 months'))
            ->groupBy('year, month')
            ->orderBy('year, month')
            ->getQuery()
            ->getResult();


        // Contracts statistics
        $contratRepo = $this->entityManager->getRepository(Contrat::class);
        $totalContracts = $contratRepo->count([]);

        // Users statistics
        $utilisateurRepo = $this->entityManager->getRepository(Utilisateur::class);
        $totalUsers = $utilisateurRepo->count([]);

        $usersByDirection = $utilisateurRepo->createQueryBuilder('u')
            ->select('d.libelle as direction, COUNT(u.id_u) as count')
            ->leftJoin('u.direction', 'd')
            ->groupBy('d.libelle')
            ->getQuery()
            ->getResult();

        return $this->render('/statistics/index.html.twig', [
            'totalEquipments' => $totalEquipments,
            'equipmentsByState' => $equipmentsByState,
            'equipmentsByModel' => $equipmentsByModel,
            'totalConsommables' => $totalConsommables,
            'consommablesByState' => $consommablesByState,
            'lowStockConsommables' => $lowStockConsommables,
            'totalConsommableValue' => $totalConsommableValue,
            'totalInterventions' => $totalInterventions,
            'interventionsByType' => $interventionsByType,
            'recentInterventions' => $recentInterventions,
            'interventionRepo' => $interventionRepo,
            'totalContracts' => $totalContracts,
            'totalUsers' => $totalUsers,
            'usersByDirection' => $usersByDirection,
        ]);
    }

    #[Route('/api/equipment-state', name: 'admin_api_equipment_state')]
    public function getEquipmentStateData(): Response
    {
        $equipmentRepo = $this->entityManager->getRepository(Equipement::class);
        $data = $equipmentRepo->createQueryBuilder('e')
            ->select('e.Etat as etat, COUNT(e.id_eq) as count')
            ->groupBy('e.Etat')
            ->getQuery()
            ->getResult();

        return $this->json($data);
    }

    #[Route('/api/interventions-monthly', name: 'admin_api_interventions_monthly')]
    public function getMonthlyInterventionsData(): JsonResponse
    {
        $conn = $this->entityManager->getConnection();

        $sql = "
            SELECT 
                YEAR(date_intervention) AS year,
                MONTH(date_intervention) AS month,
                COUNT(id) AS count
            FROM intervention
            WHERE date_intervention >= :date
            GROUP BY year, month
            ORDER BY year, month
        ";

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery([
            'date' => (new \DateTime('-12 months'))->format('Y-m-d H:i:s'),
        ]);

        $data = $result->fetchAllAssociative();

        return new JsonResponse($data);
    }

       #[Route('/admin/statistics/export/pdf', name: 'admin_statistics_export_pdf', methods: ['GET'])]
    public function exportPdf(): Response
    {
        // Get all the same data as your statistics page
        $data = $this->getStatisticsData();
        
        return $this->exportService->generatePDF($data);
    }

    #[Route('/admin/statistics/export/excel', name: 'admin_statistics_export_excel', methods: ['GET'])]
    public function exportExcel(): Response
    {
        // Get all the same data as your statistics page
        $data = $this->getStatisticsData();
        
        return $this->exportService->generateExcel($data);
    }

    private function getStatisticsData(): array
    {
        // Get repositories using EntityManager
        $equipmentRepository = $this->entityManager->getRepository(Equipement::class);
        $consommableRepository = $this->entityManager->getRepository(Consommable::class);
        $interventionRepository = $this->entityManager->getRepository(Intervention::class);
        $userRepository = $this->entityManager->getRepository(Utilisateur::class);

        // Total counts
        $totalEquipments = $equipmentRepository->count([]);
        $totalConsommables = $consommableRepository->count([]);
        $totalInterventions = $interventionRepository->count([]);

        // Total value of consommables
        $totalConsommableValue = $consommableRepository->createQueryBuilder('c')
            ->select('SUM(c.quantite * c.valeur)')
            ->getQuery()
            ->getSingleScalarResult() ?? 0;

        // Equipment by state
        $equipmentsByState = $equipmentRepository->createQueryBuilder('e')
            ->select('e.Etat, COUNT(e.id_eq) as count')
            ->groupBy('e.Etat')
            ->getQuery()
            ->getResult();

        // Equipment by model (top 10)
        $equipmentsByModel = $equipmentRepository->createQueryBuilder('e')
            ->select('e.Modele, COUNT(e.id_eq) as count')
            ->groupBy('e.Modele')
            ->orderBy('count', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        // Low stock consommables (assuming you have a stock_min field)
        $lowStockConsommables = $consommableRepository->createQueryBuilder('c')
            ->where('c.quantite >= 0') // Adjust this condition based on your logic
            ->orderBy('c.quantite', 'ASC')
            ->getQuery()
            ->getResult();

        // Interventions by type
        $interventionsByType = $interventionRepository->createQueryBuilder('i')
            ->select('i.typeIntervention as type, COUNT(i.id) as count')
            ->groupBy('i.typeIntervention')
            ->getQuery()
            ->getResult();

        // Recent interventions (last 20)
        $recentInterventions = $interventionRepository->createQueryBuilder('i')
            ->leftJoin('i.equipement', 'e')
            ->orderBy('i.dateIntervention', 'DESC')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult();

        // FIXED: Users by direction - properly join with direction entity
        $usersByDirection = $userRepository->createQueryBuilder('u')
            ->select('d.libelle as direction, COUNT(u.id_u) as count')  // Fixed: use id_u instead of id_des
            ->leftJoin('u.direction', 'd')  // Added: proper join with direction entity
            ->groupBy('d.libelle')  // Fixed: group by direction libelle
            ->getQuery()
            ->getResult();

        // Monthly interventions data (last 12 months)
        $monthlyInterventions = $interventionRepository->createQueryBuilder('i')
            ->select('YEAR(i.dateIntervention) as year, MONTH(i.dateIntervention) as month, COUNT(i.id) as count')
            ->where('i.dateIntervention >= :date')
            ->setParameter('date', new \DateTime('-12 months'))
            ->groupBy('year, month')
            ->orderBy('year, month')
            ->getQuery()
            ->getResult();

        return [
            'totalEquipments' => $totalEquipments,
            'totalConsommables' => $totalConsommables,
            'totalInterventions' => $totalInterventions,
            'totalConsommableValue' => $totalConsommableValue,
            'equipmentsByState' => $equipmentsByState,
            'equipmentsByModel' => $equipmentsByModel,
            'lowStockConsommables' => $lowStockConsommables,
            'interventionsByType' => $interventionsByType,
            'recentInterventions' => $recentInterventions,
            'usersByDirection' => $usersByDirection,
            'monthlyInterventions' => $monthlyInterventions,
        ];
    }

}