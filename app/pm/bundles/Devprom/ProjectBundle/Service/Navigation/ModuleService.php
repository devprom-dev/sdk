<?php

namespace Devprom\ProjectBundle\Service\Navigation;

class ModuleService
{
	public function getModules()
	{
		$data = array();
		
		// initialize data used by reports
		$category = getFactory()->getObject('PMReportCategory');
 	    
 	    $category_it = $category->getAll();
 	    
 	    while( !$category_it->end() )
 	    {
			$data[$category_it->get('ReferenceName')] = array (
					'id' => $category_it->get('ReferenceName'),
					'title' => \IteratorBase::wintoutf8($category_it->getDisplayName()),
					'nodes' => array()
			);
			
 	    	$category_it->moveNext();
 	    }
 	    
 		$module = getFactory()->getObject('Module');
 		
 		$module_it = $module->getAll();		

		$report_it = getFactory()->getObject('PMReport')->getRegistry()->getAll();
		
		$skip_modules = array();
		
		while( !$report_it->end() )
		{
			if ( !array_key_exists($report_it->get('Category'), $data) )
			{
				$report_it->moveNext();
				continue;
			}
			
			if ( !getFactory()->getAccessPolicy()->can_read($report_it) )
			{
				$report_it->moveNext(); continue;
			}
				
			$info = $report_it->buildMenuItem();
			
			$data[$report_it->get('Category')]['nodes'][] = array (
					'id' => $report_it->getId(),
					'title' => \IteratorBase::wintoutf8($report_it->getDisplayName()),
					'type' => 'report',
					'desc' => $report_it->get('Description') != '' ? $report_it->get('Description') : '',
					'reportUrl' => $info['url'] != '' ? $info['url'] : '#'
			);
			
			if ( $report_it->get('Module') != '' ) $skip_modules[] = $report_it->get('Module');
			
			$report_it->moveNext();
		}

		while( !$module_it->end() )
		{
			if ( in_array($module_it->getId(), $skip_modules) )
			{
				$module_it->moveNext(); continue;
			}
			
			if ( !array_key_exists($module_it->get('Area'), $data) )
			{
				$module_it->moveNext(); continue;
			}
			
			if ( !getFactory()->getAccessPolicy()->can_read($module_it) )
			{
				$module_it->moveNext(); continue;
			}
			
			$data[$module_it->get('Area')]['nodes'][] = array (
					'id' => $module_it->getId(),
					'title' => \IteratorBase::wintoutf8($module_it->getDisplayName()),
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