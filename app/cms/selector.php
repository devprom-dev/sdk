<?php

 include('common.php');
 include('design.php');
 include('c_package_view.php');
 require_once('c_selector.php');
 
 $class = $_REQUEST['class'];
 $entity = $_REQUEST['entity'];
 $kind = $_REQUEST['kind'];
 $field = $_REQUEST['field'];
 
 /////////////////////////////////////////////////////////////////////////////////////////////
 if(isset($class) && isset($kind) && isset($field))
 {
	if(isset($entity)) 
	{
		$object = getFactory()->getObject2($class, $entity );
	}
	else 
	{
		$object = getFactory()->getObject($class);
	}
	
	$selector = new $kind( $object, $field );
 }
