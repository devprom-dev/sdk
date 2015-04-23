<?php

include_once "FunctionalAreaMenuProjectBuilder.php";

class FunctionalAreaMenuFavoritesBuilder extends FunctionalAreaMenuProjectBuilder
{
    public function build( FunctionalAreaMenuRegistry & $set )
    {
    	$menus = parent::build($set);
 	    
		$report = getFactory()->getObject('PMReport');

		$this->createCustomReports();
		
		$items = array();

		$this->buildQuickItems($items);
		$menus['quick']['items'] = array_merge($items, $menus['quick']['items']); 
		$menus['quick']['items'][] = $report->getExact('discussions')->buildMenuItem();
		
		$this->buildDocumentsFolder( $menus );
		
		$set->setAreaMenus( FUNC_AREA_FAVORITES, $menus );
		
		return $menus;
    }
    
    protected function createCustomReports()
    {
		$report = getFactory()->getObject('PMReport');
		$custom = getFactory()->getObject('pm_CustomReport');
    	
        $custom_it = $custom->getMyRegistry()->getAll();
		
	    // append default reports
	    $report_it = $report->getExact('mytasks');
	    if ( $report_it->getId() != '' && !in_array('mytasks', $custom_it->fieldToArray('ReportBase')) )
	    {
    	    $custom->add_parms( array (
    	            'Caption' => $report_it->get('Caption'),
    	            'ReportBase' => $report_it->getId(),
    	            'Category' => FUNC_AREA_FAVORITES,
    	            'Url' => $report_it->get('QueryString'),
    	    		'OrderNum' => 10
    	    ));
	    }
		    
	    $report_it = $report->getExact('myissues');
	    if ( $report_it->getId() != '' && !in_array('myissues', $custom_it->fieldToArray('ReportBase')) )
	    {
   		    $custom->add_parms( array (
   		            'Caption' => $report_it->get('Caption'),
   		            'ReportBase' => $report_it->getId(),
   		            'Category' => FUNC_AREA_FAVORITES,
   		            'Url' => $report_it->get('QueryString'),
   		    		'OrderNum' => 11
   		    ));
	    }
    }
    
    protected function buildQuickItems( &$items )
    {
    	$report = getFactory()->getObject('PMReport');
    	
    	if ( getSession()->getProjectIt()->IsProgram() )
    	{
			 $item = $report->getExact('issuesboardcrossproject')->buildMenuItem();
			 $item['name'] = text(1929);
			 $items[] = $item;
    	}
    	
    	$custom_it = getFactory()->getObject('pm_CustomReport')->getMyRegistry()->Query(
					array (
							new SortOrderedClause()
					)				
			);
		while ( !$custom_it->end() )
		{
		    $it = $report->getExact($custom_it->get('ReportBase'));
		    
			if ( $it->getId() == '' || !getFactory()->getAccessPolicy()->can_read($it) ) {
			    $custom_it->moveNext(); continue;
			}
		    
			$items[$custom_it->getId()] = $report->getExact($custom_it->getId())->buildMenuItem();
			$items[$custom_it->getId()]['uid'] = $custom_it->get('ReportBase');
			
			$custom_it->moveNext();
		}
    }
    
    protected function buildDocumentsFolder( &$menus )
    {
    	if ( !getSession()->getProjectIt()->object instanceof Portfolio ) return;
    	
    	$menus['documents'] = array (
 	        'name' => translate('Документы'),
            'uid' => 'documents',
            'items' => array()
 	    );
    }
}