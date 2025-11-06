<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251106113941 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE email ADD failure_reason LONGTEXT DEFAULT NULL, CHANGE subject subject VARCHAR(255) NOT NULL, CHANGE status status VARCHAR(255) NOT NULL');
        $this->addSql('CREATE INDEX subject ON email (subject)');
        $this->addSql('ALTER TABLE email RENAME INDEX idx_e7927c742e95a675 TO sender_uuid');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX subject ON email');
        $this->addSql('ALTER TABLE email DROP failure_reason, CHANGE subject subject VARCHAR(255) DEFAULT NULL, CHANGE status status VARCHAR(50) DEFAULT \'draft\' NOT NULL');
        $this->addSql('ALTER TABLE email RENAME INDEX sender_uuid TO IDX_E7927C742E95A675');
    }
}
