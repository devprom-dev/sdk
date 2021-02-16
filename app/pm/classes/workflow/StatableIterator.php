<?php

class StatableIterator extends OrderedIterator
{
    function getDisplayNameExt( $prefix = '' )
    {
        return parent::getDisplayNameExt($this->getStateTag() . $prefix );
    }

    function getStateTag()
    {
        $stateIt = $this->getStateIt();
        if ( $stateIt->get('Caption') != '' ) {
            if ( strpos($stateIt->get('RelatedColor'),'#') !== false ) {
                $title = '<span class="label" style="background:'.$stateIt->get('RelatedColor').';'.ColorUtils::getTextStyle($stateIt->get('RelatedColor')).'">'.$stateIt->get('Caption').'</span> ';
            }
            else {
                $title = '<span class="label label-warning">'.$stateIt->get('Caption').'</span> ';
            }
        }
        return $title;
    }

    function getStateIt() {
        $state_it = WorkflowScheme::Instance()->getStateIt($this);
        $stateRefName = $this->get('State');
        $vpd = $this->get('VPD');
        $data = array_filter($state_it->getRowset(), function($value) use ($stateRefName, $vpd) {
            return $value['ReferenceName'] == $stateRefName and $value['VPD'] == $vpd;
        });
		return $state_it->object->createCachedIterator(array_values($data));
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
