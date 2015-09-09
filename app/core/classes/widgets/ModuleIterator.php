<?php

class ModuleIterator extends OrderedIterator
{
	function get( $attribute )
	{
		return preg_replace_callback('/text\(([a-zA-Z\d]+)\)/i', iterator_text_callback, parent::get($attribute));
	}

 	function buildMenuItem( $query_string = '' )
 	{
 	    if ( !getFactory()->getAccessPolicy()->can_read($this) ) return array();

 	    return array( 
			'name' => $this->getDisplayName(), 
			'url' => strpos($query_string, '/') !== false 
 	    				? $this->get('Url').$query_string.'?' 
 	    				: $this->get('Url').($query_string != '' ? '?'.trim($query_string, '?') : ''),
			'uid' => preg_replace('/\//', '-', $this->getId()),
 	        'module' => $this->getId()
		);
 	}
}