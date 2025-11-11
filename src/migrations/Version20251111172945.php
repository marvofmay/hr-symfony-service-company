<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251111172945 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification_template_setting ADD CONSTRAINT FK_46C8835C41E832AD FOREIGN KEY (event_name) REFERENCES notification_event_setting (event_name) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notification_template_setting ADD CONSTRAINT FK_46C8835CEB4193E6 FOREIGN KEY (channel_code) REFERENCES notification_channel_setting (channel_code) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification_template_setting DROP FOREIGN KEY FK_46C8835C41E832AD');
        $this->addSql('ALTER TABLE notification_template_setting DROP FOREIGN KEY FK_46C8835CEB4193E6');
    }
}
