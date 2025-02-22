<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250222120439 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Przestawienie kolumny employee_uuid za uuid w tabeli note';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `note` CHANGE `employee_uuid` `employee_uuid` CHAR(36) NOT NULL AFTER `uuid`');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `note` CHANGE `employee_uuid` `employee_uuid` CHAR(36) NOT NULL BEFORE `uuid`');
    }
}
