<?php
include_once SERVER_ROOT_PATH."cms/classes/ObjectFactoryNotificator.php";

define( 'TRIGGER_ACTION_ADD', 'add' );
define( 'TRIGGER_ACTION_MODIFY', 'modify' );
define( 'TRIGGER_ACTION_DELETE', 'delete' );

abstract class SystemTriggersBase extends ObjectFactoryNotificator
{
	private $was_data = array();
	
 	function add( $object_it ) 
	{
		if ( $object_it->getId() < 1 ) {
            throw new Exception(
                get_class($this).': unable execute trigger on empty object');
        }
		
        $this->process( $object_it->copy(), TRIGGER_ACTION_ADD, $object_it->getData() );
	}

 	function modify( $prev_object_it, $object_it ) 
	{
		if ( $object_it->getId() < 1 ) {
            throw new Exception(
                get_class($this). ': unable execute trigger on empty object');
        }
		
		$this->was_data = $prev_object_it->getData();
		
		$this->process( $object_it->copy(), TRIGGER_ACTION_MODIFY, array_diff_assoc($object_it->getData(), $this->was_data) );
	}

 	function delete( $object_it ) 
	{
		// $object_it can be empty iterator (eg., deleteAll)
		
		$this->process( $object_it->copy(), TRIGGER_ACTION_DELETE, array() );
	}

	abstract function process( $object_it, $kind, $content = array(), $visibility = 1); 

	// returns the data were before modification
	function getWasData()
	{
		return $this->was_data;
	}
}
 