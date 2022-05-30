<?php
include "classes/ReportsKanbanBuilder.php";
include "classes/StateKanbanMetadataBuilder.php";
include "classes/RequestKanbanMetadataBuilder.php";
include "classes/widgets/KanbanTourScriptBuilder.php";
include "classes/rules/KanbanIssueStateBusinessRuleBuilder.php";
include "classes/rules/KanbanTaskStateBusinessRuleBuilder.php";

class KanbanPmPlugin extends PluginPMBase
{
    function checkSpecialFeatures()
    {
        $methodology_it = getSession()->getProjectIt()->getMethodologyIt();
        if ( is_object($methodology_it) ) {
            return $methodology_it->get('IsKanbanUsed') == 'Y';
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

		if ( !$this->checkSpecialFeatures() ) return $modules;

		if ( !getSession()->IsRDD() ) {
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
        }

 		return $modules;
 	}

 	function getBuilders()
 	{
        $builders = array(
            new RequestKanbanMetadataBuilder(),
            new StateKanbanMetadataBuilder()
        );

        if ( !$this->checkSpecialFeatures() ) return $builders;

		return array_merge(
            $builders,
            array (
                new ReportsKanbanBuilder( getSession() ),
                // widgets
                new KanbanIssueStateBusinessRuleBuilder(),
                new KanbanTaskStateBusinessRuleBuilder()
            )
 	    );
 	}
 	
 	function interceptMethodListSetupColumns( & $list  )
 	{
 	 	if ( is_a($list, 'VersionList') )
 	    {
     	    if ( !$this->checkSpecialFeatures() ) return;

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
			if ( $this->method_block->hasAccess() ) {
                $actions[] = array (
                    'name' => $this->method_block->getCaption(),
                    'url' => $this->method_block->getJSCall()
                );
            }
		}
		else {
            if ( $this->method_unblock->hasAccess() ) {
                $this->method_unblock->setObjectIt($object_it);
                $actions[] = array(
                    'name' => $this->method_unblock->getCaption(),
                    'url' => $this->method_unblock->getJSCall()
                );
            }
		}

		return $actions;
	}

	private $method_block = null;
	private $method_unblock = null;
}