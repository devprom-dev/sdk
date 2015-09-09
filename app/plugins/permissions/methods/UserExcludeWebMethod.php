<?php
include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";

class UserExcludeWebMethod extends WebMethod
{
	var $user_it, $project_it;

	function UserExcludeWebMethod ( $user_it = null, $project_it = null )
	{
		$this->user_it = $user_it;
		$this->project_it = $project_it;
		
		parent::WebMethod();
	}

	function getCaption()
	{
		return text(1248);
	}
	
	function getJSCall()
	{
		return parent::getJSCall( array(
			'user' => $this->user_it->getId(),
			'project' => $this->project_it->getId()
		));
	}
	
	function execute_request()
	{
		global $_REQUEST;
		$this->execute( $_REQUEST );
	}
	
	function execute( $parms )
	{
		$this->user_it = getFactory()->getObject('User')->getExact($parms['user']);
		
		if ( $this->user_it->getId() < 1 ) throw new Exception('User should be specified');
		
		$this->project_it = getFactory()->getObject('Project')->getExact($parms['project']);
		
		if ( $this->project_it->getId() < 1 ) throw new Exception('Project should be specified');

		$session = new PMSession($this->project_it);
		
		getFactory()->setAccessPolicy(new AccessPolicy(getFactory()->getCacheService()));
		
		$part = getFactory()->getObject('Participant');
		
		$part_it = $part->getByRefArray( 
				array (
					'SystemUser' => $this->user_it->getId(),
					'Project' => $this->project_it->getId() 	
				)
			);

		if ( $part_it->getId() > 0 ) $part->delete($part_it->getId());
	}

	function hasAccess()
	{
		return getSession()->getUserIt()->IsAdministrator();
	}
}