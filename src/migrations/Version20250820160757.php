<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250820160757 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_active ON company');
        $this->addSql('ALTER TABLE company CHANGE internal_code internal_code VARCHAR(50) DEFAULT NULL');
        $this->addSql('CREATE INDEX idx_internal_code ON company (internal_code)');
        $this->addSql('DROP INDEX idx_active ON department');
        $this->addSql('ALTER TABLE department ADD internal_code VARCHAR(50) DEFAULT NULL');
        $this->addSql('CREATE INDEX idx_internal_code ON department (internal_code)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_internal_code ON company');
        $this->addSql('ALTER TABLE company CHANGE internal_code internal_code VARCHAR(200) DEFAULT NULL');
        $this->addSql('CREATE INDEX idx_active ON company (active)');
        $this->addSql('DROP INDEX idx_internal_code ON department');
        $this->addSql('ALTER TABLE department DROP internal_code');
        $this->addSql('CREATE INDEX idx_active ON department (active)');
    }
}
