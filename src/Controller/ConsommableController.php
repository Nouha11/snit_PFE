<?php

namespace App\Controller;

use App\Entity\Consommable;
use App\Entity\Equipement;
use App\Form\ConsommableType;
use App\Repository\ConsommableRepository;
use App\Service\PdfExportService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/consommable')]
class ConsommableController extends AbstractController
{
    #[Route('/', name: 'app_consommable_index', methods: ['GET'])]
    public function index(ConsommableRepository $consommableRepository): Response
    {
        return $this->render('consommable/index.html.twig', [
            'consommables' => $consommableRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_consommable_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $consommable = new Consommable();
        $form = $this->createForm(ConsommableType::class, $consommable);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($consommable);

            $equipements = $form->get('equipements')->getData();
            foreach ($equipements as $equipement) {
                $equipement->setConsommable($consommable);
                $entityManager->persist($equipement);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Le consommable a été créé avec succès');
            return $this->redirectToRoute('app_consommable_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('consommable/new.html.twig', [
            'consommable' => $consommable,
            'form' => $form,
        ]);
    }

    #[Route('/{id_cons}', name: 'app_consommable_show', methods: ['GET'])]
    public function show(int $id_cons, EntityManagerInterface $entityManager): Response
    {
        $consommable = $entityManager->getRepository(Consommable::class)->find($id_cons);
        return $this->render('consommable/show.html.twig', [
            'consommable' => $consommable,
        ]);
    }

    #[Route('/{id_cons}/edit', name: 'app_consommable_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id_cons, EntityManagerInterface $entityManager): Response
    {
        $consommable = $entityManager->getRepository(Consommable::class)->find($id_cons);
        
        if (!$consommable) {
            throw $this->createNotFoundException('Consommable non trouvé.');
        }

        $form = $this->createForm(ConsommableType::class, $consommable);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $selectedEquipement = $form->get('equipements')->getData();

            foreach ($consommable->getEquipements()->toArray() as $existingEquipement) {
                if (!$selectedEquipement->contains($existingEquipement)) {
                    $consommable->removeEquipement($existingEquipement);
                    $existingEquipement->setConsommable(null);
                    $entityManager->persist($existingEquipement);
                }
            }
            
            $equipements = $form->get('equipements')->getData();
            foreach ($equipements as $equipement) {
                $equipement->setConsommable($consommable);
                $entityManager->persist($equipement);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Le consommable a été modifié avec succès');
            return $this->redirectToRoute('app_consommable_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('consommable/edit.html.twig', [
            'consommable' => $consommable,
            'form' => $form,
        ]);
    }

    #[Route('/{id_cons}', name: 'app_consommable_delete', methods: ['POST'])]
    public function delete(Request $request, int $id_cons, EntityManagerInterface $entityManager): Response
    {
        $consommable = $entityManager->getRepository(Consommable::class)->find($id_cons);

        if (!$consommable) {
            throw $this->createNotFoundException('Consommable non trouvé.');
        }

        $csrfToken = $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete'.$consommable->getId(), $csrfToken)) {
            foreach ($consommable->getEquipements() as $equipement) {
                $equipement->setConsommable(null);
            }

            $entityManager->remove($consommable);
            $entityManager->flush();
            $this->addFlash('success', 'Le consommable a été supprimé avec succès');
        } else {
            $this->addFlash('error', 'Invalid CSRF token.');
        }

        return $this->redirectToRoute('app_consommable_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/consommable/remove-equipment/{id}', name: 'app_consommable_remove_equipment', methods: ['POST'])]
    public function removeEquipment(Equipement $equipement,int $id_cons, EntityManagerInterface $entityManager): Response
    {
        $consommable = $entityManager->getRepository(Consommable::class)->find($id_cons);
        $consommable = $equipement->getConsommable();
        if ($consommable) {
            $consommable->removeEquipement($equipement);
            $entityManager->flush();
        }

        return new JsonResponse(['status' => 'ok']);
    }

    #[Route('/consommable/export', name: 'app_consommable_export')]
    public function exportPdf(PdfExportService $pdfExportService): Response
    {
        // Get filters from request (if you want to support filtered exports)
        $filters = [];
        
        // You can add filter logic here if needed
        // For example:
        // $filters = $request->query->all();
        
        try {
            // Generate PDF using your service
            $pdfContent = $pdfExportService->exportConsommablesToPdf($filters);
            
            // Create response with PDF content
            $response = new Response($pdfContent);
            $response->headers->set('Content-Type', 'application/pdf');
            $response->headers->set('Content-Disposition', 'attachment; filename="rapport_consommables_' . date('Y-m-d_H-i-s') . '.pdf"');
            
            return $response;
            
        } catch (\Exception $e) {
            // Handle errors gracefully
            $this->addFlash('error', 'Erreur lors de la génération du PDF: ' . $e->getMessage());
            return $this->redirectToRoute('app_consommable_index');
        }
    }

}
