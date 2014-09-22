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
		global $model_factory;

		$state = $model_factory->getObject($this->object->getStateClassName());
		
		$state->setVpdContext( $this );
		
		if ( $from_state == '' ) $from_state = $this->get('State');
		
		$source_it = $state->getByRef('ReferenceName', trim($from_state) );
		
		$target_it = $state->getByRef('ReferenceName', trim($to_state) );

		$transition = $model_factory->getObject('pm_Transition');

		$transition->setVpdContext( $this );

		return $transition->getByRefArray( array( 
		        'TargetState' => $target_it->getId(),
				'SourceState' => $source_it->getId() 
		));
	}
}
