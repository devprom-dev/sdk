<?php

include_once "WebMethod.php";
include_once SERVER_ROOT_PATH."core/classes/model/persisters/ObjectAffectedDatePersister.php";

class ModifyAttributeWebMethod extends WebMethod
{
 	var $object_it, $attribute, $value, $callback;
 	
 	private $uid_service = null;
 	
 	private $method_url = '';
 	
 	private $method_script = '';
 	
 	private $project = '';
 	
 	function __construct( $object_it = null, $attribute = '', $value = '')
 	{
 		parent::WebMethod();
 		
 		$this->object_it = $object_it;
 		$this->attribute = $attribute;
 		$this->setValue($value);
 		$this->callback = "''";
 		$this->uid_service = new ObjectUID;
 		$this->method_url = '/'.getSession()->getSite().'/';
 		$this->project = getSession()->getProjectIt()->get('CodeName');
 		
 		$this->buildMethodScript();
 	}
 	
 	function getValue()
 	{
 		return $this->value;
 	}
 	
 	function setValue($value)
 	{
 		$this->value = $value;
 	}
 	
 	function setObjectIt($object_it)
 	{
 		$this->object_it = $object_it;
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
 		if ( getSession()->getSite() == 'pm' )
 		{ 
 			$project_code = is_object($this->object_it) ? $this->uid_service->getProject($this->object_it) : $this->project;
 		}
 		 
 		$method_url = $this->method_url.$project_code.'/methods.php?method='.get_class($this);
 		
 		$this->method_script = "javascript: runMethod('".$method_url."', {%data%}, ".$this->callback.", '".$this->getWarning()."');";
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
 		if ( !is_null($object_it) )
 		{
 			$this->object_it = $object_it;
 			
 			$this->buildMethodScript();
 		}
 		
		$parms = array_merge($parms, array( 
			'class' => strtolower(get_class($this->object_it->object)),
 			'attribute' => $this->attribute,
 			'object' => $this->object_it->getId(),
 			'value' => $this->value
 		));
 			
		$data = array();
		
		foreach ( $parms as $key => $value )
		{
			array_push( $data, "'".$key."' : '".$value."'" );	
		}
		
		return preg_replace('/%data%/', join(',', $data), $this->method_script);
 	}
 	
 	function execute_request()
 	{
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

		
		$object = getFactory()->getObject($_REQUEST['class']);
		$object_it = $object->getExact($_REQUEST['object']);

		if ( $object_it->getId() == '' )
		{
			echo '{"message":"object is undefined"}';
			return;
		}
			
 	 	if ( !getFactory()->getAccessPolicy()->can_modify_attribute($object, $_REQUEST['attribute']) )
		{
			echo '{"message":"lack of permissions"}';
			return;
		}
		
		if ( !getFactory()->getAccessPolicy()->can_modify($object_it) )
		{
			echo '{"message":"lack of permissions"}';
			return;
		}
		
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
		else
		{
			$registry = $object->getRegistry();
			$registry->setLimit(1);
			
			$filters = array (
					new FilterBaseVpdPredicate()
			);

			if ( $_REQUEST['type'] == 'inc' )
			{
				$neiburgh_it = $registry->Query(
						array_merge( 
								$filters, 
								array (
										new FilterNextSiblingsPredicate($object_it),
										new SortOrderedClause()
								)
						)
				);
				$parms['OrderNum'] = $neiburgh_it->get('OrderNum') > 0 
						? ($neiburgh_it->get('OrderNum') + 1) 
						: $object_it->get('OrderNum');
			}
			elseif ( $_REQUEST['type'] == 'dec' )
			{
				$neiburgh_it = $registry->Query(
						array_merge(
								$filters,
								array (
										new FilterPrevSiblingsPredicate($object_it),
										new SortRevOrderedClause()
								)
						)
				);
				$parms['OrderNum'] = $neiburgh_it->get('OrderNum') > 0 
						? max(1, $neiburgh_it->get('OrderNum') - 1) 
						: $object_it->get('OrderNum');
			}
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
		
		if ( is_array($user_parms) && array_key_exists('SkipEvents', $user_parms) ) {
			$object->setNotificationEnabled(false);
		}
		
		if ( $has_changes )	{
			$object->modify_parms($object_it->getId(), $parms);
		}

		$object->addPersister( new ObjectAffectedDatePersister() );
		$object_it = $object->getExact($object_it->getId());
		
		echo '{"message":"ok", "modified":"'.$object_it->get_native('AffectedDate').'"}';
 	}
}