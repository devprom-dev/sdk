<?php
use Devprom\ApplicationBundle\Service\CreateProjectService;
use Devprom\CommonBundle\Service\Project\InviteService;

include_once SERVER_ROOT_PATH.'admin/install/InstallationFactory.php';
include_once SERVER_ROOT_PATH.'admin/classes/CheckpointFactory.php';
include_once SERVER_ROOT_PATH."core/classes/sprites/UserPicSpritesGenerator.php";

class RegisterCourse extends CommandForm
{
 	function validate()
 	{
 	    $this->checkRequired(array('Caption','Email'));
 		return true;
 	}
 	
 	function create()
	{
	    global $session;

	    $coachIt = getFactory()->getObject('User')->getExact(COACH_USER_ID);

        getFactory()->setAccessPolicy(new AccessPolicy(getFactory()->getCacheService()));
        $auth_factory = new \AuthenticationFactory($coachIt);
        $session = new \COSession($auth_factory);
        $session->close();
        $session->open($coachIt);

        // create project first
	    $projectService = new CreateProjectService();
        $projectIt = $projectService->execute(
            array(
                'CodeName' => preg_replace('/[@-_\.+\(\)]+/', '', $_REQUEST['Email']),
                'Caption' => $_REQUEST['Caption'].' ('.$_REQUEST['Email'].')',
                'DemoData' => false,
                'Template' => COURSE_TEMPLATE_ID
            )
        );
        if ( ! $projectIt instanceof OrderedIterator ) $this->replyError($projectService->getResultDescription($projectIt));
        if ( $projectIt->getId() < 1 ) $this->replyError('Не удалось создать проекта для курса');

        // invite participant
        getFactory()->getObject('Invitation')->add_parms(
            array (
                'Project' => $projectIt->getId(),
                'ProjectRole' => getFactory()->getObject('ProjectRole')->getRegistry()->Query(
                                        array(
                                            new \FilterVpdPredicate($projectIt->get('VPD')),
                                            new \FilterAttributePredicate('ReferenceName', 'developer')
                                        )
                                    )->getId(),
                'Addressee' => $_REQUEST['Email']
            )
        );

        $inviteService = new InviteService(null, $session);
        getFactory()->setAccessPolicy(new AccessPolicy(getFactory()->getCacheService()));
        $participantIt = $inviteService->applyInvitation($_REQUEST['Email']);

        $userIt = getFactory()->getObject('User')->getExact($participantIt->get('SystemUser'));
        getFactory()->getObject('co_UserGroupLink')->getRegistry()->Create(
            array(
                'SystemUser' => $userIt->getId(),
                'UserGroup' => COACHING_USER_GROUP
            )
        );

        $auth_factory = new AuthenticationCookiesFactory($userIt);
        $auth_factory->logon();

        $session = new \PMSession($projectIt, $auth_factory);
        $session->close();
        $session->open($userIt);

	$event = new UserCreatedEvent();
	$event->setRecordData(array('RepeatPassword' => $userIt->get('Login')));
	$event->process($userIt, 'add');
        
        $generator = new \UserPicSpritesGenerator();
        $generator->storeSprites();

        $clear_cache_action = new ClearCache();
        $clear_cache_action->install();
        getFactory()->getCacheService()->invalidate();

	    $this->replyRedirect('/pm/'.$projectIt->get('CodeName'), 'Регистрация прошла успешно');
	}
}
