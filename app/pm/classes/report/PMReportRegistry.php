<?php
include "ReportsCustomBuilder.php";

class PMReportRegistry extends ReportRegistry
{
 	function createSQLIterator( $sql )
 	{
 	    $this->setReports(array());
 	    
 	    $builders = getSession()->getBuilders('ReportsBuilder');
 	    $builders[] = new ReportsCustomBuilder();
 	    
 	    foreach( $builders as $builder ) {
 	        $builder->build( $this );
 	    }
		
		$reports = $this->getReports();
		
		// pass Report Category Index for sorting
		$category = getFactory()->getObject('PMReportCategory');
		$category_it = $category->getAll();

	    foreach( $reports as $key => $report )
	    {
	        $category_it->moveTo('ReferenceName', $report['category']);
	        
	        if ( $category_it->get('ReferenceName') != $report['category'] ) {
	            // if there is no required category use empty one
	            $reports[$key]['category'] = '';
	            $reports[$key]['category-index'] = 999; 
	        }
	        else {
	            $reports[$key]['category-index'] = $category_it->getPos();
	        }	            
	    }
	    
	    $this->setReports( $reports );

	    return parent::createSQLIterator( $sql ); 
 	}
}
