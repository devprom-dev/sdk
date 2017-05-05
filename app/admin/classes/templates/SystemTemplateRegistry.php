<?php

class SystemTemplateRegistry extends ObjectRegistrySQL
{
	function createSQLIterator($sql)
	{
	    $commonPath = SERVER_ROOT_PATH."co/bundles/Devprom/CommonBundle/Resources/";
        $serviceDeskPath = SERVER_ROOT_PATH."co/bundles/Devprom/ServiceDeskBundle/Resources/";
        $projectPath = SERVER_ROOT_PATH."pm/bundles/Devprom/ProjectBundle/Resources/";

		$language = strtolower(getSession()->getLanguageUid());
        return $this->createIterator( array (
			array (
				'cms_BackupId' => 1,
				'Caption' => text(2021),
				'BackupFileName' => addslashes($commonPath."views/Emails/".$language."/user-registration.twig"),
				'AffectedDate' => microtime(true)
			),
			array (
				'cms_BackupId' => 4,
				'Caption' => text(2054),
				'BackupFileName' => addslashes($projectPath."views/Emails/".$language."/digest.twig"),
				'AffectedDate' => microtime(true)
			),
            array (
                'cms_BackupId' => 5,
                'Caption' => text(2055),
                'BackupFileName' => addslashes($projectPath."views/Emails/".$language."/discussion.twig"),
                'AffectedDate' => microtime(true)
            ),
            array (
                'cms_BackupId' => 6,
                'Caption' => text(2056),
                'BackupFileName' => addslashes($projectPath."views/Emails/".$language."/object-changed.twig"),
                'AffectedDate' => microtime(true)
            ),
			array (
				'cms_BackupId' => 7,
				'Caption' => text(2022),
				'BackupFileName' => addslashes($serviceDeskPath."views/content_base.html.twig"),
				'AffectedDate' => microtime(true)
			),
			array (
				'cms_BackupId' => 9,
				'Caption' => text(2141),
				'BackupFileName' => addslashes($serviceDeskPath."views/Email/".$language."/issueCreated.twig"),
				'AffectedDate' => microtime(true)
			),
			array (
				'cms_BackupId' => 10,
				'Caption' => text(2142),
				'BackupFileName' => addslashes($serviceDeskPath."views/Email/".$language."/issueStateChanged.twig"),
				'AffectedDate' => microtime(true)
			),
			array (
				'cms_BackupId' => 11,
				'Caption' => text(2143),
				'BackupFileName' => addslashes($serviceDeskPath."views/Email/".$language."/issueCommented.twig"),
				'AffectedDate' => microtime(true)
			),
			array (
				'cms_BackupId' => 12,
				'Caption' => text(2144),
				'BackupFileName' => addslashes($serviceDeskPath."views/Email/".$language."/resetPassword.twig"),
				'AffectedDate' => microtime(true)
			),
			array (
				'cms_BackupId' => 13,
				'Caption' => text(2145),
				'BackupFileName' => addslashes($serviceDeskPath."views/Email/".$language."/registration.twig"),
				'AffectedDate' => microtime(true)
			)
        ));
	}
}