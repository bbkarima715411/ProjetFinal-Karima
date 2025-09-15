<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250915082411 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE lot (id INT AUTO_INCREMENT NOT NULL, evenement_enchere_id INT NOT NULL, lot VARCHAR(255) DEFAULT NULL, categorie VARCHAR(255) DEFAULT NULL, paiement DOUBLE PRECISION DEFAULT NULL, facture VARCHAR(255) DEFAULT NULL, INDEX IDX_B81291B1971D2B5 (evenement_enchere_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE lot ADD CONSTRAINT FK_B81291B1971D2B5 FOREIGN KEY (evenement_enchere_id) REFERENCES evenement_enchere (id)');
        $this->addSql('ALTER TABLE enchère_utulisateur ADD lot_id INT NOT NULL, ADD utilisateur_id INT NOT NULL');
        $this->addSql('ALTER TABLE enchère_utulisateur ADD CONSTRAINT FK_54CF2F3BA8CBA5F7 FOREIGN KEY (lot_id) REFERENCES lot (id)');
        $this->addSql('ALTER TABLE enchère_utulisateur ADD CONSTRAINT FK_54CF2F3BFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_54CF2F3BA8CBA5F7 ON enchère_utulisateur (lot_id)');
        $this->addSql('CREATE INDEX IDX_54CF2F3BFB88E14F ON enchère_utulisateur (utilisateur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE enchère_utulisateur DROP FOREIGN KEY FK_54CF2F3BA8CBA5F7');
        $this->addSql('ALTER TABLE lot DROP FOREIGN KEY FK_B81291B1971D2B5');
        $this->addSql('DROP TABLE lot');
        $this->addSql('ALTER TABLE enchère_utulisateur DROP FOREIGN KEY FK_54CF2F3BFB88E14F');
        $this->addSql('DROP INDEX IDX_54CF2F3BA8CBA5F7 ON enchère_utulisateur');
        $this->addSql('DROP INDEX IDX_54CF2F3BFB88E14F ON enchère_utulisateur');
        $this->addSql('ALTER TABLE enchère_utulisateur DROP lot_id, DROP utilisateur_id');
    }
}
