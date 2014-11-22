<?php

include_once "AutoSaveTextWebMethod.php";

class AutoSaveFieldWebMethod extends AutoSaveTextWebMethod
{
 	var $object_it, $field;
 	
 	function AutoSaveFieldWebMethod( $object_it = null, $field = '' )
 	{
 		$this->object_it = $object_it;
 		$this->field = $field;

 		parent::AutoSaveTextWebMethod();
 	}
 	
 	function getTitle()
 	{
 		return translate($this->object_it->object->getAttributeUserName( $this->field ));
 	}
 	
 	function hasAccess()
 	{
 		return is_object($this->object_it) 
 			? getFactory()->getAccessPolicy()->can_modify_attribute($this->object_it->object, $this->field)
 			: true; 
 	}
 	
 	function draw()
 	{
 		parent::draw( 
 			array( 'class' => get_class($this->object_it->object),
 				   'object' => $this->object_it->getId(),
 				   'field' => $this->field ),
 			$this->object_it->get($this->field) );
 	}
 	
 	function execute( $parms, $value )
 	{
 		$class_name = getFactory()->getClass($parms['class']);
 		
 		if ( !class_exists($class_name) ) throw new Exception('Unknown class: '.$class_name);
 		
 		$object = getFactory()->getObject($class_name);
 		
 		$object_it = $object->getExact(IteratorBase::utf8towin($parms['object']));

 		if ( $object_it->getId() == '' ) throw new Exception('Object was not found: '.$parms['object']);
 		
 		if ( getFactory()->getAccessPolicy()->can_modify($object_it) )
 		{
 			$object->modify_parms($object_it->getId(), array( $parms['field'] => $value ));
 		}
 	}
}
