<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251026132116 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE access CHANGE module_uuid module_uuid CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE company CHANGE industry_uuid industry_uuid CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE department CHANGE company_uuid company_uuid CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE employee CHANGE department_uuid department_uuid CHAR(36) DEFAULT NULL, CHANGE position_uuid position_uuid CHAR(36) DEFAULT NULL, CHANGE contract_type_uuid contract_type_uuid CHAR(36) DEFAULT NULL, CHANGE role_uuid role_uuid CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE import CHANGE file_uuid file_uuid CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE import_log CHANGE import_uuid import_uuid CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE import_report CHANGE import_uuid import_uuid CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE note CHANGE employee_uuid employee_uuid CHAR(36) DEFAULT NULL');
        $this->addSql('CREATE INDEX name ON position (name)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE access CHANGE module_uuid module_uuid CHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE company CHANGE industry_uuid industry_uuid CHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE department CHANGE company_uuid company_uuid CHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE employee CHANGE department_uuid department_uuid CHAR(36) NOT NULL, CHANGE position_uuid position_uuid CHAR(36) NOT NULL, CHANGE contract_type_uuid contract_type_uuid CHAR(36) NOT NULL, CHANGE role_uuid role_uuid CHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE import CHANGE file_uuid file_uuid CHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE import_log CHANGE import_uuid import_uuid CHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE import_report CHANGE import_uuid import_uuid CHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE note CHANGE employee_uuid employee_uuid CHAR(36) NOT NULL');
        $this->addSql('DROP INDEX name ON position');
    }
}
