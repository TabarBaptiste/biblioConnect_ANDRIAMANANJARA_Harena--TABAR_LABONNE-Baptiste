<?php

namespace App\Tests\Fonctionnel;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

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
            'livre[auteur]' => 'Auteur Test',
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
}
