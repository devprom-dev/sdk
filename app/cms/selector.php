<?php

 include('common.php');
 include('design.php');
 include('c_package_view.php');
 require_once('c_selector.php');
 
 $model_factory =& getModelFactory();

 $class = $_REQUEST['class'];
 $entity = $_REQUEST['entity'];
 $kind = $_REQUEST['kind'];
 $field = $_REQUEST['field'];
 
 /////////////////////////////////////////////////////////////////////////////////////////////
 if(isset($class) && isset($kind) && isset($field))
 {
	if(isset($entity)) 
	{
		$object = $model_factory->getObject2($class, $entity );
	}
	else 
	{
		$object = $model_factory->getObject($class);
	}
	
	$selector = new $kind( $object, $field );
 }


?>