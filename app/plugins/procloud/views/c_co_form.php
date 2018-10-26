<?php

 define ('CO_ACTION_CREATE', 1);
 define ('CO_ACTION_MODIFY', 2);
 define ('CO_ACTION_DELETE', 3);
 define ('CO_ACTION_PREVIEW', 4);
 
 ////////////////////////////////////////////////////////////////////////////////
 class AjaxForm
 {
 	var $object;
 	var $action;
 	var $object_it;
 	
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
			if ( $this->object->IsAttributeVisible( $key ) )
			{
				array_push( $result, $key );
			}
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
				return $this->object->getAttributeUserName( $attribute );
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
				return '';
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

	/*
	 * returns visual type of attribute
	 */
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
		return '';
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
	 * draws a form
	 */
 	/*
	 * draws a form
	 */
	function draw()
	{
		global $_SERVER, $_REQUEST, $tab_index, $embedded_form_id;
		
		// check the access policy
		$has_access = true;
		
		// continue with embedded form id
		if ( $_REQUEST['embedded_form_id'] )
		{
			$embedded_form_id = $_REQUEST['embedded_form_id'];
		}
		
		switch ( $this->action )
		{
			case CO_ACTION_CREATE:
				$has_access = getFactory()->getAccessPolicy()->can_create($this->object);
				break;

			case CO_ACTION_MODIFY:
				$has_access = getFactory()->getAccessPolicy()->can_modify($this->object_it);
				break;

			case CO_ACTION_DELETE:
				$has_access = getFactory()->getAccessPolicy()->can_delete($this->object_it);
				break;
		}
		
		if ( !$has_access )
		{
			echo '<div class="line" style="padding:12">';
				echo translate('Нет доступа на выполнение операции');
			echo '</div>';
				
			return;
		}
		
		$this->drawStyle();
		$this->drawScript();
		
		$caption = $this->getCaption();
		
		if ( $this->isCentered() )
		{
			$align = "center";
		}
		else
		{
			$align = "left";
		}

		$form_processor_url = '/'.$this->getSite().'/command.php?'.
	        	'class='.$this->getCommandClass();
	        	
		echo '<div align="'.$align.'" style="width:100%;">';
		echo '<form class="'.$this->getClass().'" id="myForm" action="'.$form_processor_url.'" method="post">';
			echo '<input type="hidden" id="action" name="action" value="'.$this->getAction().'">';
			echo '<input type="hidden" name="MAX_FILE_SIZE" value="30485760">';
			
			if ( $this->getAction() == CO_ACTION_MODIFY )
			{
				echo '<input type="hidden" id="object_id" name="object_id" value="'.$this->object_it->getId().'">';
			}
		 
		?>
		<table width=<? echo $this->getWidth() ?> >
	    	<tr>
				<td align="<? echo $align ?>">
				<?
				if ( $caption != '' )
				{
					echo '<h3 class="title form-header">'.$this->getCaption().'</h3>';
				}
				?>
				<div id="result"></div>
				</td>
	    	</tr>
		<?
		$attributes = $this->getAttributes();

		for ( $i = 0; $i < count($attributes); $i++ )
		{
			$this->drawAttribute( $attributes[$i] );
		}
		
		?>
	    	<tr>
				<td class=value align="<? echo $align ?>">
				<?php 
					$this->drawButtons(); 
				?>
				</td>
	    	</tr>
			<?
			if ( $this->IsPreviewEnabled() )
			{
				echo '<tr><td height=10></td></tr>';
				echo '<tr><td><div id="preview"></div></td></tr>';
			}

			echo '</table>';
		echo '</form>';
		echo '</div>';
	}
	
	function drawButtons()
	{
		global $tab_index;
		
		if ( $this->getVisibleAttributesCount() > 2 )
		{
			echo '<div id="result_bottom" style="padding-bottom:12px;"></div>';
		}

		if ( $this->IsPreviewEnabled() )
		{
		?>
		<input class="btn btn-success" disabled="true" tabindex="<?php echo (++$tab_index); ?>" id="btn" class="btn btn-small" type="submit" 
			onclick="javascript: $('#action').val(<? echo CO_ACTION_PREVIEW ?>);" value="<? echo translate('Просмотр') ?>">

		&nbsp;
		
		<?php
		}
		?>

		<input class="btn btn-success" disabled="true" tabindex="<?php echo (++$tab_index); ?>" id="btn" class="btn btn-small btn-primary" type="submit" 
			onclick="javascript: $('#action').val(<? echo $this->getAction() ?>);" value="<? echo $this->getButtonText() ?>">
		<?php 
	}
	
	/*
	 * draws javascript related to a form
	 */
	function drawScript()
	{
		$attributes = $this->getAttributes();
		?>
		<script language="javascript">

		var originalFormState = '';
		
		$(document).ready(function() 
		{
			$('#myForm .btn').attr('disabled', false);
		
			focusField('myForm');

	    	registerBeforeUnloadHandler(function() {
	    		if ( originalFormState != $('#myForm').formSerialize() ) {
	    			return "<? echo text(632) ?>";
	    		}
	    	});
	    	
	    	if ( !$.browser.msie )
	    	{
	    		originalFormState = $('#myForm').formSerialize();
	    		window.onbeforeunload = beforeUnload;
	    	}

			$('#myForm').ajaxForm({
				dataType: 'html',
				beforeSerialize: function($form, options) 
				{ 
					if ( !validateForm($('#myForm')) ) return false;
		    			
	    			return true;
				},
				beforeSubmit: function(a,f,o) 
				{
					$('#result').html('<img src="/images/ajax-loader.gif">');
					$('#preview').html('');

					$('.btn').attr('disabled', true);
				},
				error: function( xhr, exception ) 
				{
					$('#result').html('<div class="alert alert-danger error">'+ajaxErrorExplain(xhr, exception)+'</div>');

					$('.btn').attr('disabled', false);
				},
				success: function( data ) 
				{
					data = jQuery.parseJSON(data);
					
					resetUnloadHandlers();
					
					$('#result').html('');
					$('.btn').attr('disabled', false);

					if ( typeof data != 'object' ) return;
					
					var state = data.state;
					var message = data.message;
					var objectid = data.object;
					
					if ( state == 'redirect' )
					{
						if ( message != '' )
						{
							$('#result').html('<div class="success">'+message+'</div>');
							
							setTimeout( function() {
								window.location = data.object;
							}, 2000);
						}
						else
						{
							window.location = data.object;
						}

						return;
					}

					if ( $('#action').val() == <? echo CO_ACTION_PREVIEW ?> && state == 'success' )
					{
						$('#preview').html(message);
						return;
					}

					$('#result').html('<div class="alert alert-danger '+state+'">'+message+'</div>');
					$('#result_bottom').html('<div class="'+state+'">'+message+'</div>');

					if ( state == 'success' )
					{
						<?
						$url = $this->getRedirectUrl();
						if ( $url != '' )
						{
						?>
						if ( objectid != '' )
						{
							window.location = '<? echo $url ?>'+objectid;
						}
						else
						{
							window.location = '<? echo $url ?>';
						}
						<?
						}
						?>
					}
				}
			});
		});
		</script>
		<?
	}
	
	/*
	 * draws attribute according to its type
	 */
	function drawAttribute( $attribute )
	{
		global $tab_index;
		
		$tab_index++;

		$value = $this->getAttributeValue($attribute);

		$attribute_type = $this->getAttributeType( $attribute );
		
		if ( !$this->IsAttributeVisible($attribute) )
		{
			echo '<input type="hidden" id="'.$attribute.'" name="'.$attribute.'" value="'.$value.'">';
			
			return;
		}

		$title = $this->getName( $attribute );
		
		if ( $attribute_type != 'char' && $title != '' )
		{
		?>
    	<tr align="left">
			<td>
				<?
				if ( $this->isBoldFieldNames() )
				{
					echo '<b>'; 
				}
				
				echo_lang($title);
				 
				if ( $this->isBoldFieldNames() )
				{
					echo '</b>'; 
				}
				?>
			</td>
		</tr>
		<?
		}
		?>
		<tr align="left">
			<td class=value>
				<div class="formvalueholder">
				<?
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
					echo '<div class="input-border form-group">';
						echo '<input type="hidden" id="'.$attribute.'" name="'.$attribute.'" value="'.$value.'">';
						echo '<input class="input_value form-control" value="'.$displayValue.'" readonly="readonly">';
					echo '</div>';
				}
				else
				{
					switch ( $attribute_type )
					{
						case 'largetext':
							echo '<div class="input-border form-group">';
							?>
							<textarea class="input_value form-control" id="<? echo $attribute; ?>" name="<? echo $attribute; ?>"
								style="overflow-x:hidden;overflow-y: scroll;" 
								tabindex="<? echo $tab_index ?>" 
								wrap="virtual"><? echo $value ?></textarea>
							<?
							echo '</div>';							
							break;
							
						case 'richtext':
							echo '<div class="input-border form-group">';
							?>
							<textarea class="input_value form-control" id="<? echo $attribute; ?>" name="<? echo $attribute; ?>"
								style="overflow-x:hidden;overflow-y: scroll;" 
								tabindex="<? echo $tab_index ?>" 
								wrap="virtual" rows="6"><? echo $value ?></textarea>
							<?
							echo '</div>';							
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
							}
							
							$field->SetTabindex($tab_index); 
							$field->SetName($attribute);
							$field->SetValue($value);
							$field->SetId($attribute);
							$field->draw();

							break;							

						case 'file':
							echo '<div class="input-border form-group">';
							?>
							<input class=input_value type="file" id="<? echo $attribute; ?>" name="<? echo $attribute; ?>" value="<? echo $value ?>" tabindex="<? echo $tab_index ?>">
							<?
							echo '</div>';
							break;							

						case 'char':
							?>
							<input class="checkbox" type="checkbox" id="<? echo $attribute; ?>" name="<? echo $attribute; ?>" <? echo ($value == 'Y' ? 'checked' : '') ?> tabindex="<? echo $tab_index ?>" style="float:left;width:18px;">
							<label style="float:left;"><? echo_lang($this->getName( $attribute )); ?></label></input>
							<div style="clear:both;"></div>
							<?
							break;							

						case 'password':
							echo '<div class="input-border form-group">';
							?>
							<input class="input_value form-control" type="password" id="<? echo $attribute; ?>" name="<? echo $attribute; ?>" value="<? echo $value ?>" tabindex="<? echo $tab_index ?>">
							<?
							echo '</div>';
							break;							

						default:
							$this->drawCustomAttribute( $attribute, $value, $tab_index );
					}
				}
				?>
				</div>
				<div class="fieldinfo">
				<?
					if ( $this->IsAttributeRequired( $attribute ) && $this->IsAttributeModifable($attribute) )
					{
						echo '('.translate('Обязательное поле').') ';
					}

					echo $this->getDescription( $attribute );
				?>
				</div>
			</td>
    	</tr>
		<?
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

		echo '<div class="input-border form-group" style="'.$width.'">';
		?>
		<input class="input_value form-control" id="<? echo $attribute; ?>" name="<? echo $attribute; ?>" value="<? echo $value ?>" tabindex="<? echo $tab_index ?>">
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
 }

///////////////////////////////////////////////////////////////////////////////
class CoPageForm extends AjaxForm
{
	function CoPageForm( $object )
	{
		parent::AjaxForm( $object );
	}
	
	function draw()
	{
		global $project_it;
		
		// check the access policy
		$has_access = true;
		
		switch ( $this->action )
		{
			case CO_ACTION_CREATE:
				$has_access = getFactory()->getAccessPolicy()->can_create($this->object);
				break;

			case CO_ACTION_MODIFY:
				$has_access = getFactory()->getAccessPolicy()->can_modify($this->object_it);
				break;

			case CO_ACTION_DELETE:
				$has_access = getFactory()->getAccessPolicy()->can_delete($this->object_it);
				break;
		}
		
		if ( !$has_access )
		{
			echo translate('Нет доступа на выполнение операции');
			return;
		}
		
		$this->drawScript();
		
		$caption = $this->getCaption();

		if ( is_object($project_it) )
		{
			$form_processor_url = '/command/'.$project_it->get('CodeName').'/'.$this->getCommandClass();
		}
		else
		{
			$form_processor_url = '/command/'.$this->getCommandClass();
		}
	        	
		echo '<h2>'.$this->getCaption().'</h2>';
		
		echo '<br/>';

		echo '<div style="width:100%;">';
			echo '<form id="myForm" action="'.$form_processor_url.'" method="post" onsubmit="javascript: return false;">';
				echo '<input type="hidden" id="action" name="action" value="'.$this->getAction().'">';
				echo '<input type="hidden" name="MAX_FILE_SIZE" value="1048576">';
				
				if ( $this->getAction() == CO_ACTION_MODIFY )
				{
					echo '<input type="hidden" id="object_id" name="object_id" value="'.$this->object_it->getId().'">';
				}
			 
				$attributes = $this->getAttributes();
		
				if ( $this->getPanes() == 2 )
				{
					echo '<div style="float:left;width:49%;">';
						for ( $i = 0; $i < count($attributes); $i++ )
						{
							$this->drawAttribute( $attributes[$i], 1 );
						}
					echo '</div>';
					echo '<div style="float:right;width:49%;">';
						for ( $i = 0; $i < count($attributes); $i++ )
						{
							$this->drawAttribute( $attributes[$i], 2 );
						}
					echo '</div>';
				}
				else
				{
					for ( $i = 0; $i < count($attributes); $i++ )
					{
						$this->drawAttribute( $attributes[$i] );
					}
				}
				
			echo '</form>';

			echo '<div id="result" style="clear:both;"></div>';
			echo '<br/>';

			echo '<div class="blackbutton">';
				echo '<div id="body">';
					echo '<a id="submit" href="javascript: '.$this->getSubmitScript().'">'.$this->getButtonText().'</a>';
				echo '</div>';
				echo '<div id="rt"></div>';
			echo '</div>';
		echo '</div>';
	}
	
	function getPanes()
	{
		return 1;
	}
	
	function getSubmitScript()
	{
		return 'submitForm(\''.$this->getAction().'\')';
	}
	
	function drawScript()
	{
		$attributes = $this->getAttributes();
		?>
		<script language="javascript">
	
		$(document).ready(function() {
			<?
			for ( $i = 0; $i < count($attributes); $i++ )
			{
				$atttype = $this->getAttributeType($attributes[$i]);
				
				if ( $this->IsAttributeVisible($attributes[$i]) && 
					 $atttype != 'largetext' && $atttype != 'richtext' )
				{
				?>
				  $("#<? echo $attributes[$i] ?>").keydown(
				     function(e){
				       var key = e.charCode || e.keyCode || 0;
				       if ( key == 13 ) setTimeout("<? echo $this->getSubmitScript() ?>", 200);
				     }
				  );
			    <?
				}
				
  			    if ( $i == 0 )
			    {
			  		echo '$("#'.$attributes[$i].'").focus();';
			    }
			}
			?>
		});
		</script>
		<?
	}	
 }

?>
