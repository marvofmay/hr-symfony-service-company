<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250223101840 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE employee ADD employee_uuid CHAR(36) DEFAULT NULL, DROP superior_uuid, CHANGE company_uuid company_uuid CHAR(36) DEFAULT NULL, CHANGE department_uuid department_uuid CHAR(36) DEFAULT NULL, CHANGE position_uuid position_uuid CHAR(36) DEFAULT NULL, CHANGE contract_type_uuid contract_type_uuid CHAR(36) DEFAULT NULL, CHANGE role_uuid role_uuid CHAR(36) DEFAULT NULL, CHANGE employee_from employment_from DATETIME NOT NULL, CHANGE employee_to employment_to DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A192124A48 FOREIGN KEY (company_uuid) REFERENCES company (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A1736537F3 FOREIGN KEY (department_uuid) REFERENCES department (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A164A61AE1 FOREIGN KEY (employee_uuid) REFERENCES employee (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A1C2E77EF8 FOREIGN KEY (position_uuid) REFERENCES position (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A12BE7CD38 FOREIGN KEY (contract_type_uuid) REFERENCES contract_type (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A16FC02232 FOREIGN KEY (role_uuid) REFERENCES role (uuid) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_5D9F75A192124A48 ON employee (company_uuid)');
        $this->addSql('CREATE INDEX IDX_5D9F75A1736537F3 ON employee (department_uuid)');
        $this->addSql('CREATE INDEX IDX_5D9F75A164A61AE1 ON employee (employee_uuid)');
        $this->addSql('CREATE INDEX IDX_5D9F75A1C2E77EF8 ON employee (position_uuid)');
        $this->addSql('CREATE INDEX IDX_5D9F75A12BE7CD38 ON employee (contract_type_uuid)');
        $this->addSql('CREATE INDEX IDX_5D9F75A16FC02232 ON employee (role_uuid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE employee DROP FOREIGN KEY FK_5D9F75A192124A48');
        $this->addSql('ALTER TABLE employee DROP FOREIGN KEY FK_5D9F75A1736537F3');
        $this->addSql('ALTER TABLE employee DROP FOREIGN KEY FK_5D9F75A164A61AE1');
        $this->addSql('ALTER TABLE employee DROP FOREIGN KEY FK_5D9F75A1C2E77EF8');
        $this->addSql('ALTER TABLE employee DROP FOREIGN KEY FK_5D9F75A12BE7CD38');
        $this->addSql('ALTER TABLE employee DROP FOREIGN KEY FK_5D9F75A16FC02232');
        $this->addSql('DROP INDEX IDX_5D9F75A192124A48 ON employee');
        $this->addSql('DROP INDEX IDX_5D9F75A1736537F3 ON employee');
        $this->addSql('DROP INDEX IDX_5D9F75A164A61AE1 ON employee');
        $this->addSql('DROP INDEX IDX_5D9F75A1C2E77EF8 ON employee');
        $this->addSql('DROP INDEX IDX_5D9F75A12BE7CD38 ON employee');
        $this->addSql('DROP INDEX IDX_5D9F75A16FC02232 ON employee');
        $this->addSql('ALTER TABLE employee ADD superior_uuid CHAR(36) NOT NULL, DROP employee_uuid, CHANGE company_uuid company_uuid CHAR(36) NOT NULL, CHANGE department_uuid department_uuid CHAR(36) NOT NULL, CHANGE position_uuid position_uuid CHAR(36) NOT NULL, CHANGE contract_type_uuid contract_type_uuid CHAR(36) NOT NULL, CHANGE role_uuid role_uuid CHAR(36) NOT NULL, CHANGE employment_from employee_from DATETIME NOT NULL, CHANGE employment_to employee_to DATETIME DEFAULT NULL');
    }
}
