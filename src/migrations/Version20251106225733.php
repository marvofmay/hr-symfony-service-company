<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251106225733 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE auth_event (uuid CHAR(36) NOT NULL, event_type VARCHAR(50) NOT NULL, ip VARCHAR(255) DEFAULT NULL, user_agent LONGTEXT DEFAULT NULL, token_uuid VARCHAR(255) DEFAULT NULL, meta JSON DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, deleted_at DATETIME DEFAULT NULL, user_id CHAR(36) NOT NULL, INDEX auth_event_user (user_id), INDEX auth_event_type (event_type), INDEX auth_event_created_at (created_at), PRIMARY KEY (uuid)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE auth_event ADD CONSTRAINT FK_F78C5BD2A76ED395 FOREIGN KEY (user_id) REFERENCES user (uuid) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE auth_event DROP FOREIGN KEY FK_F78C5BD2A76ED395');
        $this->addSql('DROP TABLE auth_event');
    }
}
