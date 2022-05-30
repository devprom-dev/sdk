<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150408160123 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        if (!$schema->getTable('cms_ExternalUser')->hasColumn('Description')) {
            $this->addSql('ALTER TABLE cms_ExternalUser ADD Description TEXT');
        }
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE cms_ExternalUser DROP COLUMN Description');
    }
}
