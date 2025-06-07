<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250607084345 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE role_access (created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, role_uuid CHAR(36) NOT NULL, access_uuid CHAR(36) NOT NULL, INDEX IDX_AD4FCAE56FC02232 (role_uuid), INDEX IDX_AD4FCAE527A636A0 (access_uuid), PRIMARY KEY(role_uuid, access_uuid)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE role_access ADD CONSTRAINT FK_AD4FCAE56FC02232 FOREIGN KEY (role_uuid) REFERENCES role (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE role_access ADD CONSTRAINT FK_AD4FCAE527A636A0 FOREIGN KEY (access_uuid) REFERENCES access (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employee DROP FOREIGN KEY FK_5D9F75A192124A48');
        $this->addSql('DROP INDEX IDX_5D9F75A192124A48 ON employee');
        $this->addSql('ALTER TABLE employee DROP company_uuid');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE role_access DROP FOREIGN KEY FK_AD4FCAE56FC02232');
        $this->addSql('ALTER TABLE role_access DROP FOREIGN KEY FK_AD4FCAE527A636A0');
        $this->addSql('DROP TABLE role_access');
        $this->addSql('ALTER TABLE employee ADD company_uuid CHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A192124A48 FOREIGN KEY (company_uuid) REFERENCES company (uuid) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_5D9F75A192124A48 ON employee (company_uuid)');
    }
}
