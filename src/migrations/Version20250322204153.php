<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250322204153 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE roles_access DROP FOREIGN KEY FK_ED4E1E356FC02232');
        $this->addSql('ALTER TABLE roles_access ADD CONSTRAINT FK_ED4E1E356FC02232 FOREIGN KEY (role_uuid) REFERENCES role (uuid) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE roles_access DROP FOREIGN KEY FK_ED4E1E356FC02232');
        $this->addSql('ALTER TABLE roles_access ADD CONSTRAINT FK_ED4E1E356FC02232 FOREIGN KEY (role_uuid) REFERENCES role (uuid) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
