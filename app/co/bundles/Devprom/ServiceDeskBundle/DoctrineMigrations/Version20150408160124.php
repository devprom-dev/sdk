<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150408160124 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        try {
            $this->addSql('ALTER TABLE cms_ExternalUser MODIFY enabled INTEGER DEFAULT 1');
            $this->addSql('ALTER TABLE cms_ExternalUser MODIFY locked INTEGER DEFAULT 0');
            $this->addSql('ALTER TABLE cms_ExternalUser MODIFY expired INTEGER DEFAULT 0');
            $this->addSql('ALTER TABLE cms_ExternalUser MODIFY credentials_expired INTEGER DEFAULT 0');
            $this->addSql('ALTER TABLE cms_ExternalUser MODIFY salt varchar(255) DEFAULT ""');
            $this->addSql('ALTER TABLE cms_ExternalUser MODIFY PASSWORD varchar(255) DEFAULT ""');
            $this->addSql('ALTER TABLE cms_ExternalUser MODIFY roles varchar(1024) DEFAULT "a:0:{}"');
        }
        catch(Exception $e) {
        }
    }

    public function down(Schema $schema)
    {
    }
}
