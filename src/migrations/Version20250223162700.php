<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250223162700 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE address DROP INDEX IDX_D4E6F8164A61AE1, ADD UNIQUE INDEX UNIQ_D4E6F8164A61AE1 (employee_uuid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE address DROP INDEX UNIQ_D4E6F8164A61AE1, ADD INDEX IDX_D4E6F8164A61AE1 (employee_uuid)');
    }
}
