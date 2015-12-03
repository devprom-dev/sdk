<?php

class SystemTemplateRegistry extends ObjectRegistrySQL
{
	function createSQLIterator($sql)
	{
		$language = strtolower(getSession()->getLanguageUid());
        return $this->createIterator( array (
			array (
				'cms_BackupId' => 1,
				'Caption' => text(2021),
				'BackupFileName' =>
						addslashes(SERVER_ROOT_PATH."co/bundles/Devprom/CommonBundle/Resources/views/Emails/".$language."/user-registration.twig"),
				'AffectedDate' => microtime(true)
			),
			array (
				'cms_BackupId' => 2,
				'Caption' => text(2022),
				'BackupFileName' =>
						addslashes(SERVER_ROOT_PATH."co/bundles/Devprom/ServiceDeskBundle/Resources/translations/client.".$language.".php"),
				'AffectedDate' => microtime(true)
			),
			array (
				'cms_BackupId' => 3,
				'Caption' => text(2023),
				'BackupFileName' =>
						addslashes(SERVER_ROOT_PATH."co/bundles/Devprom/ServiceDeskBundle/Resources/translations/emails.".$language.".php"),
				'AffectedDate' => microtime(true)
			),
			array (
				'cms_BackupId' => 4,
				'Caption' => text(2054),
				'BackupFileName' =>
					addslashes(SERVER_ROOT_PATH."pm/bundles/Devprom/ProjectBundle/Resources/views/Emails/".$language."/digest.twig"),
				'AffectedDate' => microtime(true)
			),
            array (
                'cms_BackupId' => 5,
                'Caption' => text(2055),
                'BackupFileName' =>
                    addslashes(SERVER_ROOT_PATH."pm/bundles/Devprom/ProjectBundle/Resources/views/Emails/".$language."/discussion.twig"),
                'AffectedDate' => microtime(true)
            ),
            array (
                'cms_BackupId' => 6,
                'Caption' => text(2056),
                'BackupFileName' =>
                    addslashes(SERVER_ROOT_PATH."pm/bundles/Devprom/ProjectBundle/Resources/views/Emails/".$language."/object-changed.twig"),
                'AffectedDate' => microtime(true)
            )
        ));
	}
}