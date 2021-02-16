<?php

define ('CO_ACTION_CREATE', 1);
define ('CO_ACTION_MODIFY', 2);
define ('CO_ACTION_DELETE', 3);
define ('CO_ACTION_PREVIEW', 4);
 
class AjaxForm
{
 	var $object;
 	var $action;
 	var $object_it;
 	var $view;
 	private $page = null;
 	
 	private $redirect_url = '';
 	private $form_id = '';
 	
 	/*
 	 * initializes form members
 	 */
 	function AjaxForm ( $object )
 	{
 		$this->form_id = uniqid();
 		if ( is_subclass_of($object, 'StoredObjectDB') )
 		{
 			$this->object = $object;
 			$this->action = CO_ACTION_CREATE;
 		}
 		elseif ( is_subclass_of($object, 'IteratorBase') )
 		{
 			$this->object_it = $object;
 			$this->object = $this->object_it->object;
 			$this->action = CO_ACTION_MODIFY;
 		}
 		else
 		{
 			$this->action = CO_ACTION_CREATE;
 		} 			
 		
 		$this->setRedirectUrl( SanitizeUrl::parseSystemUrl($_REQUEST['redirect']) );
 	}
 	
 	function getId()
 	{
 		return $this->form_id;
 	}
 	
 	/*
 	 * returns object iterator
 	 */
 	function getObjectIt()
 	{
 		return $this->object_it;
 	}
 	
 	function getObject()
 	{
 		return $this->object;
 	}

 	function setObject( $object ) {
 	    $this->object = $object;
    }

    function setPage( $page ) {
 	    $this->page = $page;
    }

    function getPage() {
 	    return $this->page;
    }

 	function getView()
 	{
 	    return $this->view;
 	}
 	
 	/*
 	 * returns a caption of the form when add new record 
 	 */
 	function getAddCaption() 
 	{
 		return '';
 	}

 	/*
 	 * returns a caption of the form when modify existing record 
 	 */
 	function getModifyCaption() 
 	{
 		return '';
 	}

	/*
	 * returns a command class, which implements form actions
	 */
	function getCommandClass()
	{
		return ''; 	
	}
	
	function getCaption()
	{
		if ( isset($this->object_it) )
		{
			return $this->getModifyCaption();
		}
		else
		{
			return $this->getAddCaption();
		}
	}
		
	function getAction()
	{
		if ( isset($this->object_it) )
		{
			return CO_ACTION_MODIFY;
		}
		else
		{
			return CO_ACTION_CREATE;
		}
	}
	
	function getClass()
	{
		return 'ajax_form';
	}
	
	/*
	 * returns the list of form attributes
	 */
	function getAttributes()
	{
		$result = array();
		$attributes = $this->object->getAttributes();
		
		foreach( $attributes as $key => $attribute )
		{
			if ( $key == 'OrderNum' ) continue;
			if ( $key == 'RecordCreated' ) continue;
			if ( $key == 'RecordModified' ) continue;
				
			array_push( $result, $key );
		}
		
		return $result;
	}
	
	/*
	 * returns name of an attribute
	 */
	function getName( $attribute )
	{
		switch ( $attribute )
		{
			default:
				return translate($this->object->getAttributeUserName( $attribute ));
		}
	}

	/*
	 * returns name of an attribute
	 */
	function IsAttributeRequired( $attribute )
	{
		switch ( $attribute )
		{
			default:
				return $this->object->isAttributeRequired( $attribute );
		}
	}

	/*
	 * returns visibility of an attribute
	 */
	function IsAttributeVisible( $attribute )
	{
		switch ( $attribute )
		{
			default:
				return $this->object->IsAttributeVisible($attribute);
		}
	}

	/*
	 * returns amount of visible attributes
	 */
	function getVisibleAttributesCount()
	{
		$attributes = $this->getAttributes();
		$count = 0;
		
		for ( $i = 0; $i < count($attributes); $i++ )
		{
			if ( $this->IsAttributeVisible($attributes[$i]) )
			{
				$count++;
			}
		}
		
		return $count;
	}
	
	/*
	 * returns visibility of an attribute
	 */
	function IsAttributeModifiable( $attribute )
	{
		return getFactory()->getAccessPolicy()->can_modify_attribute($this->getObject(), $attribute)
            && $this->getObject()->getAttributeEditable($attribute);
	}

	/*
	 * returns description of an attribute
	 */
	function getDescription( $attribute )
	{
		switch ( $attribute )
		{
			default:
				return $this->object->getAttributeDescription($attribute);
		}
	}
	
	/*
	 * returns visual type of attribute
	 */
	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			default:
				$type = $this->object->getAttributeVisualType( $attribute );
				
				if ( strpos( $type, 'ref_') !== false )
				{
					return 'object';
				}
				else
				{
					return $this->object->getAttributeVisualType( $attribute );
				}
		}
	}

	/*
	 * returns class of an attribute
	 */
	function getAttributeClass( $attribute )
	{
		return $this->object->getAttributeObject( $attribute );
	}

	function getAttributeValue( $attribute )
	{
		if ( isset($this->object_it) )
		{
			$value = $this->object_it->get_native($attribute);
		}
		else
		{
			$value = $this->object->getDefaultAttributeValue($attribute);
		}

		return $value;		
	}
	
	function getAttributeDefault( $attribute )
	{
		$value = $this->object->getDefaultAttributeValue($attribute);
		 
		if ( $value == '' ) return $_REQUEST[$attribute];
	
		return $value;
	}
	
	/*
	 * returns text on the main button
	 */
	function getButtonText()
	{
		if ( isset($this->object_it) )
		{
			return translate('Сохранить');
		}
		else
		{
			return translate('Создать');
		}
	}
	
	/*
	 * returns an url browser should redirect to after the form is submitted
	 */
	function getRedirectUrl()
	{
		return $this->redirect_url;
	}
	
	function setRedirectUrl( $url )
	{
		$this->redirect_url = htmlentities($url); 
	}
	
	function getWidth()
	{
		return '70%';
	}
	
	function isCentered()
	{
		return true;
	}
	
	function isBoldFieldNames()
	{
		return false;
	}

	function IsPreviewEnabled()
	{
		return false;
	}
	
 	/*
	 * draws attribute according to its type
	 */
	function drawAttribute( $attribute, $view )
	{
		global $tab_index;

		$tab_index++;

		$value = $this->getAttributeValue($attribute);
		$default = $this->getAttributeDefault($attribute);
		$attribute_type = $this->getAttributeType( $attribute );
		
		if ( !$this->IsAttributeVisible($attribute) ) {
		    if ( $value != '' ) {
                echo '<input type="hidden" id="'.$attribute.'" name="'.$attribute.'" value="'.$value.'">';
            }
			return;
		}

		if ( $attribute_type == 'object' || $attribute_type == 'dictionary' )
		{
			$object = $this->getAttributeClass( $attribute );
			
			if ( $value != '' )
			{
				$object_it = $object->getExact($value);
				$displayValue = $object_it->getDisplayName();
			} 
		}
		else
		{
			$displayValue = $value; 
		}

		if ( !$this->IsAttributeModifiable($attribute) )
		{
			echo '<input type="hidden" id="'.htmlentities($attribute).'" name="'.htmlentities($attribute).'" value="'.htmlentities($value).'">';
		    echo '<span class="input-block-level well well-text" style="word-break: break-all;padding: 4px 6px;margin-bottom: 10px;">'.$displayValue.'</span>';
		}
		else
		{
			switch ( $attribute_type )
			{
				case 'hugetext':
					?>
					<textarea class="input-block-level" id="<? echo $attribute; ?>" name="<? echo $attribute; ?>"
							  style="overflow-x:hidden;overflow-y: scroll;"
							  tabindex="<? echo $tab_index ?>"
							  wrap="virtual" rows="34" placeholder="<?=htmlentities($default,ENT_QUOTES | ENT_HTML401, APP_ENCODING)?>"><? echo $value ?></textarea>
					<?
					break;

				case 'largetext':
					?>
					<textarea class="input-block-level" id="<? echo $attribute; ?>" name="<? echo $attribute; ?>"
						style="overflow-x:hidden;overflow-y: scroll;" 
						tabindex="<? echo $tab_index ?>" 
						wrap="virtual" rows="6" placeholder="<?=htmlentities($default,ENT_QUOTES | ENT_HTML401, APP_ENCODING)?>"><? echo $value ?></textarea>
					<?
					break;
					
				case 'text':
					if ( in_array($this->getObject()->getAttributeDbType( $attribute ), array('LARGETEXT', 'RICHTEXT')) )
					{
						?>
						<textarea class="input-block-level" id="<? echo $attribute; ?>" name="<? echo $attribute; ?>"
							style="overflow-x:hidden;overflow-y: scroll;" 
							tabindex="<? echo $tab_index ?>" 
							wrap="virtual" rows="6" placeholder="<?=htmlentities($default,ENT_QUOTES | ENT_HTML401, APP_ENCODING)?>"><? echo $value ?></textarea>
						<?
					}
					else
					{
						?>
						<textarea class="input-block-level" id="<? echo $attribute; ?>" name="<? echo $attribute; ?>"
							style="overflow-x:hidden;overflow-y: scroll;" 
							tabindex="<? echo $tab_index ?>" 
							wrap="virtual" rows="1" placeholder="<?=htmlentities($default,ENT_QUOTES | ENT_HTML401, APP_ENCODING)?>"><? echo $value ?></textarea>
						<?
					}
					break;
					
				case 'dictionary':
					$field = new FieldDictionary( $object );
					
					$field->SetName($attribute);
					$field->SetValue($value);
					$field->SetId($attribute);
					$field->SetTabIndex($tab_index);
					$field->draw();

					break;	
					
				case 'object':
					if ( $object->IsDictionary() )
					{
						$field = new FieldDictionary( $object );
					}
					else
					{
						$field = new FieldAutoCompleteObject( $object );
						$field->setDefault($default);
					}

					$field->SetTabindex($tab_index); 
					$field->SetName($attribute);
					$field->SetValue($value);
					$field->SetId($attribute);
					$field->draw();

					break;							

				case 'file':
					?>
					<span>
					<input class="input-block-level" type="file" id="<? echo $attribute; ?>" name="<? echo $attribute; ?>" value="<? echo $value ?>" tabindex="<? echo $tab_index ?>" placeholder="<?=htmlentities($default,ENT_QUOTES | ENT_HTML401, APP_ENCODING)?>">
					</span>
					<?
					break;							

				default:
				    $field = $this->createFieldObject($attribute_type, $attribute);
				    if ( $field ) {
                        $field->SetTabindex($tab_index);
                        $field->SetName($attribute);
                        $field->SetValue($value);
                        $field->SetId($attribute);
                        $field->draw();
                    }
				    else {
                        $this->drawCustomAttribute( $attribute, $value, $tab_index, $view );
                    }
			}
		}
	}

	function createFieldObject( $attribute_type, $name )
    {
        switch ( $attribute_type )
        {
            case 'password':
                return new FieldPassword();
            case 'char':
                return new FieldCheck($this->getName($name));
        }
    }
	
	function drawCustomAttribute( $attribute, $value, $tab_index, $view )
	{
        switch ( $this->getObject()->getAttributeType($attribute) )
        {
            case 'float':
            case 'number':
            case 'integer':
                $width = 'width:170px';

            default:
                echo '<div style="'.$width.'">';
                ?>
                <input class="input-block-level" type="text" id="<? echo $attribute; ?>" name="<? echo $attribute; ?>" value="<? echo $value ?>" tabindex="<? echo $tab_index ?>">
                <?
                echo '</div>';
        }

	}
		
	function getSite()
	{
		return 'co';
	}
	
	/*
	 * draws style section of the form
	 */
	function drawStyle()
	{
	}
	
	function getFormUrl()
	{
	    return getSession()->getApplicationUrl().'command.php?class='.$this->getCommandClass();
	}

	function extendModel()
    {
    }

	function getRenderParms( $view )
	{
	    $this->extendModel();
		$object_it = $this->getObjectIt();
		
		$attributes = array();
		
		$index = 1;
		
		foreach( $this->getAttributes() as $attribute )
		{
            if ( in_array('system', $this->getObject()->getAttributeGroups($attribute)) ) continue;

            ob_start();
            $this->drawAttribute( $attribute, $view );
            $html = ob_get_contents();
            ob_end_clean();

            if ( $html != '' ) {
                $type = $this->getAttributeType( $attribute );
                $attributes[$attribute] = array (
                    'type' => $type,
                    'caption' => $type == 'char' ? '' : $this->getName( $attribute ),
                    'description' => $this->getDescription( $attribute ),
                    'value' => $this->getAttributeValue( $attribute  ),
                    'index' => $index,
                    'visible' => $this->IsAttributeVisible($attribute),
                    'id' => $attribute,
                    'html' => $html
                );
            }

            $index++;
		}
		
		$actions = $this->getActions();
		
		$plugins = getFactory()->getPluginsManager();
		
		$plugins_interceptors = is_object($plugins) ? $plugins->getPluginsForSection($this->getSite()) : array();
		
		foreach( $plugins_interceptors as $plugin )
		{
			$plugin->interceptMethodFormGetActions( $this, $actions );
		}

		return array(
			'form_style_class' => $this->getClass(),
			'form_processor_url' => $this->getFormUrl(),
			'form_action' => $this->getAction(),
			'url' => SanitizeUrl::parseUrl($this->getRedirectUrl()),
			'form' => $this,
			'object_id' => is_object($object_it) ? $object_it->getId() : '',
			'width' => $this->getWidth(),
			'button_text' => $this->getButtonText(),
			'b_has_preview' => $this->IsPreviewEnabled(),
            'columns' => $this->buildColumns($attributes),
            'buttons_template' => 'core/FormAsyncButtons.php',
            'buttons_parms' => array( 'actions' => $actions ),
            'form_id' => $this->form_id,
            'redirect_url' => $this->getRedirectUrl(),
			'form_url' => htmlentities($_SERVER['REQUEST_URI']), 
            'actions' => $actions,
            'form_title' => $this->getCaption(),
			'fields_separator' => '<br/>',
			'bottom_hint' => $this->getHint(),
			'bottom_hint_id' => $this->getHintId(),
			'hint_open' => getFactory()->getObject('UserSettings')->getSettingsValue($this->getHintId()) != 'off',
            'module' => strtolower(get_class($this)),
            'actions_on_top' => true
		);
	}
	
	function getTemplate()
	{
		return "core/FormAsync.php";
	}
	
	function render( $view, $parms )
	{
	    $this->view = $view;

		echo $view->render( $this->getTemplate(), array_merge($parms, array( 
                'parms' => $this->getRenderParms($view)
        ))); 

		unset($this->view);
		$this->view = null;
	}
	
	function getActions()
	{
	    $actions = array();
	
	    return $actions;
	}

	function buildColumns( $attributes )
    {
        return array( $attributes );
    }

	function getHintId()
	{
		return '';
	}

	function getHint()
	{
		return '';
	}
}
