<?php
/*
 * DEVPROM (http://www.devprom.net)
 * c_projectremove.php
 *
 * Copyright (c) 2005, 2006 Evgeny Savitsky <admin@devprom.net>
 * You can modify this code freely for your own needs,
 * but you can't distribute it manually.
 * 
 */

 ////////////////////////////////////////////////////////////////////////////
 class ProjectRemove extends CommandForm
 {
 	function execute()
	{
		global $_REQUEST, $model_factory, $user_it;

		if ( $user_it->getId() < 1 )
		{
			$this->complete();
		}
		
		$project = $model_factory->getObject('pm_Project');
		$project_it = $project->getAll();
		
		while ( !$project_it->end() )
		{
			if ( $project_it->getRemoveKey() == $_REQUEST['key'] )
			{
				// check current user is lead of the project
				$lead_it = $project_it->getLeadIt();
				$found_lead = false;
				
				while ( !$lead_it->end() )
				{
					if ( $lead_it->get('SystemUser') == $user_it->getId() )
					{
						$found_lead = true;
						break;
					}
					$lead_it->moveNext();
				}

				if ( $found_lead )
				{
					// drop project
					$model_factory->object_factory->access_policy = new AccessPolicy;
					
					$project->delete($project_it->getId());
				}				
				
				break;
			}
			
			$project_it->moveNext();
		}
		
		$this->complete();
	}
	
	function complete()
	{
		$this->replyRedirect('/room', ''); 
	}	
 }
 
?>