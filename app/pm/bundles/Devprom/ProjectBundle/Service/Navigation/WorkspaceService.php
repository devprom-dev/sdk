<?php
namespace Devprom\ProjectBundle\Service\Navigation;

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
		
		$areas = $this->getFunctionalAreas(getSession()->getProjectIt());
		
		foreach( $areas as $area )
		{
			$nodes = array();

			foreach( $area['menus'] as $group )
			{
				$items = array();
				
				foreach( $group['items'] as $key => $item )
				{
					$items[] = array (
							'title' => \IteratorBase::wintoutf8($item['name']),
							'report' => array (
									'id' => $item['report'] == '' ? $item['module'] : $item['uid'],
									'type' => $item['report'] == '' ? 'module' : 'report',
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

            if ( !is_array($area['menus']) ) {
                $nodes[] = array (
                    'id' => '0',
                    'title' => '',
                    'nodes' => array()
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
		$workspace_it = $workspace->getRegistry()->Merge(
                array (
                    'UID' => $data['id'],
                    'SystemUser' => getSession()->getUserIt()->getId(),
                    'Caption' => \IteratorBase::utf8towin($data['title']),
                    'Icon' => $data['icon']
                ),
                array('UID', 'SystemUser')
			);

		// merge nodes
		foreach( $data['menuNodes'] as $key => $node )
		{
			if ( $node['id'] == '' ) $data['menuNodes'][$key]['id'] = $node['id'] = md5($key . microtime());
			 
			$menu_it = $menu->getRegistry()->Merge(
                    array (
                        'UID' => $node['id'],
                        'Workspace' => $workspace_it->getId(),
                        'Caption' => \IteratorBase::utf8towin($node['title'])
                    ),
                    array('UID', 'Workspace')
				);

			// merge node items
			if ( is_array($node['nodes']) )
			{
				foreach($node['nodes'] as $key => $node_item )
				{
					$uid = $node_item['report']['id'];
					if ( $uid == '' ) continue;
					
					$item_it = $item->getRegistry()->Merge(
                            array (
                                'UID' => $uid,
                                'ReportUID' => $node_item['report']['type'] == 'report' ? $uid : '',
                                'ModuleUID' => $node_item['report']['type'] == 'module' ? $uid : '',
                                'WorkspaceMenu' => $menu_it->getId(),
                                'OrderNum' => $key + 1
                            ),
                            array('UID', 'WorkspaceMenu')
						);
				}
			}
		}
	}
	
	public function removeWorkspace( $area_id )
	{
		$workspace_it = getFactory()->getObject('Workspace')->getRegistry()->Query( 
                array (
                    new \FilterTextExactPredicate('UID', $area_id),
                    new \FilterAttributePredicate('SystemUser', getSession()->getUserIt()->getId()),
                    new \FilterBaseVpdPredicate()
                )
			);
		
		if ( $workspace_it->getId() < 1 ) return;
		
		$workspace_it->delete();
	}
	
	public function removeWorkspaces()
	{
		$workspace_it = getFactory()->getObject('Workspace')->getRegistry()->Query( 
					array (
							new \FilterAttributePredicate('SystemUser', getSession()->getUserIt()->getId()),
							new \FilterBaseVpdPredicate()
					)
			);
		
		while( !$workspace_it->end() )
		{
			$workspace_it->delete();
			$workspace_it->moveNext();
		}
	}
	
	public function storeReportToWorkspace( $report, $workspace_uid = FUNC_AREA_FAVORITES )
	{
		$workspaces = $this->getWorkspaces();

		foreach( $workspaces as $workspace_key => $workspace )
		{
			if ( $workspace['id'] != $workspace_uid ) continue;
			
			foreach( $workspace['menuNodes'] as $node_key => $node )
			{
				if ( $node['id'] != 'quick' && $node['id'] != '0' ) continue;
				
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
	
	public function getItemOnFavoritesWorkspace( $uids )
	{
		$reports = array();
		$workspaces = $this->getWorkspaces();

		foreach( $workspaces as $workspace_key => $workspace ) {
			if ( $workspace['id'] != FUNC_AREA_FAVORITES ) continue;

			foreach( $workspace['menuNodes'] as $node_key => $node ) {
				$reports =
					array_merge(
						$reports,
						array_filter($workspace['menuNodes'][$node_key]['nodes'], function($item) use($uids) {
							return in_array($item['report']['id'], $uids, true);
						})
					);
			}
		}
		return $reports;
	}
	
	public function getFunctionalAreas( $project_it )
	{
        getSession()->insertBuilder( new \FunctionalAreaCommonBuilder() );
 	    if ( $project_it->IsPortfolio() ) {
            if ( $project_it->get('CodeName') == 'my' ) {
                getSession()->insertBuilder( new \FunctionalAreaMenuMyProjectsBuilder() );
            }
 	    	getSession()->insertBuilder( new \FunctionalAreaMenuPortfolioBuilder() );
 	    }
 	    else {
 	    	getSession()->insertBuilder( new \FunctionalAreaMenuFavoritesBuilder() );
 	    }
 	        
 	    getSession()->insertBuilder( new \FunctionalAreaMenuManagementBuilder() ); 
 	    getSession()->insertBuilder( new \FunctionalAreaMenuSettingsBuilder() ); 
		
	    $area_set = new \FunctionalArea();
 	    $area_it = $area_set->getAll();

 	    $area_menu = new \FunctionalAreaMenuSet();
        $area_menu->setVpdContext($project_it);
 	    $area_menu_it = $area_menu->getAll();

		$workspace_it = getFactory()->getObject('Workspace')
            ->getRegistry()->getDefault($project_it);

		$areas = array();
 	    while( !$area_it->end() )
 	    {
 	    	$workspace_it->moveTo('UID', $area_it->getId());
 	    	if ( $workspace_it->get('UID') == $area_it->getId() ) {
 	    		$areas[$area_it->getId()] = $this->loadWorkspace($workspace_it);
 	            $area_it->moveNext();
 	            continue;
 	    	}
 	    	
 	        $area_menu_it->moveTo('Workspace', $area_it->getId());
 	    	$items = $area_menu_it->get('items');
 	        if ( count($items) < 1 ) {
                $items[] = array(
                    'uid' => 'quick',
                    'name' => '',
                    'nodes' => array()
                );
 	        }

 	        $areas[$area_it->getId()] = array(
 	            'name' => $area_it->getDisplayName(),
 	            'uid' => $area_it->getId(),
 	            'menus' => $items,
 	            'icon' => $area_it->get('icon')
 	        );
 	        
 	        $area_it->moveNext();
 	    }

 	    return $areas;		
	}
	
	private function loadWorkspace( $workspace_it )
	{
		$data = array(
				'name' => translate($workspace_it->get('Caption')),
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
					$item = $report_it->buildMenuItem();
					$item['name'] = $report_it->get('Caption');
					$item['report'] = $report_it->getId();
					$item['module'] = $report_it->get('Module');
					if ( $item['icon'] == '' && $item['module'] != '' ) {
						$module_it = $module->getExact($report_it->get('Module'));
						$item['icon'] = $module_it->get('Icon');
					}
				}
				else if ( $item_it->get('ModuleUID') != '' )
				{
					$module_it = $module->getExact($item_it->get('ModuleUID'));
					$item = $module_it->buildMenuItem();
					$item['name'] = $module_it->get('Caption');
					$item['report'] = '';
					$item['module'] = $module_it->getId();
				}
				else
				{
					$item_it->moveNext();
					continue;
				}
				
				$items[] = $item;
				
				$item_it->moveNext();
			}

			$data['menus'][] = array (
					'uid' => $menu_it->get('UID'),
					'name' => translate($menu_it->get('Caption')),
					'items' => $items
			);
			
			$menu_it->moveNext();
		}
			
		return $data;
	}
}