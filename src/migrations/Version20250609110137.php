<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250609110137 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE INDEX idx_short_name ON company (short_name)');
        $this->addSql('CREATE INDEX idx_nip ON company (nip)');
        $this->addSql('CREATE INDEX idx_regon ON company (regon)');
        $this->addSql('CREATE INDEX idx_active ON company (active)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_short_name ON company');
        $this->addSql('DROP INDEX idx_nip ON company');
        $this->addSql('DROP INDEX idx_regon ON company');
        $this->addSql('DROP INDEX idx_active ON company');
    }
}
