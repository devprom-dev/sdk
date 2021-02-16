<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20200512160125 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        if (!$schema->getTable('cms_ExternalUser')->hasColumn('dn')) {
            $this->addSql('ALTER TABLE cms_ExternalUser ADD dn TEXT');
        }
    }

    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE cms_ExternalUser DROP COLUMN dn');
    }
}
