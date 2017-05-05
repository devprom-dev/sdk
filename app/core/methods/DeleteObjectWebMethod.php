<?php

include_once "WebMethod.php";

class DeleteObjectWebMethod extends WebMethod
{
 	var $object_it;
 	
 	function __construct( $object_it = null )
 	{
 		$this->object_it = $object_it;
 		
 		parent::WebMethod();
 	}
 	
 	function getCaption() 
	{
		return translate('Удалить');
	}
	
	function getWarning()
	{
		if ( class_exists('UndoWebMethod') && UndoLog::Instance()->valid($this->object_it) ) return "";
		return text(636);
	}
	
	function getJSCall($parms = array())
	{
		return parent::getJSCall( 
			array( 'object' => $this->object_it->getId(),
				   'class' => strtolower(get_class($this->object_it->object))));
	}

	function hasAccess()
	{
		return getFactory()->getAccessPolicy()->can_delete($this->object_it);
	}
	
	function execute_request()
	{
		if ( $_REQUEST['object'] == '' ) throw new Exception('Object should be specified');
		
		$class = getFactory()->getClass($_REQUEST['class']);
		
		if ( !class_exists($class) ) throw new Exception('Class name given doesn\'t exist');
		
		$object_it = getFactory()->getObject($class)->getExact( $_REQUEST['object'] );
		
		if ( $object_it->getId() == '' ) throw new Exception('Object given was not found');
		
		if ( !getFactory()->getAccessPolicy()->can_delete($object_it) ) throw new Exception('You have no permissions to delete object');

		if ( $object_it->delete() < 1 ) throw new Exception('The object wasn\'t deleted');

		if ( class_exists('UndoWebMethod') ) {
			$method = new UndoWebMethod(ChangeLog::getTransaction());
			$method->setCookie();
		}
	}
} 
