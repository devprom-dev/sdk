<?php

class WidgetRegistry extends ObjectRegistrySQL
{
 	function createSQLIterator( $sql )
 	{
 		$data = array();
 		$was_modules = array();
 		
 		$resource_it = getFactory()->getObject('ContextResource')->getAll();
 		
 		$report_it = getFactory()->getObject('PMReport')->getAll();
 		while( !$report_it->end() )
 		{
 			$resource_it->moveToId($report_it->getId());
 			
 			$data[] = array (
 					'entityId' => $report_it->getId(),
 					'Caption' => $report_it->getDisplayName(),
 					'ReferenceName' => $resource_it->getId() != '' ? $resource_it->getHtmlDecoded('Caption') : $report_it->getHtmlDecoded('Description')
 			);
 			$was_modules[] = $report_it->get('Module');
 			$report_it->moveNext();
 		}

 		$module_it = getFactory()->getObject('Module')->getAll();
 		while( !$module_it->end() )
 		{
 			if ( in_array($module_it->getId(), $was_modules) ) {
 				$module_it->moveNext();
 				continue;
 			}
 				
 			$resource_it->moveToId($module_it->getId());
 			if ( $resource_it->getId() == '' ) $resource_it->moveToId($module_it->getId().':'.array_shift(preg_split('/_/', getSession()->getProjectIt()->get('Tools'))));
 			
 			$data[] = array (
 					'entityId' => $module_it->getId(),
 					'Caption' => $module_it->getDisplayName(),
 					'ReferenceName' => $resource_it->getId() != '' ? $resource_it->getHtmlDecoded('Caption') : $module_it->getHtmlDecoded('Description')
 			);
 			$module_it->moveNext();
 		}
 		
 		usort($data, function( $left, $right ) {
 		    return $left['Caption'] > $right['Caption'];
 		});

        return $this->createIterator( $data );
 	}
}