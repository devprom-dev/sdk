<?php

include_once "WebMethod.php";

class DeleteObjectWebMethod extends WebMethod
{
 	var $object_it;
 	
 	function __construct( $object_it = null )
 	{
 		$this->object_it = $object_it;
 		$this->setRedirectUrl('function(){devpromOpts.updateUI();}');
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
        $className = strtolower(get_class($this->object_it->object));
        if ( $className == 'metaobject' ) {
            $className = $this->object_it->object->getEntityRefName();
        }

		return parent::getJSCall( 
			array( 'object' => $this->object_it->getId(),
				   'class' => $className ));
	}

	function hasAccess()
	{
		return getFactory()->getAccessPolicy()->can_delete($this->object_it);
	}
	
	function execute_request()
	{
		if ( $_REQUEST['object'] == '' ) throw new Exception('Object should be specified');
		
		$class = getFactory()->getClass($_REQUEST['class']);
		if ( !class_exists($class) ) {
            $object = new \Metaobject($class);
        }
        else {
            $object = getFactory()->getObject($class);
        }
		
		$object_it = $object->getExact( $_REQUEST['object'] );
		
		if ( $object_it->getId() == '' ) throw new Exception('Object given was not found');
		
		if ( !getFactory()->getAccessPolicy()->can_delete($object_it) ) {
            throw new Exception('You have no permissions to delete object');
        }

		if ( $object_it->delete() < 1 ) throw new Exception('The object wasn\'t deleted');

		if ( class_exists('UndoWebMethod') && UndoLog::Instance()->valid($object_it) ) {
			$method = new UndoWebMethod(ChangeLog::getTransaction());
			$method->setCookie();
		}
	}
} 
