<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150128143045 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        try {
            $this->addSql('ALTER TABLE cms_ExternalUser ADD Company INTEGER');
        }
        catch(Exception $e) {
        }
        try {
            $this->addSql('ALTER TABLE cms_ExternalUser ADD RecordCreated TIMESTAMP DEFAULT CURRENT_TIMESTAMP');
        }
        catch(Exception $e) {
        }
        try {
            $this->addSql('ALTER TABLE cms_ExternalUser ADD RecordModified TIMESTAMP DEFAULT NOW()');
        }
        catch(Exception $e) {
        }
        try {
            $this->addSql('ALTER TABLE cms_ExternalUser ADD RecordVersion INTEGER DEFAULT 0');
        }
        catch(Exception $e) {
        }
        try {
    	    $this->addSql('ALTER TABLE cms_ExternalUser ADD VPD VARCHAR(32)');
        }
        catch(Exception $e) {
        }
    }

    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE cms_ExternalUser DROP COLUMN Company');
        $this->addSql('ALTER TABLE cms_ExternalUser DROP COLUMN RecordCreated');
        $this->addSql('ALTER TABLE cms_ExternalUser DROP COLUMN RecordModified');
        $this->addSql('ALTER TABLE cms_ExternalUser DROP COLUMN RecordVersion');
    	$this->addSql('ALTER TABLE cms_ExternalUser DROP COLUMN VPD');
    }
}
