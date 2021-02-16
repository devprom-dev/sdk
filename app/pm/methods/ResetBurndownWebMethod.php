<?php

class ResetBurndownWebMethod extends WebMethod
{
 	function getCaption()
 	{
 		return text(939);
 	}
 	
 	function url( $object_it )
 	{
 		return parent::getJSCall(
 			array ( 'class' => $object_it->object->getClassName(),
 					'object' => $object_it->getId() )
 		);
 	}
 	
	function hasAccess()
	{
		$project_roles = getSession()->getRoles();
		
		return $project_roles['lead'] && getSession()->getProjectIt()->getMethodologyIt()->HasVelocity();
	}

	function execute_request()
	{
		if ( $_REQUEST['object'] != '' && $_REQUEST['class'] != '' ) {
			$this->execute( $_REQUEST['class'], $_REQUEST['object'] );
		}
	}
	
	function execute( $class, $id )
	{
		global $model_factory;
		
		$object = $model_factory->getObject($class);
		$object_it = $object->getExact( $id );
		
		switch ( $object->getClassName() )
		{
			case 'pm_Version':
				$object_it->resetBurndown();
				break;
			
			case 'pm_Release':
				$object_it->resetBurndown();
				break;
		}
	}
}
