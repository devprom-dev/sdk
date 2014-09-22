<?php

include_once SERVER_ROOT_PATH."pm/classes/workflow/WorkflowTransitionAttributesModelBuilder.php";

class BulkForm extends PMForm
{
 	function getAddCaption()
 	{
 		if ( $this->lastPage() )
 		{
 		    $required_attributes = $this->getActionAttributes();

			$actions = $this->getBulkActions( $this->getIt() );
	
			$action_title = $actions[$this->getAttributeValue('operation')];

			if ( $action_title == '' && strstr($this->getAttributeValue('operation'), 'BulkDeleteWebMethod') !== false )
			{
				$action_title = translate('Удалить');
			}
			
 		    if ( count($required_attributes) > 0 )
 		    {
 		        return $action_title != '' ? $action_title : text(1063);
 		    }
 		    else
 		    {
 		        return $action_title != '' ? text(1371).': '.$action_title : text(1371);
 		    }
 		}
 		else
 		{
	        return text(495);
 		}
 	}
 	
 	function getRedirectUrl()
 	{
 		if ( !$this->lastPage() )
 		{
 			return urlencode($_REQUEST['redirect']);
 		}
 			
 		return parent::getRedirectUrl();
 	}
 	
 	function lastPage()
 	{
 		global $_REQUEST;
 		return $_REQUEST['operation'] != '';
 	}
 	
 	function getCommandClass()
 	{
 		if ( $this->lastPage() )
 		{
 			return 'bulkcomplete';
 		}
 		else
 		{
 			return 'bulk';
 		}
 	}

	function getAttributes()
	{
 		if ( $this->lastPage() )
 		{
			return array_merge( array('operation'), $this->getActionAttributes(), array('ids') ); 	
 		}
 		else
 		{
			return array('operation', 'ids'); 	
 		}
	}
	
	function getName( $attribute )
	{
		switch ( $attribute )
		{
			case 'ids':
				return ''; 	

			case 'operation':
				return translate('Действие'); 	
				
			case 'TransitionComment':
				return translate('Комментарий'); 	
				
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

			case 'TransitionComment':
				return 'largetext'; 	
				
			default:
				return parent::getAttributeType( $attribute );
		}
	}

	function getAttributeValue( $attribute )
	{
		global $_REQUEST;
		
		switch ( $attribute )
		{
			case 'ids':
				return htmlentities($_REQUEST['ids']); 	

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

 			default:
 				return parent::getDescription( $attr );
 		}
 	}
	

	function _getForm()
	{
		if ( !is_object($this->form) ) $this->form = $this->getForm();
		return $this->form;
	}
	
	function getForm()
	{
		return null;
	}
	
	function getActionAttributes()
	{
		global $_REQUEST, $model_factory;
		
		$attributes = array();
		
		$match = preg_match('/Attribute(.+)/mi', $_REQUEST['operation'], $attributes);
		
		if ( $match ) return array($attributes[1]);
		
		$match = preg_match('/Transition(.+)/mi', $_REQUEST['operation'], $attributes);
		
		if ( $match )
		{
			$model_builder = new WorkflowTransitionAttributesModelBuilder( 
					getFactory()->getObject('Transition')->getExact( trim($attributes[1]) )
			);
			
		    $object_it = $this->getIt();
			
		    $model_builder->build($object_it->object);
		    
		    $ref_names = array();
		    
			foreach( $object_it->object->getAttributes() as $attribute => $data )
			{
				if ( !$object_it->object->IsAttributeVisible($attribute) ) continue;
				
				$ref_names[] = $attribute;
			}
		    
		    return $ref_names;
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
	
	function getBulkActions( $object_it = null )
	{
		global $model_factory;
		
		$actions = array();
		
		if ( !is_object($object_it) ) $object_it = $this->getIt();
		
		// modifiable attributes
		$attributes = $object_it->object->getAttributes();
		
		foreach ( $attributes as $key => $attribute )
		{
		    if ( $key == 'Caption' || $key == 'Description' ) continue;
		    
			if ( !$this->IsAttributeModifiable($key) ) continue;
			
			$actions['Attribute'.$key] = translate('Изменить').': '.
				translate($object_it->object->getAttributeUserName( $key ));
		}
		
		// available transitions
		if ( method_exists ( $object_it, 'getStateIt' ) )
		{
			if ( count($actions) > 0 ) $actions[count($actions).'-'] = '';
			
			$state_it = $object_it->getStateIt();

			$trans_attr = getFactory()->getObject('TransitionAttribute');
			
			$transition_it = $state_it->getTransitionIt();
			
			while ( !$transition_it->end() )
			{
				$method = new TransitionStateMethod( $transition_it, $object_it );
				
				if ( !$method->hasAccess() )
				{
					$transition_it->moveNext();
					
					continue;
				}
				
				$attr_it = $trans_attr->getRegistry()->Query(
						array (
								new FilterAttributePredicate('Transition', $transition_it->getId())
						)
				);
				
				$required_attrs = $attr_it->fieldToArray('ReferenceName');
				
				if ( in_array('Tasks', $required_attrs, true) )
				{
					// skip those transitions where 'Tasks' attribute is defined as required one
					$transition_it->moveNext();
					
					continue;
				}
				
				$actions['Transition'.$transition_it->getId()] = $transition_it->getDisplayName();
					
				$transition_it->moveNext();
			}
		}
		
		return $actions;
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
		    
		    default:
		    	return true;
		}
	}
	
	function IsAttributeModifiable( $attr )
	{
	    $system_attributes = $this->getObject()->getAttributesByGroup('system');

	    if ( in_array($attr, $system_attributes) )
	    {
	        return false;
	    }
	    
	    switch ( $attr )
	    {
	        case 'State':
	            return false;
	    }
	    
	    $type = $this->getObject()->getAttributeType($attr);
	    
	    switch ( $type )
	    {
	        case 'date':
	        case 'datetime':
	            return false;
	    }

	    return $this->getObject()->IsAttributeStored( $attr ) || $this->getObject()->getAttributeOrigin( $attr ) == ORIGIN_CUSTOM;
	}

	function getButtonText()
	{
 		if ( $this->lastPage() )
 		{
			return translate('Завершить');
 		}
 		else
 		{
			return translate('Продолжить');
 		}
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
			case 'ids':
				$this->drawIds( $value );
				break;
				
			case 'operation':
				$this->drawAction( $value, $tab_index );
				break;
				
			default:
				parent::drawCustomAttribute( $attribute, $value, $tab_index );
		}
	}
	
	function getIt()
	{
	    global $_REQUEST;

	    if ( is_object($this->it) ) return $this->it->object->createCachedIterator($this->it->getRowset());
	    
	    $object = $this->getObject();

	    $this->it = $object->getExact(preg_split('/-/', trim($_REQUEST['ids'], '-')));
	     
	    return $this->it->object->createCachedIterator($this->it->getRowset());    
	}
	
	function drawIds( $value )
	{
		global $_SERVER;
		
		$uid = new ObjectUID;
		$object = $this->getObject();
		
		$it = $this->getIt();
	
		echo '<label>'.text(1303).'</label>';
		
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
		$actions = array();
		
		$it = $this->getIt();

		$ids = $it->idsToArray();
		
		asort($ids);
		
		$delete_method = 'Method:BulkDeleteWebMethod:class='.strtolower(get_class($it->object)).':objects='.join($ids,'-');
		
		$it->moveFirst();
		
		while ( !$it->end() )
		{
			$item_actions = $this->getBulkActions( $it );

			if ( getFactory()->getAccessPolicy()->can_delete($it) )
			{
				if ( count($item_actions) > 0 ) $item_actions[count($item_actions).'-'] = '';
				
				$item_actions[$delete_method] = translate('Удалить');
			}
			
			if ( count($actions) < 1 ) 
			{
				$actions = $item_actions; 
			}
			else
			{
				$actions = array_intersect( $actions, $item_actions ); 
			}
			
			$it->moveNext();
		}

		echo '<div class="line">';
			echo '<select name="operation" style="width:100%" '.($this->lastPage() ? 'disabled' : '').' >';
			foreach( $actions as $key => $action )
			{
				echo '<option value="'.$key.'" '.($value == $key ? 'selected' : '').' >'.$action.'</option>';
			}
			echo '</select>';
			if ( $this->lastPage() )
			{
				echo '<input type="hidden" name="operation" value="'.$value.'">';
			}
		echo '</div>';
	}
}