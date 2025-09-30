<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250925121842 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE enchere_utilisateur (id INT AUTO_INCREMENT NOT NULL, lot_id INT NOT NULL, utilisateur_id INT NOT NULL, montant DOUBLE PRECISION DEFAULT NULL, INDEX IDX_4AD5E89DA8CBA5F7 (lot_id), INDEX IDX_4AD5E89DFB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE enchere_utilisateur ADD CONSTRAINT FK_4AD5E89DA8CBA5F7 FOREIGN KEY (lot_id) REFERENCES lot (id)');
        $this->addSql('ALTER TABLE enchere_utilisateur ADD CONSTRAINT FK_4AD5E89DFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('DROP TABLE enchère_utulisateur');
        $this->addSql('ALTER TABLE lot ADD evenement_enchere_id INT NOT NULL, ADD image_filename VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE lot ADD CONSTRAINT FK_B81291B1971D2B5 FOREIGN KEY (evenement_enchere_id) REFERENCES evenement_enchere (id)');
        $this->addSql('CREATE INDEX IDX_B81291B1971D2B5 ON lot (evenement_enchere_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE enchère_utulisateur (id INT AUTO_INCREMENT NOT NULL, enchere_utlisateur DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE enchere_utilisateur DROP FOREIGN KEY FK_4AD5E89DA8CBA5F7');
        $this->addSql('ALTER TABLE enchere_utilisateur DROP FOREIGN KEY FK_4AD5E89DFB88E14F');
        $this->addSql('DROP TABLE enchere_utilisateur');
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE lot DROP FOREIGN KEY FK_B81291B1971D2B5');
        $this->addSql('DROP INDEX IDX_B81291B1971D2B5 ON lot');
        $this->addSql('ALTER TABLE lot DROP evenement_enchere_id, DROP image_filename');
    }
}
