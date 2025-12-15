<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251207115447 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE produit_sub_category (produit_id INT NOT NULL, sub_category_id INT NOT NULL, INDEX IDX_5648A8E5F347EFB (produit_id), INDEX IDX_5648A8E5F7BFE87C (sub_category_id), PRIMARY KEY(produit_id, sub_category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE produit_sub_category ADD CONSTRAINT FK_5648A8E5F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE produit_sub_category ADD CONSTRAINT FK_5648A8E5F7BFE87C FOREIGN KEY (sub_category_id) REFERENCES sub_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE produit DROP categorie');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit_sub_category DROP FOREIGN KEY FK_5648A8E5F347EFB');
        $this->addSql('ALTER TABLE produit_sub_category DROP FOREIGN KEY FK_5648A8E5F7BFE87C');
        $this->addSql('DROP TABLE produit_sub_category');
        $this->addSql('ALTER TABLE produit ADD categorie VARCHAR(255) NOT NULL');
    }
}
