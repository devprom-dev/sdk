<?php

include "views/KanbanVersionList.php";
include "classes/widgets/FunctionalAreaMenuKanbanBuilder.php";
include "classes/ReportsKanbanBuilder.php";
include "classes/MethodologyKanbanMetadataBuilder.php";
include "classes/StateKanbanMetadataBuilder.php";
include "classes/RequestKanbanMetadataBuilder.php";
include "classes/VersionKanbanMetadataBuilder.php";

class KanbanPmPlugin extends PluginPMBase
{
    private $enabled;
    
 	function checkEnabled()
 	{
 	    if ( isset($this->enabled) ) return $this->enabled;
 	     
 		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
 		
 		if ( is_object($methodology_it) )
 		{
 			return ($this->enabled = $methodology_it->get('IsKanbanUsed') == 'Y');
 		}
 		
 		return false;
 	}
 	
 	function getModules()
 	{
 		if ( getSession()->getProjectIt()->IsPortfolio() ) return array();
 		
		$modules = array (
		    'avgleadtime' => 
 				array(
 					'includes' => array( 'kanban/views/LeadAndCycleTimePage.php' ),
 					'classname' => 'LeadAndCycleTimePage',
 				    'title' => text('kanban19'),
 					'description' => text('kanban27'),
 				    'AccessEntityReferenceName' => 'pm_ChangeRequest',
 					'area' => FUNC_AREA_MANAGEMENT
 					)
 			);

		if ( !$this->checkEnabled() ) return $modules;
		
		$modules['requests'] = 
 				array(
 					'includes' => array( 'kanban/views/KanbanRequestPage.php' ),
 					'classname' => 'KanbanRequestPage',
 				    'title' => text('kanban17'),
 					'description' => text('kanban29'),
 				    'AccessEntityReferenceName' => 'pm_ChangeRequest',
 					'area' => FUNC_AREA_MANAGEMENT
 					);
		
 		return $modules;
 	}

 	function getBuilders()
 	{
 	    return array (
 	            new ReportsKanbanBuilder( getSession() ),
 	            new FunctionalAreaMenuKanbanBuilder(),
 	    		
 	    		// model extenders
 	    		new StateKanbanMetadataBuilder( getSession() ),
 	    		new RequestKanbanMetadataBuilder( getSession() ),
 	    		new VersionKanbanMetadataBuilder( getSession() ),
 	    		new MethodologyKanbanMetadataBuilder()
 	    );
 	}
 	
  	function interceptMethodListDrawCell( & $list, & $object_it, $attr )
 	{ 	
 	    if ( !$this->checkEnabled() ) return;
 	    
 	    if ( is_a($list, 'VersionList') )
 	    {
 	        return KanbanVersionList::drawCell( $list->getIt( $object_it ), $attr );
 	    }
 	    
 		return false;
 	}
 	
 	function interceptMethodListSetupColumns( & $list  )
 	{
 	 	if ( is_a($list, 'VersionList') )
 	    {
     	    if ( !$this->checkEnabled() ) return;

     	    $object = $list->getObject();
     	    
     	    $object->setAttributeVisible('Progress', true); 

     	    $object->setAttributeVisible('Deadlines', false); 
     	    
     	    $object->setAttributeVisible('Description', false); 
 	    }
 	}
}