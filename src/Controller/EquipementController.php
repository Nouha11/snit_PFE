<?php

namespace App\Controller;

use App\Entity\Equipement;
use App\Form\EquipementType;
use App\Repository\EquipementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/equipement')]
class EquipementController extends AbstractController
{
    #[Route('/', name: 'app_equipement_index', methods: ['GET'])]
    public function index(EquipementRepository $equipementRepository): Response
    {
        return $this->render('equipement/index.html.twig', [
            'equipements' => $equipementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_equipement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $equipement = new Equipement();
        $form = $this->createForm(EquipementType::class, $equipement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($equipement);
            $entityManager->flush();

            $this->addFlash('success', 'Équipement créé avec succès.');
            return $this->redirectToRoute('app_equipement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('equipement/new.html.twig', [
            'equipement' => $equipement,
            'form' => $form,
        ]);
    }

    #[Route('/{id_eq}/show', name: 'app_equipement_show', methods: ['GET'])]
    public function show(int $id_eq,EntityManagerInterface $entityManager): Response
    {
        $equipement = $entityManager->getRepository(Equipement::class)->find($id_eq);
        //dd($equipement);
        return $this->render('equipement/show.html.twig', [
            'equipement' => $equipement,
        ]);
    }

    #[Route('/{id_eq}/edit', name: 'app_equipement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id_eq, EntityManagerInterface $entityManager): Response
    {
        $equipement = $entityManager->getRepository(Equipement::class)->find($id_eq);

        $form = $this->createForm(EquipementType::class, $equipement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Équipement modifié avec succès.');
            return $this->redirectToRoute('app_equipement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('equipement/edit.html.twig', [
            'equipement' => $equipement,
            'form' => $form,
        ]);
    }

    #[Route('/{id_eq}/delete', name: 'app_equipement_delete', methods: ['POST'])]
    public function delete(Request $request, int $id_eq, EntityManagerInterface $entityManager): Response
    {
        $equipement = $entityManager->getRepository(Equipement::class)->find($id_eq);
/*         dd([
            'Route hit ✅',
            'Equipement ID' => $equipement->getId_eq(),
            'CSRF token received' => $request->request->get('_token'),
            'Token valid?' => $this->isCsrfTokenValid('delete' . $equipement->getId_eq(), $request->request->get('_token')),
        ]); */
        if ($this->isCsrfTokenValid('delete' . $equipement->getId_eq(), $request->request->get('_token'))) {
            $entityManager->remove($equipement);
    
            dump($equipement); // <- check this
            $entityManager->flush();
            dump('flushed!');
    
            $this->addFlash('success', 'Équipement supprimé avec succès.');
        } else {
            dump('Invalid token');
            $this->addFlash('error', 'Token CSRF invalide.');
        }
    
        return $this->redirectToRoute('app_equipement_index', [], Response::HTTP_SEE_OTHER);
    }
    
}