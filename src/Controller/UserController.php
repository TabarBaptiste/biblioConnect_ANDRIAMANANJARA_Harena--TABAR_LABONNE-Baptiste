<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use App\Entity\User;

final class UserController extends AbstractController
{
    #[Route('/user/{id}', name: 'app_user_show')]
    public function show(User $user, ReservationRepository $reservationRepository): Response
    {
        // Récupérer les réservations de l'utilisateur
        $reservations = $reservationRepository->findBy(['utilisateur' => $user]);

        return $this->render('user/show.html.twig', [
            'user' => $user,
            'reservations' => $reservations,
        ]);
    }
}
