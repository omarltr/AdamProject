<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240618171004 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE etat (id INT AUTO_INCREMENT NOT NULL, etat VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE paiement (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE annonce ADD etat_id INT DEFAULT NULL, ADD categorie_id INT DEFAULT NULL, CHANGE message message VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE annonce ADD CONSTRAINT FK_F65593E5D5E86FF FOREIGN KEY (etat_id) REFERENCES etat (id)');
        $this->addSql('ALTER TABLE annonce ADD CONSTRAINT FK_F65593E5BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('CREATE INDEX IDX_F65593E5D5E86FF ON annonce (etat_id)');
        $this->addSql('CREATE INDEX IDX_F65593E5BCF5E72D ON annonce (categorie_id)');
        $this->addSql('ALTER TABLE avis ADD date DATETIME NOT NULL, ADD commentaire VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE reclamation ADD sujet VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE reservation CHANGE date_reservation date DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce DROP FOREIGN KEY FK_F65593E5BCF5E72D');
        $this->addSql('ALTER TABLE annonce DROP FOREIGN KEY FK_F65593E5D5E86FF');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE etat');
        $this->addSql('DROP TABLE paiement');
        $this->addSql('DROP INDEX IDX_F65593E5D5E86FF ON annonce');
        $this->addSql('DROP INDEX IDX_F65593E5BCF5E72D ON annonce');
        $this->addSql('ALTER TABLE annonce DROP etat_id, DROP categorie_id, CHANGE message message VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE avis DROP date, DROP commentaire');
        $this->addSql('ALTER TABLE reclamation DROP sujet');
        $this->addSql('ALTER TABLE reservation CHANGE date date_reservation DATETIME NOT NULL');
    }
}
