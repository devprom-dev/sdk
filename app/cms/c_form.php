<?php

include ('c_form_embedded.php');

include_once "views/Field.php";
include "views/FieldCheck.php";
include "views/FieldDateTime.php";
include "views/FieldFile.php";
include "views/FieldForm.php";
include "views/FieldLargeText.php";
include "views/FieldNumber.php";
include "views/FieldPassword.php";
include "views/FieldPrice.php";
include "views/FieldShortText.php";
include "views/FieldStatic.php";
include "views/FieldText.php";
include "views/FieldTextStatic.php";
include "views/FieldColorPicker.php";

class Form
{
 	var $object;
	var	$object_it = null;
	var $required_attributes_warning = false;
	var $has_buttons = true;
	var $has_title = true;
	var $warning_message = '';
	var $action;
	var $readonly;
	var $dynamic_mode;
	var $check_access_message;
	var $form_id;
	private $has_access = null;
	
	function Form( & $object, $dynamic_mode = false )
	{
		global $model_factory;
		
		$this->object = $object;
		$this->readonly = false;
		$this->dynamic_mode = $dynamic_mode;
		$this->form_id = 'object_form';

		$class_name = $this->object->getEntityRefName();
		
		$id = htmlentities($_REQUEST[$class_name.'Id']);
		$action = htmlentities($_REQUEST[$class_name.'action']);

		$action = $id == '' && in_array($action, array('view', 'modify', 'delete')) 
			? $action = 'show' : $action;  
			
		$action = $action == '' ? 'show' : $action;
		
		$this->action = $action;

		if ( $id > 0 )
		{
		    $this->setObjectIt($this->object->getExact($id));
		}
	}
	
	function getAction()
	{
		return $this->action;
	}
	
	function getEditMode()
	{
		return in_array($this->getAction(), array('show', 'add', 'modify')); 
	}
	
	function getEmbeddedForm()
	{
		return new FormEmbedded();
	}
	
	function process()
	{
	    $object_it = $this->getObjectIt();
	    
		// first validate user input values
		if ( $this->action == 'add' || $this->action == 'modify' )
		{
			$validation_result = $this->validateInputValues( 
			    is_object($object_it) ? $object_it->getId() : '', $this->action );

			if ( $validation_result != '' ) 
			{
				$this->setRequiredAttributesWarning();		
				$this->warning_message = $validation_result;
				
				$this->edit(is_object($object_it) ? $object_it->getId() : '');

				// get url from which the form had been called
				$this->redirect_url = $this->getRedirectUrl();

				$this->action = 'show';
				
				return;
			}
		}
	    
	    if ( $this->action == 'add' )
	    {
			// get url from which the form had been called
			$this->redirect_url = $this->getRedirectUrl();

			if ( !getFactory()->getAccessPolicy()->can_create($this->getObject()) ) return; 
			
		    $this->persist();

			if ( $_REQUEST['formonly'] == 'true' )
			{
				$this->edit($this->object_it->getId());
			}
			else
			{
				$this->redirectOnAdded($this->object_it, $this->redirect_url);
			}
			
			return;
	    }
	    
	    if ( $this->action == 'cancel' )
	    {
			// get url from which the form had been called
			$this->redirect_url = $this->getRedirectUrl();
	
		    $this->redirectOnCancel($object_it, $this->redirect_url);
		    
		    return;
	    }
	    
	    if ( !is_object($object_it) ) return;
	    
	    if ( $object_it->getId() == '' ) return;
	    
		switch ($this->action)
		{
			case 'view':
				// get url from which the form had been called
				$this->redirect_url = $this->getRedirectUrl();
		
				$this->show($object_it->getId());

				break;
			//
			case 'show':
				// get url from which the form had been called
				$this->redirect_url = $this->getRedirectUrl();
				
				$this->edit($object_it->getId());

				break;

			//
			case 'createlike':
				if ( getFactory()->getAccessPolicy()->can_create($this->getObject()) ) 
				{
					$this->edit( $this->object->createlike( $object_it->getId() ) );
				} 

				// get url from which the form had been called
				$this->redirect_url = $this->getRedirectUrl();
		
				break;
			//	
			case 'modify':
		
				if ( !$this->editable() ) return;

				$this->redirect_url = $this->getRedirectUrl();
				
				if ( !$this->persist() )
				{
					$this->required_attributes_warning = true;
					$this->warning_message = text(1106);
					
					$this->edit($object_it->getId()); 
				}
				else
				{
					$this->redirectOnModified($this->object_it, $this->getRedirectUrl());
				}

				break;
			//
			case 'delete':
				// get url from which the form had been called
				$this->redirect_url = $this->getRedirectUrl();
				
				if ( getFactory()->getAccessPolicy()->can_delete($this->object_it) ) 
				{
					if( !$this->persist() )
					{
						$this->required_attributes_warning = true;
						$this->warning_message = text(1106);
						
						$this->edit($this->object_it->getId());
					} 
					else
					{
						$this->redirectOnDelete( $this->object_it, $this->redirect_url );
					}
				}
				break;
				
			//
			case 'new':
				exit(header('Location: '.$this->object->getPageName() ));
			//
			default:
				// get url from which the form had been called
				$this->redirect_url = $this->getRedirectUrl();
		}	    
	}
	
	protected function persist()
	{
		switch( $this->getAction() )
		{
		    case 'add':
		    	
			    unset($_REQUEST['RecordCreated']);
			    unset($_REQUEST['RecordModified']);
		    	
				$mapper = new ModelDataTypeMapper();
			    
				$mapper->map($this->getObject(), $_REQUEST);
				
				$this->object_it = $this->object->getExact( 
						$this->object->add_parms($_REQUEST)
				);

				$this->processEmbeddedForms( $this->object_it );
		    	
		    	break;
		    	
		    case 'modify':
		    	
   			    unset($_REQUEST['RecordCreated']);
   			    unset($_REQUEST['RecordModified']);
		    	
				$mapper = new ModelDataTypeMapper();
				
				$mapper->map($this->getObject(), $_REQUEST);
				
				if ( $this->object_it->modify($_REQUEST) < 1 )
				{
					return false;
				}

				$this->object_it = $this->object->getExact($this->object_it->getId());
					
				$this->processEmbeddedForms( $this->object_it );

				break;
				
		    case 'delete':

		    	if ( $this->object->delete( $this->object_it->getId(), $_REQUEST['RecordVersion'] ) < 1 ) return false;
		    	
		    	break;
		}
		
		return true;
	}
	
	function processEmbeddedForms( $object_it )
	{
		$embedded = $this->getEmbeddedForm();
		$embedded->process( $object_it );
	}
	
	function validateInputValues( $id, $action )
	{
		return "";
	}
	
 	function checkUniqueExcept( $id, $key )
 	{
 		global $_REQUEST;

		if ( !array_key_exists($key, $_REQUEST) )
		{
			return true;
		}
		
 		$it = $this->object->getByRefArray( 
 			array( "LCASE(".$key.")" => strtolower(trim($_REQUEST[$key])) )
 			);

 		while ( !$it->end() )
 		{
 			if ( $it->getId() != $id )
 			{
				return false;
 			}
 			$it->moveNext();
 		}
 		
 		return true;
 	}
	
 	function checkHasValue( $key )
 	{
 		global $_REQUEST;

		if ( !array_key_exists($key, $_REQUEST) )
		{
			return false;
		}
		
		if ( $_REQUEST[$key] == '' )
		{
			return false;
		}
		
		return true;
 	}
 	
	function hasAlert()
	{
		return $this->required_attributes_warning;
	}
	
	function getId()
	{
		return $this->form_id;
	}
	
	function getCaption() {
		return 'Без названия';
	}
	
	function getObject()
	{
		return $this->object;
	}
	
	function getObjectIt()
	{
		return $this->object_it;
	}
	
	function setObjectIt( $object_it )
	{
		$this->object_it = $object_it;
	}
	
	function getDeleteMessage()
	{
		return text(636);
	}
	
	function IsViewMode()
	{
		return $this->action == 'view';
	}
	
	function IsReadonly()
	{
		return $this->readonly;
	}
	
	function editable()
	{
		if( isset($this->object_it) ) 
		{
			return getFactory()->getAccessPolicy()->can_modify($this->object_it);
		} 
		else 
		{
			return getFactory()->getAccessPolicy()->can_create($this->getObject());
		}
	}
	
	function setCheckAccessMessage( $message )
	{
		$this->check_access_message = $message;
	}
	
	function checkAccess()
	{
		if ( is_bool($this->has_access) ) return $this->has_access;
		
		$has_access = false;
		
		if( isset($this->object_it) ) 
		{
			if ( $this->action == 'view' || $this->readonly )
			{
				$has_access = getFactory()->getAccessPolicy()->can_read($this->object_it);
			}
			else
			{
				$has_access = $this->editable();
			}
		} 
		else 
		{
			$has_access = $this->editable();
		}
		
		if ( !$has_access )
		{
			$this->setCheckAccessMessage( text(983) );
		}
		
		return $this->has_access = $has_access;
	}
	
	function workDynamically() 
	{
		if ( $this->action != '' )
		{
			header('Content-Type: text/html; charset=windows-1251');
			header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
			header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			header("Pragma: no-cache"); // HTTP/1.0
			
			$this->dynamic_mode = true;
			$this->draw();
			
			die();
		}
	}
	
	function redirectOnAdded( $object_it, $redirect_url = '' ) 
	{
		if ( !is_object($object_it) )
		{
		    $object = $this->getObject();
		    
			$redirect_url = $object->getPage(); 
		}
		else if ( $object_it->getId() < 1 )
		{
			$redirect_url = $object_it->object->getPage(); 
		}

		if ( $redirect_url != '' )
		{ 
			exit(header('Location: '.$redirect_url));
		}
		else
		{
			exit(header('Location: '.$object_it->getViewUrl() ));
		}
	}
	
	function redirectOnModified( $object_it, $redirect_url = '' ) {
		$this->redirectOnAdded( $object_it, $redirect_url );
	}

	function redirectOnCancel( $object_it, $redirect_url = '' ) 
	{
	    if ( $redirect_url != '' )
	    {
	        exit(header('Location: '.$redirect_url));
	    }
	    elseif ( is_object($object_it) )
	    {
	        exit(header('Location: '.$object_it->getViewUrl() ));
	    }
	    else
	    {
	        exit(header('Location: '.$this->object->getPage() ));
	    }
	}

	function redirectOnDelete( $object_it, $redirect_url = '' ) 
	{
		if ( strpos($redirect_url, $this->object->getEntityRefName().'Id') > 0 )
		{
			exit(header('Location: '.$this->object->getPage()));
		}
		else
		{
			exit(header('Location: '.$redirect_url));
		}
	}

	function edit( $objectid )
	{
		global $_REQUEST;
		
		$this->setObjectIt( $objectid > 0 ? $this->object->getExact( $objectid ) : null );
		
		if ( is_object($this->object_it) && $this->object_it->count() > 0 )
		{
			$_REQUEST[$this->object->getEntityRefName().'Id'] = $this->object_it->getId();
		}
	}
	
	function show( $objectid )
	{
		if ( is_a($objectid, 'OrderedIterator') )
		{
			$this->setObjectIt( $objectid->copy() );
		}
		else
		{
			$this->setObjectIt( $objectid > 0 ? $this->object->getExact( $objectid ) : null );
		}
		
		$this->action = 'view';
		
		$this->readonly = true;
	}

	function getEditQueryString( $object_id )
	{
		return $this->object->getEntityRefName().'Id='.$object_id.'&'.$this->object->getEntityRefName().'action=show';
	}
	
	function setRequiredAttributesWarning()
	{
		$this->required_attributes_warning = true;
	}
	
	function hasButtons( $state ) {
		$this->has_buttons = $state;
	}
	
	function showTitle( $state ) {
		$this->has_title = $state;
	}

	function canEdit() 
	{
		global $_REQUEST;
		return $_REQUEST[$this->object->getEntityRefName().'Id'] != '';
	}
	
	function IsNeedButtonNew() {
		return true;
	}

	function IsNeedButtonCopy() {
		return true;
	}

	function IsNeedButtonDelete() 
	{
		return true;
	}

	function IsNeedButtonSave() {
		return $this->canEdit();
	}
	
	function IsAttributeValueDefined( $name, $object_it = null ) 
	{
		global $_REQUEST, $_FILES;
		
		$object = $this->getObject();
		
		if ( $object->getAttributeType($name) == 'file' ) 
		{
			if(is_object($object_it)) 
			{
				return (is_uploaded_file($_FILES[$name]['tmp_name']) || $object_it->getFileName($name) != '');
			}
			else 
			{
				return is_uploaded_file($_FILES[$name]['tmp_name']);
			}
		}

		return trim($this->getFieldValue( $name )) != '';
	}
 	
	function IsAttributeEditable( $attr_name )
	{
		return getFactory()->getAccessPolicy()->can_modify_attribute($this->getObject(), $attr_name);
	}
	
	function IsAttributeVisible( $attr_name ) 
	{
	    if ( $this->IsAttributeRequired( $attr_name ) && !$this->IsAttributeValueDefined( $attr_name, $this->getObjectIt() ) ) return true;
	    
	    if ( !is_object($this->getObjectIt()) && !$this->IsAttributeEditable($attr_name) ) return false;
	    
		return $this->object->IsAttributeVisible($attr_name);
	}
	
	function IsAttributeRequired( $attr_name ) 
	{
		return $this->object->IsAttributeRequired( $attr_name );
	}
	
	function getFormPage() 
	{
		return $this->object->getPageName();
	}
	
	function getWarningMessage() 
	{
		return $this->warning_message;
	}
	
	function getButtonName( $button ) 
	{
		return $button;
	}
	
	function getAcceleratorKey( $button_name ) 
	{
		$button_name = translate($button_name);
		
		if( $button_name == 'Create' ) 
		{
			$key = substr($button_name, 1, 1);
		}
		else 
		{
			$key = substr($button_name, 0, 1);
		}
		
		if( getSession()->getLanguageUid() == 'EN' )
		{
			return $key;
		}
		else 
		{
			switch( strtoupper($key) )
			{
				case 'С': return 'C';
				case 'У': return 'E';
				case 'О': return 'J';
			}
			
		}
	}
	
	function getFieldDescription( $field_name )
	{
		return $this->object->getAttributeDescription( $field_name );
	}
	
	function getFieldValue( $field )
	{
		global $_REQUEST;
		
		$object_it = $this->getObjectIt();
		
		$required = $this->IsAttributeRequired( $field );

		$value = is_object($object_it) && $object_it->count() > 0 
			? ( $_REQUEST[$field] != '' 
				? htmlentities($_REQUEST[$field], ENT_QUOTES | ENT_HTML401, 'windows-1251') 
				: ($object_it->get_native( $field ) == '' && $this->getEditMode() && $required  
		  			? $this->object->getDefaultAttributeValue( $field ) 
		  			: $object_it->get_native( $field ) 
		  		   ) 
		  	  )
			: ($this->required_attributes_warning 
				? htmlentities($_REQUEST[$field], ENT_QUOTES | ENT_HTML401, 'windows-1251') 
				: ( $_REQUEST[$field] == '' && ($required || $this->object->getAttributeDbType( $field ) == 'CHAR') 
						? $this->object->getDefaultAttributeValue( $field ) 
						: $_REQUEST[$field]
				   ) 
			   );
		
		if ( in_array($this->getObject()->getAttributeType($field), array('datetime')) )
		{
			$value = SystemDateTime::convertToClientTime($value);
		}
		
		return $value;
	}

	function getDefaultValue( $field )
	{
		$object_it = $this->getObjectIt();

		return is_object($object_it) && $object_it->count() > 0 
			? ( $_REQUEST[$field] != '' 
				? htmlentities($_REQUEST[$field], ENT_QUOTES | ENT_HTML401, 'windows-1251') 
				: ($object_it->get_native( $field ) == ''  
		  			? $this->object->getDefaultAttributeValue( $field ) 
		  			: $object_it->get_native( $field ) 
		  		   ) 
		  	  )
			: ( $_REQUEST[$field] != '' 
				? htmlentities($_REQUEST[$field], ENT_QUOTES | ENT_HTML401, 'windows-1251') 
				: $this->object->getDefaultAttributeValue( $field )
			  );
	}
	
	function draw()
	{
		global $_REQUEST, $_SERVER, $model_factory;
		
		$id = $_REQUEST['id'];
		$action = $_REQUEST['action'];

		$has_access = $this->checkAccess();
		if ( !$has_access )
		{
			echo '<div class=form_warning>'.translate('Внимание!').' '.$this->check_access_message.'</div>';
			return;
		}
		
		if ( $this->dynamic_mode ) {
			$this->form_id = $this->object->getEntityRefName();
		}
		else {
			$this->form_id = 'object_form';
		}
		
		$formname = $this->getFormPage();
		
		if ( $this->has_title )
		{
		?>
		<div class=form_title><? echo $this->getCaption() ?></div>
		<?
		}
		
		if($this->required_attributes_warning) 
		{
		?>
		<div class=form_warning><? echo translate('Внимание!').' '.$this->getWarningMessage(); ?></div>
		<?
		}

       	$submitId = $this->object->getEntityRefName().'SubmitBtn';
        ?>
        <form action="<? echo $formname; ?>" method="post" id="<? echo $this->form_id ?>" name="object_form" enctype="multipart/form-data" style="margin-top:0pt;margin-bottom:0pt;">
		  <input id="<? echo $this->object->getEntityRefName().'action_mode'; ?>" type="hidden" name="action_mode" value="form">
		  <input name="entity" value="<? echo ($_REQUEST['entity'] != '' ? $_REQUEST['entity'] : $this->object->getEntityRefName()); ?>" type="hidden">
		  <input name="RecordVersion" value="<? echo $this->getFieldValue('RecordVersion'); ?>" type="hidden">
          <table class=formtable cellpadding=0 cellspacing=0 width=100%>
    		<?
    			$names = array_keys($this->object->getAttributesSorted());
				
    			for( $i = 0; $i < count($names); $i++) 
    			{
    				if ( $names[$i] == 'RecordModified' || $names[$i] == 'RecordCreated' )
    				{
    					continue;
    				}
    				
					if($this->IsAttributeVisible($names[$i]))
					{
						echo '<tr id="fieldRow'.$names[$i].'">';
	    				$this->drawField( $names[$i], $i );
						echo '</tr>';
					}
					else
						$this->drawHiddenFeild( $names[$i] );
    			}
    			
				?>
   				<input type="hidden" id="<? echo $this->object->getEntityRefName(); ?>action" name="<? echo $this->object->getEntityRefName(); ?>action" value="">
				<?
    			if( isset($this->object_it) && $this->object_it->count() > 0 )
    			{
				?>
				<input type="hidden" id="<? echo $this->object->getEntityRefName(); ?>Id" name="<? echo $this->object->getEntityRefName().'Id'; ?>" value="<? echo $this->object_it->getId(); ?>">
				<?
    			}

    			if ( $this->redirect_url != '' )
    			{
					echo '<input id="'.$this->object->getEntityRefName().'redirect" type="hidden" name="redirect" value="'.$this->redirect_url.'">';
				}
				
          		echo '</form>';

        		$buttons = array();

          		if ( $this->has_buttons )
          		{
					echo '<tr><td class=formfield>&nbsp;</td><td class="formvalue" style="width:auto;padding:6pt">';
					
					$this->drawButtons();
					
					echo '<div style="clear:both"></div>';
					echo '</td></tr>';
		       	}
            ?>
        </table>
        <?php

        $this->drawScript();
	}
	
	function drawButtons()
	{
		global $model_factory;
		
		$buttons = array();
		
		if ( $this->checkAccess() && $this->has_buttons && !$this->readonly )
		{
   			if( isset($this->object_it) && $this->object_it->count() > 0 )
   			{
   				
				if( $this->IsNeedButtonNew() )
				{
					array_push($buttons, 'new');
				}

				if( $this->IsNeedButtonCopy() )
				{
					array_push($buttons, 'createlike');
				}

				if( $this->IsNeedButtonSave()) 
    			{
					array_push($buttons, 'save');
    			}

				array_push($buttons, 'cancel');
    			
				if ( $this->IsNeedButtonDelete() ) 
				{
					array_push($buttons, 'delete');
				}
   			}
    		else
    		{
        		array_push($buttons, 'add');
        		
    			array_push($buttons, 'cancel');
        	}
		}
		else
		{
			if ( $this->has_buttons && $this->editable() )
			{
				array_push($buttons, 'modify');
			}
		}
		
		foreach ( $buttons as $button )
		{
			switch ( $button )
			{
				case 'delete':
					$access = getFactory()->getAccessPolicy()->can_delete($this->object_it);
					
					if ( !$access )
					{
						$reason = getFactory()->getAccessPolicy()->getReason();
					}
					
				 	echo '<div style="float:left;padding-left:25px;">';
				 	?>
       				<input class="btn" onclick="javascript: submitForm('delete');" accesskey="<? echo $this->getAcceleratorKey($this->getButtonName('Удалить'))?>" tabindex="1002" <? if ( !$access ) echo 'disabled'; ?> type="button" title="<? echo $reason ?>" value="<? echo_lang($this->getButtonName('Удалить')) ?>">
            		<?
           			echo '</div>';
					break;

				case 'save':
					echo '<div style="float:left">';
					?>
                	<input class="btn btn-primary" onclick="javascript: submitForm('modify');" type="button" accesskey="<? echo $this->getAcceleratorKey($this->getButtonName('Сохранить'))?>" tabindex="1000" id="<? echo $this->object->getEntityRefName(); ?>SubmitBtn" value="<? echo_lang($this->getButtonName('Сохранить')) ?>">
    	            <?
               		echo '</div>';
					break;

				case 'new':
				 	echo '<div style="float:left">';
					?>
               		<input class="btn " tabindex="1000" type="button" onclick="javascript: submitForm('new');" value="<? echo_lang($this->getButtonName('Новое')) ?>">
					<?
               		echo '</div>';
					break;

				case 'add':
				 	echo '<div style="float:left">';
            		?>
            		<input class="btn btn-primary" onclick="javascript: submitForm('add');" accesskey="<? echo $this->getAcceleratorKey($this->getButtonName('Создать'))?>" tabindex="1000" id="<? echo $this->object->getEntityRefName(); ?>SubmitBtn" type="button" value="<? echo_lang($this->getButtonName('Создать')) ?>">
                	<?
               		echo '</div>';
					break;

				case 'cancel':
				 	echo '<div style="float:left">';
    			 	?>
               		<input class="btn btn-link" onclick="javascript: submitForm('cancel');" accesskey="<? echo $this->getAcceleratorKey($this->getButtonName('Отменить'))?>" tabindex="1001" type="button" value="<? echo_lang($this->getButtonName('Отменить')) ?>">
                	<?
	            	echo '</div>';
					break;
	
				case 'modify':
				 	echo '<div style="float:left;">';
						echo '<input class="btn " type="button" value="'.translate('Изменить').'" onclick="javascript: {tmp = window.location; tmp = String(tmp).replace(/#/,\'&\'); window.location = encodeURI(tmp + \'&'.$this->object->getEntityRefName().'action=show\'); return;}">';
	            	echo '</div>';
					break;
			}
		}
	}
	
	function drawCancelButton()
	{
	}
	
	function drawToolbar()
	{
	}
	
 	function drawField( $name, $index )
	{
		$field = $this->createField( $name );
		
		if( !is_object($field) ) return; 
		
		$field->setTabIndex( 100 + $index );
		
		$attribute_required = $this->IsAttributeRequired($name);
		
		$field->setRequired( $attribute_required );
		
		if ( $attribute_required )
		{
			if ( $this->required_attributes_warning && $this->getFieldValue($name) == '' )
			{
				$att_name_style = 'style="color:red;text-decoration:underline;"';
			}
			elseif ( !$field->readOnly() )
			{
				$att_name_style = 'style="text-decoration:underline;"';
			}
		}
		
		$attribute_type = $this->object->getAttributeType($name);
		
		if( $attribute_type != 'char') 
		{
		?>
		<td valign=top align=right class=formfield>
			<div <? echo $att_name_style; ?> >
			<? echo str_replace(', ', ',&nbsp;', translate($this->object->getAttributeUserName($name))); ?>
			</div>
		</td>
		<?
		}
		else
		{
			echo '<td class=formfield></td>';
		}
		?>

		<td class=formvalue valign=top>
		<? 
			if( is_object($field) ) 
			{
				echo '<div class="line"></div>';

				$short_field = in_array($attribute_type, array('integer', 'float', 'date', 'datetime'), true)
				    && (is_a($field, 'FieldNumber') || is_a($field, 'FieldDateTime'));
				
				$class_name = $short_field ? 'formvalueholder formvalue-short' : 'formvalueholder formvalue-long';
				
				echo '<div class="'.$class_name.'" required="'.($attribute_required ? 'true' : '').'">';
					$field->draw(); 
				echo '</div>';
				
				$description = $this->getFieldDescription($name);
				if ( $description != '' )
				{
					echo '<div class="line small_grey">';
						echo $description;
					echo '</div>';
				}
			}
		?>
		</td>
		<?
	}
	
	function drawHiddenFeild( $name_field )
	{
	?>
		<input id="<? echo $this->object->getEntityRefName().$name_field ?>" type="hidden" name="<? echo $name_field; ?>" 
			   value="<? echo $this->getFieldValue( $name_field ); ?>">
	<?
	}
	
	function createFieldObject( $name )
	{
        switch( $this->object->getAttributeType($name) )
        {
        	case 'text' :
        		switch( strtolower($this->object->getAttributeDbType($name)) )
        		{
        		    case 'richtext':
        		    	$field = new FieldText;
        		    	break;
        		    	
        		    default:
		        		$field = new FieldLargeText;
		        		break;
        		}
        		break;
        	case 'varchar' :
        		$field = new FieldShortText;
        		break;
        	case 'integer' :
        	case 'float' :
        		$field = new FieldNumber;
        		break;
        	case 'date' :
        	case 'datetime' :
        		$field = new FieldDateTime;
        		break;
        	case 'password' :
        		$field = new FieldPassword;
        		break;
        	case 'char' :
        		$field = new FieldCheck($this->object->getAttributeUserName($name));
        		break;
        	case 'color':
        		$field = new FieldColorPicker();
        		break;
        	case 'price' :
        		$field = new FieldPrice;
    			if(isset($this->object_it)) {
    				$field->object_it = $this->object_it;
    			}
        		break;
        	case 'image' :
        	case 'file' :
        		$field = new FieldFile;
    			if(isset($this->object_it)) {
    				$field->object_it = $this->object_it;
    			}
        		break;
        	default:
        		return null;
        }
        	
		return $field;
	}
	
    function createField( $name )
    {
    	$field = $this->createFieldObject( $name );

    	if( !is_object($field) ) return null;

    	$object_it = $this->getObjectIt();
    	
      	$field->setReadOnly( !$this->checkAccess() || !$this->IsAttributeEditable($name) );
    	 
    	$field->setEditMode( $this->getEditMode() );
    	
    	$field->setName($name);

    	$field->setValue($this->getFieldValue($name));
    	
    	if ( $this->getEditMode() && $this->IsAttributeRequired($name) )
    	{
    		$field->setDefault($this->getDefaultValue($name));
    	}
    	
    	if ( is_a($field, 'FieldFile') )
    	{
    	    $field->setValue( $this->getFieldValue($name.'Ext') );
    	}
    	
    	$field->setId($this->object->getEntityRefName().$name);
    	       	
    	return $field;
    }

	function drawMenu( $actions = null )
	{
		if ( !is_array($actions) )
		{
			$actions = $this->getActions();
		}
		
		if ( count($actions) > 0 )
		{
			$popup = new PopupMenu();
			$popup->draw("list_menu", translate('Действия'), $actions); 
		
			echo '<div style="clear:both;"></div>';
		}
	}

	function getActions()
	{
		return array();
	}
}
