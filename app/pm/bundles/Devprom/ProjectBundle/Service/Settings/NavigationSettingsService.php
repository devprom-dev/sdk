<?php

namespace Devprom\ProjectBundle\Service\Settings;
use Devprom\ProjectBundle\Service\Navigation\WorkspaceService;

class NavigationSettingsService implements SettingsService
{
	public function reset()
	{
 		// disable any model events handler
		getFactory()->setEventsManager( new \ModelEventsManager() );
		
		$xml = $this->getTemplateXml();
		$context = new \CloneContext();
		
 		foreach ( $this->getIterators() as $data_it )
		{
			switch ( $data_it->object->getEntityRefName() )
			{
				case 'pm_Workspace':
					$system_it = $data_it->object->getRegistry()->Query(
							array (
									new \FilterAttributePredicate('SystemUser', array(getSession()->getUserIt()->getId(), 'none')),
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
			$iterator = $data_it->object->createXMLIterator($xml);
			\CloneLogic::Run( $context, $data_it->object, $iterator, getSession()->getProjectIt() ); 
		}
		getSession()->truncate();
	}
	
	public function makeDefault()
	{
 		// disable any model events handler
		getFactory()->setEventsManager( new \ModelEventsManager() );
		
 		$context = new \CloneContext();
 		foreach ( $this->getIterators() as $data_it )
		{
			switch ( $data_it->object->getEntityRefName() )
			{
				case 'pm_Workspace':
					$system_it = $data_it->object->getRegistry()->Query(
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

	protected function getIterators()
	{
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
	 	return $iterators;
	}

	protected function getTemplateXml()
	{
 		return file_get_contents(
 				getFactory()->getObject('ProjectTemplate')->getTemplatePath(
 						getSession()->getProjectIt()->get('Tools')
				)
 			);
	}
}