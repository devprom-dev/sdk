<?php

include_once "CommentsFormMinimal.php";

class CommentsPage extends PMPage
{
	function getObject() 
	{
		global $model_factory;

		if ( $_REQUEST['class'] == '' || $_REQUEST['object'] == '' ) throw new Exception('Unknown object is given');
		
		$class_name = $model_factory->getClass($_REQUEST['class']);
		
		if ( !class_exists($class_name, false) ) throw new Exception('Class is undefined: '.$class_name);
		
		$target = $model_factory->getObject($class_name);
		
		$this->anchor_it = $target->getExact($_REQUEST['object']);
		
		if ( $this->anchor_it->getId() < 1 ) throw new Exception('There is no given object id: '.$_REQUEST['object']);
		
		$object = $model_factory->getObject('Comment');
		
		return $object;
	}

 	function getForm() 
 	{
 		$form = new CommentsFormMinimal( $this->getObject() );
 		
 		$form->setAnchorIt($this->anchor_it);
 		
 		return $form;
 	}
 	
	private $anchor_it;
}
