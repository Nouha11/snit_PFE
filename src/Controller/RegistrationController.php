<?php
namespace App\Controller;

use App\Entity\Utilisateur;
use App\Entity\Role;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('admin_dashboard');
        }

        $user = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $hasher->hashPassword($user, $form->get('plainPassword')->getData())
            );

            $role = $em->getRepository(Role::class)->findOneBy(['libelle' => 'USER']);
            if (!$role) {
                $role = new Role();
                $role->setLibelle('USER');
                $em->persist($role);
                $em->flush();
            }
            $user->addRole($role);

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Registration successful!');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('Security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}

