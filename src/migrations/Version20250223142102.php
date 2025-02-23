<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250223142102 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contact (uuid CHAR(36) NOT NULL, type INT NOT NULL, data VARCHAR(100) NOT NULL, active TINYINT(1) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, company_uuid CHAR(36) NOT NULL, department_uuid CHAR(36) DEFAULT NULL, employee_uuid CHAR(36) DEFAULT NULL, INDEX IDX_4C62E63892124A48 (company_uuid), INDEX IDX_4C62E638736537F3 (department_uuid), INDEX IDX_4C62E63864A61AE1 (employee_uuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E63892124A48 FOREIGN KEY (company_uuid) REFERENCES company (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E638736537F3 FOREIGN KEY (department_uuid) REFERENCES department (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E63864A61AE1 FOREIGN KEY (employee_uuid) REFERENCES employee (uuid) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact DROP FOREIGN KEY FK_4C62E63892124A48');
        $this->addSql('ALTER TABLE contact DROP FOREIGN KEY FK_4C62E638736537F3');
        $this->addSql('ALTER TABLE contact DROP FOREIGN KEY FK_4C62E63864A61AE1');
        $this->addSql('DROP TABLE contact');
    }
}
