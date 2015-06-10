<?php

include "CustomAttributeEntityField.php";
include "CustomAttributeTypeClassNameField.php";

class CustomAttributeEntityForm extends PMForm
{
 	function getAddCaption()
 	{
		return text(1079);
 	}
 	
 	function getCommandClass()
 	{
		return 'customattributeprocess';
 	}

	function getAttributeType( $attribute )
	{
		switch( $attribute )
		{
		    case 'AttributeType':
		    	return parent::getAttributeType($attribute);
		    	
		    default:
		    	return 'custom';
		}
	}

	function getAttributeClass( $attribute )
	{
		switch ( $attribute )
		{
			default:
				return parent::getAttributeClass( $attribute );
		}
	}
	
	function IsAttributeRequired( $attribute )
	{
		switch( $attribute )
		{
			case 'EntityReferenceName':
			case 'AttributeType':
				return true;
				
			default:
				return false;
		}
	}

	function IsAttributeVisible( $attribute )
	{
		switch( $attribute )
		{
			case 'EntityReferenceName':
			case 'AttributeType':
			case 'AttributeTypeClassName':
				return true;
				
			default:
				return false;
		}
	}

	function getButtonText()
	{
		return translate('Продолжить');
	}

	function getWidth()
	{
		return '100%';
	}

	function IsCentered()
	{
		return false;
	}
	
	function drawCustomAttribute( $attribute, $value, $tab_index )
	{
		switch ( $attribute ) 
		{
			case 'EntityReferenceName':
			    
			    if ( $value == '' )
			    {
			        $entities = preg_split('/,/', $_REQUEST['customattributeentity']);

			        $value = $entities[0]; 
			    }
			    
				$this->drawEntities( $value, $tab_index );
				
				break;
				
			case 'AttributeTypeClassName':
				
				echo '<div>';
					echo '<label>'.text(1827).'</label>';
					
					$field = new CustomAttributeTypeClassNameField($this->getObject());
					
					$field->setId($attribute);
					$field->setName($attribute);
					$field->setTabIndex($tab_index);
					$field->setNullOption(false);
					
					$field->draw();
				echo '</div>';

				break;
				
			default:
				parent::drawCustomAttribute( $attribute, $value, $tab_index );
		}
	}
	
 	function drawEntities( $value, $tab_index )
	{
		global $model_factory;
		
		$objects = $model_factory->getObject('CustomizableObjectSet');

		$object_it = $objects->getAll();
		
		$keys = array();

		while ( !$object_it->end() )
		{
			$keys[$object_it->getId()] = $object_it->getDisplayName();
			
			$object_it->moveNext();
		}

		asort($keys);
		
		echo '<label>'.$this->getObject()->getAttributeUserName('EntityReferenceName').'</label>';
		
		$field = new CustomAttributeEntityField($this->getObject());
		
		$field->setId('EntityReferenceName');
		$field->setName('EntityReferenceName');
		$field->setValue($this->getAttributeDefault('EntityReferenceName'));
		$field->setNullOption(false);
		
		$field->draw();
		
		$ref_type_it = getFactory()->getObject('PMCustomAttribute')->getAttributeObject('AttributeType')->getByRef('ReferenceName', 'reference');
		
		?>
		<script type="text/javascript">
			$(document).ready( function()
			{
				$('#AttributeTypeClassName').parent().hide();

				window.setTimeout( function() { 
					$('#AttributeTypeText').on("autocompleteselect", function(event,ui)
					{
						if ( !ui.item ) return;
						
						if ( ui.item.label == '<?=$ref_type_it->getDisplayName()?>' ) {
							$('#AttributeTypeClassName').parent().show();						
						} else {
							$('#AttributeTypeClassName').parent().hide();						
						}
					});
				}, 500);
			});
		</script>
		<?php
	}	
}