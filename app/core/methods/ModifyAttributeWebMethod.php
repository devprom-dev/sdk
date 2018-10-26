<?php
include_once "WebMethod.php";
include_once SERVER_ROOT_PATH.'core/classes/model/validation/ModelValidator.php';
include_once SERVER_ROOT_PATH."core/classes/model/persisters/ObjectAffectedDatePersister.php";
include_once SERVER_ROOT_PATH."pm/classes/workflow/WorkflowStateAttributesModelBuilder.php";

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
		$this->buildMethodScript();
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
 		if ( getSession()->getSite() == 'pm' ) {
 			$project_code = is_object($this->object_it) ? $this->object_it->get('ProjectCodeName') : $this->project;
 		}
 		 
 		$method_url = $this->method_url.$project_code.'/methods.php?method='.get_class($this);
 		
 		$this->method_script = "javascript: runMethod('".$method_url."', %data%, ".$this->callback.", '".$this->getWarning()."');";
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

 	function getJSCall( $parms = array() )
 	{
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
		
		return preg_replace('/%data%/', preg_replace('/"/', "'", JsonWrapper::encode($parms)), $this->method_script);
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
		if ( $object instanceof WikiPage ) {
			$object->setRegistry( new WikiPageRegistryContent() );
		}
		$object_it = $object->getExact($_REQUEST['object']);

		if ( $object_it->getId() == '' ) {
			echo '{"message":"denied","description":"object is undefined"}';
			return;
		}
 	 	if ( !getFactory()->getAccessPolicy()->can_modify_attribute($object, $_REQUEST['attribute']) ) {
			echo '{"message":"denied","description":"'.text(1062).'"}';
			return;
		}
		if ( !getFactory()->getAccessPolicy()->can_modify($object_it) ) {
			echo '{"message":"denied","description":"'.text(1062).'"}';
			return;
		}
		
		if ( $_REQUEST['value'] != '' && $object->IsReference($_REQUEST['attribute']) )
		{
	 		$attr_object = $object->getAttributeObject($_REQUEST['attribute']);
	 		$attr_object_it = $attr_object->getExact(preg_split('/,/', $_REQUEST['value']));
			if ( $attr_object_it->count() < 1 ) {
				echo '{"message":"denied","description":"object is undefined"}';
				return;
			}
		}
		
		$user_parms = $_REQUEST['parms'];
		$parms = array (
			$_REQUEST['attribute'] => $_REQUEST['value']
		);

		if ( !array_key_exists('OrderNum', $parms) ) {
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

        $mapper = new ModelDataTypeMapper();
        $mapper->map( $object, $parms );

        if ( $object instanceof MetaobjectStatable and $object_it->getStateIt()->getId() != '' ) {
            $model_builder = new WorkflowStateAttributesModelBuilder(
                $object_it->getStateIt(), array_keys($parms)
            );
            $model_builder->build($object);
        }

        $validator = new ModelValidator();
        $validator->addValidator(new ModelValidatorObligatory());
        $validationResult = $validator->validate($object, $parms);
        if ( $validationResult != "" ) {
            echo JsonWrapper::encode(
                array(
                    'message' => 'denied',
                    'description' => $validationResult
                )
            );
            return;
        }

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