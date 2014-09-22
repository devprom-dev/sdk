<?php

include SERVER_ROOT_PATH."pm/classes/widgets/FunctionalArea.php";

class PMReportCategoryRegistry extends ReportCategoryRegistry
{
 	function createSQLIterator( $sql )
 	{
 	    $set = new ModuleCategory();
        
 	    $area_it = $set->getAll();
 	            
 	    while( !$area_it->end() )
 	    {
    		$this->addCategory( array ( 
		        'name' => $area_it->getId(),
				'title' => $area_it->getId() == 'favs' ? text(1811) : $area_it->getDisplayName() 
    		));
    		
 	        $area_it->moveNext();
 	    }
 	    
 	    return parent::createSQLIterator( $sql );
 	}
}
