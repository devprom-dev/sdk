<?php

include_once "WebMethod.php";
include_once SERVER_ROOT_PATH."core/classes/model/persisters/ObjectAffectedDatePersister.php";

class ModifyAttributeWebMethod extends WebMethod
{
 	var $object_it, $attribute, $value, $callback;
 	
 	private $method_script = '';
 	
 	function __construct( $object_it = null, $attribute = '', $value = '')
 	{
 		parent::WebMethod();
 		
 		$this->object_it = $object_it;
 		$this->attribute = $attribute;
 		$this->value = $value;
 		$this->callback = "''";
 		
 		$this->buildMethodScript();
 	}
 	
 	function getValue()
 	{
 		return $this->value;
 	}
 	
 	function hasAccess()
 	{
 		return is_object($this->object_it) 
 			? getFactory()->getAccessPolicy()->can_modify_attribute($this->object_it->object, $this->attribute)
 			: true; 
 	}
 	
 	function setCallback( $callback )
 	{
 		$this->callback = $callback;
 		
 		$this->buildMethodScript();
 	}
 	
 	private function buildMethodScript()
 	{ 
 		$this->method_script = "javascript: runMethod('".$this->getModule().'?method='.get_class($this).
			"', {%data%}, ".$this->callback.", '".$this->getWarning()."');";
 	}
 	
 	function getRedirectUrl()
 	{
 		return $this->callback;
 	}
 	
 	function getCaption()
 	{
 		$object = $this->object_it->object->getAttributeObject($this->attribute);
 			
 		$object_it = $object->getExact($this->value);
 		
 		return $object->getDisplayName().': '.$object_it->getDisplayName();
 	}

 	function getJSCall( $parms = array(), $object_it = null )
 	{
 		if ( is_null($object_it) ) $object_it = $this->object_it;
 		
		$parms = array( 
			'class' => strtolower(get_class($object_it->object)),
 			'attribute' => $this->attribute,
 			'object' => $object_it->getId(),
 			'value' => $this->value
 			);
 			
		$data = array();
		
		foreach ( $parms as $key => $value )
		{
			array_push( $data, "'".$key."' : '".$value."'" );	
		}
		
		return preg_replace('/%data%/', join(',', $data), $this->method_script);
 	}
 	
 	function execute_request()
 	{
 		global $_REQUEST, $model_factory;

		if ( $_REQUEST['class'] == '' || $_REQUEST['attribute'] == '' )
		{
			echo '{"message":"denied"}';
			return;
		}
		
		if ( $_REQUEST['object'] == '' )
		{
			echo '{"message":"denied"}';
			return;
		}

		
		$object = $model_factory->getObject($_REQUEST['class']);
		$object_it = $object->getExact($_REQUEST['object']);

		if ( $object_it->count() > 0 )
		{
			if ( $_REQUEST['value'] != '' && $object->IsReference($_REQUEST['attribute']) )
			{
		 		$attr_object = $object->getAttributeObject($_REQUEST['attribute']);

		 		$attr_object_it = $attr_object->getExact(preg_split('/,/', $_REQUEST['value']));

				if ( $attr_object_it->count() < 1 )
				{
					echo '{"message":"denied"}';
					return;
				}
			}
			
			$user_parms = $_REQUEST['parms'];
			
			if ( is_array($user_parms) )
			{
				foreach( $user_parms as $key => $value )
				{
					$user_parms[$key] = IteratorBase::utf8towin($value);
				}
			}
			
			$parms = array (
				$_REQUEST['attribute'] => IteratorBase::utf8towin($_REQUEST['value'])
			);
			
			if ( !array_key_exists('OrderNum', $parms) )
			{
				$parms['OrderNum'] = $object_it->get('OrderNum');
			}
			
			$parms = is_array($user_parms) ? array_merge($user_parms, $parms) : $parms;
			
			// check if there are changes
			$has_changes = false;
			
			foreach( $parms as $key => $value )
			{
				if ( $object_it->get_native($key) == $value ) continue;
				
				$has_changes = true;
					
				break;
			}
			
			$skip_events = preg_split('/,/', $user_parms['SkipEvents']);
			
			foreach( $skip_events as $event )
			{
				getFactory()->getEventsManager()->removeNotificator($event);
			}
			
			if ( $has_changes ) $object_it->modify( $parms );

			$object->addPersister( new ObjectAffectedDatePersister() );
			
			$object_it = $object->getExact($object_it->getId());
			
			echo '{"message":"ok", "modified":"'.$object_it->get_native('AffectedDate').'"}';
		}
 	}
}