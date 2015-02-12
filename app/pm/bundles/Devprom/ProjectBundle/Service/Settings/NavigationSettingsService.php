<?php

namespace Devprom\ProjectBundle\Service\Settings;
use Devprom\ProjectBundle\Service\Navigation\WorkspaceService;

class NavigationSettingsService implements SettingsService
{
	public function reset()
	{
		$service = new WorkspaceService();
		$service->removeWorkspaces();
	}
	
	public function makeDefault()
	{
 		// disable any model events handler
		getFactory()->setEventsManager( new \ModelEventsManager() );
		
		// prepare settings object
		$iterators = array();
		
   		$iterators[] = getFactory()->getObject('pm_CustomReport')->getRegistry()->Query(
   				array (
   						new \CustomReportMyPredicate(),
   						new \FilterVpdPredicate()
   				)
   			);
		
	 	$workspace = getFactory()->getObject('Workspace');
	 	$workspace_it = $workspace->getRegistry()->getDefault();
	 		
	 	$iterators[] = $workspace->getRegistry()->Query(
	 			array (
	 					new \FilterInPredicate($workspace_it->idsToArray())
	 			)
	 	);

	 	$menu_it = getFactory()->getObject('pm_WorkspaceMenu')->getRegistry()->Query(
	 			array (
	 					new \FilterAttributePredicate('Workspace', $workspace_it->idsToArray()),
	 					new \SortOrderedClause()
	 			)
	 	);
	 	$iterators[] = $menu_it;
	 	
	 	$iterators[] = getFactory()->getObject('pm_WorkspaceMenuItem')->getRegistry()->Query(
	 			array (
	 					new \FilterAttributePredicate('WorkspaceMenu', $menu_it->idsToArray()),
	 					new \SortOrderedClause()
	 			)
	 	);
		
 		$context = new \CloneContext();
 		foreach ( $iterators as $data_it )
		{
			switch ( $data_it->object->getEntityRefName() )
			{
				case 'pm_Workspace':
					$system_it = $workspace->getRegistry()->Query(
							array (
									new \FilterAttributePredicate('SystemUser', 'none'),
									new \FilterBaseVpdPredicate(),
							)
					);
					while( !$system_it->end() )
					{
						$system_it->delete();
						$system_it->moveNext();
					}
					break;
			}

			\CloneLogic::Run( $context, $data_it->object, $data_it, getSession()->getProjectIt() ); 
		}

		getSession()->truncate();
	}
}