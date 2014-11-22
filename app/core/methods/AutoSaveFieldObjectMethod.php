<?php

include_once "SelectWebMethod.php";

class AutoSaveFieldObjectMethod extends SelectWebMethod
{
 	var $attribute;
 	var $object_it;
 	
 	function AutoSaveFieldObjectMethod ( $object_it = null, $attribute = '' )
 	{
 		parent::SelectWebMethod();
 		
 		$this->attribute = $attribute;
 		$this->object_it = $object_it;
 	}
 	
 	function getValues()
	{
		$values = array();
		$values[''] = '';
		
		$ref = $this->object_it->object->
			getAttributeObject( $this->attribute );
			
		$it = $ref->getAll();
		while ( !$it->end() )
		{
			$values[' '.$it->getId()] = $it->getDisplayName();
			$it->moveNext();
		}
		
		return $values;
	}
 	
 	function getStyle()
 	{
 		return 'width:100%;';
 	}
 	
	function draw( $parms_array = array() ) 
 	{
 		SelectWebMethod::drawSelect( 
 			array(
 				'class' => strtolower(get_class($this->object_it->object)),
 				'object' => $this->object_it->getId(),
 				'attribute' => $this->attribute 
 			), 
 			$this->object_it->get($this->attribute) 
 		);
 	}
 	 	
 	function execute_request()
 	{
 		$object = getFactory()->getObject($_REQUEST['class']);
 		$object_it = $object->getExact($_REQUEST['object']);
 		
 		if ( !getFactory()->getAccessPolicy()->can_modify($object_it) ) return;
 		
 		$object->modify_parms($object_it->getId(), 
 			array( $_REQUEST['attribute'] => trim($_REQUEST['value']) ) 
 		);
	}
}