<?php

include_once "DictionaryRegistry.php";

class WorkflowRegistry extends DictionaryRegistry
{
 	function createSQLIterator( $sql )
 	{
 		foreach( getSession()->getBuilders('WorkflowBuilder') as $builder )
 		{
 			$builder->build( $this );
 		}
 		
 		return $this->createIterator($this->getEntities());  
 	}
}