<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251107100903 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE revoked_token (token_uuid CHAR(36) NOT NULL, revoked_at DATETIME NOT NULL, expires_at DATETIME DEFAULT NULL, user_uuid CHAR(36) NOT NULL, INDEX user_uuid (user_uuid), PRIMARY KEY (token_uuid)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE revoked_token ADD CONSTRAINT FK_E5F664CABFE1C6F FOREIGN KEY (user_uuid) REFERENCES user (uuid) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE revoked_token DROP FOREIGN KEY FK_E5F664CABFE1C6F');
        $this->addSql('DROP TABLE revoked_token');
    }
}
