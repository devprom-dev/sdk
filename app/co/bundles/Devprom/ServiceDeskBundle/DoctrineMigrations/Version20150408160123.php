<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150408160123 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        try {
            $this->addSql('ALTER TABLE cms_ExternalUser ADD Description TEXT');
        }
        catch(Exception $e) {
        }
    }

    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE cms_ExternalUser DROP COLUMN Description');
    }
}
