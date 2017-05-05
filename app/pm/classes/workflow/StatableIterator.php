<?php

class StatableIterator extends OrderedIterator
{
    function getDisplayNameExt( $prefix = '' )
    {
        return $this->getStateTag().parent::getDisplayNameExt($prefix);
    }

    function getStateTag() {
        if ( $this->get('StateName') != '' ) {
            if ( strpos($this->get('StateColor'),'#') !== false ) {
                $title = '<span class="label" style="background:'.$this->get('StateColor').';'.ColorUtils::getTextStyle($this->get('StateColor')).'">'.$this->get('StateName').'</span> ';
            }
            else {
                $title = '<span class="label label-warning">'.$this->get('StateName').'</span> ';
            }
        }
        return $title;
    }

    function getStateIt() {
        $state_it = WorkflowScheme::Instance()->getStateIt($this->object);
        $stateRefName = $this->get('State');
        $vpd = $this->get('VPD');
        $data = array_filter($state_it->getRowset(), function($value) use ($stateRefName, $vpd) {
            return $value['ReferenceName'] == $stateRefName and $value['VPD'] == $vpd;
        });
		return $state_it->object->createCachedIterator(array_values($data));
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
