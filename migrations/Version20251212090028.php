<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251212090028 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commander_produits (id INT AUTO_INCREMENT NOT NULL, commande_id INT DEFAULT NULL, produit_id INT NOT NULL, qte INT NOT NULL, INDEX IDX_3A4248CE82EA2E54 (commande_id), INDEX IDX_3A4248CEF347EFB (produit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commander_produits ADD CONSTRAINT FK_3A4248CE82EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE commander_produits ADD CONSTRAINT FK_3A4248CEF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commander_produits DROP FOREIGN KEY FK_3A4248CE82EA2E54');
        $this->addSql('ALTER TABLE commander_produits DROP FOREIGN KEY FK_3A4248CEF347EFB');
        $this->addSql('DROP TABLE commander_produits');
    }
}
