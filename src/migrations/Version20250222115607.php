<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250222115607 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA1464A61AE1 FOREIGN KEY (employee_uuid) REFERENCES employee (uuid) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_CFBDFA1464A61AE1 ON note (employee_uuid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA1464A61AE1');
        $this->addSql('DROP INDEX IDX_CFBDFA1464A61AE1 ON note');
    }
}
