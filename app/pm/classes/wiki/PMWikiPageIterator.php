<?php

class PMWikiPageIterator extends WikiPageIterator
{
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