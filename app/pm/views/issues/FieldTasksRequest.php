<?php
include_once SERVER_ROOT_PATH."pm/classes/tasks/TaskModelExtendedBuilder.php";
include "FormRequestTasksEmbedded.php";

class FieldTasksRequest extends FieldForm
{
 	var $object_it;
    private $releaseId = '';
 	
 	function FieldTasksRequest( $object_it )
 	{
 		$this->object_it = $object_it;
 	}
 	
 	function render( $view )
	{
	    $this->draw( $view );    
	}

	function setRelease( $value ) {
	    $this->releaseId = $value;
    }
 	
 	function draw( $view = null )
 	{
 		$task = getFactory()->getObject('pm_Task');
        $builder = new TaskModelExtendedBuilder();
        $builder->build($task);
 		$task->disableVpd();
 		
 		$task->addFilter( new FilterAttributePredicate( 'ChangeRequest', 		 
 			is_object($this->object_it) ? $this->object_it->getId() : 0 ) );
 		
 		$task->addSort( new SortAttributeClause('State') );
 		$task->addSort( new SortOrderedClause() );
 		$task->addSort( new SortKeyClause() );
 		
 		echo '<div id="'.$this->getId().'" class="'.(!$this->readOnly() ? "attwritable" : "attreadonly").'">';

 		    $form = new FormRequestTasksEmbedded( $task, 'ChangeRequest' );

            $form->setRelease($this->releaseId);
            if ( $_REQUEST['Iteration'] > 0 ) {
                $_REQUEST['Release'] = $_REQUEST['Iteration'];
            }
            if ( is_object($this->object_it) && $this->object_it->get('Iteration') > 0 ) {
                $_REQUEST['Release'] = $this->object_it->get('Iteration');
            }
 		    if ( is_object($this->object_it) && !$this->getEditMode() ) $form->setObjectIt($this->object_it);
 		    
 		    $form->setReadonly( $this->readOnly() );
 		    $form->setTabIndex( $this->getTabIndex() );
	 		
 		    $form->draw( $view );
 		echo '</div>';
 	}
}