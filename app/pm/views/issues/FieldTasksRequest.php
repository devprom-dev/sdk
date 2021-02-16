<?php
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
 		if ( is_object($this->object_it) ) {
            $taskIt = $task->getRegistry()->Query(array(
                new FilterAttributePredicate( 'ChangeRequest', $this->object_it->getId()),
                new SortAttributeClause('State'),
                new SortOrderedClause(),
                new SortKeyClause()
            ));
        }
 		else {
 		    $taskIt = $task->getEmptyIterator();
        }

 		echo '<div id="'.$this->getId().'" class="'.(!$this->readOnly() ? "attwritable" : "attreadonly").'">';
 		    $form = new FormRequestTasksEmbedded( $taskIt, 'ChangeRequest' );
            $form->setRelease($this->releaseId);

            if ( $_REQUEST['Iteration'] > 0 ) {
                $_REQUEST['Release'] = $_REQUEST['Iteration'];
            }
            if ( is_object($this->object_it) && $this->object_it->get('Iteration') > 0 ) {
                $_REQUEST['Release'] = $this->object_it->get('Iteration');
            }
 		    if ( is_object($this->object_it) ) $form->setObjectIt($this->object_it);
 		    
 		    $form->setReadonly( $this->readOnly() );
 		    $form->setTabIndex( $this->getTabIndex() );
	 		
 		    $form->draw( $view );
 		echo '</div>';
 	}
}