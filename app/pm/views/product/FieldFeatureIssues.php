<?php

include "FormFeatureIssuesEmbedded.php";

class FieldFeatureIssues extends FieldForm
{
 	var $object_it;
 	
 	function __construct( $object_it ) {
 		$this->object_it = $object_it;
 	}
 	
 	function render( $view ) {
	    $this->draw( $view );    
	}
 	
 	function draw( $view = null )
 	{
 		$task = getFactory()->getObject('pm_ChangeRequest');
 		$task->disableVpd();

 		$task->addFilter( new FilterAttributePredicate( 'Function',
 			is_object($this->object_it) ? $this->object_it->getId() : -1 ) );
 		
 		$task->addSort( new SortAttributeClause('State') );
 		$task->addSort( new SortOrderedClause() );
 		$task->addSort( new SortKeyClause() );
 		
 		echo '<div id="'.$this->getId().'" class="'.(!$this->readOnly() ? "attwritable" : "attreadonly").'">';
 		    $form = new FormFeatureIssuesEmbedded( $task, 'Function' );
 		    if ( is_object($this->object_it) && !$this->getEditMode() ) $form->setObjectIt($this->object_it);
 		    $form->setReadonly( $this->readOnly() );
 		    $form->setTabIndex( $this->getTabIndex() );
	 		
 		    $form->draw( $view );
 		echo '</div>';
 	}
} 