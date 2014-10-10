<?php

namespace Devprom\ApplicationBundle\Controller;

use Devprom\ApplicationBundle\Controller\PageController;
use Devprom\ApplicationBundle\Service\CreateProjectService;
use Devprom\CommonBundle\Service\Project\InviteService;


include_once SERVER_ROOT_PATH."co/views/Common.php";
include SERVER_ROOT_PATH."co/views/ProjectCreatePage.php";
 
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
        global $model_factory;
         
        $response = $this->checkUserAuthorized();
        
        if ( is_object($response) ) return $response;
        
        // validate values
        
        $request = $this->getRequest();

        $empty_values = $request->request->get('Codename') == ''
            || $request->request->get('Caption') == ''
            || $request->request->get('Template') == '';
            
        if ( $empty_values ) return $this->replyError(text(200));
        
		$prj_cls = $model_factory->getObject('pm_Project');

		if ( !$prj_cls->validCodeName($request->request->get('Codename')) ) return $this->replyError(text(208));

		$project_it = $prj_cls->getByRef(
		    'LCASE(CodeName)', strtolower($request->request->get('Codename'))
		);
			
		if ( $project_it->count() > 0 ) return $this->replyError(text(202));

		if ( !getFactory()->getAccessPolicy()->can_create($prj_cls) ) return $this->replyError(text(706));
        
		// create new project

		$_REQUEST['Caption'] = \IteratorBase::utf8towin($request->request->get('Caption'));

		$strategy = new CreateProjectService();
		
		$result = $strategy->execute();
		
		if ( $result < 1 ) 
		{
		    return $this->replyError( self::getResultDescription($strategy, $result) );
		}

		$emails = preg_split('/,/', $request->request->get('Participants'));
		
		if ( count($emails) > 0 )
		{
			$invite_service = new InviteService($this, getSession());
			$invite_service->inviteByEmails($emails);
		}
		
		return $this->replySuccess(
				$strategy->getSuccessMessage(), 
				$request->request->get('Codename').'/'
		); 
    }
    
	static function getResultDescription( $strategy, $result )
	{
		switch($result)
		{
			case -1:
				return text(200);
				
			case -2:
				return text(201);
				
			case -3:
				return text(202);
				
			case -4:
				return text(203);

			case -5:
				return text(204);
				
			case -6:
				return text(205);
				
			case -7:
				return text(206);
				
			case -8:
				return text(207);
				
			case -9:
				return text(208);
				
			case -10:
				return text(209);
				
			case -11:
				return text(1424);
				
			default:
				return $strategy->getSuccessMessage();
		}
	}    
}