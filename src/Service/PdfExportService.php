<?php

namespace App\Service;

use App\Entity\Consommable;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Twig\Environment;

class PdfExportService
{
    private EntityManagerInterface $entityManager;
    private Environment $twig;

    public function __construct(EntityManagerInterface $entityManager, Environment $twig)
    {
        $this->entityManager = $entityManager;
        $this->twig = $twig;
    }

    public function exportConsommablesToPdf(array $filters = []): string
    {
        // Configure Dompdf
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);

        // Get filtered data
        $consommables = $this->getFilteredConsommables($filters);

        // Render HTML template
        $html = $this->twig->render('consommable/pdf_export.html.twig', [
            'consommables' => $consommables,
            'filters' => $filters,
            'exportDate' => new \DateTime(),
            'totalItems' => count($consommables)
        ]);

        // Generate PDF
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return $dompdf->output();
    }

    private function getFilteredConsommables(array $filters): array
    {
        $qb = $this->entityManager->getRepository(Consommable::class)->createQueryBuilder('c');

        // Apply filters
        if (!empty($filters['etat'])) {
            $qb->andWhere('c.etat IN (:etats)')
               ->setParameter('etats', $filters['etat']);
        }

        if (!empty($filters['designation'])) {
            $qb->andWhere('c.designation LIKE :designation')
               ->setParameter('designation', '%' . $filters['designation'] . '%');
        }

        if (!empty($filters['modele'])) {
            $qb->andWhere('c.modele LIKE :modele')
               ->setParameter('modele', '%' . $filters['modele'] . '%');
        }

        if (!empty($filters['couleur'])) {
            $qb->andWhere('c.couleur IN (:couleurs)')
               ->setParameter('couleurs', $filters['couleur']);
        }

        if (!empty($filters['quantite_min'])) {
            $qb->andWhere('c.quantite >= :quantite_min')
               ->setParameter('quantite_min', $filters['quantite_min']);
        }

        if (!empty($filters['quantite_max'])) {
            $qb->andWhere('c.quantite <= :quantite_max')
               ->setParameter('quantite_max', $filters['quantite_max']);
        }

        if (!empty($filters['valeur_min'])) {
            $qb->andWhere('c.valeur >= :valeur_min')
               ->setParameter('valeur_min', $filters['valeur_min']);
        }

        if (!empty($filters['valeur_max'])) {
            $qb->andWhere('c.valeur <= :valeur_max')
               ->setParameter('valeur_max', $filters['valeur_max']);
        }

        // Use property name id_cons for Doctrine queries
        if (!empty($filters['ids'])) {
            $qb->andWhere('c.id_cons IN (:ids)')
               ->setParameter('ids', $filters['ids']);
        }

        // Apply sorting with correct property name
        $sortField = $filters['sort_field'] ?? 'id_cons'; 
        $sortDirection = $filters['sort_direction'] ?? 'ASC';
        
        // Validate sort field to prevent SQL injection (use property names)
        $allowedSortFields = ['id_cons', 'designation', 'modele', 'couleur', 'quantite', 'etat', 'valeur'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'id_cons';
        }
        
        $qb->orderBy('c.' . $sortField, $sortDirection);

        return $qb->getQuery()->getResult();
    }

    public function getAvailableFilters(): array
    {
        // Get unique values for filter options
        $etats = $this->entityManager->getRepository(Consommable::class)
            ->createQueryBuilder('c')
            ->select('DISTINCT c.etat')
            ->where('c.etat IS NOT NULL')
            ->getQuery()
            ->getSingleColumnResult();

        $couleurs = $this->entityManager->getRepository(Consommable::class)
            ->createQueryBuilder('c')
            ->select('DISTINCT c.couleur')
            ->where('c.couleur IS NOT NULL')
            ->getQuery()
            ->getSingleColumnResult();

        return [
            'etats' => $etats,
            'couleurs' => $couleurs
        ];
    }
}