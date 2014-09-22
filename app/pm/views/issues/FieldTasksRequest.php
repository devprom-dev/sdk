<?php

include "FormRequestTasksEmbedded.php";

class FieldTasksRequest extends FieldForm
{
 	var $object_it;
 	
 	function FieldTasksRequest( $object_it )
 	{
 		$this->object_it = $object_it;
 	}
 	
 	function render( & $view )
	{
	    $this->draw( $view );    
	}
 	
 	function draw( & $view = null )
 	{
 		$task = getFactory()->getObject('pm_Task');
 		$task->disableVpd();
 		
 		$task->addFilter( new FilterAttributePredicate( 'ChangeRequest', 		 
 			is_object($this->object_it) ? $this->object_it->getId() : 0 ) );
 		
 		$task->addSort( new SortAttributeClause('State') );
 		$task->addSort( new SortOrderedClause() );
 		
 		echo '<div id="'.$this->getId().'" class="'.(!$this->readOnly() ? "attwritable" : "attreadonly").'">';

 		    $form = new FormRequestTasksEmbedded( $task, 'ChangeRequest' );
	 		
 		    if ( is_object($this->object_it) && !$this->getEditMode() ) $form->setObjectIt($this->object_it);
 		    
 		    $form->setReadonly( $this->readOnly() );
	 		
 		    $form->setTabIndex( $this->getTabIndex() );
	 		
 		    $form->draw( $view );
	 		
			$report_it = getFactory()->getObject('PMReport')->getExact('currenttasks');
			
			if ( is_object($this->object_it) && getFactory()->getAccessPolicy()->can_read($report_it) )
			{
		 		if( $this->object_it->get('Tasks') != '' )
		 		{
		 		    echo '<br/>';
				 
		 		    echo '<a href="'.$report_it->getUrl().'&issue='.$this->object_it->getId().'&release=all">'.text(1014).'</a>';
				}
	 		}
 		echo '</div>';
 	}
} 