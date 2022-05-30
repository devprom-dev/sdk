<?php

class ObjectHierarchyIterator extends OrderedIterator
{
    function getParentsArray() {
		return array_filter(
            preg_split('/,/',$this->get('ParentPath')), function($value) {
                return is_numeric($value);
		});		
	}
	
 	function getTransitiveRootArray()
	{
	    $roots = array();
        $parentAttribute = array_shift($this->object->getAttributesByGroup('hierarchy-parent'));
	    
		$parent_page = $this->getId();
		while( $parent_page != '' )
		{
		    $roots[] = $parent_page;
			$parent_page_it = $this->object->getExact($parent_page);
			if( $parent_page_it->get($parentAttribute) == '' ) break;
			$parent_page = $parent_page_it->get($parentAttribute);
		}
		
		return $roots;
	}
} 