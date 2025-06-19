<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250618112928 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE access (uuid CHAR(36) NOT NULL, name VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, module_uuid CHAR(36) NOT NULL, UNIQUE INDEX UNIQ_6692B545E237E06 (name), INDEX IDX_6692B546B4315AA (module_uuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE address (uuid CHAR(36) NOT NULL, street VARCHAR(250) NOT NULL, postcode VARCHAR(10) NOT NULL, city VARCHAR(50) NOT NULL, country VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, company_uuid CHAR(36) DEFAULT NULL, department_uuid CHAR(36) DEFAULT NULL, employee_uuid CHAR(36) DEFAULT NULL, UNIQUE INDEX UNIQ_D4E6F8192124A48 (company_uuid), UNIQUE INDEX UNIQ_D4E6F81736537F3 (department_uuid), UNIQUE INDEX UNIQ_D4E6F8164A61AE1 (employee_uuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE company (uuid CHAR(36) NOT NULL, full_name VARCHAR(1000) NOT NULL, short_name VARCHAR(200) DEFAULT NULL, nip VARCHAR(20) NOT NULL, regon VARCHAR(20) NOT NULL, description VARCHAR(500) DEFAULT NULL, active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, company_uuid CHAR(36) DEFAULT NULL, industry_uuid CHAR(36) NOT NULL, INDEX IDX_4FBF094F92124A48 (company_uuid), INDEX IDX_4FBF094F4B8AA895 (industry_uuid), INDEX idx_short_name (short_name), INDEX idx_nip (nip), INDEX idx_regon (regon), INDEX idx_active (active), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE contact (uuid CHAR(36) NOT NULL, type VARCHAR(50) NOT NULL, data VARCHAR(100) NOT NULL, active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, company_uuid CHAR(36) DEFAULT NULL, department_uuid CHAR(36) DEFAULT NULL, employee_uuid CHAR(36) DEFAULT NULL, INDEX IDX_4C62E63892124A48 (company_uuid), INDEX IDX_4C62E638736537F3 (department_uuid), INDEX IDX_4C62E63864A61AE1 (employee_uuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE contract_type (uuid CHAR(36) NOT NULL, name VARCHAR(200) NOT NULL, description LONGTEXT DEFAULT NULL, active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE department (uuid CHAR(36) NOT NULL, name VARCHAR(1000) NOT NULL, description VARCHAR(500) DEFAULT NULL, active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, company_uuid CHAR(36) NOT NULL, department_uuid CHAR(36) DEFAULT NULL, INDEX IDX_CD1DE18A92124A48 (company_uuid), INDEX IDX_CD1DE18A736537F3 (department_uuid), INDEX idx_active (active), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE employee (uuid CHAR(36) NOT NULL, external_uuid VARCHAR(100) DEFAULT NULL, first_name VARCHAR(50) NOT NULL, last_name VARCHAR(50) NOT NULL, pesel VARCHAR(11) NOT NULL, employment_from DATE NOT NULL, employment_to DATE DEFAULT NULL, active TINYINT(1) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, department_uuid CHAR(36) NOT NULL, employee_uuid CHAR(36) DEFAULT NULL, position_uuid CHAR(36) NOT NULL, contract_type_uuid CHAR(36) NOT NULL, role_uuid CHAR(36) NOT NULL, INDEX IDX_5D9F75A1736537F3 (department_uuid), INDEX IDX_5D9F75A164A61AE1 (employee_uuid), INDEX IDX_5D9F75A1C2E77EF8 (position_uuid), INDEX IDX_5D9F75A12BE7CD38 (contract_type_uuid), INDEX IDX_5D9F75A16FC02232 (role_uuid), INDEX index_external_uuid (external_uuid), INDEX index_first_name (first_name), INDEX index_last_name (last_name), INDEX index_pesel (pesel), INDEX index_employment_from (employment_from), INDEX index_employment_to (employment_to), INDEX index_active (active), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE file (uuid CHAR(36) NOT NULL, file_name VARCHAR(150) NOT NULL, file_path VARCHAR(250) NOT NULL, extension VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, kind VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, employee_uuid CHAR(36) DEFAULT NULL, INDEX IDX_8C9F361064A61AE1 (employee_uuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE import (uuid CHAR(36) NOT NULL, kind VARCHAR(255) NOT NULL, started_at DATETIME NOT NULL, stopped_at DATETIME DEFAULT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, employee_uuid CHAR(36) DEFAULT NULL, file_uuid CHAR(36) NOT NULL, INDEX IDX_9D4ECE1D64A61AE1 (employee_uuid), UNIQUE INDEX UNIQ_9D4ECE1D588338C8 (file_uuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE import_log (uuid CHAR(36) NOT NULL, kind VARCHAR(255) NOT NULL, data JSON DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, import_uuid CHAR(36) NOT NULL, INDEX IDX_1B52C8453C53BE26 (import_uuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE import_report (uuid CHAR(36) NOT NULL, new_records INT NOT NULL, updated_records INT NOT NULL, errors INT NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, import_uuid CHAR(36) NOT NULL, UNIQUE INDEX UNIQ_B81ECD9C3C53BE26 (import_uuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE industry (uuid CHAR(36) NOT NULL, name VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_CDFA6CA05E237E06 (name), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE module (uuid CHAR(36) NOT NULL, name VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_C2426285E237E06 (name), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE note (uuid CHAR(36) NOT NULL, title VARCHAR(100) NOT NULL, content LONGTEXT DEFAULT NULL, priority VARCHAR(20) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, employee_uuid CHAR(36) NOT NULL, INDEX IDX_CFBDFA1464A61AE1 (employee_uuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE permission (uuid CHAR(36) NOT NULL, name VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_E04992AA5E237E06 (name), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE position (uuid CHAR(36) NOT NULL, name VARCHAR(200) NOT NULL, description LONGTEXT DEFAULT NULL, active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE position_department (position_uuid CHAR(36) NOT NULL, department_uuid CHAR(36) NOT NULL, INDEX IDX_E3FCCD0AC2E77EF8 (position_uuid), INDEX IDX_E3FCCD0A736537F3 (department_uuid), PRIMARY KEY(position_uuid, department_uuid)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE role (uuid CHAR(36) NOT NULL, name VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_57698A6A5E237E06 (name), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE role_access (created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, role_uuid CHAR(36) NOT NULL, access_uuid CHAR(36) NOT NULL, INDEX IDX_AD4FCAE56FC02232 (role_uuid), INDEX IDX_AD4FCAE527A636A0 (access_uuid), PRIMARY KEY(role_uuid, access_uuid)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE role_access_permission (created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, role_uuid CHAR(36) NOT NULL, access_uuid CHAR(36) NOT NULL, permission_uuid CHAR(36) NOT NULL, INDEX IDX_8A9ECDD46FC02232 (role_uuid), INDEX IDX_8A9ECDD427A636A0 (access_uuid), INDEX IDX_8A9ECDD480B1CB06 (permission_uuid), PRIMARY KEY(role_uuid, access_uuid, permission_uuid)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (uuid CHAR(36) NOT NULL, employee_uuid CHAR(36) DEFAULT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D64964A61AE1 (employee_uuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE access ADD CONSTRAINT FK_6692B546B4315AA FOREIGN KEY (module_uuid) REFERENCES module (uuid)');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F8192124A48 FOREIGN KEY (company_uuid) REFERENCES company (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F81736537F3 FOREIGN KEY (department_uuid) REFERENCES department (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F8164A61AE1 FOREIGN KEY (employee_uuid) REFERENCES employee (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F92124A48 FOREIGN KEY (company_uuid) REFERENCES company (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F4B8AA895 FOREIGN KEY (industry_uuid) REFERENCES industry (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E63892124A48 FOREIGN KEY (company_uuid) REFERENCES company (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E638736537F3 FOREIGN KEY (department_uuid) REFERENCES department (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E63864A61AE1 FOREIGN KEY (employee_uuid) REFERENCES employee (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE department ADD CONSTRAINT FK_CD1DE18A92124A48 FOREIGN KEY (company_uuid) REFERENCES company (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE department ADD CONSTRAINT FK_CD1DE18A736537F3 FOREIGN KEY (department_uuid) REFERENCES department (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A1736537F3 FOREIGN KEY (department_uuid) REFERENCES department (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A164A61AE1 FOREIGN KEY (employee_uuid) REFERENCES employee (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A1C2E77EF8 FOREIGN KEY (position_uuid) REFERENCES position (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A12BE7CD38 FOREIGN KEY (contract_type_uuid) REFERENCES contract_type (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A16FC02232 FOREIGN KEY (role_uuid) REFERENCES role (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F361064A61AE1 FOREIGN KEY (employee_uuid) REFERENCES employee (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE import ADD CONSTRAINT FK_9D4ECE1D64A61AE1 FOREIGN KEY (employee_uuid) REFERENCES employee (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE import ADD CONSTRAINT FK_9D4ECE1D588338C8 FOREIGN KEY (file_uuid) REFERENCES file (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE import_log ADD CONSTRAINT FK_1B52C8453C53BE26 FOREIGN KEY (import_uuid) REFERENCES import (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE import_report ADD CONSTRAINT FK_B81ECD9C3C53BE26 FOREIGN KEY (import_uuid) REFERENCES import (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA1464A61AE1 FOREIGN KEY (employee_uuid) REFERENCES employee (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE position_department ADD CONSTRAINT FK_E3FCCD0AC2E77EF8 FOREIGN KEY (position_uuid) REFERENCES position (uuid)');
        $this->addSql('ALTER TABLE position_department ADD CONSTRAINT FK_E3FCCD0A736537F3 FOREIGN KEY (department_uuid) REFERENCES department (uuid)');
        $this->addSql('ALTER TABLE role_access ADD CONSTRAINT FK_AD4FCAE56FC02232 FOREIGN KEY (role_uuid) REFERENCES role (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE role_access ADD CONSTRAINT FK_AD4FCAE527A636A0 FOREIGN KEY (access_uuid) REFERENCES access (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE role_access_permission ADD CONSTRAINT FK_8A9ECDD46FC02232 FOREIGN KEY (role_uuid) REFERENCES role (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE role_access_permission ADD CONSTRAINT FK_8A9ECDD427A636A0 FOREIGN KEY (access_uuid) REFERENCES access (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE role_access_permission ADD CONSTRAINT FK_8A9ECDD480B1CB06 FOREIGN KEY (permission_uuid) REFERENCES permission (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64964A61AE1 FOREIGN KEY (employee_uuid) REFERENCES employee (uuid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE access DROP FOREIGN KEY FK_6692B546B4315AA');
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F8192124A48');
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F81736537F3');
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F8164A61AE1');
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F92124A48');
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F4B8AA895');
        $this->addSql('ALTER TABLE contact DROP FOREIGN KEY FK_4C62E63892124A48');
        $this->addSql('ALTER TABLE contact DROP FOREIGN KEY FK_4C62E638736537F3');
        $this->addSql('ALTER TABLE contact DROP FOREIGN KEY FK_4C62E63864A61AE1');
        $this->addSql('ALTER TABLE department DROP FOREIGN KEY FK_CD1DE18A92124A48');
        $this->addSql('ALTER TABLE department DROP FOREIGN KEY FK_CD1DE18A736537F3');
        $this->addSql('ALTER TABLE employee DROP FOREIGN KEY FK_5D9F75A1736537F3');
        $this->addSql('ALTER TABLE employee DROP FOREIGN KEY FK_5D9F75A164A61AE1');
        $this->addSql('ALTER TABLE employee DROP FOREIGN KEY FK_5D9F75A1C2E77EF8');
        $this->addSql('ALTER TABLE employee DROP FOREIGN KEY FK_5D9F75A12BE7CD38');
        $this->addSql('ALTER TABLE employee DROP FOREIGN KEY FK_5D9F75A16FC02232');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F361064A61AE1');
        $this->addSql('ALTER TABLE import DROP FOREIGN KEY FK_9D4ECE1D64A61AE1');
        $this->addSql('ALTER TABLE import DROP FOREIGN KEY FK_9D4ECE1D588338C8');
        $this->addSql('ALTER TABLE import_log DROP FOREIGN KEY FK_1B52C8453C53BE26');
        $this->addSql('ALTER TABLE import_report DROP FOREIGN KEY FK_B81ECD9C3C53BE26');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA1464A61AE1');
        $this->addSql('ALTER TABLE position_department DROP FOREIGN KEY FK_E3FCCD0AC2E77EF8');
        $this->addSql('ALTER TABLE position_department DROP FOREIGN KEY FK_E3FCCD0A736537F3');
        $this->addSql('ALTER TABLE role_access DROP FOREIGN KEY FK_AD4FCAE56FC02232');
        $this->addSql('ALTER TABLE role_access DROP FOREIGN KEY FK_AD4FCAE527A636A0');
        $this->addSql('ALTER TABLE role_access_permission DROP FOREIGN KEY FK_8A9ECDD46FC02232');
        $this->addSql('ALTER TABLE role_access_permission DROP FOREIGN KEY FK_8A9ECDD427A636A0');
        $this->addSql('ALTER TABLE role_access_permission DROP FOREIGN KEY FK_8A9ECDD480B1CB06');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64964A61AE1');
        $this->addSql('DROP TABLE access');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE contract_type');
        $this->addSql('DROP TABLE department');
        $this->addSql('DROP TABLE employee');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE import');
        $this->addSql('DROP TABLE import_log');
        $this->addSql('DROP TABLE import_report');
        $this->addSql('DROP TABLE industry');
        $this->addSql('DROP TABLE module');
        $this->addSql('DROP TABLE note');
        $this->addSql('DROP TABLE permission');
        $this->addSql('DROP TABLE position');
        $this->addSql('DROP TABLE position_department');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE role_access');
        $this->addSql('DROP TABLE role_access_permission');
        $this->addSql('DROP TABLE user');
    }
}
