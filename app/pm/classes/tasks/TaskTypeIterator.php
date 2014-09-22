<?php

class TaskTypeIterator extends OrderedIterator
{
 	function getTasksCount()
 	{
 		global $model_factory;
 		$task = $model_factory->getObject('pm_Task');

 		return $task->getByRefArrayCount(
 			array ( 'TaskType' => $this->getId() )
 			);
 	}
}
