<?php
include_once SERVER_ROOT_PATH."core/classes/model/validation/ModelValidatorEmbeddedForm.php";
include SERVER_ROOT_PATH.'cms/c_metaobject_view.php';
include_once SERVER_ROOT_PATH.'cms/views/FieldDictionary.php';
include_once SERVER_ROOT_PATH.'cms/views/FieldAutoCompleteObject.php';
include "FieldListOfReferences.php";

class PageForm extends MetaObjectForm
{
    var $page;
	private $system_attributes = array();
	private $transitions_array = array();
	private $target_states_array = array();
	private $transition_it = null;
	private $transition_appliable = array();
	private $transition_messages = array();
	private $plugins = array();
	private $workflowParms = array();
     
  	function PageForm( $object )
 	{
 		parent::__construct( $object );

 		$this->setRedirectUrl( $this->buildRedirectUrl() );
 		
 		$this->system_attributes = $this->buildSystemAttributes();

		$plugins = getFactory()->getPluginsManager();
		$this->plugins = is_object($plugins)
			? $plugins->getPluginsForSection(getSession()->getSite()) : array();

 		$this->buildRelatedDataCache();
 	}
 	
 	function __destruct()
 	{
 		$this->page = null;
 	}

    function buildForm()
    {
        $this->extendModel();
    }

    protected function extendModel()
    {
    }

    function setObjectIt( $object_it )
    {
        $this->transition_it = null;
        parent::setObjectIt($object_it);
    }

 	function getValidators()
 	{
 		$validators = array();
		$type_validation_attrs = array();
		
		// build specific field validators
		foreach( $this->getObject()->getAttributes() as $attribute => $data )
		{
		    if ( $attribute == 'TransitionComment' ) {
                $validators[] = new ModelValidatorObligatory(array('TransitionComment'));
                continue;
            }

			$field = $this->createField($attribute);
			if ( is_null($field) ) {
				if ( !$this->getObject()->IsAttributePersisted($attribute) ) continue;
				$type_validation_attrs[] = $attribute;
				continue;
			}

            $field->setName($attribute);
			$field_validator = $field->getValidator();

			if ( $field_validator instanceof ModelValidatorType ) {
				if ( !$this->getObject()->IsAttributePersisted($attribute) ) continue;
				$type_validation_attrs[] = $attribute;
			}
			else {
                $validators[] = $field_validator;
			}
		}

		// basic (type based) validation used for hidden fields and simple types (int, string, etc.)
		if ( count($type_validation_attrs) > 0 ) {
            $validators[] = new ModelValidatorObligatory($type_validation_attrs);
            $validators[] = new ModelValidatorTypes($type_validation_attrs);
		}

        return $validators;
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
 		if ( !getFactory()->getAccessPolicy()->can_modify_attribute($this->getObject(), 'State') ) return;

 		$state_it = WorkflowScheme::Instance()->getStateIt($this->getObject());
		$transition_it = WorkflowScheme::Instance()->getTransitionIt($this->getObject());

 		while( !$transition_it->end() )
 		{
			$state_key = $transition_it->get('VPD').'-'.$transition_it->get('SourceStateReferenceName');
 			$this->transitions_array[$state_key][] = $transition_it->getData();

			$state_it->moveToId($transition_it->get('TargetState'));
			$this->target_states_array[$transition_it->getId()] = $state_it->copy();
			$this->transition_appliable[$transition_it->getId()] = $transition_it->appliable();

			$transition_it->moveNext();
 		}
		foreach( $this->transitions_array as $key => $data ) {
			$this->transitions_array[$key] = $transition_it->object->createCachedIterator($data);
		}
 	}
 	
 	function getTransitionIt()
 	{
 		if ( is_object($this->transition_it) ) return $this->transition_it; 
 		if ( $_REQUEST['Transition'] != '' ) {
			return $this->transition_it = getFactory()->getObject('pm_Transition')->getExact($_REQUEST['Transition']);
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

	function setWorkflowParameters( $parms ) {
		$this->workflowParms = $parms;
	}
 	
 	function getFormPage() {
		return $this->getObject()->getPage().trim(\SanitizeUrl::getSelfUrl(), '?');
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
	
	function IsNeedButtonNew()
	{
		return false;
	}

	function IsNeedButtonCopy() 
	{
		return false;
	}
	
	function getFieldValue( $field )
	{
		switch ( $field )
		{
			case 'Transition':
			case 'TransitionComment':
				return htmlentities($_REQUEST[$field], ENT_QUOTES | ENT_HTML401, APP_ENCODING);
			
			default:
				return parent::getFieldValue( $field );
		}
	}
	
  	function getCaption() 
 	{
		return translate($this->object->getDisplayName());
	}

    function createField( $name )
    {
        $field = null;
        foreach( $this->plugins as $plugin ) {
            $field = $plugin->interceptMethodFormCreateFieldObject( $this, $name );
            if ( is_object($field) ) break;
        }
        if ( !is_object($field) ) {
            $field = $this->createFieldObject( $name );
        }
        if( !is_object($field) ) return null;

        if ( $field instanceof FieldDateTime || $field instanceof FieldDate ) {
            $field->setId($this->object->getEntityRefName().$name.$this->getId());
        }
        else {
            $field->setId($this->object->getEntityRefName().$name);
        }
        $field->setReadOnly( !$this->IsAttributeEditable($name) );
        $field->setEditMode( $this->getEditMode() );
        $field->setName($name);

        if ( $this->getEditMode() ) {
            $field->setDefault($this->getDefaultValue($name));
        }

        if ( is_a($field, 'FieldFile') ) {
            $field->setValue( $this->getFieldValue($name.'Ext') );
        }
        else {
            $field->setValue($this->getFieldValue($name));
        }

        return $field;
    }

	function createFieldObject( $name )
	{
	    $attributeGroups = $this->getObject()->getAttributeGroups($name);

		if( $this->object->IsReference( $name ) )
		{
    		$object = $this->object->getAttributeObject($name);

    		if ( is_object($object->entity) && $object->IsDictionary() ) {
                if ($this->getAction() == 'view') {
                    return new FieldReferenceAttribute(
                        $this->getObjectIt(),
                        $name,
                        $this->getObject()->getAttributeObject($name),
                        array(),
                        'btn-xs'
                    );
                } else {
                    return new FieldDictionary( $object );
                }
            }

    		if ( in_array('multiselect', $attributeGroups) ) {
                $field = new FieldDictionary( $object, $name );
                $field->setMultiple(true);
                $field->setAttributes($attributeGroups);
                return $field;
            }

            $field = new FieldAutoCompleteObject( $object );
    		if ( count($object->getAttributesByGroup('alternative-key')) > 0 ) {
                $field->setAppendable();
            }
    		return $field;
    	}

        if ( in_array('positive-negative', $attributeGroups) ) {
            return new FieldHoursPositiveNegative();
        }

    	if ( in_array('hours', $attributeGroups) ) {
            return new FieldHours();
        }

        if ( in_array('astronomic-time', $attributeGroups) ) {
            return new FieldHours(FieldHours::HOURS_CALENDAR);
        }

        if ( in_array('working-time', $attributeGroups) ) {
            return new FieldHours(FieldHours::HOURS_WORKING);
        }
		return parent::createFieldObject( $name );
	}
	
	function IsFormDisplayed()
	{
		return $_REQUEST[$this->getObject()->getEntityRefName().'action'] != '' ;
	}

	function setFormDisplayed()
	{
		$_REQUEST[$this->getObject()->getEntityRefName().'action'] = $this->action;
	}

	function IsAttributeEditable($attr_name)
    {
        if ( !$this->getEditMode() && defined('WYSIWYG_EDITABLE') ) {
            return WYSIWYG_EDITABLE && parent::IsAttributeEditable($attr_name);
        }
        return parent::IsAttributeEditable($attr_name);
    }

    function IsAttributeVisible($attr_name)
    {
        if ( !$this->getEditMode() ) {
            if ( !$this->IsAttributeEditable($attr_name) ) {
                return $this->getFieldValue($attr_name) != '' && parent::IsAttributeVisible($attr_name);
            }
        }
        return parent::IsAttributeVisible($attr_name);
    }

    function getModifyActions( $objectIt )
    {
        $method = new ObjectModifyWebMethod($objectIt);
        return array(
            'modify' => array(
                'name' => $method->getCaption(),
                'url' => $this->IsFormDisplayed() ? $method->getJSCall() : '#',
                'click' => $this->IsFormDisplayed() ? '' : $method->getJSCall(),
                'uid' => 'modify',
                'view' => 'button',
                'button-class' => 'btn-info',
                'icon' => 'icon-pencil'
            )
        );
    }

    function getActions()
	{
		$actions = array();

		$object_it = $this->getObjectIt();

		$modifyActions = $this->getModifyActions($object_it);
		if ( count($modifyActions) > 0 ) {
            $actions += $modifyActions;
        }

		if( getFactory()->getAccessPolicy()->can_modify_attribute($object_it->object, 'State') )
		{
			$transition_actions = $this->getTransitionActions();
			if ( count($transition_actions) > 6 && !$this->IsFormDisplayed() )
			{
				$actions[] = array();
				$actions['workflow'] = array (
					'name' => translate('Состояние'),
					'items' => $transition_actions,
                    'uid' => 'workflow'
				);
			}
			else if ( count($transition_actions) > 0 )
			{
				$actions[] = array();
				$actions = array_merge($actions, $transition_actions);
			}
		}

		$add_actions = $this->getMoreActions();
		if ( count($add_actions) > 0 ) {
			$actions[] = array('uid' => 'middle');
			$actions = array_merge($actions, $add_actions);
		}

		$more_actions['create'] = array (
			'name' => translate('Создать'),
			'items' => $this->getNewRelatedActions(),
			'uid' => 'create'
		);

		$plugin_actions = array();
		foreach( $this->plugins as $plugin ) {
			$plugin_actions = array_merge($plugin_actions, $plugin->getObjectActions( $object_it ));
		}
		if ( count($plugin_actions) > 0 ) {
			$more_actions = array_merge( $more_actions, array(array()), $plugin_actions );
		}

		foreach( $this->plugins as $plugin ) {
			$plugin->interceptMethodFormGetActions( $this, $more_actions );
		}
		if ( count($more_actions) > 1 || count($more_actions['create']['items']) > 0 ) {
			$actions = array_merge($actions, array(array()), $more_actions);
		}

        $export_actions = $this->getExportActions($object_it);
        if ( count($export_actions) > 1 ) {
            $actions[] = array();
            $actions[] = array(
                'name' => translate('Экспорт'),
                'items' => $export_actions,
                'uid' => 'export'
            );
        }

        return $actions;
	}

	function getExportActions( $object_it )
    {
        return array();
    }

 	function getTransitionActions()
	{
		$actions = array();
		$object_it = $this->getObjectIt();

		if ( $object_it->get('State') == '' ) {
			$transition_it = array_shift(array_values($this->transitions_array));
		}
		else {
			$transition_it = $this->transitions_array[$object_it->get('VPD').'-'.$object_it->get('State')];
		}
		if ( !is_object($transition_it) ) return $actions;

		$transition_it->moveFirst();
		while ( !$transition_it->end() )
		{
			if ( !$this->transition_appliable[$transition_it->getId()] ) {
				$transition_it->moveNext();
				continue;
			}

			$skip_transition = !$transition_it->doable( $object_it,
                WorkflowScheme::Instance()->getTransitionPredicateIt($transition_it)
            );
			if ( $skip_transition ) {
			    $reason = $transition_it->getNonDoableReason();
                if ( $reason != '' ) {
                    $this->transition_messages[] = $reason;
                }
				$transition_it->moveNext();
				continue;
			}

			$method = new TransitionStateMethod( $transition_it, $object_it );
			$target_state = $this->target_states_array[$transition_it->getId()]->get('ReferenceName');
			$method->setTargetStateRefName($target_state);

			$actions[] = array ( 
					'name' => $method->getCaption(), 
					'url' => $method->getJSCall($this->workflowParms),
					'title' => $method->getDescription(),
					'uid' => 'workflow-'.$target_state,
					'view' => 'button',
					'button-class' => 'btn-warning'
			);
			
			$transition_it->moveNext();
		}	

		return $actions;
	}

	function getMoreActions()
	{
		return array();
	}

	function getNewRelatedActions()
	{
		return array();
	}

	function getDeleteActions( $objectIt )
	{
	    $actions = array();
	    
		if ( is_object($objectIt) && !$this->getEditMode() ) {
			$method = new DeleteObjectWebMethod($objectIt);
			if ( $method->hasAccess() ) {
			    $actions[] = array(
				    'name' => $method->getCaption(),
                    'url' => $method->getJSCall(),
                    'uid' => 'row-delete'
			    );
			}
		}
		
		return $actions;
	}
	
	function drawButtons()
	{
	    if ( !$this->getEditMode() ) return;
	    
	    parent::drawButtons();
	}
	
	function getRenderParms()
	{
		$object_it = $this->getObjectIt();
		$uid = new ObjectUid;

		$attributes = array();
		$system = $this->getObject()->getAttributesByGroup('system');
		$scripts = '';

		foreach( $this->object->getAttributes() as $key => $attribute )
		{
		    if ( in_array($key, $system) ) continue;

		    $visible = $this->IsAttributeVisible($key);

		    if ( !$this->object->IsAttributeStored($key) && $this->object->getAttributeOrigin($key) != ORIGIN_CUSTOM  ) {
		        if ( $this->object->IsReference($key) && !$visible ) continue;
            }
		    if ( $this->object->IsReference($key) ) {
		        if ( !getFactory()->getAccessPolicy()->can_read($this->object->getAttributeObject($key)) ) continue;
            }
		    if ( $this->object->getAttributeType($key) == 'password' && !$this->getEditMode() ) continue;

			$attributes[$key] = array (
				'visible' => $visible,
				'required' => $this->IsAttributeRequired($key),
                'editable' => $this->IsAttributeEditable($key),
				'custom' => $this->object->getAttributeOrigin($key) == ORIGIN_CUSTOM,
				'name' => $this->getFieldName($key),
				'description' => $this->getFieldDescription($key),
				'type' => $this->object->getAttributeType($key),
			    'value' => $this->getFieldValue($key),
                'groups' => $this->object->getAttributeGroups($key),
                'editmode' => $this->getEditMode()
			);
			
			$field = $this->createField( $key );
			if ( !is_object($field) ) continue;

			if ( $field instanceof FieldAutoCompleteObject && $this->getEditMode() )
			{
				$ref_it = $field->getObjectIt();
				if ( $ref_it->getId() != '' && $uid->hasUid($ref_it) ) {
					$info = $uid->getUidInfo($ref_it);
					if ( $info['url'] != '' ) {
						$attributes[$key]['description'] =
							'<a class="dashed" target="_blank" href="'.$info['url'].'">'.text(2084).'</a> &nbsp; &nbsp; '.$attributes[$key]['description'];
					}
				}
			}

			if ( $field instanceof FieldEditable ) {
			}
            else {
                $attributes[$key]['text'] = $field->getText();
			}

			if ( !$visible && $this->getEditMode() && $field instanceof FieldDictionary ) {
				if ( $field->getObject()->hasAttribute('ReferenceName') && $field->getValue() != '' ) {
					$attributes[$key]['referenceName'] =
						$field->getObject()->getRegistry()->Query(
							array (
								new FilterInPredicate($field->getValue())
							)
 						)->get('ReferenceName');
				}
			}

			if ( !$visible ) continue;
		    			
		    $field->setRequired( $attributes[$key]['required'] );
		    
 			ob_start();
 			$field->drawScripts();
 				
 			$scripts .= ob_get_contents();
 			ob_end_clean();
 			
 			$attributes[$key]['class'] = strtolower(get_class($field));
			$attributes[$key]['value'] = $field->getValue();
			$attributes[$key]['id'] = $field->getId();
            $attributes[$key]['attribute'] = $key;
            $attributes[$key]['field'] = $field;

            if ( is_object($this->getObjectIt()) ) {
                $method = new ModifyAttributeWebMethod($this->getObjectIt(), $key);
                $attributes[$key]['url'] = $method->getUrl();
            }
		}

		if ( $_REQUEST['formonly'] != '' )
		{
			$actions = array();
		}
		else
		{
			$actions = is_object($this->getObjectIt()) ? $this->getActions() : array();
			
			$delete_actions = $this->getDeleteActions($this->getObjectIt());
			
			if ( count($delete_actions) > 0 )
			{
			    if ( $actions[array_pop(array_keys($actions))]['name'] != '' ) $actions[] = array();
			    
			    $actions = array_merge($actions, $delete_actions);
			}
		}  
		
		if ( is_object($object_it) )
		{
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

		$form_groups = array();
		foreach ($this->getPage()->getInfoSections() as $section) {
		    if ( $section instanceof PageSectionAttributes ) {
		        foreach( $section->getReferenceName() as $referenceName ) {
                    $form_groups[$referenceName] = $section->getCaption();
                }
            }
        }

		return array(
			'form' => $this,
			'caption' => $this->getCaption(),
			'warning' => $this->hasAlert() ? translate('Внимание!').' '.$this->getWarningMessage() : $this->getWarningMessage(),
			'form_processor_url' => $this->getFormPage(),
			'form_id' => $this->getId(),
			'action_mode' => $this->object->getEntityRefName().'action_mode',
			'entity' => $this->object->getEntityRefName(),
            'className' => get_class($this->object),
			'record_version' => defined('SKIP_TARGET_BLANK') && SKIP_TARGET_BLANK ? '' : $this->getFieldValue('RecordVersion'),
			'object_id' => is_object($object_it) ? $object_it->getId() : '',
		 	'redirect_url' => $this->getRedirectUrl(),
			'attributes' => $attributes,
			'action' => $this->getAction(),
			'actions' => $actions,
			'uid_icon' => $object_uid_icon,
		    'scripts' => $scripts,
		    'form_body_template' => $this->getBodyTemplate(),
		    'title' => $this->getPageTitle(),
			'button_save_title' => translate('Сохранить'),
			'transition' => $this->getTransitionIt()->getId(),
			'form_class_name' => strtolower(get_class($this)),
			'bottom_hint' => $this->getHint(),
			'bottom_hint_id' => $this->getHintId(),
            'hint_open' => getFactory()->getObject('UserSettings')->getSettingsValue($this->getHintId()) != 'off',
			'alert' => join('<br/>',array_unique($this->transition_messages)),
			'uid' => $uid_number,
			'uid_url' => $uid_url,
			'source_parms' => $this->getSourceParms($attributes),
			'form_class' => 'delete-confirm',
            'showtabs' => true,
            'form_groups' => $form_groups
		);
	}
	
	function getTemplate() {
        if ( $_REQUEST['attributesonly'] != '' ) return "core/PageFormDetails.php";
  	    if ( $_REQUEST['formonly'] != '' ) return "core/PageFormDialog.php";
		return "core/PageForm.php";
	}
	
	function getBodyTemplate() {
	    return $_REQUEST['formonly'] != ''
            ? "core/PageFormBody.php" : "core/PageFormBodyView.php";
	}
	
	function getPageTitle()
	{
	    $object_it = $this->getObjectIt();
	    
	    if ( !is_object($object_it) )
	    {
	    	return $this->getCaption();
	    }
	    
		$uid = new ObjectUID;
		
		$uid_info = $uid->getUidInfo($object_it,true);
		
	    return $uid_info['uid'] != '' 
	        ? $uid_info['uid'].' '.$uid_info['caption']
	        : $object_it->object->getDisplayName().': '.$object_it->getDisplayName();
	}
	
	function render( $view, $parms )
	{
		$render_parms = $this->getRenderParms();

		if ( is_array($parms['sections']) )	{
		    $wasAttributes = array();
			foreach ( $parms['sections'] as $section ) {
				if ( $section instanceof PageSectionAttributes ) {
					$section->setObject($this->getObject());
					$attributes = $section->getAttributes($wasAttributes);
					foreach( $attributes as $key => $attribute ) {
						if ( !$this->IsAttributeVisible($attribute) ) unset($attributes[$key]);
					}
                    $wasAttributes = array_merge($wasAttributes, $attributes);
					if ( count($attributes) < 1 ) {
						unset($parms['sections'][$section->getId()]);
					}
				}
			}
		}

		echo $view->render( $this->getTemplate(), array_merge($parms, $render_parms) ); 
	}
	
	function validateInputValues( $id, $action )
	{
		//skip values user can't modify
		$parms = $_REQUEST;
        $object_it = $this->getObjectIt();
        $object = $this->getObject();

		foreach( $object->getAttributes() as $attribute => $info ) {
			if ( !$this->IsAttributeEditable($attribute) ) {
			    if ( is_object($object_it) ) {
                    $parms[$attribute] = $object_it->getHtmlDecoded($attribute);
                }
                elseif ( $parms[$attribute] != '' ) {
			        $default = $object->getDefaultAttributeValue($attribute);
			        if ( $default != '' ) {
                        $parms[$attribute] = $default;
                    }
                    else {
			            unset($parms[$attribute]);
                    }
                }
			}
            $groups = $object->getAttributeGroups($attribute);
            if ( in_array('multiselect', $groups) ) {
                if ( !array_key_exists($attribute, $parms) ) {
                    $parms[$attribute] = array();
                }
            }
        }

		$_REQUEST = array_merge($_REQUEST, $parms);

		return '';
	}

 	function getSite()
	{
		return 'co';
	}

	function getHintId() {
		return get_class($this);
	}

 	function getHint()
	{
		$resource = getFactory()->getObject('ContextResource');

		$resource_it = $resource->getExact(strtolower(get_class($this)));
		if ( $resource_it->getId() != '' ) return $resource_it->get('Caption');

		$resource_it = $resource->getExact(strtolower(get_class($this)).'-'.$this->getMode());
		if ( $resource_it->getId() != '' ) return $resource_it->get('Caption');

		if ( $this->getObject() instanceof MetaobjectStatable and $this->getObject()->getStateClassName() != '' ) {
            $resource_it = $resource->getExact('requestform');
            if ( $resource_it->getId() != '' ) return $resource_it->get('Caption');
        }

		return '';
	}

	function drawScripts()
	{
	}

	protected function getSourceParms($attributes)
	{
		$uid = new ObjectUid();
        $parms = array();

        foreach( $this->getSourceIt() as $item ) {
            $source_it = array_shift($item);
            $text_attribute = array_shift($item);

            if ( is_subclass_of($text_attribute, 'IteratorExport') ) {
                ob_start();
                $iteratorObject = new $text_attribute($source_it->copyAll());
                $iteratorObject->export();
                $text = '<div class="reset wysiwyg">'.ob_get_contents().'</div>';
                ob_end_clean();
            }
            else {
                $field = new FieldWYSIWYG(
                    $source_it->get('ContentEditor') != ''
                        ? $source_it->get('ContentEditor')
                        : getSession()->getProjectIt()->get('WikiEditorClass')
                );
                $field->setValue($source_it->get($text_attribute));
                $field->setObjectIt($source_it);
                $field->setReadOnly(true);
                $text = '<div class="reset wysiwyg">'.$field->getText().'</div>';
                if ( $text != '' ) {
                    $text = '<br/>'.$text;
                }
            }

            if ( $source_it->getId() != '' ) {
                $parms[] = array (
                    'uid' => $uid->getUidWithCaption($source_it),
                    'text' => $text
                );
            }
        }
        return $parms;
	}

	protected function getSourceIt()
	{
		return array();
	}
}
