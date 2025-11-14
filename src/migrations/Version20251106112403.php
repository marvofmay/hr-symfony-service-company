<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251106112403 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE email (uuid CHAR(36) NOT NULL, recipients JSON NOT NULL, subject VARCHAR(255) DEFAULT NULL, message LONGTEXT NOT NULL, sent_at DATETIME DEFAULT NULL, status VARCHAR(50) DEFAULT \'draft\' NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, deleted_at DATETIME DEFAULT NULL, sender_uuid CHAR(36) DEFAULT NULL, INDEX IDX_E7927C742E95A675 (sender_uuid), INDEX sent_at (sent_at), PRIMARY KEY (uuid)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE email ADD CONSTRAINT FK_E7927C742E95A675 FOREIGN KEY (sender_uuid) REFERENCES employee (uuid) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE contract_type CHANGE name name VARCHAR(100) NOT NULL, CHANGE active active TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E4AB19415E237E06 ON contract_type (name)');
        $this->addSql('CREATE INDEX name ON contract_type (name)');
        $this->addSql('ALTER TABLE file ADD email_uuid CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F36104E497023 FOREIGN KEY (email_uuid) REFERENCES email (uuid) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_8C9F36104E497023 ON file (email_uuid)');
        $this->addSql('CREATE INDEX name ON industry (name)');
        $this->addSql('ALTER TABLE position CHANGE name name VARCHAR(100) NOT NULL, CHANGE active active TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_462CE4F55E237E06 ON position (name)');
        $this->addSql('CREATE INDEX name ON role (name)');
        $this->addSql('ALTER TABLE user CHANGE email email VARCHAR(100) NOT NULL');
        $this->addSql('CREATE INDEX email ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE email DROP FOREIGN KEY FK_E7927C742E95A675');
        $this->addSql('DROP TABLE email');
        $this->addSql('DROP INDEX UNIQ_E4AB19415E237E06 ON contract_type');
        $this->addSql('DROP INDEX name ON contract_type');
        $this->addSql('ALTER TABLE contract_type CHANGE name name VARCHAR(200) NOT NULL, CHANGE active active TINYINT(1) DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F36104E497023');
        $this->addSql('DROP INDEX IDX_8C9F36104E497023 ON file');
        $this->addSql('ALTER TABLE file DROP email_uuid');
        $this->addSql('DROP INDEX name ON industry');
        $this->addSql('DROP INDEX UNIQ_462CE4F55E237E06 ON position');
        $this->addSql('ALTER TABLE position CHANGE name name VARCHAR(200) NOT NULL, CHANGE active active TINYINT(1) DEFAULT 1 NOT NULL');
        $this->addSql('DROP INDEX name ON role');
        $this->addSql('DROP INDEX email ON user');
        $this->addSql('ALTER TABLE user CHANGE email email VARCHAR(255) NOT NULL');
    }
}
