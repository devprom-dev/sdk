<?php

class WidgetRegistry extends ObjectRegistrySQL
{
	function createSQLIterator( $sql )
 	{
 		$data = array();

 		$resource_it = getFactory()->getObject('ContextResource')->getAll();

		$module_it = getFactory()->getObject('Module')->getAll();
		while( !$module_it->end() )
		{
			$resource_it->moveToId($module_it->getId());
			if ( $resource_it->getId() == '' ) $resource_it->moveToId($module_it->getId().':'.array_shift(preg_split('/_/', getSession()->getProjectIt()->get('Tools'))));

			$data[$module_it->getDisplayName()] = array (
				'entityId' => $module_it->getId(),
				'Caption' => $module_it->getDisplayName(),
				'ReferenceName' => $resource_it->getId() != '' ? $resource_it->getHtmlDecoded('Caption') : $module_it->getHtmlDecoded('Description'),
                'url' => $module_it->getUrl()
			);
			$module_it->moveNext();
		}

		$report_it = getFactory()->getObject('PMReport')->getAll();
 		while( !$report_it->end() )
 		{
 			$resource_it->moveToId($report_it->getId());
			$description = $resource_it->getHtmlDecoded('Caption');

			if ( $description == '' ) {
				$resource_it->moveToId($report_it->get('Module'));
				$description = $resource_it->getHtmlDecoded('Caption');
			}

			if ( $description == '' ) {
				$description = $report_it->getHtmlDecoded('Description');
			}
 			
 			$data[$report_it->getDisplayName()] = array (
                'entityId' => $report_it->getId(),
                'Caption' => $report_it->getDisplayName(),
                'ReferenceName' => $description,
                'url' => $report_it->getUrl()
 			);
 			$report_it->moveNext();
 		}

		foreach( $this->getFilters() as $filter ) {
			if ( $filter instanceof FilterSearchAttributesPredicate ) {
				$words = SearchRules::getSearchItems($filter->getValue(), getSession()->getLanguageUid());
				$data = array_filter($data, function($value) use ($words) {
					$title = $value['Caption'].$value['Description'];
					return array_sum(
						array_map(
							function($item) use ($title) {
								return mb_stripos($title, $item) !== false;
							},
							$words
						)
					) == count($words);
				});
			}
		}

 		usort($data, function( $left, $right ) {
 		    return $left['Caption'] > $right['Caption'];
 		});

        return $this->createIterator( $data );
 	}
}