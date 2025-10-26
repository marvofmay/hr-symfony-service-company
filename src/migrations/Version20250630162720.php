<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250630162720 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event_store (uuid CHAR(36) NOT NULL, aggregate_uuid CHAR(36) NOT NULL, aggregate_type VARCHAR(255) NOT NULL, aggregate_class VARCHAR(255) NOT NULL, payload JSON NOT NULL, created_at DATETIME NOT NULL, employee_uuid CHAR(36) DEFAULT NULL, INDEX idx_aggregate_uuid (aggregate_uuid), INDEX idx_aggregate_type (aggregate_type), INDEX idx_employee_uuid (employee_uuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE event_store ADD CONSTRAINT FK_BE4CE95B64A61AE1 FOREIGN KEY (employee_uuid) REFERENCES employee (uuid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event_store DROP FOREIGN KEY FK_BE4CE95B64A61AE1');
        $this->addSql('DROP TABLE event_store');
    }
}
