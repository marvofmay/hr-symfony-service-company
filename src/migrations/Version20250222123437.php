<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250222123437 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE company (uuid CHAR(36) NOT NULL, full_name VARCHAR(1000) NOT NULL, short_name VARCHAR(200) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, company_uuid CHAR(36) DEFAULT NULL, INDEX IDX_4FBF094F92124A48 (company_uuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F92124A48 FOREIGN KEY (company_uuid) REFERENCES company (uuid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F92124A48');
        $this->addSql('DROP TABLE company');
    }
}
