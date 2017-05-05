<?php

include_once "FormAsync.php";
		
class BulkFormBase extends AjaxForm
{
 	function getCommandClass()
 	{
		return 'bulkcomplete';
 	}

	function getAttributes()
	{
		$attributes = $this->getActionAttributes();
		$this->visibleAttributes = $attributes;
		return array_merge( array('operation'), $attributes, array('ids') );
	}
	
	function getName( $attribute )
	{
		switch ( $attribute )
		{
			case 'ids':
				return ''; 	

			case 'operation':
				return translate('Действие');

			default:
				return parent::getName( $attribute );
		}
	}

	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'ids':
				return 'custom'; 	

			case 'operation':
				return 'custom';

			default:
				if ( is_object($this->getForm()) ) return 'custom';
				return parent::getAttributeType( $attribute );
		}
	}

	function getAttributeValue( $attribute )
	{
		switch ( $attribute )
		{
			case 'ids':
				return join(',',$this->getIt()->idsToArray());

			case 'operation':
				return htmlentities($_REQUEST['operation']);

			default:
				return htmlentities($_REQUEST[$attribute]);
		}
	}
	
 	function getDescription( $attr )
 	{
 		switch ( $attr )
 		{
 			case 'operation': return ' ';
 			case 'ids': return '';
 			
 			default:
 			    $text = parent::getDescription( $attr );
                if ( $this->getAttributeType($attr) == 'char' ) return $text;
 				return $text.' ';
 		}
 	}
	
	function getForm()
	{
		if ( !is_object($this->form) ) {
			$this->form = $this->buildForm();
			if ( is_object($this->form) ) {
				$this->form->setObjectIt($this->getIt());
                $this->form->getRenderParms();
			}
		}
		return $this->form;
	}

	protected function buildForm() {
		return null;
	}
	
	function getActionAttributes()
	{
		$attributes = array();

		$match = preg_match('/Attribute(.+)/mi', $_REQUEST['operation'], $attributes);
		if ( $match ) {
			$attributes = preg_split('/:/', $attributes[1]);
			$this->object->setAttributeVisible(array_shift(array_values($attributes)), true);
			return $attributes;
		}
		
		$match = preg_match('/Method:(.+)/mi', $_REQUEST['operation'], $attributes);
		if ( $match )
		{
			$parms = preg_split('/:/', $attributes[1]);

			$method = $parms[0]; 
			array_shift($parms);
			
			$attrs = array();
			if ( count($parms) > 0 )
			{
				foreach( $parms as $parm )
				{
					$pair = preg_split('/=/', $parm);
					$attrs[$pair[0]] = $pair[1];
				}
			}
			
			$attributes = array();
			foreach( $attrs as $attribute => $value )
			{
				if ( $value != '' ) continue;
				$attributes[] = $attribute;
			    $this->object->setAttributeVisible( $attribute, true );
			}

			return $attributes;  
		}
		
		return $attributes;
	}
	
	function IsAttributeRequired( $attribute )
	{
		switch( $attribute )
		{
			case 'ids':
			case 'operation':
				return false;

			default:
				return parent::IsAttributeRequired( $attribute );
		}
	}

	function IsAttributeVisible( $attribute )
	{
		switch( $attribute )
		{
		    case 'operation':
		    	return $this->getAttributeValue($attribute) == '';
			case 'ids':
				return true;
		    default:
		    	return parent::IsAttributeVisible($attribute) || in_array($attribute, $this->visibleAttributes);
		}
	}
	
	function IsAttributeModifiable( $attr )
	{
	    $system_attributes =
				array_merge(
						$this->getObject()->getAttributesByGroup('system'),
						$this->getObject()->getAttributesByGroup('nonbulk')
				);
	    if ( in_array($attr, $system_attributes) ) return false;

	    $type = $this->getObject()->getAttributeType($attr);
	    switch ( $type )
	    {
	        case 'date':
	        case 'datetime':
	            return false;
	    }

	    return $this->getObject()->IsAttributeStored( $attr ) || $this->getObject()->getAttributeOrigin( $attr ) == ORIGIN_CUSTOM;
	}

	function drawCustomAttribute( $attribute, $value, $tab_index, $view )
	{
		switch ( $attribute ) 
		{
			case 'ids':
				$this->drawIds( $value );
				break;
				
			case 'operation':
				$this->drawAction( $value, $tab_index );
				break;
				
			default:
				$form = $this->getForm();
				if ( is_object($form) )
				{
					$field = $form->createFieldObject($attribute);
                    if ( !is_object($field) ) return;

					$field->SetId($attribute);
					$field->SetName($attribute);
					$field->SetValue($value);
					$field->SetTabIndex($tab_index);
					$field->SetRequired($this->getObject()->IsAttributeRequired($attribute));
					
					if ( $this->showAttributeCaption() ) {
						echo translate($this->getObject()->getAttributeUserName($attribute));
					}
					
					if ( is_a($field, 'FieldForm') )
					{
						echo '<span id="'.$field->getId().'" class="input-block-level well well-text" style="width:100%;height:auto;">';
							$field->render($this->getView());
						echo '</span>';
					}
					else {
						$field->render($this->getView());
					}
				}
				else {
					parent::drawCustomAttribute( $attribute, $value, $tab_index, $view );
				}
		}
	}
	
	function getIt()
	{
	    global $_REQUEST;

	    if ( is_object($this->it) ) return $this->it->object->createCachedIterator($this->it->getRowset());

	    $object = $this->getObject();

	    $this->it = $object->getExact($this->getIds());
	     
	    return $this->it->object->createCachedIterator($this->it->getRowset());    
	}

	function getIds() {
		return array_unique(preg_split('/-/', trim($_REQUEST['ids'], '-')));
	}
	
	function drawIds( $value )
	{
		global $_SERVER;
		
		$uid = new ObjectUID;
		$object = $this->getObject();

		$it = $this->getIt();

        echo '<br/>';
		echo '<label><b>'.text(1303).'</b></label>';
		
		echo '<input type="hidden" name="ids" value="'.$value.'">';
		echo '<input type="hidden" name="object" value="'.strtolower(get_class($object)).'">';
			
		$it->moveFirst();
		while ( !$it->end() && $it->getPos() < 7 )
		{
			echo '<div class="line">';
				$uid->drawUidInCaption($it);
			echo '</div>';

			$it->moveNext();
		}
		
		if ( $it->getPos() + 1 < $it->count() )
		{
			echo '<div class="line">';
				echo str_replace('%1', $it->count() - $it->getPos(), text(1064));
			echo '</div>';
		}
		
		echo '<br/>';
	}
	
	function drawAction( $value, $tab_index )
	{
		echo '<input type="hidden" name="operation" value="'.$value.'">';
	}

	protected function showAttributeCaption()
	{
		return !preg_match('/Attribute(.+)/mi', $this->getAttributeValue('operation'), $match);		
	}

	function getMethod() {
		preg_match('/Method:(.+)/mi', $_REQUEST['operation'], $attributes);
		return array_shift(preg_split('/:/', $attributes[1]));
	}

	function getHintId() {
		return $this->getMethod();
	}
	
	private $form = null;
	private $visibleAttributes = array();
}