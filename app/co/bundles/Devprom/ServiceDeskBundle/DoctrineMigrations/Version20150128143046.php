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
        try {
            $this->addSql('DROP INDEX UNIQ_59F2E2C792FC23A8 ON cms_ExternalUser');
        }
        catch(Exception $e) {
        }
    }

    public function down(Schema $schema)
    {
        $this->addSql('CREATE UNIQUE INDEX UNIQ_59F2E2C792FC23A8 ON cms_ExternalUser (username_canonical)');
    }
}
