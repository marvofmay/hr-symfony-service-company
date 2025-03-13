<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250305135844 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE address DROP INDEX IDX_D4E6F81736537F3, ADD UNIQUE INDEX UNIQ_D4E6F81736537F3 (department_uuid)');
        $this->addSql('ALTER TABLE address CHANGE company_uuid company_uuid CHAR(36) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE address DROP INDEX UNIQ_D4E6F81736537F3, ADD INDEX IDX_D4E6F81736537F3 (department_uuid)');
        $this->addSql('ALTER TABLE address CHANGE company_uuid company_uuid CHAR(36) NOT NULL');
    }
}
