<?php

namespace Devprom\ProjectBundle\Service\Navigation;

include_once SERVER_ROOT_PATH."pm/classes/widgets/FunctionalArea.php";
include_once SERVER_ROOT_PATH."pm/classes/widgets/FunctionalAreaCommonBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/widgets/FunctionalAreaMyProjectsBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/widgets/FunctionalAreaPortfolioBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/widgets/FunctionalAreaMenuSet.php";
include_once SERVER_ROOT_PATH.'pm/classes/widgets/FunctionalAreaMenuBuilder.php';
include_once SERVER_ROOT_PATH.'pm/classes/widgets/FunctionalAreaMenuFavoritesBuilder.php';
include_once SERVER_ROOT_PATH.'pm/classes/widgets/FunctionalAreaMenuManagementBuilder.php';
include_once SERVER_ROOT_PATH.'pm/classes/widgets/FunctionalAreaMenuPortfolioBuilder.php';
include_once SERVER_ROOT_PATH.'pm/classes/widgets/FunctionalAreaMenuMyProjectsBuilder.php';
include_once SERVER_ROOT_PATH.'pm/classes/widgets/FunctionalAreaMenuSettingsBuilder.php';

class WorkspaceService
{
	public function getWorkspaces()
	{
		$data = array();
		
		$areas = $this->getFunctionalAreas();
		
		foreach( $areas as $area )
		{
			$nodes = array();

			if ( !is_array($area['menus']) ) continue;
			
			foreach( $area['menus'] as $group )
			{
				$items = array();
				
				foreach( $group['items'] as $key => $item )
				{
					$items[] = array (
							'title' => \IteratorBase::wintoutf8($item['name']),
							'report' => array (
									'id' => $item['module'] != '' ? $item['module'] : $item['uid'],
									'type' => $item['module'] != '' ? 'module' : 'report',
									'title' => \IteratorBase::wintoutf8($item['name'])
							)
					);
				}
				
				$nodes[] = array (
						'id' => $group['uid'],
						'title' => \IteratorBase::wintoutf8($group['name']),
						'nodes' => $items
				);
			}
			
			$data[] = array(
					'id' => $area['uid'],
					'title' => \IteratorBase::wintoutf8($area['name']),
					'icon' => $area['icon'],
					'menuNodes' => $nodes
			); 
		}
		
		return $data;
	}
	
	public function storeWorkspace( & $data )
	{
		$workspace = getFactory()->getObject('Workspace');
		
		$menu = getFactory()->getObject('WorkspaceMenu');
		
		$item = getFactory()->getObject('WorkspaceMenuItem');
		
		// merge workspace
		$workspace_it = $workspace->getRegistry()->Query( 
					array (
							new \FilterAttributePredicate('UID', $data['id']),
							new \FilterAttributePredicate('SystemUser', getSession()->getUserIt()->getId()),
							new \FilterBaseVpdPredicate()
					)
			);
		
		$parms = array (
				'UID' => $data['id'],
				'SystemUser' => getSession()->getUserIt()->getId(),
				'Caption' => \IteratorBase::utf8towin($data['title']),
				'Icon' => $data['icon']
		);
		
		if ( $workspace_it->getId() > 0 )
		{
			$workspace_it->modify( $parms ); 
		}
		else
		{
			$workspace_it = $workspace->getExact($workspace->add_parms($parms));
		}
		
		// merge nodes
		$ids = array();
		
		foreach( $data['menuNodes'] as $key => $node )
		{
			if ( $node['id'] == '' ) $data['menuNodes'][$key]['id'] = $node['id'] = $key;
			 
			$ids[] = $node['id'];
			
			$menu_it = $menu->getRegistry()->Query( 
						array (
								new \FilterAttributePredicate('Workspace', $workspace_it->getId()),
								new \FilterAttributePredicate('UID', $node['id'])
						)
				);
			
			$parms = array (
					'UID' => $node['id'],
					'Workspace' => $workspace_it->getId(),
					'Caption' => \IteratorBase::utf8towin($node['title'])
			);
			
			if ( $menu_it->getId() > 0 )
			{
				$menu_it->modify( $parms ); 
			}
			else
			{
				$menu_it = $menu->getExact($menu->add_parms($parms));
			}
			
			// merge node items
			$node_item_ids = array();
			
			if ( is_array($node['nodes']) )
			{
				foreach($node['nodes'] as $key => $node_item )
				{
					$uid = $node_item['report']['id'];
					
					$node_item_ids[] = $uid;
					
					$item_it = $item->getRegistry()->Query( 
								array (
										new \FilterAttributePredicate('WorkspaceMenu', $menu_it->getId()),
										new \FilterAttributePredicate('UID', $uid)
								)
						);
					
					$parms = array (
							'UID' => $uid,
							'ReportUID' => $node_item['report']['type'] == 'report' ? $uid : '',
							'ModuleUID' => $node_item['report']['type'] == 'module' ? $uid : '',
							'WorkspaceMenu' => $menu_it->getId(),
							'OrderNum' => $key + 1 
					);
					
					if ( $item_it->getId() > 0 )
					{
						$item_it->modify( $parms ); 
					}
					else
					{
						$item_it = $item->getExact($item->add_parms($parms));
					}
				}
			}
			
			// remove deleted node items
			$item_it = $item->getRegistry()->Query( 
						array (
								new \FilterAttributePredicate('WorkspaceMenu', $menu_it->getId()),
								new \FilterHasNoAttributePredicate('UID', $node_item_ids)
						)
				);
			
			while( !$item_it->end() )
			{
				$item_it->delete();
				$item_it->moveNext();
			}
		}

		// remove deleted nodes
		$menu_it = $menu->getRegistry()->Query( 
					array (
							new \FilterAttributePredicate('Workspace', $workspace_it->getId()),
							new \FilterHasNoAttributePredicate('UID', $ids)
					)
			);
		
		while( !$menu_it->end() )
		{
			$menu_it->delete();
			$menu_it->moveNext();
		}
	}
	
	public function removeWorkspace( $area_id )
	{
		$workspace = getFactory()->getObject('Workspace');
		
		$workspace_it = $workspace->getRegistry()->Query( 
					array (
							new \FilterAttributePredicate('UID', $area_id),
							new \FilterAttributePredicate('SystemUser', getSession()->getUserIt()->getId()),
							new \FilterBaseVpdPredicate()
					)
			);
		
		if ( $workspace_it->getId() < 1 ) return;
		
		$workspace_it->delete();
	}
	
	public function storeReportToWorkspace( $report, $workspace_uid = FUNC_AREA_FAVORITES )
	{
		$workspaces = $this->getWorkspaces();
		
		foreach( $workspaces as $workspace_key => $workspace )
		{
			if ( $workspace['id'] != $workspace_uid ) continue;
			
			foreach( $workspace['menuNodes'] as $node_key => $node )
			{
				if ( $node['id'] != 'quick' ) continue;
				
				$item_found = false;
				
				foreach( $workspace['menuNodes'][$node_key]['nodes'] as $item )
				{
					if ( $item['report']['id'] == $report['id'] )
					{
						$item_found = true;
						break;
					}
				}
				
				if ( !$item_found )
				{
					$workspace['menuNodes'][$node_key]['nodes'][] = array (
							'report' => $report
					);
					
					$this->storeWorkspace($workspace);
				}
			}
		}
	}
	
	public function getItemOnFavoritesWorkspace( $uid )
	{
    	$workspace_it = getFactory()->getObject('Workspace')->getRegistry()->getDefault(
    			array (
    					new \FilterAttributePredicate('UID', FUNC_AREA_FAVORITES)
    			)
		);
    	
    	if ( $workspace_it->getId() < 1 )
    	{
    		return getFactory()->getObject('WorkspaceMenuItem')->getEmptyIterator();
    	}
    	
    	$menu_it = getFactory()->getObject('WorkspaceMenu')->getRegistry()->Query(
    			array (
    					new \FilterAttributePredicate('Workspace', $workspace_it->getId()),
    			)
    	);
    	
	    if ( $menu_it->getId() < 1 )
    	{
    		return getFactory()->getObject('WorkspaceMenuItem')->getEmptyIterator();
    	}
    	
    	return getFactory()->getObject('WorkspaceMenuItem')->getRegistry()->Query(
    			array (
    					new \FilterAttributePredicate('WorkspaceMenu', $menu_it->idsToArray()),
    					new \FilterAttributePredicate('UID', $uid)
    			)
    	);
	}
	
	public function getFunctionalAreas()
	{
 	 	$project_it = getSession()->getProjectIt();

 	    if ( $project_it->get('CodeName') == 'my' )
 	    {
 	        getSession()->insertBuilder( new \FunctionalAreaMyProjectsBuilder() ); 
 	    	getSession()->insertBuilder( new \FunctionalAreaMenuMyProjectsBuilder() );
 	    }
 	    else if ( $project_it->IsPortfolio() )
 	    {
 	        getSession()->insertBuilder( new \FunctionalAreaPortfolioBuilder() ); 
 	    	getSession()->insertBuilder( new \FunctionalAreaMenuPortfolioBuilder() ); 
 	    }
 	    else
 	    {
 	        getSession()->insertBuilder( new \FunctionalAreaCommonBuilder() ); 
 	    	getSession()->insertBuilder( new \FunctionalAreaMenuFavoritesBuilder() ); 
 	    }
 	        
 	    getSession()->insertBuilder( new \FunctionalAreaMenuManagementBuilder() ); 
 	    getSession()->insertBuilder( new \FunctionalAreaMenuSettingsBuilder() ); 
		
	    $area_set = new \FunctionalArea();
 	    
 	    $area_it = $area_set->getAll();

 		$category = getFactory()->getObject('PMReportCategory');
 	    
 	    $category_it = $category->getAll();
 		
 		$module = getFactory()->getObject('Module');
 		
 		$module_it = $module->getAll();
 	    
 		$report = getFactory()->getObject('PMReport');
 	    
 	    $report_it = $report->getAll();
 	    
 	    $area_menu = new \FunctionalAreaMenuSet();

 	    $workspace_it = getFactory()->getObject('Workspace')->getRegistry()->getDefault();
 	    
 	    $area_menu_it = $area_menu->getAll();

 	    $areas = array();
 	    
 	    while( !$area_it->end() )
 	    {
 	    	$workspace_it->moveTo('UID', $area_it->getId());
 	    	
 	    	if ( !$workspace_it->end() )
 	    	{
 	    		$areas[$area_it->getId()] = $this->loadWorkspace($workspace_it);

 	            $area_it->moveNext();
 	            
 	            continue;
 	    	}
 	    	
 	        $area_menu_it->moveTo('Workspace', $area_it->getId());
 	         
 	        if ( count($area_menu_it->get('items')) < 1 )
 	        {
 	            $area_it->moveNext();
 	            
 	            continue;
 	        }
 	        
 	        $areas[$area_it->getId()] = array( 
 	            'name' => $area_it->getDisplayName(),
 	            'uid' => $area_it->getId(),
 	            'menus' => $area_menu_it->get('items'),
 	            'icon' => $area_it->get('icon')
 	        );
 	        
 	        $area_it->moveNext();
 	    }

 	    return $areas;		
	}
	
	private function loadWorkspace( $workspace_it )
	{
		$data = array(
				'name' => $workspace_it->get('Caption'),
				'uid' => $workspace_it->get('UID'),
				'icon' => $workspace_it->get('Icon')
		);
		
		$menu_it = getFactory()->getObject('WorkspaceMenu')->getRegistry()->Query( 
					array (
							new \FilterAttributePredicate('Workspace', $workspace_it->getId()),
							new \SortOrderedClause()
					)
			);
		
		$items_registry = getFactory()->getObject('WorkspaceMenuItem')->getRegistry();
		
		$report = getFactory()->getObject('PMReport');
		
		$module = getFactory()->getObject('Module');
		
		while( !$menu_it->end() )
		{
			$items = array();
			
			$item_it = $items_registry->Query(
					array (
							new \FilterAttributePredicate('WorkspaceMenu', $menu_it->getId()),
							new \SortOrderedClause()
					)
			);
			
			while( !$item_it->end() )
			{
				if ( $item_it->get('ReportUID') != '' )
				{
					$report_it = $report->getExact($item_it->get('ReportUID'));
				}
				else if ( $item_it->get('ModuleUID') != '' )
				{
					$report_it = $module->getExact($item_it->get('ModuleUID'));
				}
				else
				{
					$item_it->moveNext();
					
					continue;
				}
				
				$item = $report_it->buildMenuItem();
				$item['name'] = $report_it->get('Caption');
				$items[] = $item;
				
				$item_it->moveNext();
			}
			
			$data['menus'][] = array (
					'uid' => $menu_it->get('UID'),
					'name' => $menu_it->get('Caption'),
					'items' => $items
			);
			
			$menu_it->moveNext();
		}
			
		return $data;
	}
}