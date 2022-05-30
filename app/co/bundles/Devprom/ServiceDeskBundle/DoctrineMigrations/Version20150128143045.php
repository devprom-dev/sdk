<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150128143045 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        if (!$schema->getTable('cms_ExternalUser')->hasColumn('Company')) {
            $this->addSql('ALTER TABLE cms_ExternalUser ADD Company INTEGER');
        }
        if (!$schema->getTable('cms_ExternalUser')->hasColumn('RecordCreated')) {
            $this->addSql('ALTER TABLE cms_ExternalUser ADD RecordCreated DATETIME DEFAULT NOW()');
        }
        if (!$schema->getTable('cms_ExternalUser')->hasColumn('RecordModified')) {
            $this->addSql('ALTER TABLE cms_ExternalUser ADD RecordModified DATETIME DEFAULT NOW()');
        }
        if (!$schema->getTable('cms_ExternalUser')->hasColumn('RecordVersion')) {
            $this->addSql('ALTER TABLE cms_ExternalUser ADD RecordVersion INTEGER DEFAULT 0');
        }
        if (!$schema->getTable('cms_ExternalUser')->hasColumn('VPD')) {
    	    $this->addSql('ALTER TABLE cms_ExternalUser ADD VPD VARCHAR(32)');
        }
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE cms_ExternalUser DROP COLUMN Company');
        $this->addSql('ALTER TABLE cms_ExternalUser DROP COLUMN RecordCreated');
        $this->addSql('ALTER TABLE cms_ExternalUser DROP COLUMN RecordModified');
        $this->addSql('ALTER TABLE cms_ExternalUser DROP COLUMN RecordVersion');
    	$this->addSql('ALTER TABLE cms_ExternalUser DROP COLUMN VPD');
    }
}
