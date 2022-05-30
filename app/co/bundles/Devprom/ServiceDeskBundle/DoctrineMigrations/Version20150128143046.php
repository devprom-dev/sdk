<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150128143046 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        if ($schema->getTable('cms_ExternalUser')->hasIndex('UNIQ_59F2E2C792FC23A8')) {
            $this->addSql('DROP INDEX UNIQ_59F2E2C792FC23A8 ON cms_ExternalUser');
        }
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('CREATE UNIQUE INDEX UNIQ_59F2E2C792FC23A8 ON cms_ExternalUser (username_canonical)');
    }
}
