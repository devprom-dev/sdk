<?php

include_once SERVER_ROOT_PATH."core/views/BulkFormBase.php";
include_once SERVER_ROOT_PATH."pm/classes/workflow/WorkflowTransitionAttributesModelBuilder.php";
include_once SERVER_ROOT_PATH."pm/views/watchers/FieldWatchers.php";

class BulkForm extends BulkFormBase
{
 	function getCommandClass()
 	{
		return 'bulkcompleteproject';
 	}
	
	function getName( $attribute )
	{
		switch ( $attribute )
		{
			case 'TransitionComment':
				return translate('Комментарий'); 	
				
 			case 'Watchers':
 				return translate('Наблюдатели');
				
			default:
				return parent::getName( $attribute );
		}
	}

	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'TransitionComment':
				return 'wysiwyg';

			case 'Watchers':
				return 'custom';
				
			default:
				if ( $this->getObject()->IsReference($attribute) && $this->getObject()->getAttributeObject($attribute) instanceof PMCustomDictionary )
				{
					return 'custom';
				}
				
				return parent::getAttributeType( $attribute );
		}
	}

	function getActionAttributes()
	{
		$match = preg_match('/Transition(.+)/mi', $_REQUEST['operation'], $attributes);
		if ( $match )
		{
			$object = $this->getIt()->object;
			$_REQUEST['Transition'] = trim($attributes[1]);

			$system_attributes =
					array_merge(
							$object->getAttributesByGroup('system'),
							$object->getAttributesByGroup('nonbulk')
					);

			$model_builder = new WorkflowTransitionAttributesModelBuilder(
					getFactory()->getObject('Transition')->getExact( trim($attributes[1]) )
			);
			
		    $model_builder->build($object);
		    
		    $ref_names = array();
			foreach( $object->getAttributes() as $attribute => $data )
			{
				if ( in_array($attribute, $system_attributes) ) continue;
				if ( !$this->IsAttributeVisible($attribute) ) continue;
				
				$ref_names[] = $attribute;
			}
		    return $ref_names;
		}
		
		return parent::getActionAttributes();
	}
	
	function IsAttributeModifiable( $attr )
	{
	    switch ( $attr )
	    {
	        case 'State':
	            return false;

	        case 'Watchers':
	            return true;
	    }
	    
	    return parent::IsAttributeModifiable( $attr );
	}

	function drawCustomAttribute( $attribute, $value, $tab_index )
	{
		switch ( $attribute ) 
		{
 			case 'Watchers':
				$field = new FieldWatchers( $this->getObject() );
				$field->SetId($attribute);
				$field->SetName('value');
				$field->SetValue($value);
				$field->SetTabIndex($tab_index);
				
				echo $this->getName($attribute);
				echo '<span id="'.$field->getId().'" class="input-block-level well well-text" style="width:100%;height:auto;">';
				    $field->draw();
				echo '</span>';
				break;
				
			default:
				if ( $this->getObject()->IsReference($attribute) )
				{
					$ref_object = $this->getObject()->getAttributeObject($attribute);
					
					if ( $ref_object instanceof PMCustomDictionary )
					{
						$field = new FieldCustomDictionary($this->getObject(), $attribute);
	
						$field->SetId($attribute);
						$field->SetName($attribute);
						$field->SetValue($value);
						$field->SetTabIndex($tab_index);
						
						echo $this->getName($attribute);
						$field->draw();
						
						return;
					}
				}

				if ( $this->getObject()->getAttributeType($attribute) == 'wysiwyg' )
				{
					$field = new FieldWYSIWYG();
					$field->setObject($this->getObject());
					$editor = $field->getEditor();
					$editor->setMode( WIKI_MODE_MINIMAL );
					$field->setCssClassName( 'wysiwyg-text' );

					$field->SetId($attribute);
					$field->SetName($attribute);
					$field->SetTabIndex($tab_index);
					$field->draw();
					return;
				}

				parent::drawCustomAttribute( $attribute, $value, $tab_index );
		}
	}

	function getHint()
	{
		switch( $this->getMethod() ) {
			case 'BulkDeleteWebMethod':
				return preg_replace('/%1/', getFactory()->getObject('PMReport')->getExact('project-log')->getUrl(), text(2210));
		}
		return parent::getHint();
	}
}