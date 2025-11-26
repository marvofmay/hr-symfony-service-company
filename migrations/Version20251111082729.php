<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251111082729 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification_template_setting ADD is_default TINYINT(1) DEFAULT 0 NOT NULL, ADD is_active TINYINT(1) DEFAULT 0 NOT NULL, DROP `default`, DROP active');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification_template_setting ADD `default` TINYINT(1) DEFAULT 0 NOT NULL, ADD active TINYINT(1) DEFAULT 0 NOT NULL, DROP is_default, DROP is_active');
    }
}
