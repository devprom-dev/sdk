<?php
include_once SERVER_ROOT_PATH."pm/classes/report/ReportsBuilder.php";

class ReportsCustomBuilder extends ReportsBuilder
{
    public function build( ReportRegistry & $object )
    {
		$custom_it = getFactory()->getObject('pm_CustomReport')->getRegistry()->Query(
            array(
                new CustomReportMyPredicate(),
                new SortOrderedClause(),
                new FilterBaseVpdPredicate()
            )
		);

		while ( !$custom_it->end() )
		{
			$base_report = array();
			
			if ( $custom_it->get_native('Module') != '' ) {
				$base_report['module'] = $custom_it->get_native('Module');
			}
			
			if ( !isset($base_report['module']) ) {
				$base_report = $object->getReport($custom_it->get_native('ReportBase'));
				if ( count($base_report) < 1 ) {
				    $custom_it->moveNext(); continue;
				}
			}
						
			$object->addReport( array ( 
			    'name' => $custom_it->getId(),
				'title' => $custom_it->get_native('Caption'),
			    'description' => $custom_it->get_native('Description'),
				'report' => $custom_it->get_native('ReportBase'),
				'module' => $base_report['module'],
			    'query' => $custom_it->getHtmlDecoded('Url'),
				'category' => trim($custom_it->get_native('Category')),
				'author' => intval($custom_it->get_native('Author')),
				'custom' => true,
                'created' => $custom_it->get_native('RecordCreated'),
                'modified' => $custom_it->get_native('RecordModified'),
                'active' => $custom_it->get_native('IsActive')
			));

			$custom_it->moveNext();
		}
    }
}