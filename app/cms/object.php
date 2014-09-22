<?php

 include('common.php');
 include('design.php');
 include('c_package_view.php');
 include('c_selector.php');
 include('c_entity_view.php');
 include('c_attribute_view.php');
 
 $model_factory =& getModelFactory();
   $model_factory->enableVpd(false);
 
 $entity = $_REQUEST['entity'];
 $class = $_REQUEST['class'];
 $aggregateentity = $_REQUEST['aggregateentity'];
 $aggregate_id = $_REQUEST[$aggregateentity.'Id'];
 
 if ( preg_match('/^[a-zA-Z0-9\_]+$/im', $class) < 1 )
 {
 	unset($class);
 }

 require_once('c_'.strtolower($class).'.php');

 if(isset($aggregateentity)) 
 {
 	require_once('c_'.strtolower($aggregateentity).'.php');
	
 	$aggregate = $model_factory->getObject($aggregateentity);
	
	$container_it = $aggregate->getExact($aggregate_id);
	$object = $model_factory->getObject2($class, $container_it);
 }
 elseif (isset($entity)) {
 	$object = $model_factory->getObject2($class, $entity);
 }
 else {
 	$object = $model_factory->getObject($class);
 }
 $view = $object->createDefaultView();
 
// $form = $object->createForm();
// $list = $object->createListForm();
 
 beginPage($view->getCaption());
 
 $view->draw();
 
 endPage();
 
?>
