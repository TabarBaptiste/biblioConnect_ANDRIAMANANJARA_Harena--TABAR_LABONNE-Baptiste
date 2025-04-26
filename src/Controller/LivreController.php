<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;

use App\Repository\LivreRepository;
use App\Entity\Livre;
use App\Entity\Reservation;
use App\Form\CommentaireType;
use App\Entity\Commentaire;

#[Route('/livre')]
final class LivreController extends AbstractController
{
    #[Route('/', name: 'app_livre')]
    public function index(Request $request, LivreRepository $livreRepository): Response
    {
        $search = $request->query->get('search');

        if ($search) {
            $livres = $livreRepository->createQueryBuilder('l')
                ->where('l.titre LIKE :search OR l.auteur LIKE :search')
                ->setParameter('search', '%' . $search . '%')
                ->getQuery()
                ->getResult();
        } else {
            $livres = $livreRepository->findAll();
        }

        return $this->render('livre/index.html.twig', [
            'livres' => $livres,
            'search' => $search,
        ]);
    }

    #[Route('/{id}', name: 'app_livre_show', methods: ['GET', 'POST'])]
    public function show(Livre $livre, Request $request, EntityManagerInterface $entityManager): Response
    {
        $commentaire = new Commentaire();
        $user = $this->getUser();

        $form = null;
        if ($this->isGranted('ROLE_USER')) {
            $form = $this->createForm(CommentaireType::class, $commentaire);
            $form->handleRequest($request);

            // Vérifie si le commentaire n'existe pas déjà
            $alreadyCommented = $entityManager->getRepository(Commentaire::class)->findOneBy([
                'utilisateur' => $user,
                'livre' => $livre,
            ]);

            if ($form && $form->isSubmitted() && $form->isValid()) {
                if ($alreadyCommented) {
                    $this->addFlash('warning', 'Vous avez déjà commenté ce livre.');
                } else {
                    $commentaire->setLivre($livre);
                    $commentaire->setUtilisateur($user);
                    $commentaire->setDateCommentaire(new \DateTime());

                    $entityManager->persist($commentaire);
                    $entityManager->flush();

                    $this->addFlash('success', 'Merci pour votre avis !');
                    return $this->redirectToRoute('app_livre_show', ['id' => $livre->getId()]);
                }
            }
        }

        $reservationExistante = $entityManager->getRepository(Reservation::class)->findOneBy([
            'utilisateur' => $user,
            'livre' => $livre,
        ]);

        // Moyenne des notes des commentaires
        $commentaires = $livre->getCommentaires();
        $noteMoyenne = null;

        if (count($commentaires) > 0) {
            $total = 0;
            $nbNotes = 0;

            foreach ($commentaires as $commentaire) {
                if ($commentaire->getNote() !== null) {
                    $total += $commentaire->getNote();
                    $nbNotes++;
                }
            }

            if ($nbNotes > 0) {
                $noteMoyenne = round($total / $nbNotes, 1);
            }
        }

        // Nombre de réservations pour le livre
        $nbReservations = $livre->getReservations()->count();

        return $this->render('livre/show.html.twig', [
            'livre' => $livre,
            'comment_form' => $form ? $form->createView() : null,
            'noteMoyenne' => $noteMoyenne,
            'nbReservations' => $nbReservations,
            'reservationExistante' => $reservationExistante,
        ]);
    }

    #[Route('/livre/{id}/reserver', name: 'app_livre_reserver')]
    #[IsGranted('ROLE_USER')]
    public function reserver(Livre $livre, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $stock = $livre->getStock();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour réserver un livre');
        }

        // Check if the book is available
        if ($stock <= 0) {
            $this->addFlash('error', 'Ce livre n\'est plus disponible à la réservation.');
            return $this->redirectToRoute('app_livre_show', ['id' => $livre->getId()]);
        }

        $reservationExistante = $entityManager->getRepository(Reservation::class)->findOneBy([
            'utilisateur' => $user,
            'livre' => $livre,
        ]);

        if ($reservationExistante) {
            $this->addFlash('warning', 'Vous avez déjà réservé ce livre.');
            return $this->redirectToRoute('app_livre_show', ['id' => $livre->getId()]);
        }

        // Create a new reservation
        $reservation = new Reservation();
        $reservation->setUtilisateur($user);
        $reservation->setLivre($livre);
        $reservation->setDateReservation(new \DateTime());

        $livre->setStock($stock - 1);
        $entityManager->persist($reservation);
        $entityManager->flush();

        $this->addFlash('success', 'Livre réservé ! Consultez vos livres réservés dans votre profil.');

        return $this->redirectToRoute('app_livre_show', ['id' => $livre->getId()]);
    }

    #[Route('/admin/comment/{id}/delete', name: 'admin_comment_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteComment(Commentaire $commentaire, EntityManagerInterface $entityManager): Response
    {
        $livre = $commentaire->getLivre();
        $entityManager->remove($commentaire);
        $entityManager->flush();

        $this->addFlash('success', 'Commentaire supprimé.');

        return $this->redirectToRoute('app_livre_show', ['id' => $livre->getId()]);
    }
}
