<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\Livre;
use App\Entity\Categorie;
use App\Entity\Langue;
use App\Entity\User;
use App\Entity\Auteur;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    // On injecte le service pour hasher les mots de passe
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // ----- 1. Créer les catégories -----
        $categories = [];
        $nomsCategories = ['Roman', 'Manga', 'Essai', 'Science-Fiction', 'Fantastique'];

        foreach ($nomsCategories as $nomCategorie) {
            $categorie = new Categorie();
            $categorie->setNom($nomCategorie);
            $manager->persist($categorie);
            $categories[] = $categorie;
        }

        // ----- 2. Créer les langues -----
        $langues = [];
        $nomsLangues = ['Français', 'Anglais', 'Japonais', 'Espagnol', 'Allemand'];

        foreach ($nomsLangues as $nomLangue) {
            $langue = new Langue();
            $langue->setNom($nomLangue);
            $manager->persist($langue);
            $langues[] = $langue;
        }

        // ----- 2. Créer les Auteurs -----
        $auteurs = [];
        $nomsAuteurs = ['Albert Camus', 'Honoré de Balzac', 'Gustave Flaubert', 'Émile Zola', 'Victor Hugo', 'Jean-Paul Sartre', 'Guillaume Musso', 'Marc Levy'];

        foreach ($nomsAuteurs as $nomAuteur) {
            $auteur = new Auteur();
            $auteur->setNom($nomAuteur);
            $manager->persist($auteur);
            $auteurs[] = $auteur;
        }

        // ----- 3. Créer les livres -----
        for ($i = 1; $i <= 20; $i++) {
            $livre = new Livre();
            $livre->setTitre("Livre $i");
            $livre->setTheme("Thème $i");
            $livre->setStock(rand(1, 100));

            // Choisir une catégorie et une langue au hasard
            $livre->setAuteur($auteurs[array_rand($auteurs)]);
            $livre->setCategorie($categories[array_rand($categories)]);
            $livre->setLangue($langues[array_rand($langues)]);

            $manager->persist($livre);
        }

        // --- 4. Utilisateurs ---

        // Admin
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setNom('Admin');
        $admin->setPrenom('Super');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setDateDeCreation(new \DateTime());
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'motdepasse'));
        $manager->persist($admin);

        // Bibliothécaire
        $librarian = new User();
        $librarian->setEmail('librarian@example.com');
        $librarian->setNom('Biblio');
        $librarian->setPrenom('Tech');
        $librarian->setRoles(['ROLE_LIBRARIAN']);
        $librarian->setDateDeCreation(new \DateTime());
        $librarian->setPassword($this->passwordHasher->hashPassword($librarian, 'password'));
        $manager->persist($librarian);

        // Utilisateur simple
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setNom('User');
        $user->setPrenom('Lambda');
        $user->setRoles(['ROLE_USER']);
        $user->setDateDeCreation(new \DateTime());
        $user->setPassword($this->passwordHasher->hashPassword($user, 'motdepasse'));
        $manager->persist($user);

        $manager->flush();
    }
}
