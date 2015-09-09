<?php

include_once SERVER_ROOT_PATH.'core/classes/model/validation/ModelValidator.php';
include_once SERVER_ROOT_PATH."core/classes/model/validation/ModelValidatorEmbeddedForm.php";
include_once SERVER_ROOT_PATH.'core/classes/model/mappers/ModelDataTypeMapper.php';

include SERVER_ROOT_PATH.'cms/c_metaobject_view.php';
include_once SERVER_ROOT_PATH.'cms/views/FieldDictionary.php';
include_once SERVER_ROOT_PATH.'cms/views/FieldAutoCompleteObject.php';

class PageForm extends MetaObjectForm
{

    var $page;

    private $model_validator = null;
	private $redirect_url = '';
	private $system_attributes = array();
	private $transitions_array = array();
	private $target_states_array = array();
	private $transition_it = null;
	private $transition_rules_it = null;
	private $transition_appliable = array();
	private $transition_messages = array();
     
  	function PageForm( $object )
 	{
 		parent::__construct( $object );

 		$this->setRedirectUrl( $this->buildRedirectUrl() );
 		
 		$this->system_attributes = $this->buildSystemAttributes();
 		
 		$this->buildRelatedDataCache();
 	}
 	
 	function __destruct()
 	{
 		$this->page = null;
 	}
 	
 	function buildModelValidator()
 	{
 		$validator = new ModelValidator();
 		
		$type_validation_attrs = array();
		
		// build specific field validators
		foreach( $this->getObject()->getAttributes() as $attribute => $data )
		{
			$field = $this->createFieldObject($attribute);
			
			if ( is_null($field) )
			{
				if ( !$this->getObject()->IsAttributeStored($attribute) ) continue;
				$type_validation_attrs[] = $attribute;
				continue;
			}
			
			$field_validator = $field->getValidator();
			
			if ( $field_validator instanceof ModelValidatorType )
			{ 
				if ( !$this->getObject()->IsAttributeStored($attribute) ) continue;
				
				$type_validation_attrs[] = $attribute;
			}
			else
			{
				$validator->addValidator($field_validator);
			}
		}

		// basic (type based) validation used for hidden fields and simple types (int, string, etc.) 
		if ( count($type_validation_attrs) > 0 )
		{
			$validator->addValidator(new ModelValidatorObligatory($type_validation_attrs));
			$validator->addValidator(new ModelValidatorTypes($type_validation_attrs));
		}

 		return $validator;
 	}
 	
 	function buildSystemAttributes()
 	{
 		$attributes = $this->getObject()->getAttributesByGroup('system');
 		
		unset( $attributes[array_search('OrderNum', $attributes)] );
		
		return $attributes;
 	}
 	
 	function buildRelatedDataCache()
 	{
 		if ( !$this->getObject() instanceof MetaobjectStatable ) return;
 		
 		$state_it = $this->getObject()->cacheStates();
        $target_it = $state_it->copyAll();

        $transition_it = getFactory()->getObject('Transition')->getRegistry()->Query(
            array (
                new FilterAttributePredicate('SourceState', $state_it->idsToArray()),
                new TransitionSourceStateSort()
            )
        );
        $transition_it->buildPositionHash(array('SourceState'));
        $transition_it->object->setStateAttributeType( $state_it->object );

 		while( !$state_it->end() )
 		{
 			$tmp_it = $transition_it->object->createCachedIterator($transition_it->getSubset('SourceState', $state_it->getId()));
 			$this->transitions_array[$state_it->get('VPD').'-'.$state_it->get('ReferenceName')] = $tmp_it;

 			while( !$tmp_it->end() )
 			{
                $target_it->moveToId($tmp_it->get('TargetState'));
 				$this->target_states_array[$tmp_it->getId()] = $target_it->copy();
 				$this->transition_appliable[$tmp_it->getId()] = $tmp_it->appliable();
                $tmp_it->moveNext();
 			}
 			$state_it->moveNext();
 		}

 		if ( count($this->target_states_array) > 0 )
 		{
	 		$rule = getFactory()->getObject('StateBusinessRule');
 			$predicate_it = getFactory()->getObject('pm_TransitionPredicate')->getRegistry()->Query(
 					array (
 							new FilterAttributePredicate('Transition', array_keys($this->target_states_array))
 					)
	 		);
	 		while ( !$predicate_it->end() )
 			{
 				$rule_it = $predicate_it->getRef('Predicate', $rule)->copy();
 				$this->transition_rules_it[$predicate_it->get('Transition')][] = $rule_it;
	 			$predicate_it->moveNext();
 			}
 		}
 	}
 	
 	function getTransitionIt()
 	{
 		if ( is_object($this->transition_it) ) return $this->transition_it; 
 		if ( $_REQUEST['Transition'] != '' )
 		{
			$object_it = $this->getObjectIt();
			$transition_it = $this->transitions_array[$object_it->get('VPD').'-'.$object_it->get('State')];

			$transition_it->moveToId($_REQUEST['Transition']);
			if ( $transition_it->getId() != '' ) {
				return $this->transition_it = $transition_it->copy();
			}
 		}
 		return $this->transition_it = getFactory()->getObject('pm_Transition')->getEmptyIterator();
 	}
 	
 	function setPage( $page )
 	{
 	    $this->page = $page;
 	}
 	
 	function getPage()
 	{
 	    return $this->page;
 	}
 	
 	function getFormPage()
 	{
		global $_SERVER;

		$parts = preg_split('/\&/', $_SERVER['QUERY_STRING']);
		
		foreach ( array_keys($parts) as $key )
		{ 
			if ( strpos($parts[$key], 'project=') !== false )
			{
				unset($parts[$key]);
			}

			if ( strpos($parts[$key], 'namespace=') !== false )
			{
				unset($parts[$key]);
			}

			if ( strpos($parts[$key], 'module=') !== false )
			{
				unset($parts[$key]);
			}
		}
		
		return $this->getObject()->getPage().join($parts, '&');
 	}

 	public function setRedirectUrl( $url )
 	{
 		$this->url = $url;
 	}
 	
 	public function getRedirectUrl()
 	{
 		return $this->url;
 	}
 	
	function buildRedirectUrl()
	{
		if( $_REQUEST['redirect'] != '' ) 
		{
			if ( preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $_REQUEST['redirect']) )
			{
				$url = base64_decode($_REQUEST['redirect']);
			}
			else
			{
				$url = $_REQUEST['redirect'];
			}
		}
		else
		{
			// does a browser support referer
			$url = $_SERVER['HTTP_REFERER'] != '' 
				? $_SERVER['HTTP_REFERER'] : $_SERVER['REDIRECT_URL'].'?'.$_SERVER['REDIRECT_QUERY_STRING'];
			
			if ( $url != '' && strpos($url, 'entity='.$this->object->getEntityRefName()) > 0 )
			{
				$url = '';
			}
		}
		
		if ( $url == '' && is_object($this->object_it) )
		{
			$url = $this->object_it->getViewUrl();
		}
		
		return SanitizeUrl::parseSystemUrl($url);
	}
	
	function getModelValidator()
	{
		if ( !is_object($this->model_validator) )
		{
			$this->model_validator = $this->buildModelValidator();
		}
		
		return $this->model_validator;
	}
 	
	function IsNeedButtonNew() 
	{
		return false;
	}

	function IsNeedButtonCopy() 
	{
		return false;
	}
	
	function IsAttributeVisible( $attr_name )
	{
	    // hide system attributes
		if ( in_array($key, $this->system_attributes) ) return false;
	    
	    return parent::IsAttributeVisible( $attr_name );
	}
	
	function getFieldValue( $field )
	{
		global $_REQUEST;
		
		switch ( $field )
		{
			case 'TransitionComment':
			case 'Transition':
				return htmlentities($_REQUEST[$field], ENT_QUOTES | ENT_HTML401, APP_ENCODING);
			
			default:
				return parent::getFieldValue( $field );
		}
	}
	
  	function getCaption() 
 	{
		return translate($this->object->getDisplayName());
	}

	function createFieldObject( $name ) 
	{
		$plugins = getSession()->getPluginsManager();
		$plugins_interceptors = is_object($plugins) ? $plugins->getPluginsForSection($this->getSite()) : array();
   	    foreach( $plugins_interceptors as $plugin )
        {
        	$field = $plugin->interceptMethodFormCreateFieldObject( $this, $name );
        	if ( is_object($field) ) return $field;
		}
    		    
		if( $this->object->IsReference( $name ) ) 
		{
    		$object = $this->object->getAttributeObject($name);
    		if ( is_object($this->getObjectIt()) && $object->getVpdValue() != '' )
    		{
    			$object->setVpdContext($this->getObjectIt());
    		}
    				
    		return $object->entity->get('IsDictionary') == 'Y' 
    			? new FieldDictionary( $object ) : new FieldAutoCompleteObject( $object );
    	}
		
		return parent::createFieldObject( $name );
	}
	
	function IsFormDisplayed()
	{
		return $_REQUEST[$this->getObject()->getEntityRefName().'action'] != '' ;
	}
	
	function getActions()
	{
		global $model_factory;
		
		$actions = array();

		$object_it = $this->getObjectIt();
		
		if( getFactory()->getAccessPolicy()->can_modify($object_it) )
		{
			$method = new ObjectModifyWebMethod($object_it);
			$method->setRedirectUrl('donothing');
			
			$actions[] = array(
					'name' => translate('Изменить'),
					'url' => $this->IsFormDisplayed() ? $object_it->getEditUrl() : '#', 
					'click' => $this->IsFormDisplayed() ? '' : $method->getJSCall() 
			);

			$transition_actions = $this->getTransitionActions();
			if ( count($transition_actions) > 0 )
			{
				$actions[] = array();
				$actions = array_merge($actions, $transition_actions);
			}
		}
		
		$plugins = getSession()->getPluginsManager();
		$plugins_interceptors = is_object($plugins) ? $plugins->getPluginsForSection($this->getSite()) : array();
		
		foreach( $plugins_interceptors as $plugin )
		{
			$plugin->interceptMethodFormGetActions( $this, $actions );
		}
				
		return $actions;
	}

 	function getTransitionActions()
	{
		$actions = array();
		$object_it = $this->getObjectIt();

		$transition_it = $this->transitions_array[$object_it->get('VPD').'-'.$object_it->get('State')];
		
		if ( !is_object($transition_it) ) $transition_it = array_shift(array_values($this->transitions_array));		
		if ( !is_object($transition_it) ) return $actions;
		
		$transition_it->moveFirst();
		
		while ( !$transition_it->end() )
		{
			if ( !$this->transition_appliable[$transition_it->getId()] )
			{
				$transition_it->moveNext();
				continue;
			}
			
			$rules = $this->transition_rules_it[$transition_it->getId()];
			if ( is_array($rules) )
			{
				$skip_transition = false;
				
				foreach( $rules as $rule_it )
				{
					if ( !$rule_it->check($object_it) )
					{
						$reason = $rule_it->getNegativeReason();
						if ( $reason != '' ) $this->transition_messages[] = $reason;
						
						$skip_transition = true;
						break;
					}
				}
				
				if ( $skip_transition )
				{
					$transition_it->moveNext();
					continue;
				}
			}
			
			$method = new TransitionStateMethod( $transition_it, $object_it );
			$method->setTargetStateRefName($this->target_states_array[$transition_it->getId()]->get('ReferenceName'));
			
			if ( !$this->IsFormDisplayed() )
			{
				$method->setRedirectUrl('donothing');
			}

			$actions[] = array ( 
					'name' => $method->getCaption(), 
					'url' => $method->getJSCall(),
					'title' => $method->getDescription()
			);
			
			$transition_it->moveNext();
		}	

		return $actions;
	}
	
	function getDeleteActions()
	{
	    $actions = array();
	    
	    $object_it = $this->getObjectIt();
	    
		if ( is_object($object_it) && !$this->getEditMode() )
		{
			$method = new DeleteObjectWebMethod($object_it);
			if ( $method->hasAccess() )
			{
				if ( !$this->IsFormDisplayed() ) $method->setRedirectUrl('donothing');
				
			    $actions[] = array(
				    'name' => $method->getCaption(), 'url' => $method->getJSCall() 
			    );
			}
		}
		
		return $actions;
	}
	
	function checkAccess()
	{
		global $_REQUEST, $model_factory;
		
		if ( $_REQUEST['Transition'] > 0 )
		{
			$transition = $model_factory->getObject('pm_Transition');
			$transition->setVpdContext( $this->getObjectIt() );
			
			$transition_it = $transition->getExact($_REQUEST['Transition']);

	 		$object_it = $this->getObjectIt();
	 		if ( is_object($object_it) )
	 		{
	 			$state_it = $object_it->getStateIt();
	 			$access = $state_it->getId() == $transition_it->get('SourceState');
	 			
	 			if ( !$access )
	 			{
	 				$this->setCheckAccessMessage( text(984) );
	 			}
	 			
	 			return $access;
	 		}
		}
		
		return parent::checkAccess();
	}
	
	function drawButtons()
	{
	    if ( !$this->getEditMode() ) return;
	    
	    parent::drawButtons();
	}
	
	function getRenderParms()
	{
		global $_REQUEST, $model_factory;
		
		$object_it = $this->getObjectIt();
		
		$attributes = array();
		$scripts = '';
		$index = 1;
		
		foreach( $this->object->getAttributesSorted() as $key => $attribute )
		{
		    $visible = $this->IsAttributeVisible($key);

		    if ( !$visible && !$this->object->IsAttributeStored($key) ) continue;
		    
			$attributes[$key] = array (
				'visible' => $visible,
				'required' => $this->IsAttributeRequired($key),
				'name' => translate($this->object->getAttributeUserName($key)),
				'description' => $this->getFieldDescription($key),
				'type' => $this->object->getAttributeType($key),
			    'value' => $this->getFieldValue($key)
			);
			
			$field = $this->createField( $key );
			
			if ( !is_object($field) ) continue;
			
			$attributes[$key]['text'] = $field->getText();
			
			if ( !$visible ) continue;
		    			
			$field->setTabIndex( $index++ );

		    $field->setRequired( $attributes[$key]['required'] );
		    
 			ob_start();
 			$field->drawScripts();
 				
 			$scripts .= ob_get_contents();
 			ob_end_clean();
 			
 			$attributes[$key]['class'] = strtolower(get_class($field));
			$attributes[$key]['value'] = $field->getValue();
			$attributes[$key]['id'] = $field->getId();
			$attributes[$key]['field'] = $field;
		}

		if ( $_REQUEST['formonly'] != '' )
		{
			$actions = array();
		}
		else
		{
			$actions = is_object($this->getObjectIt()) ? $this->getActions() : array();
			
			$delete_actions = $this->getDeleteActions();
			
			if ( count($delete_actions) > 0 )
			{
			    if ( $actions[array_pop(array_keys($actions))]['name'] != '' ) $actions[] = array();
			    
			    $actions = array_merge($actions, $delete_actions);
			}
		}  
		
		if ( is_object($object_it) )
		{
			$uid = new ObjectUid;
			if ( $uid->hasUid( $object_it ) )
			{
				$info = $uid->getUidInfo($object_it);
			    $object_uid_icon = $uid->getUidIconGlobal($object_it);
			    $uid_number = $info['uid'];
			    $uid_url = $info['url'];
			}
			else
			{
			    $object_uid_icon = $object_it->object->getDisplayName().': '.$object_it->getDisplayName();
			    $uid_url = $object_it->getViewUrl();
			}
			if ( $this->getTransitionIt()->getId() != '' )
    		{
    			$object_uid_icon = $this->getTransitionIt()->getDisplayName().': '.$object_uid_icon;
    		}
		}
		
		ob_start();
		$this->drawScripts();
			
		$scripts .= ob_get_contents();
		ob_end_clean();
		
		return array(
			'form' => $this,
			'caption' => $this->getCaption(),
			'warning' => $this->hasAlert() ? translate('Внимание!').' '.$this->getWarningMessage() : '',
			'form_processor_url' => $this->getFormPage(),
			'form_id' => $this->getId(),
			'action_mode' => $this->object->getEntityRefName().'action_mode',
			'entity' => ($_REQUEST['entity'] != '' ? htmlentities($_REQUEST['entity']) : $this->object->getEntityRefName()),
			'record_version' => $this->getFieldValue('RecordVersion'),
			'class_name' => $this->object->getEntityRefName(),
			'object_id' => is_object($object_it) ? $object_it->getId() : '',
		 	'redirect_url' => $this->getRedirectUrl(),
			'attributes' => $attributes,
			'action' => $this->getAction(),
			'actions' => $actions,
			'uid_icon' => $object_uid_icon,
		    'scripts' => $scripts,
		    'draw_sections' => true,
		    'form_body_template' => $this->getBodyTemplate(),
		    'title' => $this->getPageTitle(),
			'button_save_title' => translate('Сохранить'),
			'transition' => $this->getTransitionIt()->getId(),
			'form_class_name' => strtolower(get_class($this)),
			'bottom_hint' => getFactory()->getObject('UserSettings')->getSettingsValue($this->getId()) != 'off' ? $this->getHint() : '',
			'alert' => join('<br/>',$this->transition_messages),
			'uid' => $uid_number,
			'uid_url' => $uid_url
		);
	}
	
	function getTemplate()
	{
		return $_REQUEST['formonly'] ? "core/PageFormDialog.php" : "core/PageForm.php";
	}
	
	function getBodyTemplate()
	{
	    return "core/PageFormBody.php";
	}
	
	function getPageTitle()
	{
	    $object_it = $this->getObjectIt();
	    
	    if ( !is_object($object_it) )
	    {
	    	return $this->getCaption();
	    }
	    
		$uid = new ObjectUID;
		
		$uid_info = $uid->getUidInfo($object_it);
		
	    return $uid_info['uid'] != '' 
	        ? $uid_info['uid'].' '.$uid_info['caption']
	        : $object_it->object->getDisplayName().': '.$object_it->getDisplayName();
	}
	
	function render( $view, $parms )
	{
		$render_parms = $this->getRenderParms();

		echo $view->render( $this->getTemplate(), array_merge($parms, $render_parms) ); 
	}
	
	function validateInputValues( $id, $action )
	{
		$message = $this->getModelValidator()->validate( $this->getObject(), $_REQUEST );
		
		if ( $message != '' ) return $message;
		
		return '';
	}
			
 	function getSite()
	{
		return 'co';
	}
	
 	function getHint()
	{
		$resource = getFactory()->getObject('ContextResource');
		
		$resource_it = $resource->getExact(strtolower(get_class($this)));
		if ( $resource_it->getId() != '' ) return $resource_it->get('Caption');
		
		$resource_it = $resource->getExact(strtolower(get_class($this)).'-'.$this->getMode());
		if ( $resource_it->getId() != '' ) return $resource_it->get('Caption');
		
		return '';
	}
	
	function drawScripts()
	{
	    $plugins = getSession()->getPluginsManager();
	    
	    $plugins_interceptors = is_object($plugins) ? $plugins->getPluginsForSection($this->getSite()) : array();
	
	    foreach( $plugins_interceptors as $plugin )
	    {
	        $result = $plugin->interceptMethodFormDrawScripts( $this );
	        	
	        if ( is_bool($result) ) return;
	    }
	}
}
