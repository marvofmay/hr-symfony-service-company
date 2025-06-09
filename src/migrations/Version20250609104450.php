<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250609104450 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE INDEX index_external_uuid ON employee (external_uuid)');
        $this->addSql('CREATE INDEX index_first_name ON employee (first_name)');
        $this->addSql('CREATE INDEX index_last_name ON employee (last_name)');
        $this->addSql('CREATE INDEX index_employment_from ON employee (employment_from)');
        $this->addSql('CREATE INDEX index_employment_to ON employee (employment_to)');
        $this->addSql('CREATE INDEX index_active ON employee (active)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX index_external_uuid ON employee');
        $this->addSql('DROP INDEX index_first_name ON employee');
        $this->addSql('DROP INDEX index_last_name ON employee');
        $this->addSql('DROP INDEX index_employment_from ON employee');
        $this->addSql('DROP INDEX index_employment_to ON employee');
        $this->addSql('DROP INDEX index_active ON employee');
    }
}
