<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251112115346 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notification_message (uuid CHAR(36) NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, sent_at DATETIME DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, deleted_at DATETIME DEFAULT NULL, event_name VARCHAR(250) NOT NULL, channel_code VARCHAR(50) NOT NULL, template_uuid CHAR(36) DEFAULT NULL, INDEX IDX_A3A3BAC84B17ACB (template_uuid), INDEX event_name (event_name), INDEX channel_code (channel_code), PRIMARY KEY (uuid)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE notification_recipient (uuid CHAR(36) NOT NULL, user_uuid CHAR(36) NOT NULL, received_at DATETIME DEFAULT NULL, read_at DATETIME DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, deleted_at DATETIME DEFAULT NULL, message_uuid CHAR(36) NOT NULL, INDEX IDX_CEEDBC1678EACB7F (message_uuid), INDEX idx_user_uuid (user_uuid), PRIMARY KEY (uuid)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE notification_message ADD CONSTRAINT FK_A3A3BAC841E832AD FOREIGN KEY (event_name) REFERENCES notification_event_setting (event_name) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notification_message ADD CONSTRAINT FK_A3A3BAC8EB4193E6 FOREIGN KEY (channel_code) REFERENCES notification_channel_setting (channel_code) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notification_message ADD CONSTRAINT FK_A3A3BAC84B17ACB FOREIGN KEY (template_uuid) REFERENCES notification_template_setting (uuid) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE notification_recipient ADD CONSTRAINT FK_CEEDBC1678EACB7F FOREIGN KEY (message_uuid) REFERENCES notification_message (uuid) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification_message DROP FOREIGN KEY FK_A3A3BAC841E832AD');
        $this->addSql('ALTER TABLE notification_message DROP FOREIGN KEY FK_A3A3BAC8EB4193E6');
        $this->addSql('ALTER TABLE notification_message DROP FOREIGN KEY FK_A3A3BAC84B17ACB');
        $this->addSql('ALTER TABLE notification_recipient DROP FOREIGN KEY FK_CEEDBC1678EACB7F');
        $this->addSql('DROP TABLE notification_message');
        $this->addSql('DROP TABLE notification_recipient');
    }
}
