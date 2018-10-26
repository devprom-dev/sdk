<?php
namespace Devprom\ProjectBundle\Service\Email;

use Devprom\CommonBundle\Service\Emails\RenderService;
include_once SERVER_ROOT_PATH.'cms/c_mail.php';
include_once SERVER_ROOT_PATH."pm/classes/tasks/TaskViewModelBuilder.php";

class DeadlinesReport
{
    private $session = null;
    private $template = 'deadlines.twig';

    function __construct( $session = null ) {
        $this->session = $session;
    }

    function send( $userIt )
    {
        $renderService = new RenderService(
            getSession(), SERVER_ROOT_PATH."pm/bundles/Devprom/ProjectBundle/Resources/views/Emails"
        );

        $mail = new \HtmlMailBox();
        $mail->setFromUser($this->session->getUserIt());
        $mail->appendAddress( $userIt->get('Email') );
        $mail->setSubject( text(2620) );

        $data = $this->getReportParms($userIt);
        if ( count($data['deadlines']) < 1 ) return;

        $mail->setBody(
            $renderService->getContent(
                $this->template, $data
            )
        );
        $mail->send();
    }

    protected function getReportParms( $userIt )
    {
        $result = array(
            'log_url' =>
                defined('PERMISSIONS_ENABLED')
                    ? \EnvironmentSettings::getServerUrl().'/pm/my/tasks/list/nearesttasks'
                    : \EnvironmentSettings::getServerUrl().'/pm/all/tasks/list/nearesttasks',
            'profile_url' => \EnvironmentSettings::getServerUrl().'/profile',
            'deadlines' => array()
        );

        $builders = array_merge(
            getSession()->getBuilders('TaskViewModelBuilder'),
            array(
                new \TaskModelExtendedBuilder()
            )
        );
        $object = getFactory()->getObject('WorkItem');
        foreach( $builders as $builder ) {
            $builder->build($object);
        }

        $uid = new \ObjectUID();
        $now = strtotime(\SystemDateTime::date());
        $dateTime = strftime('%Y-%m-%d', strtotime('4 days', $now));

        $objectIt = $object->getRegistry()->Query(
            array(
                new \FilterAttributePredicate('Assignee', $userIt->getId()),
                new \FilterDateBeforePredicate('DueDate', $dateTime),
                new \WorkItemStatePredicate('initial,progress'),
                new \SortAttributeClause('DueDate.A'),
                new \SortAttributeClause('Priority.A')
            )
        );
        while( !$objectIt->end() ) {
            $dateText = $this->session->getLanguage()->getDateFormattedShort($objectIt->get('DueDate'));
            $artefactIt = $objectIt->getObjectIt();

            $uidInfo = $uid->getUIDInfo($artefactIt, true);
            $result['deadlines'][$dateText][] = array(
                'id' => '<a href="'.$uidInfo['url'].'">'.$uidInfo['uid'].'</a>',
                'title' => '{'.$uidInfo['project'] . '}' . $uidInfo['caption']
            );
            $objectIt->moveNext();
        }

        return $result;
    }
}