<?php

namespace Devprom\ApplicationBundle\Controller;

use Devprom\CommonBundle\Controller\PageController;
use Symfony\Component\HttpFoundation\Request;
use Devprom\ApplicationBundle\Service\CreateProjectService;

include_once SERVER_ROOT_PATH."co/views/Common.php";
include SERVER_ROOT_PATH."co/views/ProjectCreatePage.php";
include SERVER_ROOT_PATH."co/views/ProjectWelcomePage.php";
 
class ProjectController extends PageController
{
    public function newAction(Request $request)
    {
        $response = $this->checkUserAuthorized($request);
        
        if ( is_object($response) ) return $response;

    	return $this->responsePage( new \CreateProjectPage() );
    }
    
    public function createAction(Request $request)
    {
        $response = $this->checkUserAuthorized($request);
        if ( is_object($response) ) return $response;

		$prj_cls = getFactory()->getObject('pm_Project');
        
		$parms = array();

		// get defaults
        $parms['CodeName'] = $request->request->get('CodeName');
        $parms['Caption'] = \IteratorBase::utf8towin($request->request->get('Caption'));
        $parms['Template'] = $request->request->get('Template');

        if ( $parms['CodeName'] == '' ) $parms['CodeName'] = $prj_cls->getDefaultAttributeValue('CodeName');
        if ( $parms['Caption'] == '' ) $parms['Caption'] = $prj_cls->getDefaultAttributeValue('Caption');
		
        // validate values
        foreach( $parms as $key => $value ) {
        	if ( $value == '' ) return $this->replyError(text(200));
        }

        $parms['DemoData'] = $request->request->get('DemoData');
        $parms['Tracker'] = $request->request->get('Tracker');

        $validator = new \ModelValidator(
            array (
                new \ModelValidatorProjectCodeName(),
                new \ModelValidatorUnique(array('CodeName')),
                new \ModelValidatorProjectIntegration()
            )
		);
        
        $message = $validator->validate($prj_cls, $parms);
        if ( $message != "" ) return $this->replyError($message);

		// check access policy
		if ( !getFactory()->getAccessPolicy()->can_create($prj_cls) )
		{
			$result = getFactory()->getAccessPolicy()->getReason() != '' ? getFactory()->getAccessPolicy()->getReason() : text(706);
			return $this->replyError($result);
		}

        $parms['DemoData'] = in_array(strtolower(trim($request->request->get('DemoData'))), array('y','on'));
		if ( is_numeric($request->request->get('portfolio')) ) {
			$parms['portfolio'] = $request->request->get('portfolio');
		}

    	if ( $request->request->get('Participants') != '' ) {
			if ( !defined('PERMISSIONS_ENABLED') ) {
				$invite_service = new \Devprom\CommonBundle\Service\Users\InviteService($this, getSession());
				$invite_service->inviteByEmails($request->request->get('Participants'));
			}
		}
        
		$strategy = new CreateProjectService();
		$project_it = $strategy->execute($parms);
		
		if ( !is_object($project_it) ) {
		    return $this->replyError( $strategy->getResultDescription($project_it) );
		}

        $invite_service = new \Devprom\CommonBundle\Service\Project\InviteService($this, getSession());
		if ( $request->request->get('Participants') != '' )	{
			if ( defined('PERMISSIONS_ENABLED') ) {
				$invite_service->inviteByEmails($request->request->get('Participants'));
			}
		}

		$userIt = getFactory()->getObject('User')->getAll();
		while( !$userIt->end() ) {
		    if ( $request->request->get('_user_'.$userIt->getId()) == 'on' ) {
                $invite_service->addParticipant($project_it, $userIt);
            }
            $userIt->moveNext();
        }

		$strategy->invalidateCache();
		if ( $project_it->getMethodologyIt()->get('IsSupportUsed') == 'Y' ) {
			$strategy->invalidateServiceDeskCache();
		}

		if ( $request->request->get('Tracker') != '' ) {
            return $this->replyRedirect(
                sprintf('/module/integration/fill?project=%s&tracker=%s',
                    $project_it->get('CodeName'),
                    $request->request->get('Tracker')
                ),
                $strategy->getResultDescription(0)
            );
        }
        else {
            return $this->replySuccess($strategy->getResultDescription(0), $project_it->get('CodeName').'/');
        }
    }
    
    public function welcomeAction(Request $request)
    {
        if ( is_object($response = $this->checkUserAuthorized($request)) ) return $response;
        
    	return $this->responsePage( new \ProjectWelcomePage() );
    }
}