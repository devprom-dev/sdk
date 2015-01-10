<?php

class StatableIterator extends OrderedIterator
{
	function getStateIt()
	{
		$state_it = $this->object->cacheStates( $this );

		if ( $this->get('State') == '' )
		{
		    return $state_it->object->createCachedIterator(array());
		}
		else
		{
			$ref_name = $this->get('State');
			$vpd = $this->get('VPD');

    		return $state_it->object->createCachedIterator(
    				array_values(
    						array_filter( $state_it->getRowset(), function($value) use ($ref_name, $vpd) {
    							return $value['ReferenceName'] == $ref_name && $value['VPD'] == $vpd;
    						})
		    		)
    		);
		}
	}
	
	function getStateName()
	{
	    if ( $this->get('StateName') != '' ) return $this->get('StateName');
	    
	    $state_it = $this->getStateIt();
	    
		return $state_it->getId() != '' ? $state_it->getDisplayName() : '';
	}
	
	function IsTransitable()
	{
		$state_it = $this->object->cacheStates( $this );
		
		return $state_it->count() > 0;
	}
	
	function getRef( $attr, $object = null )
	{
		switch ( $attr )
		{
			case 'State':
				return $this->getStateIt();

			default:
				return parent::getRef( $attr, $object );
		}
	}
	
	function getTransitionTo( $to_state, $from_state = '' )
	{
		if ( $from_state == '' ) $from_state = $this->get('State');
		
		$state = getFactory()->getObject($this->object->getStateClassName());
		
		$source_it = $state->getRegistry()->Query(
					array (
							new FilterAttributePredicate('ReferenceName', trim($from_state)),
							new FilterBaseVpdPredicate()
					)
			);	
				
		$target_it = $state->getRegistry()->Query(
					array (
							new FilterAttributePredicate('ReferenceName', trim($to_state)),
							new FilterBaseVpdPredicate()
					)
			);

		return getFactory()->getObject('pm_Transition')->getByRefArray( array( 
		        'TargetState' => $target_it->getId(),
				'SourceState' => $source_it->getId() 
		));
	}
}
