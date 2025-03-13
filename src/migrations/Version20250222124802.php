<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250222124802 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE department (uuid CHAR(36) NOT NULL, name VARCHAR(1000) NOT NULL, active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, company_uuid CHAR(36) DEFAULT NULL, department_uuid CHAR(36) DEFAULT NULL, INDEX IDX_CD1DE18A92124A48 (company_uuid), INDEX IDX_CD1DE18A736537F3 (department_uuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE department ADD CONSTRAINT FK_CD1DE18A92124A48 FOREIGN KEY (company_uuid) REFERENCES company (uuid)');
        $this->addSql('ALTER TABLE department ADD CONSTRAINT FK_CD1DE18A736537F3 FOREIGN KEY (department_uuid) REFERENCES department (uuid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE department DROP FOREIGN KEY FK_CD1DE18A92124A48');
        $this->addSql('ALTER TABLE department DROP FOREIGN KEY FK_CD1DE18A736537F3');
        $this->addSql('DROP TABLE department');
    }
}
