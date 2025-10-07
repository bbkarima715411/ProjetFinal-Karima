<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250101000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add missing columns to EvenementEnchere entity';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenement_enchere ADD titre VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE evenement_enchere ADD debut_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE evenement_enchere ADD fin_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE evenement_enchere ADD statut VARCHAR(20) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenement_enchere DROP titre');
        $this->addSql('ALTER TABLE evenement_enchere DROP debut_at');
        $this->addSql('ALTER TABLE evenement_enchere DROP fin_at');
        $this->addSql('ALTER TABLE evenement_enchere DROP statut');
    }
}
