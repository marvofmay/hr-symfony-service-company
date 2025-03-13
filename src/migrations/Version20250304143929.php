<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250304143929 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change column type from INT to STRING(50) in contact table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE contact MODIFY COLUMN type VARCHAR(50) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE contact MODIFY COLUMN type INT NOT NULL');
    }
}
