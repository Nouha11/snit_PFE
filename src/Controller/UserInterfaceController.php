<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserInterfaceController extends AbstractController
{
    #[Route('/user', name: 'app_user_interface')]
    public function index(): Response
    {
        $user = $this->getUser(); // Symfony automatically gives you the logged-in user

        return $this->render('user_interface/dashboard.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/user/intervention', name: 'app_user_intervention')]
    public function intervention(): Response
    {
        $user = $this->getUser(); 

        return $this->render('user_interface/intervention.html.twig', [
            'user' => $user,
        ]);
    }

        #[Route('/user/myintervention', name: 'app_user_myintervention')]
    public function myintervention(): Response
    {
        $user = $this->getUser(); 

        return $this->render('user_interface/myInterventions.html.twig', [
            'user' => $user,
        ]);
    }
}