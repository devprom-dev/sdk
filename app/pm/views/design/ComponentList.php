<?php

class ComponentList extends PMPageList
{
	function getGroupFields()
	{
		$fields = array_diff(
            parent::getGroupFields(),
            array('ParentComponent', 'Children')
        );
		return $fields;
	}

    function getGroupDefault() {
        return '';
    }

    function drawCell( $object_it, $attr )
	{
		switch( $attr )
		{
		    default:
		    	parent::drawCell( $object_it, $attr );
		}
	}
}