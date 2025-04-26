<?php

namespace App\Tests\Unitaire;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Entity\Livre;
use App\Entity\Categorie;
use App\Entity\Langue;

class LivreTest extends KernelTestCase
{
    public function LivreTest(): void
    {
        $livre = new Livre();
        $livre->setTitre('Livre test');
        $this->assertEquals('Livre test', $livre->getTitre());

        $livre->setAuteur('Auteur test');
        $this->assertEquals('Auteur test', $livre->getAuteur());

        $livre->setTheme('Theme test');
        $this->assertEquals('Theme test', $livre->getTheme());

        $livre->setStock(1000);
        $this->assertEquals(1000, $livre->getStock());

        $categorie = new Categorie();
        $categorie->setNom('Roman');

        $livre->setCategorie($categorie);
        $this->assertSame($categorie, $livre->getCategorie());

        $langue = new Langue();
        $langue->setNom('Français');

        $livre->setLangue($langue);
        $this->assertSame($langue, $livre->getLangue());
    }

    public function testValidLivre(): void
    {
        self::bootKernel();

        $livre = new Livre();
        $livre->setTitre('Livre test')
            ->setAuteur('Auteur test')
            ->setTheme('Theme test')
            ->setStock(100)
            ->setCategorie((new Categorie())->setNom('Roman'))
            ->setLangue((new Langue())->setNom('Français'));

        $errors = self::getContainer()->get('validator')->validate($livre);

        $this->assertCount(0, $errors); // Pas d'erreurs = entité valide
    }

    public function testInvalidLivre(): void
    {
        self::bootKernel();

        $livre = new Livre();

        $errors = self::getContainer()->get('validator')->validate($livre);

        $this->assertGreaterThan(0, count($errors)); // Il doit y avoir des erreurs
    }
}
