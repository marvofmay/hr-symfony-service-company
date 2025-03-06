<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250305085020 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company ADD nip VARCHAR(20) NOT NULL, ADD regon VARCHAR(20) NOT NULL, ADD description VARCHAR(500) NOT NULL, ADD industry_uuid CHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F4B8AA895 FOREIGN KEY (industry_uuid) REFERENCES industry (uuid) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4FBF094F4B8AA895 ON company (industry_uuid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F4B8AA895');
        $this->addSql('DROP INDEX IDX_4FBF094F4B8AA895 ON company');
        $this->addSql('ALTER TABLE company DROP nip, DROP regon, DROP description, DROP industry_uuid');
    }
}
