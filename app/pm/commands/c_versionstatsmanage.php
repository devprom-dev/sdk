<?php
 
 class VersionStatsManage extends CommandForm
 {
 	function validate()
 	{
		global $_REQUEST, $model_factory;

		$this->version = $model_factory->getObject('pm_Version');

		// proceeds with validation
		$this->checkRequired( 
			array('InitialEstimationError', 'InitialBugsInWorkload') );

		// check authorization was successfull
		if ( getSession()->getUserIt()->getId() < 1 )
		{
			return false;
		}
		
		return true;
 	}
 	
 	function modify( $version_id )
	{
		global $_REQUEST, $model_factory;
		
		$version_it = $this->version->getExact($version_id);
		
		if ( !getFactory()->getAccessPolicy()->can_modify($version_it) )
		{
			$this->replyError( text(983) );
			return;
		}
		
		$this->version->modify_parms($version_it->getId(), 	
			array( 'InitialEstimationError' => $_REQUEST['InitialEstimationError'],
				   'InitialBugsInWorkload' => $_REQUEST['InitialBugsInWorkload']
				 )
			);

		$this->replySuccess( 
			$this->getResultDescription( 1001 ) );
	}

	function getResultDescription( $result )
	{
		switch($result)
		{
			case 1001:
				return text(384);

			default:
				return parent::getResultDescription( $result );
		}
	}
 }
 
?>