<?php

class SystemTemplateRegistry extends ObjectRegistrySQL
{
	function createSQLIterator($sql)
	{
	    $commonPath = SERVER_ROOT_PATH."co/bundles/Devprom/CommonBundle/Resources/";
        $serviceDeskPath = SERVER_ROOT_PATH."co/bundles/Devprom/ServiceDeskBundle/Resources/";
        $projectPath = SERVER_ROOT_PATH."pm/bundles/Devprom/ProjectBundle/Resources/";

		$language = strtolower(getSession()->getLanguageUid());
        $data = array (
            array (
                'Caption' => text(2512),
                'BackupDirName' => addslashes($projectPath."views/EmailSubject/".$language),
                'BackupFileName' => addslashes("subject-changed.twig")
            ),
			array (
				'Caption' => text(2021),
				'BackupDirName' => addslashes($commonPath."views/Emails/".$language),
                'BackupFileName' => addslashes("user-registration.twig")
            ),
            array (
                'Caption' => text(2521),
                'BackupDirName' => addslashes($commonPath."views/Emails/".$language),
                'BackupFileName' => addslashes("restore.html.twig")
            ),
			array (
				'Caption' => text(2054),
				'BackupDirName' => addslashes($projectPath."views/Emails/".$language),
                'BackupFileName' => addslashes("digest.twig")
			),
            array (
                'Caption' => text(2055),
                'BackupDirName' => addslashes($projectPath."views/Emails/".$language),
                'BackupFileName' => addslashes("discussion.twig")
            ),
            array (
                'Caption' => text(2056),
                'BackupDirName' => addslashes($projectPath."views/Emails"),
                'BackupFileName' => addslashes("object-changed.twig")
            ),
            array (
                'Caption' => text(2612),
                'BackupDirName' => addslashes($projectPath."views/Emails/".$language),
                'BackupFileName' => addslashes("share-widget.twig")
            ),
            array (
                'Caption' => text(2696),
                'BackupDirName' => addslashes($projectPath."views/Emails/".$language),
                'BackupFileName' => addslashes("deadlines.twig")
            ),
            array (
                'Caption' => text(2022),
                'BackupDirName' => addslashes($serviceDeskPath."views"),
                'BackupFileName' => addslashes("content_base.html.twig")
            ),
            array (
                'Caption' => text(2516),
                'BackupDirName' => addslashes($serviceDeskPath."views"),
                'BackupFileName' => addslashes("Issue/new.html.twig")
            ),
            array (
                'Caption' => text(2517),
                'BackupDirName' => addslashes($serviceDeskPath."views"),
                'BackupFileName' => addslashes("Issue/edit.html.twig")
            ),
            array (
                'Caption' => text(2687),
                'BackupDirName' => addslashes($serviceDeskPath."views"),
                'BackupFileName' => addslashes("Issue/show.html.twig")
            ),
            array (
                'Caption' => text(2688),
                'BackupDirName' => addslashes($serviceDeskPath."views"),
                'BackupFileName' => addslashes("Issue/index.html.twig")
            ),
			array (
				'Caption' => text(2141),
                'BackupDirName' => addslashes($serviceDeskPath."views/Email/".$language),
				'BackupFileName' => addslashes("issueCreated.twig")
			),
			array (
				'Caption' => text(2142),
                'BackupDirName' => addslashes($serviceDeskPath."views/Email/".$language),
				'BackupFileName' => addslashes("issueStateChanged.twig")
			),
			array (
				'Caption' => text(2143),
                'BackupDirName' => addslashes($serviceDeskPath."views/Email/".$language),
				'BackupFileName' => addslashes("issueCommented.twig")
			),
			array (
				'Caption' => text(2144),
                'BackupDirName' => addslashes($serviceDeskPath."views/Email/".$language),
				'BackupFileName' => addslashes("resetPassword.twig")
			),
			array (
				'Caption' => text(2145),
                'BackupDirName' => addslashes($serviceDeskPath."views/Email/".$language),
				'BackupFileName' => addslashes("registration.twig")
			),
        );
        foreach( $data as $key => $row ) {
            $data[$key]['cms_BackupId'] = $key + 1;
            $data[$key]['AffectedDate'] = microtime(true);
        }
        return $this->createIterator($data);
	}
}