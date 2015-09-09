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
        foreach( $parms as $key => $value )
        {
        	if ( $value == '' ) return $this->replyError(text(200));
        }
        
        $validator = new \ModelValidator(
        		array (
        				new \ModelValidatorProjectCodeName(),
        				new \ModelValidatorUnique(array('CodeName'))
        		)
		);
        
        $message = $validator->validate($prj_cls, $parms);
        
		if ( $message != "" ) return $this->replyError($message);

		// check access policy
		if ( !getFactory()->getAccessPolicy()->can_create($prj_cls) )
		{
			return $this->replyError(text(706));
		}

        $parms['DemoData'] = in_array(strtolower(trim($request->request->get('DemoData'))), array('y','on'));

    	if ( $request->request->get('Participants') != '' ) {
			if ( !class_exists('PortfolioMyProjectsBuilder', false) ) {
				$invite_service = new \Devprom\CommonBundle\Service\Users\InviteService($this, getSession());
				$invite_service->inviteByEmails($request->request->get('Participants'));
			}
		}
        
		$strategy = new CreateProjectService();
		$project_it = $strategy->execute($parms);
		
		if ( !is_object($project_it) ) {
		    return $this->replyError( $strategy->getResultDescription($project_it) );
		}

		if ( $request->request->get('Participants') != '' )	{
			if ( class_exists('PortfolioMyProjectsBuilder', false) ) {
				$invite_service = new \Devprom\CommonBundle\Service\Project\InviteService($this, getSession());
				$invite_service->inviteByEmails($request->request->get('Participants'));
			}
		}

		$strategy->invalidateCache();
		if ( $project_it->getMethodologyIt()->get('IsSupportUsed') == 'Y' ) {
			$strategy->invalidateServiceDeskCache();
		}
		return $this->replySuccess($strategy->getResultDescription(0), $project_it->get('CodeName').'/');
    }
    
    public function welcomeAction(Request $request)
    {
        if ( is_object($response = $this->checkUserAuthorized($request)) ) return $response;
        
    	return $this->responsePage( new \ProjectWelcomePage() );
    }
}