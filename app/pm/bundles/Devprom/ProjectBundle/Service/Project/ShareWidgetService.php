<?php
namespace Devprom\ProjectBundle\Service\Project;
use Devprom\CommonBundle\Service\Emails\RenderService;
include_once SERVER_ROOT_PATH."core/classes/sprites/UserPicSpritesGenerator.php";

class ShareWidgetService
{
    private $factory = null;
    private $session = null;

    function __construct($factory, $session) {
        $this->factory = $factory;
        $this->session = $session;
    }

    function execute( $email, $emailSubject, $url, $projectRoleIt, $projectIt )
    {
        $name = $login = array_shift(preg_split('/@/', $email));
        $userIt = $this->createUser($name, $login, $email);

        if ( $projectRoleIt->getId() != '' ) {
            $this->createParticipant($userIt, $projectRoleIt, $projectIt);
        }

        $this->sendNotification(
            $emailSubject, $userIt, $url . '&appkey='.\AuthenticationAppKeyFactory::getKey($userIt->getId())
        );
    }

    function createUser( $name, $login, $email )
    {
        $password = \TextUtils::getRandomPassword();
        $_REQUEST['Password'] = $password;

        $userIt = getFactory()->getObject('User')->getRegistry()->Merge(
            array(
                'Caption' => $name,
                'Login' => $login,
                'Email' => $email,
                'Password' => $password,
                'IsReadonly' => 'Y'
            ),
            array(
                'Email'
            )
        );

        $generator = new \UserPicSpritesGenerator();
        $generator->storeSprites();

        return $userIt;
    }

    function createParticipant( $userIt, $roleIt, $projectIt )
    {
        $partIt = getFactory()->getObject('Participant')->getRegistry()->Merge(
            array(
                'SystemUser' => $userIt->getId(),
                'Project' => $projectIt->getId(),
                'IsActive' => 'N'
            ),
            array(
                'SystemUser','Project'
            )
        );
        getFactory()->getObject('ParticipantRole')->getRegistry()->Merge(
            array(
                'Participant' => $partIt->getId(),
                'ProjectRole' => $roleIt->getId()
            ),
            array(
                'Participant'
            )
        );
    }

    function sendNotification( $emailSubject, $userIt, $url )
    {
        $parms = array(
            'url' => $url
        );

        $render_service = new RenderService(
            $this->session, SERVER_ROOT_PATH."pm/bundles/Devprom/ProjectBundle/Resources/views/Emails"
        );

        $mail = new \HtmlMailBox();
        $mail->appendAddress( $userIt->get('Email') );
        $mail->setSubject( $emailSubject );
        $mail->setBody($render_service->getContent('share-widget.twig', $parms));
        $mail->send();
    }
}