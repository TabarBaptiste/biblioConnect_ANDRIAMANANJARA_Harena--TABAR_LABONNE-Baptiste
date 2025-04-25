<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\Livre;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // create 20 products! Bam!
        for ($i = 1; $i < 10; $i++) {
            $livre = new Livre();
            $livre->setTitre('Titre ' . $i);
            $livre->setAuteur('Auteur ' . $i);
            $livre->setCategorie('Romance');
            $livre->setLangue('Français');
            $livre->setTheme('Amour');
            $livre->setStock(50);
            $manager->persist($livre);
        }

        for ($i = 1; $i < 10; $i++) {
            $livre = new Livre();
            $livre->setTitre('Titre ' . $i);
            $livre->setAuteur('Auteur ' . $i);
            $livre->setCategorie('Horreur');
            $livre->setTheme('Vengeance');
            $livre->setLangue('Anglais');
            $livre->setStock(50);
            $manager->persist($livre);
        }

        $manager->flush();
    }
}
