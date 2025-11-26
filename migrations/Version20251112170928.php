<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251112170928 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE email DROP FOREIGN KEY `FK_E7927C742E95A675`');
        $this->addSql('ALTER TABLE email ADD CONSTRAINT FK_E7927C742E95A675 FOREIGN KEY (sender_uuid) REFERENCES user (uuid) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE employee RENAME INDEX index_external_uuid TO external_uuid');
        $this->addSql('ALTER TABLE employee RENAME INDEX index_first_name TO first_name');
        $this->addSql('ALTER TABLE employee RENAME INDEX index_last_name TO last_name');
        $this->addSql('ALTER TABLE employee RENAME INDEX index_pesel TO pesel');
        $this->addSql('ALTER TABLE employee RENAME INDEX index_employment_from TO employment_from');
        $this->addSql('ALTER TABLE employee RENAME INDEX index_employment_to TO employment_to');
        $this->addSql('ALTER TABLE event_log DROP FOREIGN KEY `FK_9EF0AD1664A61AE1`');
        $this->addSql('DROP INDEX IDX_9EF0AD1664A61AE1 ON event_log');
        $this->addSql('ALTER TABLE event_log CHANGE employee_uuid user_uuid CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE event_log ADD CONSTRAINT FK_9EF0AD16ABFE1C6F FOREIGN KEY (user_uuid) REFERENCES user (uuid) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_9EF0AD16ABFE1C6F ON event_log (user_uuid)');
        $this->addSql('ALTER TABLE event_store DROP FOREIGN KEY `FK_BE4CE95B64A61AE1`');
        $this->addSql('DROP INDEX idx_employee_uuid ON event_store');
        $this->addSql('ALTER TABLE event_store CHANGE employee_uuid user_uuid CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE event_store ADD CONSTRAINT FK_BE4CE95BABFE1C6F FOREIGN KEY (user_uuid) REFERENCES user (uuid)');
        $this->addSql('CREATE INDEX user_uuid ON event_store (user_uuid)');
        $this->addSql('ALTER TABLE event_store RENAME INDEX idx_aggregate_uuid TO aggregate_uuid');
        $this->addSql('ALTER TABLE event_store RENAME INDEX idx_aggregate_type TO aggregate_type');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY `FK_8C9F361064A61AE1`');
        $this->addSql('DROP INDEX IDX_8C9F361064A61AE1 ON file');
        $this->addSql('ALTER TABLE file CHANGE employee_uuid user_uuid CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610ABFE1C6F FOREIGN KEY (user_uuid) REFERENCES user (uuid) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_8C9F3610ABFE1C6F ON file (user_uuid)');
        $this->addSql('ALTER TABLE import DROP FOREIGN KEY `FK_9D4ECE1D64A61AE1`');
        $this->addSql('DROP INDEX IDX_9D4ECE1D64A61AE1 ON import');
        $this->addSql('ALTER TABLE import CHANGE employee_uuid user_uuid CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE import ADD CONSTRAINT FK_9D4ECE1DABFE1C6F FOREIGN KEY (user_uuid) REFERENCES user (uuid) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX kind ON import (kind)');
        $this->addSql('CREATE INDEX status ON import (status)');
        $this->addSql('CREATE INDEX file_uuid ON import (file_uuid)');
        $this->addSql('CREATE INDEX user_uuid ON import (user_uuid)');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY `FK_CFBDFA1464A61AE1`');
        $this->addSql('DROP INDEX IDX_CFBDFA1464A61AE1 ON note');
        $this->addSql('ALTER TABLE note CHANGE employee_uuid user_uuid CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA14ABFE1C6F FOREIGN KEY (user_uuid) REFERENCES user (uuid) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_CFBDFA14ABFE1C6F ON note (user_uuid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE email DROP FOREIGN KEY FK_E7927C742E95A675');
        $this->addSql('ALTER TABLE email ADD CONSTRAINT `FK_E7927C742E95A675` FOREIGN KEY (sender_uuid) REFERENCES employee (uuid) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('ALTER TABLE employee RENAME INDEX external_uuid TO index_external_uuid');
        $this->addSql('ALTER TABLE employee RENAME INDEX first_name TO index_first_name');
        $this->addSql('ALTER TABLE employee RENAME INDEX last_name TO index_last_name');
        $this->addSql('ALTER TABLE employee RENAME INDEX pesel TO index_pesel');
        $this->addSql('ALTER TABLE employee RENAME INDEX employment_from TO index_employment_from');
        $this->addSql('ALTER TABLE employee RENAME INDEX employment_to TO index_employment_to');
        $this->addSql('ALTER TABLE event_log DROP FOREIGN KEY FK_9EF0AD16ABFE1C6F');
        $this->addSql('DROP INDEX IDX_9EF0AD16ABFE1C6F ON event_log');
        $this->addSql('ALTER TABLE event_log CHANGE user_uuid employee_uuid CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE event_log ADD CONSTRAINT `FK_9EF0AD1664A61AE1` FOREIGN KEY (employee_uuid) REFERENCES employee (uuid) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_9EF0AD1664A61AE1 ON event_log (employee_uuid)');
        $this->addSql('ALTER TABLE event_store DROP FOREIGN KEY FK_BE4CE95BABFE1C6F');
        $this->addSql('DROP INDEX user_uuid ON event_store');
        $this->addSql('ALTER TABLE event_store CHANGE user_uuid employee_uuid CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE event_store ADD CONSTRAINT `FK_BE4CE95B64A61AE1` FOREIGN KEY (employee_uuid) REFERENCES employee (uuid) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX idx_employee_uuid ON event_store (employee_uuid)');
        $this->addSql('ALTER TABLE event_store RENAME INDEX aggregate_uuid TO idx_aggregate_uuid');
        $this->addSql('ALTER TABLE event_store RENAME INDEX aggregate_type TO idx_aggregate_type');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610ABFE1C6F');
        $this->addSql('DROP INDEX IDX_8C9F3610ABFE1C6F ON file');
        $this->addSql('ALTER TABLE file CHANGE user_uuid employee_uuid CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT `FK_8C9F361064A61AE1` FOREIGN KEY (employee_uuid) REFERENCES employee (uuid) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_8C9F361064A61AE1 ON file (employee_uuid)');
        $this->addSql('ALTER TABLE import DROP FOREIGN KEY FK_9D4ECE1DABFE1C6F');
        $this->addSql('DROP INDEX kind ON import');
        $this->addSql('DROP INDEX status ON import');
        $this->addSql('DROP INDEX file_uuid ON import');
        $this->addSql('DROP INDEX user_uuid ON import');
        $this->addSql('ALTER TABLE import CHANGE user_uuid employee_uuid CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE import ADD CONSTRAINT `FK_9D4ECE1D64A61AE1` FOREIGN KEY (employee_uuid) REFERENCES employee (uuid) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_9D4ECE1D64A61AE1 ON import (employee_uuid)');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA14ABFE1C6F');
        $this->addSql('DROP INDEX IDX_CFBDFA14ABFE1C6F ON note');
        $this->addSql('ALTER TABLE note CHANGE user_uuid employee_uuid CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT `FK_CFBDFA1464A61AE1` FOREIGN KEY (employee_uuid) REFERENCES employee (uuid) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_CFBDFA1464A61AE1 ON note (employee_uuid)');
    }
}
