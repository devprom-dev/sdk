<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

include_once SERVER_ROOT_PATH."core/classes/model/persisters/ObjectRecordAgePersister.php";
include_once SERVER_ROOT_PATH."core/classes/model/mappers/ModelDataTypeMapper.php";
include_once SERVER_ROOT_PATH.'cms/views/FieldDictionary.php';
include_once SERVER_ROOT_PATH.'cms/views/FieldAutoCompleteObject.php';

class FormEmbedded
{
 	var $object, 
 		$form_id, 
 		$anchor_field, 
 		$readonly, 
 		$object_it, 
 		$singleton,
 		$iterator,
 		$button_text,
 	    $tabindex;
 	
 	private $form_field = '';
 	
 	function __construct( $object = null, $anchor_field = null, $form_field = '' )
 	{
 		$this->object = $object;
 		$this->setFormId(0);
 		$this->anchor_field = $anchor_field;
 		$this->readonly = false;
 		$this->singleton = false;
 		$this->form_field = $form_field;
 		
 		if ( is_object($this->object) )
 		{
 			$this->iterator = $this->object->getAll();
 		}

 		$this->button_text = translate('добавить');
 	}
 	
 	public function extendModel()
 	{
        foreach( getFactory()->getPluginsManager()->getPluginsForSection(getSession()->getSite()) as $plugin ) {
            $plugin->interceptMethodFormExtendModel($this);
        }
 	}
 	
 	function IsAttributeVisible( $attribute )
 	{
 	    if ( $attribute == $this->anchor_field ) return false;
 		return $this->object->IsAttributeVisible($attribute);
 	}

  	function IsAttributeRequired( $attribute )
 	{
 		return $this->object->IsAttributeRequired($attribute);
 	}
 	
 	function IsAttributeObject( $attribute )
 	{
 		return $this->object->IsReference( $attribute );
 	}
 	
 	function getObject()
 	{
 		return $this->object;
 	}
 	
 	function setObjectIt( $object_it )
 	{
 		$this->object_it = $object_it;
 		
 		$this->setFormId( $this->object_it->getId() );
 	}
 	
 	function getObjectIt()
 	{
		return $this->object_it;
 	}
 	
 	function setTabIndex( $index )
 	{
 	    $this->tabindex = $index;    
 	}
 	
 	function setReadonly( $readonly )
 	{
 		$this->readonly = $readonly;
 	}
 	
 	function getReadonly()
 	{
 		return $this->readonly;
 	}
 	
 	function getShowMenu()
 	{
 		return true;
 	}
 	
 	function setFormId( $form_id )
 	{
 		$this->form_id = abs(crc32(uniqid("", true)));
 	}
 	
 	function getFormId()
 	{
 		return $this->form_id;
 	}
 	
 	function setSingleton( $singleton )
 	{
 		$this->singleton = $singleton;
 	}
 	
 	function getAnchorField()
 	{
 		$this->anchor_field;
 	}
 	
 	function setFormField( $name )
 	{
 		$this->form_field = $name;
 	}
 	
 	function getFormField()
 	{
 		return $this->form_field;
 	}
 	
 	function getAttributes()
 	{
		$names = array_keys($this->object->getAttributes());
		$visible = array();

		for ( $i = 0; $i < count($names); $i++ ) {
			if ( !$this->object->IsAttributeVisible($names[$i]) && !$this->object->IsAttributeStored($names[$i]) ) continue;
            $visible[] = $names[$i];
		}
		
		return $visible;
 	}
 	
 	function getAttributeObject( $attr )
 	{
		return $this->object->getAttributeObject($attr);
 	}
 	
 	function & getIteratorRef()
 	{
 		return $this->iterator;
 	}

	function getRowStyle()
	{
	}
	
	function getItemDisplayName( $object_it )
	{
		return $object_it->getDisplayName();
	}

 	function getItemVisibility( $object_it )
	{
		return true;
	}
	
	function getSaveCallback()
	{
		return '';
	}
	
	function getFieldValue( $attr )
	{
	    if ( $attr == $this->anchor_field && is_object($this->getObjectIt()) ) {
	        return $this->getObjectIt()->getId();
        }

		$value = $_REQUEST[$this->getFieldName( $attr )];
        if ( $value != '' ) return $value;

        $value = $_REQUEST[$attr];
        if ( $value != '' ) return $value;

		return $this->object->getDefaultAttributeValue($attr);
	}
	
	function createField( $attr )
	{
		$object = $this->getAttributeObject( $attr );
		
		$field =
            is_object($object->entity) && $object->entity->get('IsDictionary') == 'Y'
                ? new FieldDictionary( $object )
                : new FieldAutoCompleteObject( $object );

		return $field;
	}
	
	function getHeaderMessage()
	{
		return '';
	}
	
	function getPrefix()
	{
		return 'F'.$this->form_id.'_';
	}
	
	function getFieldName( $attr )
	{
		if ( $this->singleton )
		{
			return $this->getPrefix().$attr.$this->form_id;
		}
		else
		{
			return $this->getPrefix().$attr;
		}
	}

 	function getRowId( $attr )
	{
		return 'fieldRow'.$attr.$this->form_id;
	}
	
	function getFieldDescription( $attr )
	{
		return "";
	}
	
	function drawFieldTitle( $attr )
	{
		echo '<div>';
			echo translate($this->object->getAttributeUserName($attr));
		echo '</div>';
	}
	
	function drawField( $attr, $type, $value, $tabindex )
	{
	    echo '<div id="'.$this->getRowId($attr).'" style="padding-top:3px;">';
	    
	    if ( $type != 'char' )
	    {
	    	$this->drawFieldTitle( $attr );
	    }
		
		$field_name = $this->getFieldName( $attr );
		
		$class_name = in_array($type, array('integer', 'float', 'date', 'datetime'), true)
		    ? 'formvalueholder formvalue-short' : 'formvalueholder formvalue-long';
		
		echo '<div class="row-fluid '.$class_name.'">';
		
		if ( $this->IsAttributeObject( $attr ) )
		{
			$field = $this->createField( $attr );

			$field->setName( $field_name );
			$field->setId( $field_name );
			$field->setValue( $value );
			$field->setDefault( $value != '' ? $value : $this->object->getDefaultAttributeValue($attr) );
			$field->setTabIndex( $tabindex );

			$field->draw();
		}
		else
		{
			switch ( $type )
			{
				case 'date':
				case 'datetime':
					echo '<input class="input-medium datepickerform" type="text" id="'.$field_name.'" name="'.$field_name.'" default="'.$value.'" tabindex="'.$tabindex.'">';
					break;
					
				case 'float':
				case 'integer':
					echo '<input class="input-medium" type="text" id="'.$field_name.'" name="'.$field_name.'" value="'.$value.'" tabindex="'.$tabindex.'">';
					break;
	
				case 'varchar':
					echo '<input class="input-block-level" type="text" id="'.$field_name.'" name="'.$field_name.'" value="'.$value.'" tabindex="'.$tabindex.'">';
					break;
				
				case 'password':
					echo '<input class="input-block-level" type="password" id="'.$field_name.'" name="'.$field_name.'" value="'.$value.'" tabindex="'.$tabindex.'">';
					break;
					
				case 'char':
					echo '<label class="checkbox"><input class=checkbox type="checkbox" id="'.$field_name.'" name="'.$field_name.'" '.($value == 'Y' ? 'checked' : '').' tabindex="'.$tabindex.'">'.translate($this->object->getAttributeUserName($attr)).'</label>';
					break;
				
				case 'file':
					echo '<input class="input-block-level" id="'.$field_name.'" type="file" name="'.$field_name.'" value="'.$value.'" tabindex="'.$tabindex.'">';
					echo '<input type="hidden" name="MAX_FILE_SIZE" value="'.EnvironmentSettings::getMaxFileSize().'">';
					break;
	
				case 'text':
					echo '<textarea class="input-block-level" id="'.$field_name.'" name="'.$field_name.'" rows=3 tabindex="'.$tabindex.'">'.$value.'</textarea>';
					break;
	
				default:
					$field = $this->createField( $attr );
	
					$field->setName( $field_name );
					$field->setId( $field_name );
					$field->setValue( $value );
					$field->setTabIndex( $tabindex );
					$field->setDefault( $this->object->getDefaultAttributeValue($attr) );

					if ( $field instanceof FieldWYSIWYG and $field->hasBorder() ) {
						echo '<div class="well well-wysiwyg">';
                            $field->setRows(2);
							$field->draw();
						echo '</div>';
					}
					else {
						$field->draw();
					}
			}
		}
		
		$description = $this->getFieldDescription($attr);
		
		if ( $description != '' )
		{
			echo '<div class="line small_grey">';
				echo $description;
			echo '</div>';
		}
		else {
            echo '<div class="line"></div>';
        }
		echo '</div>';
		
		echo '</div>';
	}
	
 	function draw( $view = null )
 	{
 		global $tabindex;

        $this->extendModel();

        $prefix = $this->getPrefix();
		$names = $this->getAttributes();
		$html = '';

		// prepare form for new records
		$fields = array();
		$required = array();
		$focused_field = '';
		
		echo '<div class="embedded_form form-inline">';
		
		$header_message = $this->getHeaderMessage();
		if ( !$this->getReadonly() && $header_message != '' )
		{
			echo '<div class="line" style="border-bottom:1px solid silver;padding-bottom:6px;margin-bottom:6px;">';
				echo $header_message;
			echo '</div>';
		}
		
		for ( $i = 0; $i < count($names); $i++ )
		{
			$type = strtolower($this->object->getAttributeType($names[$i]));
			if ( $this->object->IsReference( $names[$i] ) )
			{
				$type = 'object';
			}
			
			if ( $this->singleton )
			{
				$field_name = $prefix.$names[$i].$this->form_id;
			}
			else
			{
				$field_name = $prefix.$names[$i];
			}
			
			array_push($fields, $prefix.$names[$i]);
			$value = $this->getFieldValue($names[$i]);
			
			$tabindex = 100 + $i;

			if ( !$this->IsAttributeVisible( $names[$i] ) )
			{
				$html .= '<input id="'.$field_name.'" name="'.$field_name.'" type="hidden" value="'.$value.'" tabindex="'.$tabindex.'">';
				continue;
			}
			
			ob_start();
			$this->drawField( $names[$i], $type, $value, $tabindex );

			$html .= ob_get_contents();
			ob_end_clean();
			
			if ( $focused_field == '' )
			{
				$focused_field = $field_name;
			}
			
			if ( $this->object->IsAttributeRequired($names[$i]) )
			{
				array_push($required, $field_name);
			}
		}

		if ( !$this->singleton )
		{
			$tabindex++;
			
			$callback = $this->getSaveCallback() != ''
				? 'function(formid,data,row_number) { if ( typeof '.$this->getSaveCallback().' == \'function\' ) {'.$this->getSaveCallback().'(formid,data,row_number);}}' : 'function(formid,data,row_number) {}';
			 
			$html .= '<div class="embedded_footer clearfix">';
				$html .= '<input class="btn btn-primary btn-sm" tabindex="'.$tabindex.'" id="saveEmbedded'.$this->form_id.'" style="float:left;" action="save" type="button" value="'.translate('Добавить').'" '.
					'onclick="javascript: saveEmbeddedItem(\''.$this->form_id.'\', [\''.join("','", $fields).'\'], [\''.join("','", $required).'\'], '.$callback.')">';
	
			$tabindex++;
			
				$html .= ' <input class="btn btn-link btn-sm" tabindex="'.$tabindex.'" id="closeEmbedded'.$this->form_id.'" style="float:left;" action="cancel" type="button" value="'.translate('Отменить').'" ' .
					'onclick="javascript: closeEmbeddedForm('.$this->form_id.')">';
			$html .= '</div>';
		}
		else
		{
			echo '<script type="text/javascript">';
				echo " $(document).ready(function()	{ registerFormValidator( $('embeddedForm".$this->form_id."').parents('form').attr('id'), " .
						" function () { return validateEmbedded(".$this->form_id.", ['".join("','", $required)."']) }); }); ";
			echo '</script>';
		}

		$project = is_object($this->getObjectIt())
            ? $this->getObjectIt()->get('ProjectCodeName')
            : getSession()->getProjectIt()->get('CodeName');

		$anchor_credentials = '<input type="hidden" name="embedded'.$this->form_id.'" value="'.strtolower(get_class($this->object)).'">'.
			 '<input type="hidden" name="embeddedFields'.$this->form_id.'" value="'.join(',', $names).'">'.
			 '<input type="hidden" name="embeddedPrefix'.$this->form_id.'" value="'.$prefix.'">'.
			 '<input type="hidden" id="embeddedProject'.$this->form_id.'" value="'.$project.'">'.
             '<input type="hidden" name="embeddedFieldName'.$this->form_id.'" value="'.$this->getFormField().'">';

		if ( is_object($this->object_it) && $this->object_it->count() > 0 )
		{
			 $anchor_credentials .= '<input type="hidden" name="anchorObject'.$this->form_id.'" value="'.$this->object_it->getId().'">'.
			 	'<input type="hidden" name="anchorClass'.$this->form_id.'" value="'.get_class($this->object_it->object).'">'.
			 	'<input type="hidden" name="embeddedAnchor'.$this->form_id.'" value="'.$this->anchor_field.'">';
			 	
			 echo '<input type="hidden" id="embeddedMode'.$this->form_id.'" value="standalone">';
		}

		echo '<input type="hidden" id="embedded'.$this->form_id.'" value="'.strtolower(get_class($this->object)).'">';

		echo '<div id="embeddedForm'.$this->form_id.'" '.($this->singleton ? '' : 'multiple="true"').' style="display:'.($this->singleton ? 'block' : 'none').';">';
			echo $anchor_credentials;
			echo $html;
		echo '</div>';

		echo '<div id="embeddedTemplates'.$this->form_id.'" style="display:none;">';
			echo '<div class="embeddedRowTemplate">';
				echo '<div class="embeddedRow">';
					echo '<div class="embeddedRowTitle" style="margin-bottom:6px;">';
    					if ( is_object($view) && $this->getShowMenu() )
    					{
    					    $actions = array( array( 
    					        'url' => 'javascript:;', 
    					        'name' => translate('Удалить'),
    					        'uid' => 'delete'
    					    ));
    					    
        					echo $view->render('core/EmbeddedRowTitleMenu.php', array (
        					    'title' => '%title%',
    					        'items' => $actions,
        						'position' => 'last'
        					));
    					}
    					else
    					{
    					    echo '<div class="btn-group last">';
    					        echo '<div class="btn dropdown-toggle transparent-btn">';
    					            echo '<span class="title"></span>';
    					        echo '</div>';
    					    echo '</div>';
    					}
					echo '</div>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
		
		if ( $this->singleton )
		{
				echo '<input type="hidden" name="embeddedAnchor'.$this->form_id.'" value="'.$this->anchor_field.'">';
				echo '<input type="hidden" id="embeddedActive'.$this->form_id.'" name="embeddedActive'.$this->form_id.'" value="'.($this->getFieldValue('FormActive') == 'N' ? 'N' : 'Y').'">';
				echo '<input type="hidden" id="embeddedItemsCount'.$this->form_id.'" name="embeddedItemsCount'.$this->form_id.'" value="1">';
				echo '<input type="hidden" id="'.$this->form_id.'Id" name="'.$prefix.'Id'.$this->form_id.'" value="'.(is_object($this->getObjectIt()) ? $this->getObjectIt()->getId() : "").'">';
				
	 		echo '<div style="clear:both"></div>';
	 		echo '</div>';
		}
		else
		{
			echo '<div class="embdded-items-list" id="embeddedList'.$this->form_id.'">';
				echo '<div>';
							
					echo $anchor_credentials;
					echo '<input type="hidden" name="embeddedAnchor'.$this->form_id.'" value="'.$this->anchor_field.'">';
	
					// draw exist embedded items
					echo '<div id="embeddedItems'.$this->form_id.'" style="position:relative;">';
					
					$object_it = $this->getIteratorRef();
					
					$item = 100;
					$items_count = 0;
					
					while ( !$object_it->end() )
					{
						if ( !$this->getItemVisibility($object_it) )
						{
							$object_it->moveNext();
							continue;
						}
						
						$display_name = $this->getItemDisplayName($object_it);
						
						if ( $_REQUEST[$prefix.'Delete'.$item] == 1 )
						{
							$delete_value = 1;
						}
						else
						{
							$delete_value = 0;
						}
						
						echo '<input type="hidden" id="'.$this->form_id.'Id'.$item.'" name="'.$prefix.'Id'.$item.'" value="'.$object_it->getId().'">';
						echo '<input type="hidden" id="'.$this->form_id.'Delete'.$item.'" name="'.$prefix.'Delete'.$item.'" value="'.$delete_value.'">';
						
						echo '<div class="embeddedRow" style="'.$this->getRowStyle().'" id="'.$this->form_id.'Caption'.$item.'">';
						
							if ( $delete_value == 1 )
							{
								$display_name = '<strike>'.$display_name.'</strike>';
							}
							
							echo '<div class="embeddedRowTitle">';

							    $actions = $this->getActions($object_it, $item);

    							if ( is_object($view) && $this->getShowMenu() && !$this->readonly && $delete_value == 0 && count($actions) > 0 )
    							{
    								echo $view->render($this->getTitleTemplate(), array (
    								    'title' => $display_name,
    								    'items' => $actions,
    									'position' => 'last'
    								));
    							}
    							else
    							{
    							    echo '<div class="btn-group last">';
    							        echo '<div class="btn dropdown-toggle transparent-btn">';
    							            echo $display_name;
    							        echo '</div>';
    							    echo "</div>";
    							}
								
							echo '</div>';
							
						echo '</div>';
						
						$item++;
						
						$items_count++;
						
						$object_it->moveNext();
					}
	
					// add new items that were not saved because of 
					// missing required fields on the main form
					// 
					while( true )
					{ 
						if ( $_REQUEST[$fields[0].$item] == '' ) break; // no more items
							
						for ( $j = 0; $j < count($fields); $j++ )
						{
							$field_name = $fields[$j].$item;
							
							if ( $_REQUEST[$field_name] != '' )
							{
								echo '<input type="hidden" name="'.$field_name.'" value="'.htmlentities($_REQUEST[$field_name], ENT_QUOTES | ENT_HTML401, APP_ENCODING).'">';
							}
						}
						
						for ( $j = 0; $j < count($names); $j++ )
						{
							$field_name = $prefix.$names[$j].'Tmp'.$i;
							
							if ( $_REQUEST[$field_name] != '' && $this->object->getAttributeType($names[$j]) == 'file' )
							{
								echo '<input type="hidden" name="'.$field_name.'" value="'.htmlentities($_REQUEST[$field_name], ENT_QUOTES | ENT_HTML401, APP_ENCODING).'">';
							}
						}
						
						$caption_id = htmlentities($_REQUEST['embedded'.$this->form_id.'Caption'.$i]);
								 
						echo '<div class="embeddedRow" style="'.$this->getRowStyle().'">';
							echo $caption_id;
							echo ' &nbsp; <a style="cursor:hand;" onclick="javascript: deleteEmbeddedItem(\''.$this->form_id.'\', \''.$i.'\');">' .
								'<img border=0 src="/images/cut.png"></a>';
								
							echo '<input type="hidden" name="embedded'.$this->form_id.'Caption'.$i.'" value="'.$caption_id.'">';
						echo '</div>';
							
						$item++;
						
						$items_count++;
					}

					echo '</div>';

					echo '<input type="hidden" id="embeddedItemsCount'.$this->form_id.'" name="embeddedItemsCount'.$this->form_id.'" value="'.$items_count.'">';
                    echo '<div class="end-of-list"></div>';
				echo '</div>';
			echo '</div>';

			echo '<div>';
    			$this->drawAddButton( $view, $this->tabindex );
            echo '</div>';

	 		echo '</div>';
		}
 	}
 	
 	function getNoItemsMessage()
 	{
 		return '';
 	}
 	
 	function setAddButtonText( $text )
 	{
 		$this->button_text = $text;
 	}
 	
 	function getAddButtonText()
 	{
 		return $this->button_text;
 	}
 	
 	function drawAddButton( $view, $tabindex )
 	{
        if ( !$this->getReadonly() && getFactory()->getAccessPolicy()->can_create($this->getObject()) )
        {
            echo '<a class="dashed embedded-add-button" tabindex="'.$tabindex.'" href="javascript: appendEmbeddedItem('.
                $this->getFormId().');">'.$this->getAddButtonText().'</a>';
        }
 	}
 	
 	function process( $object_it, $e, $process_record_callback = null )
 	{
		global $model_factory;

		$_REQUEST['RecordVersion'] = '';
		
        $embedded = $this->object;
        $embedded->setVpdContext( $object_it );

        $fields = preg_split('/,/', $_REQUEST['embeddedFields'.$e]);
        $anchor_field = $_REQUEST['embeddedAnchor'.$e];
        $active_form = $_REQUEST['embeddedActive'.$e] != 'N';
        $prefix = $_REQUEST['embeddedPrefix'.$e];

        if ( !$active_form || $object_it->getId() < 1 ) return;

        foreach( $_REQUEST as $key => $value )
        {
            if( !preg_match('/'.$prefix.'Delete([\d]+)/', $key, $matches) || $value != '1' ) continue;

            $id_field = $prefix.'Id'.$matches[1];

            $delete_it = $embedded->getExact($_REQUEST[$id_field]);

            if ( $delete_it->getId() != '' && getFactory()->getAccessPolicy()->can_delete($delete_it) )
            {
                $delete_it->delete();
            }

            unset($_REQUEST[$id_field]);
        }

        foreach( $_REQUEST as $key => $value )
        {
            if( !preg_match('/'.$prefix.'Id([\d]+)/', $key, $matches) ) continue;

            $i = $matches[1];

            $field_id = $prefix.'Id'.$i;

            $parms = array();

            foreach ( $fields as $field )
            {
                $field_name = $prefix.$field.$i;

                if ( $_REQUEST[$field_name] == '' ) continue;
                if ( $embedded->getAttributeType($field) == 'file' )
                {
                    $tmp_file = $model_factory->getObject('cms_TempFile');
                    $tmp_field_name = $prefix.$field.'Tmp'.$i;

                    $file_it = $tmp_file->getByRef('Caption', $_REQUEST[$tmp_field_name]);

                    if ( $file_it->count() > 0 )
                    {
                        $_FILES[$field]['tmp_name'] = $file_it->getFilePath('File');
                        $_FILES[$field]['name'] = $file_it->get('FileName');
                        $_FILES[$field]['type'] = $file_it->get('MimeType');

                        $parms[$field] = 'file';
                    }
                    else
                    {
                    }
                }
                else
                {
                    $parms[$field] = $_REQUEST[$field_name];
                }
            }

            $mapper = new ModelDataTypeMapper();
            $mapper->map( $embedded, $parms );

            if ( $_REQUEST[$field_id] > 0 )
            {
                $embedded_it = $embedded->getExact($_REQUEST[$field_id]);

                if ( $embedded_it->getId() < 1 ) continue;
                if ( !getFactory()->getAccessPolicy()->can_modify($embedded_it) ) continue;

                if ( is_callable($process_record_callback, true, $how_to_call) )
                {
                    if ( $process_record_callback( $embedded, $field_id, $anchor_field, $prefix, $i ) ) continue;
                }

                if ( count($parms) > 0 )
                {
                    $parms[$anchor_field] = $object_it->getId();

                    // check for required fields
                    $keys = array_keys($embedded->getAttributes());

                    foreach ( $keys as $key )
                    {
                        $check_tobe_required =
                                $embedded->IsAttributeRequired( $key )
                                && $embedded->getAttributeType( $key ) != 'file';

                        if ( $check_tobe_required && $parms[$key] == '' ) {
                            $parms[$key] = $embedded->getDefaultAttributeValue( $key );
                            if ( $parms[$key] == '' ) $parms[$key] = $_REQUEST[$key];
                        }
                    }

                    $embedded->modify_parms($_REQUEST[$field_id], $parms);

                    $item_it = $embedded->getExact($_REQUEST[$field_id]);

                    $this->processAdded( $item_it );
                }
            }
            else if ( count($parms) > 0 && getFactory()->getAccessPolicy()->can_create($embedded) )
            {
                if ( $parms[$anchor_field] == '' ) {
                    $parms[$anchor_field] = $object_it->getId();
                }

                // check for required fields
                $keys = array_keys($embedded->getAttributes());
                $was_errors = false;

                foreach ( $keys as $key )
                {
                    if ( $embedded->getAttributeType( $key ) == 'file' && $parms[$key] != 'file' ) {
                        $this->logError('File wasn\'t uploaded for the "'.get_class($embedded).'" entity');
                        $was_errors = true;
                        break;
                    }

                    $check_tobe_required = $embedded->IsAttributeRequired( $key );
                    if ( $check_tobe_required && $parms[$key] == '' )
                    {
                        $parms[$key] = $embedded->getDefaultAttributeValue( $key );
                        if ( $parms[$key] == '' ) {
                            $this->logError('Attribute "'.$key.'" of the "'.get_class($embedded).'" entity is required but empty');
                            $was_errors = true;
                            break;
                        }
                    }
                }

                if ( !$was_errors ) {
                    $this->processAdded( $embedded->getExact( $embedded->add_parms( $parms ) ) );
                } else {
                    $this->logError('Embedded item skipped because of errors found');
                }
            }
        }
	}

	protected function logError( $message )
	{
        $logger = \Logger::getLogger('System');
        if ( !is_object($logger) ) return;
        $logger->error($message);
	}

 	function processAdded( $object_it )
 	{ 
 	}
 	
 	function getActions( $object_it, $item )
 	{
 	    if ( !getFactory()->getAccessPolicy()->can_delete($object_it) ) return array();

 	    $script = 'javascript: deleteEmbeddedItem(\''.$this->form_id.'\', \''.$item.'\');';
 	    
 	    if ( $_REQUEST['formonly'] != '' )
 	    {
     		return array(
    			array( 'click' => $script,
    				   'name' => translate('Удалить'),
    			       'uid' => 'delete' )
    		);
 	    }
 	    else
 	    {
     		return array(
    			array( 'url' => $script,
    				   'name' => translate('Удалить'),
    			       'uid' => 'delete' )
    		);
 	    }
	}

	function getTitleTemplate() {
 	    return 'core/EmbeddedRowTitleMenu.php';
    }
 }
