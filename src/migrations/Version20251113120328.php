<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251113120328 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_E4AB19415E237E06 ON contract_type');
        $this->addSql('DROP INDEX UNIQ_CDFA6CA05E237E06 ON industry');
        $this->addSql('DROP INDEX UNIQ_462CE4F55E237E06 ON position');
        $this->addSql('DROP INDEX UNIQ_57698A6A5E237E06 ON role');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E4AB19415E237E06 ON contract_type (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CDFA6CA05E237E06 ON industry (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_462CE4F55E237E06 ON position (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_57698A6A5E237E06 ON role (name)');
    }
}
