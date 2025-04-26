<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250426152706 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE langue (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE livre ADD categorie_id INT DEFAULT NULL, ADD langue_id INT DEFAULT NULL, DROP categorie, DROP langue
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE livre ADD CONSTRAINT FK_AC634F99BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE livre ADD CONSTRAINT FK_AC634F992AADBACD FOREIGN KEY (langue_id) REFERENCES langue (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_AC634F99BCF5E72D ON livre (categorie_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_AC634F992AADBACD ON livre (langue_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE livre DROP FOREIGN KEY FK_AC634F992AADBACD
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE langue
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE livre DROP FOREIGN KEY FK_AC634F99BCF5E72D
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_AC634F99BCF5E72D ON livre
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_AC634F992AADBACD ON livre
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE livre ADD categorie VARCHAR(255) NOT NULL, ADD langue VARCHAR(255) NOT NULL, DROP categorie_id, DROP langue_id
        SQL);
    }
}
