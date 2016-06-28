<?php
include_once SERVER_ROOT_PATH . 'tasks/commands/TaskCommand.php';
include_once SERVER_ROOT_PATH . 'pm/classes/sessions/PMSession.php';
include_once SERVER_ROOT_PATH . 'pm/classes/common/persisters/EntityProjectPersister.php';
include_once SERVER_ROOT_PATH . 'plugins/integration/classes/IntegrationService.php';

class IntegrationTask extends TaskCommand
{
 	function execute()
	{
		global $session;

		$this->logStart();
		$this->setupLogger();
		
		$system_it = getFactory()->getObject('SystemSettings')->getAll();

		$parameters = $this->getData()->getParameters();
		$itemsToProcess = $parameters['limit'] > 0 ? $parameters['limit'] : 30;

		$integration_it = getFactory()->getObject('Integration')->getRegistry()->Query(
			array (
				new FilterInPredicate($this->getChunk()),
				new EntityProjectPersister()
			)
		);
		while( !$integration_it->end() )
		{
			$session = new \PMSession(
				getFactory()->getObject('Project')->getExact($integration_it->get('Project')),
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

			ob_start();

			$service = new IntegrationService($integration_it);
			$service->setItemsToProcess($itemsToProcess);
			$service->process();

			$log_content = ob_get_contents();
			ob_end_clean();

			$integration_it->object->modify_parms(
				$integration_it->getId(),
				array (
					'Log' => $log_content
				)
			);
			$integration_it->moveNext();
		}

		$this->logFinish();
	}

	protected function setupLogger() {
		$layout = new LoggerLayoutPattern();
		$layout->setConversionPattern("\n%d %l %n %m");
		$layout->activateOptions();

		$appEcho = new LoggerAppenderEcho('bar');
		$appEcho->setLayout($layout);
		$appEcho->setHtmlLineBreaks(false);
		$appEcho->setThreshold('debug');
		$appEcho->activateOptions();

		Logger::getLogger('Commands')->addAppender($appEcho);
		Logger::getLogger('Commands')->setLevel('debug');
	}
}
