<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251110131157 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notification_channel_setting (channel_code VARCHAR(50) NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, deleted_at DATETIME DEFAULT NULL, PRIMARY KEY (channel_code)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE notification_event (notification_event VARCHAR(250) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, deleted_at DATETIME DEFAULT NULL, PRIMARY KEY (notification_event)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE notification_channel_setting');
        $this->addSql('DROP TABLE notification_event');
    }
}
