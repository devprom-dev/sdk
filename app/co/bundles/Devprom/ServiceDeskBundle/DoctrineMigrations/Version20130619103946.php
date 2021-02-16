<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Database migration for [I-4035]
 * @see http://support.devprom.ru/pm/features/issues/board?mode=request&class=metaobject&entity=pm_ChangeRequest&pm_ChangeRequestId=4035&pm_ChangeRequestaction=view
 */
class Version20130619103946 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        if (!$schema->getTable('cms_ExternalUser')->hasColumn('language')) {
            $this->addSql('ALTER TABLE cms_ExternalUser ADD language VARCHAR(3)');
        }
    }

    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE cms_ExternalUser DROP language');
    }
}
