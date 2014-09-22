<?php

class ModuleIterator extends OrderedIterator
{
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