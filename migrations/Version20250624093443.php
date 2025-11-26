<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250624093443 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event_log ADD employee_uuid CHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE event_log ADD CONSTRAINT FK_9EF0AD1664A61AE1 FOREIGN KEY (employee_uuid) REFERENCES employee (uuid) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_9EF0AD1664A61AE1 ON event_log (employee_uuid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event_log DROP FOREIGN KEY FK_9EF0AD1664A61AE1');
        $this->addSql('DROP INDEX IDX_9EF0AD1664A61AE1 ON event_log');
        $this->addSql('ALTER TABLE event_log DROP employee_uuid');
    }
}
