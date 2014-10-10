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
     
  	function PageForm( $object )
 	{
 		$this->model_validator = new ModelValidator();

 		parent::__construct( $object );

 		$this->setRedirectUrl( $this->buildRedirectUrl() );
 		
 		$this->system_attributes = $this->buildSystemAttributes();
 		
 		$this->buildRelatedDataCache();
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
 		
 		while( !$state_it->end() )
 		{
 			$transition_it = $state_it->getTransitionIt();
 			
 			$this->transitions_array[$state_it->get('VPD').'-'.$state_it->get('ReferenceName')] = $transition_it->copyAll();
 			
 			while( !$transition_it->end() )
 			{
 				$this->target_states_array[$transition_it->getId()] = $transition_it->getRef('TargetState')->copy();
 				
 				$transition_it->moveNext();
 			}
 			
 			$state_it->moveNext();
 		}

 	 	$predicate_it = getFactory()->getObject('pm_TransitionPredicate')->getRegistry()->Query(
 				array (
 						new FilterAttributePredicate('Transition', array_keys($this->target_states_array))
 				)
 		);
 		
 		$rule = getFactory()->getObject('StateBusinessRule');
 		
 		while ( !$predicate_it->end() )
 		{
 			$this->transition_rules_it[$predicate_it->get('Transition')][] = 
 					$predicate_it->getRef('Predicate', $rule)->copy();
	 		
 			$predicate_it->moveNext();
 		}
 		
 	}
 	
 	function getTransitionIt()
 	{
 		if ( is_object($this->transition_it) ) return $this->transition_it; 
 		
 		if ( $_REQUEST['Transition'] != '' )
 		{
 			foreach( $this->transitions_array as $transition_it )
 			{
 				$transition_it->moveToId($_REQUEST['Transition']);
 				
 				if ( $transition_it->getId() != '' )
 				{
 					return $this->transition_it = $transition_it->copy();
 				}
 				
 				$transition_it->moveFirst();
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
				return htmlentities($_REQUEST[$field], ENT_QUOTES | ENT_HTML401, 'windows-1251');
			
			default:
				return parent::getFieldValue( $field );
		}
	}
	
  	function getCaption() 
 	{
		return $this->object->getDisplayName();
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
			// object reference
    		$object = clone($this->object->getAttributeObject( $name ));
    				
    		if( !is_object($object) ) return null;
    				
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
		}
		
		$transition_actions = $this->getTransitionActions($object_it);

		if ( count($transition_actions) > 0 )
		{
			$actions[] = array();
			$actions = array_merge($actions, $transition_actions);
		}

		$plugins = getSession()->getPluginsManager();
		
		$plugins_interceptors = is_object($plugins) ? $plugins->getPluginsForSection($this->getSite()) : array();
		
		foreach( $plugins_interceptors as $plugin )
		{
			$plugin->interceptMethodFormGetActions( $this, $actions );
		}
		
		return $actions;
	}

 	function getTransitionActions( $object_it )
	{
		$actions = array();
		
		$transition_it = $this->transitions_array[$object_it->get('VPD').'-'.$object_it->get('State')];
		
		if ( !is_object($transition_it) ) return $actions;		
		
		$transition_it->moveFirst();
		
		while ( !$transition_it->end() )
		{
			$rules = $this->transition_rules_it[$transition_it->getId()];
			
			if ( is_array($rules) )
			{
				$skip_transition = false;
				
				foreach( $rules as $rule_it )
				{
					if ( !$rule_it->check($object_it) )
					{
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
			else
			{
				if ( !$method->hasAccess() )
				{
					$transition_it->moveNext();
					
					continue;
				}
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

			ob_start();
	
			if ( $uid->hasUid( $object_it ) )
			{
			    $uid->drawUidIcon( $object_it ); 
			}
			else
			{
			    echo '<a href="'.$object_it->getViewUrl().'">'.
			        $object_it->object->getDisplayName().': '.$object_it->getDisplayName().'</a>'; 
			}
			
			$object_uid_icon = ob_get_contents();

			ob_end_clean();
			
			if ( $_REQUEST['Transition'] > 0 )
    		{
    			$transition = $model_factory->getObject('pm_Transition');
    			
    			$transition->setVpdContext( $object_it );
    			
    			$transition_it = $transition->getExact($_REQUEST['Transition']);
    			
    			$object_uid_icon = $transition_it->getDisplayName().': '.$object_uid_icon;
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
			'button_save_title' => translate('Сохранить')
		);
	}
	
	function getTemplate()
	{
		return "core/PageForm.php";
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
	
	function render( &$view, $parms )
	{
		echo $view->render( $this->getTemplate(), array_merge($parms, $this->getRenderParms()) ); 
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
