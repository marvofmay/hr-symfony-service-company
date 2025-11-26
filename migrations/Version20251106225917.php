<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251106225917 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE auth_event DROP FOREIGN KEY `FK_F78C5BD2A76ED395`');
        $this->addSql('DROP INDEX auth_event_user ON auth_event');
        $this->addSql('ALTER TABLE auth_event CHANGE user_id user_uuid CHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE auth_event ADD CONSTRAINT FK_F78C5BD2ABFE1C6F FOREIGN KEY (user_uuid) REFERENCES user (uuid) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX auth_event_user ON auth_event (user_uuid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE auth_event DROP FOREIGN KEY FK_F78C5BD2ABFE1C6F');
        $this->addSql('DROP INDEX auth_event_user ON auth_event');
        $this->addSql('ALTER TABLE auth_event CHANGE user_uuid user_id CHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE auth_event ADD CONSTRAINT `FK_F78C5BD2A76ED395` FOREIGN KEY (user_id) REFERENCES user (uuid) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX auth_event_user ON auth_event (user_id)');
    }
}
