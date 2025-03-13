<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250222125424 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F92124A48');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F92124A48 FOREIGN KEY (company_uuid) REFERENCES company (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE department DROP FOREIGN KEY FK_CD1DE18A736537F3');
        $this->addSql('ALTER TABLE department DROP FOREIGN KEY FK_CD1DE18A92124A48');
        $this->addSql('ALTER TABLE department ADD CONSTRAINT FK_CD1DE18A736537F3 FOREIGN KEY (department_uuid) REFERENCES department (uuid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE department ADD CONSTRAINT FK_CD1DE18A92124A48 FOREIGN KEY (company_uuid) REFERENCES company (uuid) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F92124A48');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F92124A48 FOREIGN KEY (company_uuid) REFERENCES company (uuid) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE department DROP FOREIGN KEY FK_CD1DE18A92124A48');
        $this->addSql('ALTER TABLE department DROP FOREIGN KEY FK_CD1DE18A736537F3');
        $this->addSql('ALTER TABLE department ADD CONSTRAINT FK_CD1DE18A92124A48 FOREIGN KEY (company_uuid) REFERENCES company (uuid) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE department ADD CONSTRAINT FK_CD1DE18A736537F3 FOREIGN KEY (department_uuid) REFERENCES department (uuid) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
