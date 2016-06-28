<?php

class StatableIterator extends OrderedIterator
{
	function getStateIt() {
		return WorkflowScheme::Instance()->getStateIt($this->object, $this->get('State'));
	}
	
	function getStateName()
	{
	    if ( $this->get('StateName') != '' ) return $this->get('StateName');
		return $this->getStateIt()->getDisplayName();
	}
	
	function IsTransitable() {
		return count(WorkflowScheme::Instance()->getStates($this->object)) > 0;
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
}
