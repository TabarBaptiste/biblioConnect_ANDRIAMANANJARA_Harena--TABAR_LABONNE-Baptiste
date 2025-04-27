<?php

namespace App\Tests\Fonctionnel;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\UserRepository;
use App\Repository\LivreRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;

class LivreCreationTest extends WebTestCase
{

    public function testCatalogueAccessible()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h1');
    }

    private function loginUser($client)
    {
        $user = self::getContainer()->get('doctrine')->getRepository(User::class)->findOneByEmail('admin@example.com');

        if (!$user) {
            throw new \Exception('Aucun utilisateur admin@exemple.com trouvé. Vérifie ta fixture.');
        }

        $client->loginUser($user);
    }

    public function testLivreCreationPageAccessible(): void
    {
        $client = static::createClient();
        $this->loginUser($client);

        $crawler = $client->request('GET', 'librarian/new');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }

    public function testCreateLivre(): void
    {
        $client = static::createClient();
        $this->loginUser($client);

        $crawler = $client->request('GET', '/librarian/new');

        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('✔️ Ajouter')->form([
            'livre[titre]' => 'Nouveau Livre Test',
            'livre[auteur]' => 1,
            'livre[theme]' => 'Test Theme',
            'livre[stock]' => 10,
            'livre[categorie]' => 1,
            'livre[langue]' => 1,
        ]);

        $client->submit($form);

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorTextContains('body', 'Nouveau Livre Test');
    }

    public function testRedirectionSiNonConnecte()
    {
        $client = static::createClient();
        $client->request('GET', '/1/reserver');
        $this->assertResponseRedirects('/login');
    }

    public function testStockDiminueApresReservation()
    {
        $client = static::createClient();
        // Récupérer un utilisateur existant (ex: avec ROLE_USER)
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('admin@example.com');
        $client->loginUser($testUser);
        
        // Récupérer un livre existant
        $livreRepository = static::getContainer()->get(LivreRepository::class);
        $livre = $livreRepository->findOneBy([]); // Prend n'importe quel livre
        $stockAvant = $livre->getStock();
    
        // Supprimer toute réservation existante pour ce livre
        $reservationRepository = static::getContainer()->get(ReservationRepository::class);
        $reservationExistante = $reservationRepository->findOneBy([
            'utilisateur' => $testUser,
            'livre' => $livre,
        ]);
        
        if ($reservationExistante) {
            // Supprimer la réservation existante pour permettre de tester à nouveau
            $entityManager = static::getContainer()->get(EntityManagerInterface::class);
            $entityManager->remove($reservationExistante);
            $entityManager->flush();
        }
    
        // Réserver ce livre
        $crawler = $client->request('GET', '/' . $livre->getId() . '/reserver');
        
        // Vérifier que la réservation fonctionne (peut-être un code de redirection ou de confirmation)
        $this->assertResponseRedirects();
        $client->followRedirect();
    
        // Recharger le livre pour avoir les nouvelles valeurs en base
        $livreRepository = static::getContainer()->get(LivreRepository::class);
        $livreMisAJour = $livreRepository->find($livre->getId());
        $stockApres = $livreMisAJour->getStock();
    
        // Test : le stock a diminué de 1
        $this->assertEquals($stockAvant - 1, $stockApres);
    }
    
}
