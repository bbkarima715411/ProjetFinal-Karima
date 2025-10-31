<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251029212750 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // 1. Ajouter la colonne en autorisant NULL
        $this->addSql('ALTER TABLE user ADD created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        
        // 2. Mettre à jour les enregistrements existants avec la date actuelle
        $this->addSql('UPDATE user SET created_at = NOW() WHERE created_at IS NULL');
        
        // 3. Modifier la colonne pour la rendre NOT NULL
        $this->addSql('ALTER TABLE user MODIFY created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP created_at');
    }
}
