<?php
include_once SERVER_ROOT_PATH . 'tasks/commands/TaskCommand.php';
include_once SERVER_ROOT_PATH . 'pm/classes/sessions/PMSession.php';
include_once SERVER_ROOT_PATH . 'plugins/integration/classes/IntegrationService.php';

class IntegrationTask extends TaskCommand
{
 	function execute()
	{
		global $session;

		$this->logStart();

		$parameters = $this->getData()->getParameters();
		$itemsToProcess = $parameters['limit'] > 0 ? $parameters['limit'] : 60;

		$queryParms = array (
            new FilterAttributePredicate('IsActive', 'Y'),
            new ProjectActiveVpdPredicate(),
            new EntityProjectPersister()
        );

		$chunk = $this->getChunk();
		if ( count($chunk) > 0 ) {
            $queryParms[] = new FilterInPredicate($chunk);
        }

		$integration_it = getFactory()->getObject('Integration')->getRegistry()->Query($queryParms);
		while( !$integration_it->end() )
		{
            if ( $integration_it->get('Project') == '' ) {
                $integration_it->moveNext();
                continue;
            }
		    $project_it = getFactory()->getObject('Project')->getExact($integration_it->get('Project'));
            if ( $project_it->getId() < 1 ) {
                $integration_it->moveNext();
                continue;
            }

			$session = new \PMSession(
                $project_it,
				new \AuthenticationFactory(
					getFactory()->getObject('User')->createCachedIterator(
						array (
							array (
								'Caption' => $integration_it->getDisplayName()
							)
						)
					)
				)
			);
			// reset all cached data/metadata
			getFactory()->resetCache();
			getFactory()->setAccessPolicy(new AccessPolicy(getFactory()->getCacheService()));
            getFactory()->getEventsManager()->removeNotificator( new \EmailNotificator() );

            $logFilePath = SERVER_LOGS_PATH . '/' . \TextUtils::getFileSafeString(
			    'integration-' . $project_it->get('CodeName') . '-' . $integration_it->get('ProjectKey') . '.log'
            );
            $this->setupLogger($logFilePath);

			$service = new IntegrationService(
			    $integration_it, \Logger::getLogger('Commands'), $parameters['delay'] != '' ? $parameters['delay'] : 0
            );

			$service->setItemsToProcess($itemsToProcess);
			$service->process();

            $maxLogLength = 1 * 1024 * 1024;
			$integration_it->object->modify_parms(
				$integration_it->getId(),
				array (
					'Log' => file_get_contents($logFilePath, null, null, -$maxLogLength, $maxLogLength)
				)
			);
			$integration_it->moveNext();
		}

		$this->logFinish();
	}

	protected function setupLogger( $filePath ) {
		$layout = new LoggerLayoutPattern();
		$layout->setConversionPattern("\n%d %l %n %m");
		$layout->activateOptions();

        $appFile = new LoggerAppenderRollingFile('foo');
        $appFile->setFile($filePath);
        $appFile->setLayout($layout);
        $appFile->setAppend(true);
        $appFile->setThreshold('debug');
        $appFile->activateOptions();

        $logger = Logger::getLogger('Commands');
        $logger->removeAllAppenders();
        $logger->addAppender($appFile);
        $logger->setLevel('debug');
	}
}
