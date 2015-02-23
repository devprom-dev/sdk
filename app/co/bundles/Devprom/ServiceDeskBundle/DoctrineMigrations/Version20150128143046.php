<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150128143046 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('DROP INDEX UNIQ_59F2E2C792FC23A8 ON cms_ExternalUser');
        $this->addSql('ALTER TABLE cms_ExternalUser ADD Company INTEGER');
        $this->addSql('ALTER TABLE cms_ExternalUser ADD RecordCreated TIMESTAMP DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE cms_ExternalUser ADD RecordModified TIMESTAMP');
        $this->addSql('ALTER TABLE cms_ExternalUser ADD RecordVersion INTEGER DEFAULT 0');
    	$this->addSql('ALTER TABLE cms_ExternalUser ADD VPD VARCHAR(32)');
    }

    public function down(Schema $schema)
    {
        $this->addSql('CREATE UNIQUE INDEX UNIQ_59F2E2C792FC23A8 ON cms_ExternalUser (username_canonical)');
        $this->addSql('ALTER TABLE cms_ExternalUser DROP COLUMN Company');
        $this->addSql('ALTER TABLE cms_ExternalUser DROP COLUMN RecordCreated');
        $this->addSql('ALTER TABLE cms_ExternalUser DROP COLUMN RecordModified');
        $this->addSql('ALTER TABLE cms_ExternalUser DROP COLUMN RecordVersion');
    	$this->addSql('ALTER TABLE cms_ExternalUser DROP COLUMN VPD');
    }
}
