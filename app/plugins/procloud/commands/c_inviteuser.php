<?php
 
 class InviteUser extends CommandForm
 {
 	function validate()
 	{
		global $_REQUEST, $model_factory, $project_it;

		// proceeds with validation
		$this->checkRequired( array('Email') );

		// check authorization was successfull
		if ( getSession()->getUserIt()->getId() < 1 || !is_object($project_it) )
		{
			return false;
		}
		
		return true;
 	}
 	
 	function create()
	{
		global $_REQUEST, $model_factory, $project_it;
		
		$parms['Project'] = $project_it->getId();
		$parms['Author'] = getSession()->getUserIt()->getId();
		$parms['Addressee'] = $_REQUEST['Email'];
		
		$invitation = $model_factory->getObject('pm_Invitation');
		$invitation_id = $invitation->add_parms($parms);
		
		$this->replySuccess( 
			$this->getResultDescription( 1000 ) );
	}

	function getResultDescription( $result )
	{
		global $_REQUEST, $model_factory;
		
		$session = getSession();
		
		switch($result)
		{
			case 1000:
			    
			    $menu = $model_factory->getObject('Module')->getExact('procloud/invite')->buildMenuItem();
			    
				return str_replace('%2', $menu['url'], str_replace('%1', $_REQUEST['Email'], text(448)));

			default:
				return parent::getResultDescription( $result );
		}
	}
 }
 