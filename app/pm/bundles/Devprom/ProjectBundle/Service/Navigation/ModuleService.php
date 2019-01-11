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

class ModuleService
{
	public function getModules()
	{
		$data = array();

		// initialize data used by reports
 	    $category_it = getFactory()->getObject('PMReportCategory')->getAll();
 	    while( !$category_it->end() )
 	    {
			$data[$category_it->get('ReferenceName')] = array (
                'id' => $category_it->get('ReferenceName'),
                'title' => \IteratorBase::wintoutf8($category_it->getDisplayName()),
                'nodes' => array()
			);
 	    	$category_it->moveNext();
 	    }

        $area = new \FunctionalArea();
        $areaIt = $area->getAll();

        while( !$areaIt->end() )
        {
            if ( $areaIt->getId() == 'favs' || array_key_exists($areaIt->getId(), $data) ) {
                $areaIt->moveNext();
                continue;
            }

            $data[$areaIt->getId()] = array (
                'id' => $areaIt->getId(),
                'title' => $areaIt->getDisplayName(),
                'nodes' => array()
            );
            $areaIt->moveNext();
        }

 		$module_it = getFactory()->getObject('Module')->getAll();
		$report_it = getFactory()->getObject('PMReport')->getRegistry()->getAll();
		
		$skip_modules = array();
		
		while( !$report_it->end() )
		{
			if ( !getFactory()->getAccessPolicy()->can_read($report_it) ) {
				$report_it->moveNext();
				continue;
			}

			if ( $report_it->getDisplayName() == '' ) {
                $report_it->moveNext();
                continue;
            }
				
			$info = $report_it->buildMenuItem();
			
			$data[$report_it->get('Category')]['nodes'][] = array (
                'id' => $report_it->getId(),
                'title' => $report_it->getDisplayName(),
                'type' => 'report',
                'desc' => $report_it->get('Description') != '' ? $report_it->get('Description') : '',
                'reportUrl' => $info['url'] != '' ? $info['url'] : '#'
			);
			
			if ( $report_it->get('Module') != '' ) $skip_modules[] = $report_it->getDisplayName();
			
			$report_it->moveNext();
		}

		while( !$module_it->end() )
		{
			if ( in_array($module_it->getDisplayName(), $skip_modules) ) {
				$module_it->moveNext(); continue;
			}
			
			if ( !getFactory()->getAccessPolicy()->can_read($module_it) ) {
				$module_it->moveNext(); continue;
			}

            if ( $module_it->getDisplayName() == '' ) {
                $module_it->moveNext(); continue;
            }

			$data[$module_it->get('Area')]['nodes'][] = array (
                'id' => $module_it->getId(),
                'title' => $module_it->getDisplayName(),
                'type' => 'module',
                'desc' => $module_it->get('Description') != '' ? $module_it->get('Description') : '',
                'reportUrl' => $module_it->get('Url') != '' ? $module_it->get('Url') : '#'
			);
			
			$module_it->moveNext();
		}

		foreach( $data as $key => $area )
		{
			usort( $data[$key]['nodes'], function($left, $right) {
				return $left['title'] > $right['title'] ? 1 : -1;
			});
		}
		
		return array_values($data);
	}
}