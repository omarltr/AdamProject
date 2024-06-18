<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240618224210 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adresse ADD annonce_id INT DEFAULT NULL, ADD complement VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE adresse ADD CONSTRAINT FK_C35F08168805AB2F FOREIGN KEY (annonce_id) REFERENCES annonce (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C35F08168805AB2F ON adresse (annonce_id)');
        $this->addSql('ALTER TABLE categorie ADD icon VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE paiement ADD url_pdf VARCHAR(255) NOT NULL, ADD montant_total VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adresse DROP FOREIGN KEY FK_C35F08168805AB2F');
        $this->addSql('DROP INDEX UNIQ_C35F08168805AB2F ON adresse');
        $this->addSql('ALTER TABLE adresse DROP annonce_id, DROP complement');
        $this->addSql('ALTER TABLE categorie DROP icon');
        $this->addSql('ALTER TABLE paiement DROP url_pdf, DROP montant_total');
    }
}
