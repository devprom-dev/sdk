<?php
include_once SERVER_ROOT_PATH."pm/classes/common/SharedObjectsBuilder.php";

class SharedObjectsTasksBuilder extends SharedObjectsBuilder
{
    public function getGroup() {
        return 'Tasks';
    }
    
    public function build( SharedObjectRegistry & $set )
    {
        $entities = array (
            'TaskState',
            'TaskType',
            'Task',
            'WorkItem'
		);
 		foreach( $entities as $key ) {
			$set->add( $key, $this->getGroup() );
		} 	            
    }
}