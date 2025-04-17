<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class UtilisateurController extends AbstractController
{
    #[Route('/utilisateurs', name: 'app_utilisateur_index', methods: ['GET'])]
    public function index(UtilisateurRepository $utilisateurRepository): Response
    {
        return $this->render('utilisateur/index.html.twig', [
            'utilisateurs' => $utilisateurRepository->findAll(),
        ]);
    }

    #[Route('/utilisateur/new', name: 'app_utilisateur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Persist the user first
            $entityManager->persist($utilisateur);
            
            // Get the selected Equipements
            $equipements = $form->get('equipements')->getData();
            //dump(count($equipements));
            foreach ($equipements as $equipement) {
                // Update the equipement to point to this user
                $equipement->setUtilisateur($utilisateur);
                // Let Doctrine know the equipement has been updated
                $entityManager->persist($equipement);
            }
            
            // Flush all changes
            $entityManager->flush();
            
            $this->addFlash('success', 'Utilisateur créé avec succès!');
            return $this->redirectToRoute('app_utilisateur_index');
        }
        
        return $this->render('utilisateur/new.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
        ]);
    }

    

    #[Route('/utilisateur/{id_u}', name: 'app_utilisateur_show', requirements: ['id_u' => '\d+'])]
    public function show(int $id_u, UtilisateurRepository $utilisateurRepository): Response
    {
        $utilisateur = $utilisateurRepository->find($id_u);
    
        if (!$utilisateur) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }
    
        return $this->render('utilisateur/show.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }
    


    #[Route('/utilisateur/{id_u}/edit', name: 'app_utilisateur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id_u, EntityManagerInterface $entityManager): Response
    {
        $utilisateur = $entityManager->getRepository(Utilisateur::class)->find($id_u);
        
        if (!$utilisateur) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }
        
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            $submittedPassword = $form->get('password')->getData();
            if (!empty($submittedPassword)) {
                $utilisateur->setPassword($submittedPassword);
            }
            //dd($submittedPassword , !empty($submittedPassword));

            // Handle equipment relationships
            $selectedEquipement = $form->get('equipements')->getData();
            //dd($utilisateur->getEquipements());

            // First, remove any equipment that are no longer selected
            foreach ($utilisateur->getEquipements()->toArray() as $existingEquipement) {
                if (!$selectedEquipement->contains($existingEquipement)) {
                    $utilisateur->removeEquipement($existingEquipement);
                    $existingEquipement->setUtilisateur(null);
                    $entityManager->persist($existingEquipement);
                }
            }
            
            // Then add newly selected equipment
            $equipements = $form->get('equipements')->getData();
            //dump(count($equipements));
            foreach ($equipements as $equipement) {
                // Update the equipement to point to this user
                $equipement->setUtilisateur($utilisateur);
                // Let Doctrine know the equipement has been updated
                $entityManager->persist($equipement);
            }
            
            $entityManager->flush();
            
            $this->addFlash('success', 'Utilisateur modifié avec succès!');
            return $this->redirectToRoute('app_utilisateur_index');
        }
        
        return $this->render('utilisateur/edit.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);
    }
    

    #[Route('/utilisateur/{id_u}/delete', name: 'app_utilisateur_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        int $id_u,
        EntityManagerInterface $entityManager
    ): Response {
        $utilisateur = $entityManager->getRepository(Utilisateur::class)->find($id_u);
    
        if (!$utilisateur) {
            throw $this->createNotFoundException('Utilisateur not found.');
        }
    
        $csrfToken = $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete' . $utilisateur->getIdU(), $csrfToken)) {
            // Detach all related equipments
            foreach ($utilisateur->getEquipements() as $equipement) {
                $equipement->setUtilisateur(null);
            }
    
            $entityManager->remove($utilisateur);
            $entityManager->flush();
    
            $this->addFlash('success', 'Utilisateur deleted successfully!');
        } else {
            $this->addFlash('error', 'Invalid CSRF token.');
        }
    
        return $this->redirectToRoute('app_utilisateur_index');
    }    
    
}