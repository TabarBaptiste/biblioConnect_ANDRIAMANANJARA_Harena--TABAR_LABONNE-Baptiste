<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

final class UserController extends AbstractController
{
    #[Route('/user/{id}', name: 'app_user_show')]
    public function show(User $user, ReservationRepository $reservationRepository): Response
    {
        // Récupérer les réservations de l'utilisateur
        $reservations = $reservationRepository->findBy(['utilisateur' => $user]);
        $role = $user->getRoles();

        return $this->render('user/show.html.twig', [
            'user' => $user,
            'role' => $role[0],
            'reservations' => $reservations,
        ]);
    }

    #[Route('admin/user/{id}/update-role', name: 'app_user_update_role', methods: ['POST'])]
    public function updateRole(User $user, Request $request, EntityManagerInterface $em, UserRepository $userRepository): Response
    {
        // Empêcher de changer son propre rôle
        $currentUser = $this->getUser();
        if ($currentUser instanceof User && $currentUser->getId() === $user->getId()) {
            $this->addFlash('error', 'Vous ne pouvez pas modifier votre propre rôle.');
            return $this->redirectToRoute('app_user_show', ['id' => $user->getId()]);
        }

        // Vérifier que l'utilisateur connecté est admin
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        // Récupérer le rôle depuis le formulaire
        $newRole = $request->request->get('role');

        // On applique le nouveau rôle
        $user->setRoles([$newRole]);
        $em->flush();

        $this->addFlash('success', 'Rôle mis à jour.');
        return $this->redirectToRoute('app_user_show', ['id' => $user->getId()]);
    }

    #[Route('admin/user', name: 'app_user')]
    public function user(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('reservation/{id}/delete', name: 'app_user_reservation_delete', methods: ['POST'])]
    public function deleteReservation(int $id, ReservationRepository $reservationRepository, EntityManagerInterface $em): Response
    {
        $reservation = $reservationRepository->find($id);

        if (!$reservation) {
            throw $this->createNotFoundException('Réservation non trouvée.');
        }

        // Vérifie que l'utilisateur connecté est bien celui qui a fait la réservation
        if ($reservation->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer cette réservation.');
        }

        // Récupérer le livre associé à la réservation
        $livre = $reservation->getLivre();
        if ($livre) {
            // Augmenter le stock du livre
            $livre->setStock($livre->getStock() + 1);
            $em->persist($livre);
        }

        $em->remove($reservation);
        $em->flush();

        $this->addFlash('success', 'Réservation supprimée.');
        $currentUser = $this->getUser();
        if (!$currentUser instanceof User) {
            throw new \LogicException('L\'utilisateur n\'est pas connecté ou est invalide.');
        }

        return $this->redirectToRoute('app_user_show', ['id' => $currentUser->getId()]);
    }

}
