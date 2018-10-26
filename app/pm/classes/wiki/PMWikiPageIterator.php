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

    function get($attr)
    {
        if ( $attr == 'StateName' && parent::get('StateNameAlt') != '' ) {
            return parent::get('StateNameAlt');
        }
        return parent::get($attr);
    }

    function getHistoryUrl()
	{
		$class_name = strtolower(get_class($this->object));
		
		return $this->object->getPageHistory().'object='.$class_name.'&'.$class_name.'='.$this->getId();
	}

    function getPageVersions() {
        $url = $this->object->getPageVersions();
        if ( $url == '' ) return $url;
        return $url.'page='.$this->getId();
    }
}