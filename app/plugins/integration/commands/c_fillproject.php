<?php
include_once SERVER_ROOT_PATH . 'plugins/integration/classes/IntegrationService.php';
include_once SERVER_ROOT_PATH . "core/classes/sprites/UserPicSpritesGenerator.php";

class FillProject extends CommandForm
{
    private $appIt = null;
    private $projectIt = null;
    private $object = null;

 	function validate()
 	{
        $this->appIt = getFactory()->getObject('IntegrationApplication')->getByRef('entityId', $_REQUEST['Caption']);
        if ( $this->appIt->getId() == '' ) $this->replyError($this->getResultDescription(1));

        $this->projectIt = getFactory()->getObject('Project')->getExact($_REQUEST['project']);
        if ( $this->projectIt->getId() == '' ) $this->replyError($this->getResultDescription(1));

        $this->object = getFactory()->getObject('Integration');

        if ( $this->appIt->get('ModelBuilder') != '' ) {
            $builderClassName = $this->appIt->get('ModelBuilder');
            if ( class_exists($builderClassName) ) {
                $builder = new $builderClassName;
                $builder->build($this->object);
            }
        }

        $validator = new ModelValidator();
        $validator->addValidator(new ModelValidatorObligatory(array_keys($this->object->getAttributes())));
        $result = $validator->validate($this->object, $_REQUEST);
        if ( $result != '' ) $this->replyError($result);

 		return true;
 	}
 	
 	function create()
	{
	    global $session;

        $_REQUEST['VPD'] = $this->projectIt->get('VPD');
        $_REQUEST['Project'] = $this->projectIt->getId();
        if (  $_REQUEST['MappingSettings'] == '' ) {
            $_REQUEST['MappingSettings'] = file_get_contents(SERVER_ROOT_PATH . $this->appIt->get('ReferenceName'));
        }

	    $objectIt = $this->object->getRegistry()->Create($_REQUEST);

        $session = new \PMSession(
            $this->projectIt,
            new \AuthenticationFactory(
                getFactory()->getObject('User')->createCachedIterator(
                    array (
                        array (
                            'Caption' => $objectIt->getDisplayName()
                        )
                    )
                )
            )
        );
        // reset all cached data/metadata
        getFactory()->resetCache();
        getFactory()->setAccessPolicy(new AccessPolicy(getFactory()->getCacheService()));
        getFactory()->getEventsManager()->removeNotificator( new \EmailNotificator() );

        try {
            $appender = $this->setupLogger();

            if ( $this->appIt->getId() == 'jirarest' ) {
                // delete all states and transitions
                getFactory()->getObject('RequestState')->deleteAll();
                getFactory()->getObject('TaskState')->deleteAll();
                getFactory()->getObject('Release')->deleteAll();
                getFactory()->getObject('Iteration')->deleteAll();
                getFactory()->getObject('RequestType')->deleteAll();
            }

            ob_start();
            $service = new IntegrationService($objectIt, \Logger::getLogger('Commands'));
            $service->setDoMigration();
            $service->setItemsToProcess(120);
            $service->process();
            $log_content = ob_get_contents();

            // make transitions across states
            if ( $this->appIt->getId() == 'jirarest' ) {
                getFactory()->resetCache();
                $transitionRegistry = getFactory()->getObject('Transition')->getRegistry();
                foreach (array('IssueState', 'TaskState') as $stateClass) {
                    $objectState = getFactory()->getObject($stateClass);
                    $stateIt = $objectState->getAll();
                    $stateIds = $stateIt->idsToArray();
                    foreach ($stateIds as $stateId) {
                        $stateIt->moveFirst();
                        while (!$stateIt->end()) {
                            if ($stateIt->getId() != $stateId) {
                                $transitionRegistry->Create(
                                    array(
                                        'Caption' => $stateIt->getDisplayName(),
                                        'SourceState' => $stateId,
                                        'TargetState' => $stateIt->getId(),
                                    )
                                );
                            }
                            $stateIt->moveNext();
                        }
                    }
                }
            }

            Logger::getLogger('Commands')->removeAppender($appender);
            ob_end_clean();
        }
        catch( \Exception $e ) {
            $log_content = ob_get_contents();
            Logger::getLogger('Commands')->removeAppender($appender);
            ob_end_clean();

            $this->replyError(nl2br($log_content.PHP_EOL.$e->getMessage()));
        }

        $requestsCount = getFactory()->getObject('Request')->getRegistry()->Count(
            array(
                new FilterAttributePredicate('Project', $this->projectIt->getId())
            )
        );
        if ( $requestsCount < 1 ) {
            $this->replyError(
                text('integration25').'<br/><br/>'.nl2br(htmlentities($log_content))
            );
        }

        $generator = new UserPicSpritesGenerator();
        $generator->storeSprites();

		$this->replyRedirect( '/pm/'.$this->projectIt->get('CodeName'),
            str_replace('%1',$requestsCount,text('integration26')) );
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
        return $appEcho;
    }
}
