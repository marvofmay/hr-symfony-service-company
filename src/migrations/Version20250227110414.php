<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250227110414 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE position_department (position_uuid CHAR(36) NOT NULL, department_uuid CHAR(36) NOT NULL, INDEX IDX_E3FCCD0AC2E77EF8 (position_uuid), INDEX IDX_E3FCCD0A736537F3 (department_uuid), PRIMARY KEY(position_uuid, department_uuid)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE position_department ADD CONSTRAINT FK_E3FCCD0AC2E77EF8 FOREIGN KEY (position_uuid) REFERENCES position (uuid)');
        $this->addSql('ALTER TABLE position_department ADD CONSTRAINT FK_E3FCCD0A736537F3 FOREIGN KEY (department_uuid) REFERENCES department (uuid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE position_department DROP FOREIGN KEY FK_E3FCCD0AC2E77EF8');
        $this->addSql('ALTER TABLE position_department DROP FOREIGN KEY FK_E3FCCD0A736537F3');
        $this->addSql('DROP TABLE position_department');
    }
}
