<?php

include "classes/widgets/FunctionalAreaMenuKanbanBuilder.php";
include "classes/ReportsKanbanBuilder.php";
include "classes/MethodologyKanbanMetadataBuilder.php";
include "classes/StateKanbanMetadataBuilder.php";
include "classes/RequestKanbanMetadataBuilder.php";
include "classes/widgets/KanbanTourScriptBuilder.php";

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
		$modules = array (
		    'avgleadtime' => 
 				array(
 					'includes' => array( 'kanban/views/LeadAndCycleTimePage.php' ),
 					'classname' => 'LeadAndCycleTimePage',
 				    'title' => text('kanban19'),
 					'description' => text('kanban27'),
 				    'AccessEntityReferenceName' => 'pm_ChangeRequest',
 					'area' => FUNC_AREA_MANAGEMENT,
					'icon' => 'icon-signal'
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
 					'area' => FUNC_AREA_MANAGEMENT,
					'icon' => 'icon-th-large'
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
			new MethodologyKanbanMetadataBuilder(),
			new RequestKanbanMetadataBuilder(),

			// widgets
			new KanbanTourScriptBuilder(getSession())
 	    );
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

	function getObjectActions( $object_it )
	{
		if ( $object_it->object instanceof Request ) {
			return $this->getIssueActions($object_it);
		}
		return array();
	}

	protected function getIssueActions( $object_it )
	{
		if ( is_null($this->kanban_vpds) ) {
			$this->kanban_vpds = getFactory()->getObject('Project')->getRegistry()->Query(
					array(new ProjectUseKanbanPredicate())
				)->fieldToArray('VPD');
		}
		if ( !in_array($object_it->get('VPD'), $this->kanban_vpds) ) return array();
		if ( $object_it->object->getAttributeType('BlockReason') == '' ) return array();

		if ( !is_object($this->method_block) ) {
			$this->method_block = new KanbanBlockIssueWebMethod($object_it);
		}
		if ( !is_object($this->method_unblock) ) {
			$this->method_unblock = new KanbanUnblockIssueWebMethod($object_it);
		}

		$actions = array();

		if ( $object_it->get('BlockReason') == '' ) {
			$this->method_block->setObjectIt($object_it);
			$actions[] = array (
				'name' => $this->method_block->getCaption(),
				'url' => $this->method_block->getJSCall()
			);
		}
		else {
			$this->method_unblock->setObjectIt($object_it);
			$actions[] = array (
				'name' => $this->method_unblock->getCaption(),
				'url' => $this->method_unblock->getJSCall()
			);
		}

		return $actions;
	}

	private $method_block = null;
	private $method_unblock = null;
	private $kanban_vpds = null;
}