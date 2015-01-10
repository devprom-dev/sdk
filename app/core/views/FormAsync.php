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
 	
 	private $redirect_url = '';
 	
 	/*
 	 * initializes form members
 	 */
 	function AjaxForm ( $object )
 	{
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
		$attributes = $this->object->getAttributesSorted();
		
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
	function IsAttributeModifable( $attribute )
	{
		return getFactory()->getAccessPolicy()->can_modify_attribute($this->getObject(), $attribute);
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
			return translate('���������');
		}
		else
		{
			return translate('�������');
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
	function drawAttribute( $attribute )
	{
		global $tab_index;
		
		$tab_index++;

		$value = $this->getAttributeValue($attribute);
		$default = $this->getAttributeDefault($attribute);

		$attribute_type = $this->getAttributeType( $attribute );
		
		if ( !$this->IsAttributeVisible($attribute) )
		{
			echo '<input type="hidden" id="'.$attribute.'" name="'.$attribute.'" value="'.$value.'">';
			
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

		if ( !$this->IsAttributeModifable($attribute) )
		{
			echo '<input type="hidden" id="'.$attribute.'" name="'.$attribute.'" value="'.$value.'">';
		    echo '<input class="input-block-level" type="text" value="'.$displayValue.'" readonly>';
		}
		else
		{
			switch ( $attribute_type )
			{
				case 'largetext':
					?>
					<textarea class="input-block-level" id="<? echo $attribute; ?>" name="<? echo $attribute; ?>"
						style="overflow-x:hidden;overflow-y: scroll;" 
						tabindex="<? echo $tab_index ?>" 
						wrap="virtual" rows="6" placeholder="<?=htmlentities($default,ENT_QUOTES | ENT_HTML401, 'windows-1251')?>"><? echo $value ?></textarea>
					<?
					break;
					
				case 'text':
					if ( in_array($this->getObject()->getAttributeDbType( $attribute ), array('LARGETEXT', 'RICHTEXT')) )
					{
						?>
						<textarea class="input-block-level" id="<? echo $attribute; ?>" name="<? echo $attribute; ?>"
							style="overflow-x:hidden;overflow-y: scroll;" 
							tabindex="<? echo $tab_index ?>" 
							wrap="virtual" rows="6" placeholder="<?=htmlentities($default,ENT_QUOTES | ENT_HTML401, 'windows-1251')?>"><? echo $value ?></textarea>
						<?
					}
					else
					{
						?>
						<textarea class="input-block-level" id="<? echo $attribute; ?>" name="<? echo $attribute; ?>"
							style="overflow-x:hidden;overflow-y: scroll;" 
							tabindex="<? echo $tab_index ?>" 
							wrap="virtual" rows="1" placeholder="<?=htmlentities($default,ENT_QUOTES | ENT_HTML401, 'windows-1251')?>"><? echo $value ?></textarea>
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
					if ( $object->entity->get('IsDictionary') == 'Y' )
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
					<input class="input-block-level" type="file" id="<? echo $attribute; ?>" name="<? echo $attribute; ?>" value="<? echo $value ?>" tabindex="<? echo $tab_index ?>" placeholder="<?=htmlentities($default,ENT_QUOTES | ENT_HTML401, 'windows-1251')?>">
					</span>
					<?
					break;							

				case 'password':
					?>
					<input class="input-block-level" type="password" id="<? echo $attribute; ?>" name="<? echo $attribute; ?>" value="<? echo $value ?>" tabindex="<? echo $tab_index ?>" placeholder="<?=htmlentities($default,ENT_QUOTES | ENT_HTML401, 'windows-1251')?>">
					<?
					break;				

				case 'char':
					
					$field = new FieldCheck($value);

					$field->SetTabindex($tab_index); 
					$field->SetName($attribute);
					$field->SetValue($value);
					$field->SetId($attribute);
					$field->draw();
					
					break;

				default:
					$this->drawCustomAttribute( $attribute, $value, $tab_index );
			}
		}
	}	
	
	function drawCustomAttribute( $attribute, $value, $tab_index )
	{
	    $type = $this->getAttributeType( $attribute );
	
	    switch ( $type )
	    {
	        case 'float':
	        case 'number':
	        case 'integer':
	
	            $width = 'width:170px';
	
	            break;
	
	        default:
	
	            $width = '';
	    }
	
	    echo '<div style="'.$width.'">';
	    ?>
		<input class="input-block-level" type="text" id="<? echo $attribute; ?>" name="<? echo $attribute; ?>" value="<? echo $value ?>" tabindex="<? echo $tab_index ?>">
		<?
		echo '</div>';							
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
	
	function getRenderParms()
	{
		$object_it = $this->getObjectIt();
		
		$attributes = array();
		
		$index = 1;
		
		foreach( $this->getAttributes() as $attribute )
		{
			$attributes[$attribute] = array (
				'type' => $this->getAttributeType( $attribute ),
				'caption' => $this->getName( $attribute ),
				'description' => $this->getDescription( $attribute ),
                'value' => $this->getAttributeValue( $attribute  ),
                'index' => $index,
                'visible' => $this->IsAttributeVisible($attribute),
                'id' => $attribute
			);

            ob_start();
            $this->drawAttribute( $attribute );
             
            $attributes[$attribute]['html'] = ob_get_contents();
            ob_end_clean();

            $index++;
		}
		
		$actions = $this->getActions();
		
		$plugins = getSession()->getPluginsManager();
		
		$plugins_interceptors = is_object($plugins) ? $plugins->getPluginsForSection($this->getSite()) : array();
		
		foreach( $plugins_interceptors as $plugin )
		{
			$plugin->interceptMethodFormGetActions( $this, $actions );
		}

		return array(
			'form_style_class' => $this->getClass(),
			'form_processor_url' => $this->getFormUrl(),
			'form_action' => $this->getAction(),
			'url' => $this->getRedirectUrl(),
			'form' => $this,
			'object_id' => is_object($object_it) ? $object_it->getId() : '',
			'width' => $this->getWidth(),
			'button_text' => $this->getButtonText(),
			'b_has_preview' => $this->IsPreviewEnabled(),
			'attributes' => $attributes,
            'buttons_template' => 'core/FormAsyncButtons.php',
            'buttons_parms' => array( 'actions' => $actions ),
            'form_id' => uniqid(),
            'redirect_url' => $this->getRedirectUrl(),
			'form_url' => htmlentities($_SERVER['REQUEST_URI']), 
            'actions' => $actions,
            'form_title' => $this->getCaption()
		);
	}
	
	function getTemplate()
	{
		return "core/FormAsync.php";
	}
	
	function render( &$view, $parms )
	{
	    $this->view = $view;

		echo $view->render( $this->getTemplate(), array_merge($parms, array( 
                'parms' => $this->getRenderParms()
        ))); 
	}
	
	function getActions()
	{
	    $actions = array();
	
	    return $actions;
	}
}
