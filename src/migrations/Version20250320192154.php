<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250320192154 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE role_access_permission (created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, role_uuid CHAR(36) NOT NULL, access_uuid CHAR(36) NOT NULL, permission_uuid CHAR(36) NOT NULL, INDEX IDX_8A9ECDD46FC02232 (role_uuid), INDEX IDX_8A9ECDD427A636A0 (access_uuid), INDEX IDX_8A9ECDD480B1CB06 (permission_uuid), PRIMARY KEY(role_uuid, access_uuid, permission_uuid)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE role_access_permission ADD CONSTRAINT FK_8A9ECDD46FC02232 FOREIGN KEY (role_uuid) REFERENCES role (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE role_access_permission ADD CONSTRAINT FK_8A9ECDD427A636A0 FOREIGN KEY (access_uuid) REFERENCES access (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE role_access_permission ADD CONSTRAINT FK_8A9ECDD480B1CB06 FOREIGN KEY (permission_uuid) REFERENCES permission (uuid) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE role_access_permission DROP FOREIGN KEY FK_8A9ECDD46FC02232');
        $this->addSql('ALTER TABLE role_access_permission DROP FOREIGN KEY FK_8A9ECDD427A636A0');
        $this->addSql('ALTER TABLE role_access_permission DROP FOREIGN KEY FK_8A9ECDD480B1CB06');
        $this->addSql('DROP TABLE role_access_permission');
    }
}
