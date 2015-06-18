<?php

namespace Devprom\ApplicationBundle\Controller;

use Devprom\CommonBundle\Controller\PageController;
use Devprom\ApplicationBundle\Service\CreateProjectService;

include_once SERVER_ROOT_PATH."co/views/Common.php";
include SERVER_ROOT_PATH."co/views/ProjectCreatePage.php";
include SERVER_ROOT_PATH."co/views/ProjectWelcomePage.php";
 
class ProjectController extends PageController
{
    public function newAction()
    {
        $response = $this->checkUserAuthorized();
        
        if ( is_object($response) ) return $response;
        
    	return $this->responsePage( new \CreateProjectPage() );
    }
    
    public function createAction()
    {
        $response = $this->checkUserAuthorized();
        if ( is_object($response) ) return $response;

        $request = $this->getRequest();
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
		$result = $strategy->execute($parms);
		
		if ( $result < 1 ) {
		    return $this->replyError( $strategy->getResultDescription($result) );
		}

		if ( $request->request->get('Participants') != '' )	{
			if ( class_exists('PortfolioMyProjectsBuilder', false) ) {
				$invite_service = new \Devprom\CommonBundle\Service\Project\InviteService($this, getSession());
				$invite_service->inviteByEmails($request->request->get('Participants'));
			}
		}
		
		return $this->replySuccess($strategy->getResultDescription(0), $parms['CodeName'].'/');
    }
    
    public function welcomeAction()
    {
        if ( is_object($response = $this->checkUserAuthorized()) ) return $response;
        
    	return $this->responsePage( new \ProjectWelcomePage() );
    }
}