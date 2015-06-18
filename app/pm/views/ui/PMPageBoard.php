<?php

class PMPageBoard extends PageBoard
{
    function PMPageBoard( $object )
    {
        parent::PageBoard( $object );
    }
    
	function getGroupFields()
	{
	    return array_diff(parent::getGroupFields(), $this->getObject()->getAttributesByGroup('trace')); 
	}
	
	function getColumnFields()
	{
		return array_merge(parent::getColumnFields(), $this->getObject()->getAttributesByGroup('workflow'));
	}
	
	function hasCommonStates()
	{
 		$classname = $this->getBoardAttributeClassName();
 		if ( $classname == '' ) return false;
 		
 		$value_it = getFactory()->getObject($classname)->getRegistry()->Query(
 				array (
 						new FilterVpdPredicate()
 				)
 		);
 		
 		$values = array();
 		while( !$value_it->end() )
 		{
 			$values[$value_it->get('VPD')][] = $value_it->get('Caption');
 			$value_it->moveNext();
 		}
 		
 		$example = array_shift($values);
 		foreach( $values as $attributes )
 		{
 			if ( count(array_diff($example, $attributes)) > 0 || count(array_diff($attributes, $example)) > 0 ) return false;
 		}
 		
 		return true;
	}
	
	function drawCell( $object_it, $attr )
	{
		switch( $attr )
		{
			case 'State':
            	echo $this->getTable()->getView()->render('pm/StateColumn.php', array (
									'color' => $object_it->get('StateColor'),
									'name' => $object_it->get('StateName'),
									'terminal' => $object_it->get('StateTerminal') == 'Y'
							));
				break;
				
			default:
				parent::drawCell( $object_it, $attr );
		}
	}
	
	function getHeaderActions( $board_value )
	{
		$actions = parent::getHeaderActions($board_value);

		$custom_actions = array();
		
		$iterator = $this->getBoardAttributeIterator();
		$iterator->moveTo('ReferenceName', $board_value);
		
		if ( $iterator->getId() != '' && !$this->getTable()->hasCrossProjectFilter() )
		{
			$method = new ObjectModifyWebMethod($iterator);
			if ( $method->hasAccess() ) {
				$custom_actions[] = array (
						'name' => translate('Изменить'),
						'url' => $method->getJSCall() 
				);
				$custom_actions[] = array();
			}

			$method = new ObjectCreateNewWebMethod($iterator->object);
			if ( $method->hasAccess() ) {
				$custom_actions[] = array (
						'name' => text(2011),
						'url' => $method->getJSCall(array('OrderNum' => $iterator->get('OrderNum') + 2)) 
				);
				$custom_actions[] = array (
						'name' => text(2012),
						'url' => $method->getJSCall(array('OrderNum' => max(1,$iterator->get('OrderNum') - 2))) 
				);
				$custom_actions[] = array();
			}
		}
		
		return array_merge($custom_actions, $actions);
	}
}
