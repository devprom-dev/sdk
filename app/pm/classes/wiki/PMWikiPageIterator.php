<?php

class PMWikiPageIterator extends WikiPageIterator
{
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
	
	function getHistoryUrl()
	{
		$class_name = strtolower(get_class($this->object));
		
		return $this->object->getPageHistory().'object='.$class_name.'&'.$class_name.'='.$this->getId();
	}
}