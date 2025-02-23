<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250223113022 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE employee CHANGE department_uuid department_uuid CHAR(36) NOT NULL, CHANGE position_uuid position_uuid CHAR(36) NOT NULL, CHANGE contract_type_uuid contract_type_uuid CHAR(36) NOT NULL, CHANGE role_uuid role_uuid CHAR(36) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE employee CHANGE department_uuid department_uuid CHAR(36) DEFAULT NULL, CHANGE position_uuid position_uuid CHAR(36) DEFAULT NULL, CHANGE contract_type_uuid contract_type_uuid CHAR(36) DEFAULT NULL, CHANGE role_uuid role_uuid CHAR(36) DEFAULT NULL');
    }
}
